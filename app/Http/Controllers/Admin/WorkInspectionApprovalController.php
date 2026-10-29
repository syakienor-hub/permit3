<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkInspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkInspectionApprovalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeViewer();

        $query = $this->visibleQuery()->with('reviewer');

        if ($this->currentRole() === 'manager' && $request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        } elseif ($this->currentRole() === 'manager') {
            $query->where('status', WorkInspection::STATUS_REVIEW_MANAGER);
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

        $inspections = $query->orderByRaw('COALESCE(submitted_at, created_at) DESC')
            ->paginate(15)
            ->withQueryString();

        $visible = $this->visibleQuery();

        $counts = [
            'review' => (clone $visible)->where('status', WorkInspection::STATUS_REVIEW_MANAGER)->count(),
            'pending' => (clone $visible)->where('status', WorkInspection::STATUS_PENDING)->count(),
            'approved' => (clone $visible)->where('status', WorkInspection::STATUS_APPROVED)->count(),
            'rejected' => (clone $visible)->where('status', WorkInspection::STATUS_REJECTED)->count(),
        ];

        return view('admin.work-inspections.index', [
            'inspections' => $inspections,
            'counts' => $counts,
            'isManager' => $this->currentRole() === 'manager',
        ]);
    }

    public function create()
    {
        $site = $this->authorizeCreator();

        return view('admin.work-inspections.create', [
            'site' => $site,
        ]);
    }

    public function store(Request $request)
    {
        $site = $this->authorizeCreator();

        $rules = [
            'otorisasi' => 'nullable|string|max:255',
            'nama_pekerjaan' => 'required|string|max:255',
            'jenis_peralatan' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'petugas_pemeriksa' => 'required|string|max:255',
            'staff_signature' => 'required|string',
        ];

        foreach (array_keys(WorkInspection::ITEMS) as $key) {
            $rules['answers.' . $key] = 'required|in:ok,nok,na';
        }

        $validated = $request->validate($rules);

        $answers = [];

        foreach (array_keys(WorkInspection::ITEMS) as $key) {
            $answers[$key] = $validated['answers'][$key];
        }

        $inspection = DB::transaction(function () use ($validated, $answers, $site) {
            $year = date('Y');
            $sequence = WorkInspection::whereYear('created_at', $year)->count() + 1;

            return WorkInspection::create([
                'no_periksa' => sprintf('%03d/DPIKBT/INKA/%d', $sequence, $year),
                'tanggal_terbit' => now()->toDateString(),
                'site' => $site,
                'otorisasi' => $validated['otorisasi'] ?? null,
                'tipe' => 'Internal',
                'divisi_pengaju' => 'HSE PT INKA',
                'nama_pekerjaan' => $validated['nama_pekerjaan'],
                'kontraktor' => 'PT INKA (Persero)',
                'jenis_peralatan' => $validated['jenis_peralatan'] ?? null,
                'material' => $validated['material'] ?? null,
                'lokasi' => $validated['lokasi'],
                'telepon' => '-',
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'petugas_pemeriksa' => $validated['petugas_pemeriksa'],
                'answers' => $answers,
                'tanda_tangan' => '',
                'safety_officer_nama' => Auth::user()->name,
                'manager_nama' => null,
                'staff_signature' => $validated['staff_signature'],
                'status' => WorkInspection::STATUS_REVIEW_MANAGER,
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);
        });

        return redirect('/admin/work-inspections/' . $inspection->id)
            ->with('success', 'Daftar Periksa berhasil ditandatangani Staff HSE dan diteruskan ke Manager HSE.');
    }

    public function history(Request $request)
    {
        $this->authorizeViewer();

        $query = $this->visibleQuery()
            ->with('reviewer')
            ->whereIn('status', [WorkInspection::STATUS_APPROVED, WorkInspection::STATUS_REJECTED]);

        if ($this->currentRole() === 'manager' && $request->filled('site')) {
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

        $visible = $this->visibleQuery();

        $counts = [
            'approved' => (clone $visible)->where('status', WorkInspection::STATUS_APPROVED)->count(),
            'rejected' => (clone $visible)->where('status', WorkInspection::STATUS_REJECTED)->count(),
        ];

        return view('admin.work-inspections.history', [
            'inspections' => $inspections,
            'counts' => $counts,
            'isManager' => $this->currentRole() === 'manager',
        ]);
    }

    public function show($id)
    {
        $this->authorizeViewer();

        $inspection = $this->visibleQuery()
            ->with('reviewer')
            ->findOrFail($id);

        return view('admin.work-inspections.show', [
            'inspection' => $inspection,
            'isManager' => $this->currentRole() === 'manager',
            'isStaff' => $this->currentRole() === 'staff',
        ]);
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
        $this->authorizeViewer();

        return $this->visibleQuery()
            ->with('reviewer')
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
        $this->authorizeViewer();

        $inspection = $this->visibleQuery()->findOrFail($id);

        if ($this->currentRole() === 'manager') {
            return $this->approveAsManager($request, $inspection);
        }

        return $this->approveLegacyAsStaff($request, $inspection);
    }

    private function approveAsManager(Request $request, WorkInspection $inspection)
    {
        if ($inspection->status !== WorkInspection::STATUS_REVIEW_MANAGER) {
            return redirect('/admin/work-inspections/' . $inspection->id)
                ->with('error', 'Daftar periksa ini tidak menunggu persetujuan Manager HSE.');
        }

        $validated = $request->validate([
            'manager_nama' => 'required|string|max:255',
            'tanda_tangan' => 'required|string',
        ]);

        $inspection->forceFill([
            'status' => WorkInspection::STATUS_APPROVED,
            'manager_nama' => $validated['manager_nama'],
            'tanda_tangan' => $validated['tanda_tangan'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ])->save();

        return redirect('/admin/work-inspections/' . $inspection->id)
            ->with('success', 'Daftar Periksa disetujui Manager HSE.');
    }

    private function approveLegacyAsStaff(Request $request, WorkInspection $inspection)
    {
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
        $this->authorizeViewer();

        $inspection = $this->visibleQuery()->findOrFail($id);

        if ($this->currentRole() === 'manager') {
            if ($inspection->status !== WorkInspection::STATUS_REVIEW_MANAGER) {
                return redirect('/admin/work-inspections/' . $inspection->id)
                    ->with('error', 'Daftar periksa ini tidak menunggu persetujuan Manager HSE.');
            }
        } elseif ($inspection->status !== WorkInspection::STATUS_PENDING) {
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
        ])->save();

        return redirect('/admin/work-inspections/' . $inspection->id)
            ->with('success', 'Daftar Periksa ditolak dan alasan sudah dicatat.');
    }

    private function currentRole(): string
    {
        return (string) (Auth::user()->role ?? '');
    }

    private function authorizeViewer(): void
    {
        $user = Auth::user();

        if (! in_array($user->role ?? null, ['staff', 'manager'], true)) {
            abort(403, 'Daftar Periksa hanya dapat diakses oleh Staff HSE dan Manager HSE.');
        }

        if (($user->role ?? null) === 'staff' && ! in_array($user->site, WorkInspection::SITES, true)) {
            abort(403, 'Daftar Periksa hanya dapat diakses oleh Staff HSE site Madiun atau Banyuwangi.');
        }
    }

    private function authorizeCreator(): string
    {
        $user = Auth::user();

        if (($user->role ?? null) !== 'staff' || ! in_array($user->site, WorkInspection::SITES, true)) {
            abort(403, 'Daftar Periksa hanya dapat dibuat oleh Staff HSE site Madiun atau Banyuwangi.');
        }

        return $user->site;
    }

    private function visibleQuery()
    {
        $query = WorkInspection::query();

        if ($this->currentRole() === 'staff') {
            $query->visibleTo(Auth::user());
        }

        return $query;
    }
}
