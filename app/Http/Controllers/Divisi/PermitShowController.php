<?php

namespace App\Http\Controllers\Divisi;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PermitShowController extends Controller
{
    public function show($id)
    {
        $userId = Auth::id();

        $permit = Permit::with('documents')
                         ->where('user_id', $userId)
                         ->where('id', $id)
                         ->firstOrFail();

        return view('divisi.permits.show', compact('permit'));
    }

    public function viewPdf($id)
    {
        $permit = $this->ownedPermit($id);

        return $this->permitPdf($permit)->stream($this->permitPdfName($permit));
    }

    public function downloadPdf($id)
    {
        $permit = $this->ownedPermit($id);

        return $this->permitPdf($permit)->download($this->permitPdfName($permit));
    }

    private function ownedPermit($id): Permit
    {
        return Permit::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
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
