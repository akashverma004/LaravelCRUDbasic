<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Support\TenantContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Controller for Payroll Binary Responses (PDF Downloads).
 * All UI logic is now handled by Livewire Components.
 */
class PayrollController extends Controller
{
    /**
     * Download a payslip as a PDF.
     */
    public function downloadPdf(Payslip $payslip): Response
    {
        $pdf      = $this->generatePdf($payslip);
        $employee = $payslip->employee;
        $filename = 'Payslip_' . str_replace(' ', '_', $payslip->month) . '_' . str_replace(' ', '_', $employee->full_name) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preview a payslip in the browser.
     */
    public function viewPdf(Payslip $payslip): Response
    {
        $pdf = $this->generatePdf($payslip);
        return $pdf->stream();
    }

    /**
     * Internal helper to generate the PDF instance with shared configuration.
     */
    private function generatePdf(Payslip $payslip): \Barryvdh\DomPDF\PDF
    {
        $payslip->load([
            'employee.department',
            'employee.payStructure',
        ]);

        $employee     = $payslip->employee;
        $payStructure = $employee->payStructure;
        $tenant       = \App\Models\Tenant::find(TenantContext::id());
        $details      = $payslip->details ?? [];

        // Prepare Base64 assets for PDF reliable rendering
        $logoBase64      = $this->getBase64Asset($tenant->logo_path);
        $signatureBase64 = $this->getBase64Asset($tenant->signature_path);
        $stampBase64     = $this->getBase64Asset($tenant->stamp_path);

        return Pdf::loadView('hrms.payroll.payslip-pdf', compact(
            'payslip',
            'employee',
            'payStructure',
            'tenant',
            'details',
            'logoBase64',
            'signatureBase64',
            'stampBase64'
        ))
        ->setPaper('a4', 'portrait')
        ->setOption('defaultFont', 'Helvetica')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);
    }

    /**
     * Helper to convert storage path to base64 for DomPDF.
     * Hardened to handle paths with and without 'public/' prefix.
     */
    private function getBase64Asset(?string $path): ?string
    {
        if (!$path) return null;

        // Try 'public' disk first for all corporate/tenant assets
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            $imageData = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        } 
        // Fallback to local if specifically prefixed or in root
        elseif (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            $imageData = \Illuminate\Support\Facades\Storage::disk('local')->get($path);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        } 
        // Handle common 'public/' prefix in path string
        elseif (str_starts_with($path, 'public/')) {
            $strippedPath = str_replace('public/', '', $path);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($strippedPath)) {
                $imageData = \Illuminate\Support\Facades\Storage::disk('public')->get($strippedPath);
                $ext = strtolower(pathinfo($strippedPath, PATHINFO_EXTENSION));
            } else {
                return null;
            }
        } else {
            return null;
        }

        try {
            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'jpeg' : ($ext === 'png' ? 'png' : $ext);
            return 'data:image/' . $mime . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            return null;
        }
    }
}
