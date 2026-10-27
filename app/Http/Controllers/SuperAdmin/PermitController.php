<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Barryvdh\DomPDF\Facade\Pdf;

class PermitController extends Controller
{
    public function viewPdf($id)
    {
        $permit = $this->pdfPermit($id);

        return $this->permitPdf($permit)->stream($this->permitPdfName($permit));
    }

    public function downloadPdf($id)
    {
        $permit = $this->pdfPermit($id);

        return $this->permitPdf($permit)->download($this->permitPdfName($permit));
    }

    private function pdfPermit($id): Permit
    {
        $permit = Permit::findOrFail($id);

        $allowedStatuses = [Permit::STATUS_WAITING_INSPECTION, 'Review Staff', 'Review Manager', 'Revision', 'Active', 'Closed', 'Cancelled'];
        if (!in_array($permit->status, $allowedStatuses)) {
            abort(403, 'Permit tidak tersedia untuk diakses.');
        }

        return $permit;
    }

    private function permitPdf(Permit $permit)
    {
        return Pdf::loadView('divisi.permits.pdf', compact('permit'))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);
    }

    private function permitPdfName(Permit $permit): string
    {
        return 'Permit-' . str_replace('/', '-', $permit->no_permit) . '.pdf';
    }
}
