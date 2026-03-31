<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\Employee;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Digital Vault - PeopleFlow HRMS')]
class DocumentVault extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $category = 'all';
    public $employeeId = '';
    
    // Upload Modal State
    public bool $showUploadModal = false;
    public $file;
    public $title = '';
    public $uploadCategory = 'other';
    public $targetEmployeeId = '';
    public $expiryDate = '';
    public $notes = '';
    public bool $isPrivate = false;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }

    public function mount()
    {
        $this->uploadCategory = array_key_first(Document::categories());
    }

    public function getCategoriesProperty()
    {
        return Document::categories();
    }

    public function getEmployeesProperty()
    {
        if (!Auth::user()->hasAnyRole(['admin', 'hr_manager'])) {
            return [];
        }

        return Employee::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);
    }

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|max:10240',
            'title' => 'required|string|max:255',
            'uploadCategory' => 'required|string|in:' . implode(',', array_keys(Document::categories())),
            'targetEmployeeId' => 'nullable|integer|exists:employees,id',
            'expiryDate' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $path = $this->file->store('documents/' . $tenantId, 'local');

        $doc = Document::create([
            'tenant_id' => $tenantId,
            'employee_id' => $this->targetEmployeeId ?: null,
            'title' => $this->title,
            'category' => $this->uploadCategory,
            'file_path' => $path,
            'file_name' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
            'mime_type' => $this->file->getMimeType(),
            'expiry_date' => $this->expiryDate ?: null,
            'notes' => $this->notes ?: null,
            'is_private' => $this->isPrivate,
            'uploaded_by' => Auth::id(),
        ]);

        ActivityLogger::log('created', $doc, ['title' => $doc->title, 'category' => $doc->category]);

        $this->reset(['file', 'title', 'expiryDate', 'notes', 'isPrivate', 'targetEmployeeId', 'showUploadModal']);
        $this->dispatch('notify', message: 'Document archived in vault.', type: 'success');
    }

    public function downloadDocument($id)
    {
        $doc = Document::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $this->authorizeAccess($doc);
        return Storage::disk('local')->download($doc->file_path, $doc->file_name);
    }

    public function deleteDocument($id)
    {
        $user = Auth::user();
        $doc = Document::where('tenant_id', $user->tenant_id)->findOrFail($id);

        if (!$user->hasAnyRole(['admin', 'hr_manager']) && $doc->uploaded_by !== $user->id) {
            return;
        }

        ActivityLogger::log('deleted', $doc, ['title' => $doc->title]);
        $doc->delete();
        $this->dispatch('notify', message: 'Document purged from vault.', type: 'success');
    }

    private function authorizeAccess(Document $doc)
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['admin', 'hr_manager'])) return;

        $employee = Employee::where('email', $user->email)->where('tenant_id', $user->tenant_id)->first();
        if ($doc->employee_id && (!$employee || $doc->employee_id !== $employee->id)) abort(403);
        if ($doc->is_private && (!$employee || $doc->employee_id !== $employee->id)) abort(403);
    }

    public function render()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $query = Document::where('tenant_id', $tenantId);

        if (!$user->hasAnyRole(['admin', 'hr_manager'])) {
            $employee = Employee::where('email', $user->email)->where('tenant_id', $tenantId)->first();
            $query->where(function ($q) use ($employee) {
                if ($employee) $q->where('employee_id', $employee->id);
                $q->orWhere(function ($q2) {
                    $q2->whereNull('employee_id')->where('is_private', false);
                });
            });
        }

        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }

        if ($this->employeeId && $user->hasAnyRole(['admin', 'hr_manager'])) {
            $query->where('employee_id', $this->employeeId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('file_name', 'like', "%{$this->search}%");
            });
        }

        $documents = $query->with(['employee:id,full_name', 'uploader:id,name'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('livewire.documents.document-vault', [
            'documents' => $documents
        ]);
    }
}
