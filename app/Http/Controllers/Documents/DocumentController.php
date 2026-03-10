<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Employee;
use App\Support\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Show the documents page.
     */
    public function index(): View
    {
        return view('hrms.documents.index');
    }

    /**
     * Fetch documents as JSON (async).
     */
    public function data(Request $request): JsonResponse
    {
        $tenantId = TenantContext::id();
        $user = auth()->user();

        $query = Document::where('tenant_id', $tenantId);

        // Non-admin users see only their own documents + non-private company docs
        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)
                ->where('tenant_id', $tenantId)
                ->first();

            $query->where(function ($q) use ($employee) {
                if ($employee) {
                    $q->where('employee_id', $employee->id);
                }
                $q->orWhere(function ($q2) {
                    $q2->whereNull('employee_id')->where('is_private', false);
                });
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by employee (admin only)
        if ($request->filled('employee_id') && $user->hasAnyRole(['admin', 'hr_manager'])) {
            $query->where('employee_id', $request->employee_id);
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('title', 'like', "%{$q}%")
                   ->orWhere('file_name', 'like', "%{$q}%")
                   ->orWhere('notes', 'like', "%{$q}%");
            });
        }

        $documents = $query->with(['employee:id,full_name', 'uploader:id,name'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'documents' => $documents->through(fn($doc) => [
                'id' => $doc->id,
                'title' => $doc->title,
                'category' => $doc->category,
                'category_label' => Document::categories()[$doc->category] ?? $doc->category,
                'file_name' => $doc->file_name,
                'file_size' => $doc->readable_size,
                'mime_type' => $doc->mime_type,
                'expiry_date' => $doc->expiry_date?->format('Y-m-d'),
                'expiry_display' => $doc->expiry_date?->format('d M Y'),
                'is_expired' => $doc->isExpired(),
                'expires_soon' => $doc->expiresSoon(),
                'is_private' => $doc->is_private,
                'notes' => $doc->notes,
                'employee_name' => $doc->employee?->full_name,
                'employee_id' => $doc->employee_id,
                'uploaded_by' => $doc->uploader?->name,
                'created_at' => $doc->created_at->format('d M Y'),
            ]),
            'categories' => Document::categories(),
        ]);
    }

    /**
     * Upload a new document.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(Document::categories())),
            'employee_id' => 'nullable|integer|exists:employees,id',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'is_private' => 'nullable|boolean',
        ]);

        $tenantId = TenantContext::id();
        $file = $request->file('file');

        $path = $file->store(
            'documents/' . $tenantId,
            'local'  // stored in private storage (not public)
        );

        $doc = Document::create([
            'tenant_id' => $tenantId,
            'employee_id' => $validated['employee_id'] ?? null,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'expiry_date' => $validated['expiry_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_private' => $validated['is_private'] ?? false,
            'uploaded_by' => auth()->id(),
        ]);

        $doc->load(['employee:id,full_name', 'uploader:id,name']);

        \App\Support\ActivityLogger::log('created', $doc, ['title' => $doc->title, 'category' => $doc->category]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'document' => [
                'id' => $doc->id,
                'title' => $doc->title,
                'category' => $doc->category,
                'category_label' => Document::categories()[$doc->category] ?? $doc->category,
                'file_name' => $doc->file_name,
                'file_size' => $doc->readable_size,
                'mime_type' => $doc->mime_type,
                'expiry_date' => $doc->expiry_date?->format('Y-m-d'),
                'expiry_display' => $doc->expiry_date?->format('d M Y'),
                'is_expired' => false,
                'expires_soon' => false,
                'is_private' => $doc->is_private,
                'notes' => $doc->notes,
                'employee_name' => $doc->employee?->full_name,
                'employee_id' => $doc->employee_id,
                'uploaded_by' => $doc->uploader?->name,
                'created_at' => $doc->created_at->format('d M Y'),
            ],
        ]);
    }

    /**
     * Download a document.
     */
    public function download(int $id)
    {
        $doc = Document::where('tenant_id', TenantContext::id())->findOrFail($id);

        $this->authorizeAccess($doc);

        return Storage::disk('local')->download($doc->file_path, $doc->file_name);
    }

    /**
     * Delete a document.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = auth()->user();
        $doc = Document::where('tenant_id', TenantContext::id())->findOrFail($id);

        // Only admin/HR or the uploader can delete
        if (!$user->hasAnyRole(['admin', 'hr_manager']) && $doc->uploaded_by !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        \App\Support\ActivityLogger::log('deleted', $doc, ['title' => $doc->title]);

        Storage::disk('local')->delete($doc->file_path);
        $doc->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted.']);
    }

    /**
     * Get employees list for the upload form (admin/HR only).
     */
    public function employees(): JsonResponse
    {
        if (!auth()->user()->hasAnyRole(['admin', 'hr_manager'])) {
            return response()->json(['employees' => []], 403);
        }

        $employees = Employee::where('tenant_id', TenantContext::id())
            ->orderBy('full_name')
            ->get(['id', 'full_name']);

        return response()->json(['employees' => $employees]);
    }

    /**
     * Authorize document access.
     */
    private function authorizeAccess(Document $doc): void
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'hr_manager'])) {
            return; // Full access
        }

        // Employees can see their own docs and non-private company docs
        $employee = Employee::where('email', $user->email)
            ->where('tenant_id', $doc->tenant_id)
            ->first();

        if ($doc->employee_id && (!$employee || $doc->employee_id !== $employee->id)) {
            abort(403);
        }

        if ($doc->is_private && (!$employee || $doc->employee_id !== $employee->id)) {
            abort(403);
        }
    }
}
