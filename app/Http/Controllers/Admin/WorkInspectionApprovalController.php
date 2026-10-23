<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkInspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkInspectionApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->with('reviewer');

        if ($request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        } elseif (! $request->filled('status')) {
            $query->where('status', WorkInspection::STATUS_PENDING);
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->search) . '%';
            $query->where(function ($sub) use ($term) {
                $sub->where('no_periksa', 'like', $term)
                    ->orWhere('nama_pekerjaan', 'like', $term)
                    ->orWhere('kontraktor', 'like', $term)
                    ->orWhere('lokasi', 'like', $term);
            });
        }

        $inspections = $query->orderByRaw('COALESCE(submitted_at, created_at) ASC')
            ->paginate(15)
            ->withQueryString();

        $visible = WorkInspection::query()->visibleTo(Auth::user());

        $counts = [
            'pending' => (clone $visible)->where('status', WorkInspection::STATUS_PENDING)->count(),
            'approved' => (clone $visible)->where('status', WorkInspection::STATUS_APPROVED)->count(),
            'rejected' => (clone $visible)->where('status', WorkInspection::STATUS_REJECTED)->count(),
        ];

        return view('admin.work-inspections.index', compact('inspections', 'counts'));
    }

    public function history(Request $request)
    {
        $query = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->with('reviewer')
            ->whereIn('status', [WorkInspection::STATUS_APPROVED, WorkInspection::STATUS_REJECTED]);

        if ($request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('status') && in_array($request->status, [WorkInspection::STATUS_APPROVED, WorkInspection::STATUS_REJECTED], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->search) . '%';
            $query->where(function ($sub) use ($term) {
                $sub->where('no_periksa', 'like', $term)
                    ->orWhere('nama_pekerjaan', 'like', $term)
                    ->orWhere('kontraktor', 'like', $term)
                    ->orWhere('lokasi', 'like', $term);
            });
        }

        $inspections = $query->orderByRaw('COALESCE(reviewed_at, updated_at) DESC')
            ->paginate(15)
            ->withQueryString();

        $visible = WorkInspection::query()->visibleTo(Auth::user());

        $counts = [
            'approved' => (clone $visible)->where('status', WorkInspection::STATUS_APPROVED)->count(),
            'rejected' => (clone $visible)->where('status', WorkInspection::STATUS_REJECTED)->count(),
        ];

        return view('admin.work-inspections.history', compact('inspections', 'counts'));
    }

    public function show($id)
    {
        $inspection = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->with(['reviewer', 'permit'])
            ->findOrFail($id);

        return view('admin.work-inspections.show', compact('inspection'));
    }

    public function viewPdf($id)
    {
        $inspection = $this->pdfInspection($id);

        return $this->inspectionPdf($inspection)->stream($this->inspectionPdfName($inspection));
    }

    public function downloadPdf($id)
    {
        $inspection = $this->pdfInspection($id);

        return $this->inspectionPdf($inspection)->download($this->inspectionPdfName($inspection));
    }

    private function pdfInspection($id): WorkInspection
    {
        return WorkInspection::query()
            ->visibleTo(Auth::user())
            ->with(['reviewer', 'permit'])
            ->findOrFail($id);
    }

    private function inspectionPdf(WorkInspection $inspection)
    {
        return Pdf::loadView('admin.work-inspections.pdf', compact('inspection'))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);
    }

    private function inspectionPdfName(WorkInspection $inspection): string
    {
        return 'Daftar-Periksa-' . str_replace('/', '-', $inspection->no_periksa) . '.pdf';
    }

    public function approve(Request $request, $id)
    {
        $inspection = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->findOrFail($id);

        if ($inspection->status !== WorkInspection::STATUS_PENDING) {
            return redirect('/admin/work-inspections/' . $inspection->id)
                ->with('error', 'Daftar periksa ini sudah diproses.');
        }

        $validated = $request->validate([
            'safety_officer_nama' => 'required|string|max:255',
            'tanda_tangan' => 'required|string',
        ]);

        $inspection->forceFill([
            'status' => WorkInspection::STATUS_APPROVED,
            'safety_officer_nama' => $validated['safety_officer_nama'],
            'staff_signature' => $validated['tanda_tangan'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ])->save();

        return redirect('/admin/work-inspections/' . $inspection->id)
            ->with('success', 'Daftar Periksa berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $inspection = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->findOrFail($id);

        if ($inspection->status !== WorkInspection::STATUS_PENDING) {
            return redirect('/admin/work-inspections/' . $inspection->id)
                ->with('error', 'Daftar periksa ini sudah diproses.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $inspection->forceFill([
            'status' => WorkInspection::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'safety_officer_nama' => null,
            'staff_signature' => null,
        ])->save();

        return redirect('/admin/work-inspections/' . $inspection->id)
            ->with('success', 'Daftar Periksa ditolak dan alasan sudah dicatat.');
    }
}
