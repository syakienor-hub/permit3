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
        $this->authorizeSite();

        $query = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->with('reviewer');

        if ($request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
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

        $inspections = $query->orderByRaw('COALESCE(submitted_at, created_at) DESC')
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

    public function create()
    {
        $this->authorizeSite();

        return view('admin.work-inspections.create', [
            'site' => Auth::user()->site,
        ]);
    }

    public function store(Request $request)
    {
        $site = $this->authorizeSite();

        $validated = $request->validate([
            'divisi_pengaju' => 'required|string|max:255',
            'tipe' => 'required|in:Internal,Eksternal',
            'nama_pekerjaan' => 'required|string|max:255',
            'kontraktor' => 'required|string|max:255',
            'jenis_peralatan' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'answers' => 'required|array',
            'answers.izin_lain' => 'required|boolean',
            'answers.izin_lain_ket' => 'nullable|string|max:500',
            'answers.listrik_diputus' => 'required|boolean',
            'answers.keran_ditutup' => 'required|boolean',
            'answers.pipa_dikosongkan' => 'required|boolean',
            'answers.tanda_bahaya' => 'required|boolean',
            'answers.karyawan_diberitahu' => 'required|boolean',
            'answers.alat_listrik_diputus' => 'required|boolean',
            'answers.pipa_fluida_diputus' => 'required|boolean',
            'answers.pipa_dikeringkan' => 'required|boolean',
            'answers.energi_dilepas' => 'required|boolean',
            'answers.tes_energi' => 'required|boolean',
            'answers.masih_ada_aliran' => 'required|boolean',
            'answers.supervisor_siap' => 'required|boolean',
            'answers.apd_khusus' => 'required|boolean',
            'answers.apd_khusus_ket' => 'nullable|string|max:500',
            'answers.perancah_baik' => 'required|boolean',
            'answers.barikade' => 'required|boolean',
            'answers.apar' => 'required|boolean',
            'answers.evakuasi_diberitahu' => 'required|boolean',
            'answers.safety_briefing' => 'required|boolean',
            'loto_nama' => 'required|string|max:255',
            'loto_tanggal' => 'required|date',
            'loto_jam' => 'required|date_format:H:i',
            'tanda_tangan' => 'required|string',
            'supervisor_nama' => 'nullable|string|max:255',
            'supervisor_tanggal' => 'nullable|date',
            'supervisor_jam' => 'nullable|date_format:H:i',
            'safety_officer_nama' => 'required|string|max:255',
            'staff_signature' => 'required|string',
        ]);

        $answers = [];

        foreach (array_keys(WorkInspection::ANSWER_LABELS) as $key) {
            $answers[$key] = $request->boolean('answers.' . $key);
        }

        $answers['izin_lain_ket'] = $validated['answers']['izin_lain_ket'] ?? null;
        $answers['apd_khusus_ket'] = $validated['answers']['apd_khusus_ket'] ?? null;

        $inspection = DB::transaction(function () use ($validated, $answers, $site) {
            $year = date('Y');
            $sequence = WorkInspection::whereYear('created_at', $year)->count() + 1;

            return WorkInspection::create([
                'no_periksa' => sprintf('%03d/DPIKBT/INKA/%d', $sequence, $year),
                'site' => $site,
                'tipe' => $validated['tipe'],
                'divisi_pengaju' => $validated['divisi_pengaju'],
                'nama_pekerjaan' => $validated['nama_pekerjaan'],
                'kontraktor' => $validated['kontraktor'],
                'jenis_peralatan' => $validated['jenis_peralatan'] ?? null,
                'lokasi' => $validated['lokasi'],
                'telepon' => $validated['telepon'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'answers' => $answers,
                'loto_nama' => $validated['loto_nama'],
                'loto_tanggal' => $validated['loto_tanggal'],
                'loto_jam' => $validated['loto_jam'],
                'tanda_tangan' => $validated['tanda_tangan'],
                'supervisor_nama' => $validated['supervisor_nama'] ?? null,
                'supervisor_tanggal' => $validated['supervisor_tanggal'] ?? null,
                'supervisor_jam' => $validated['supervisor_jam'] ?? null,
                'safety_officer_nama' => $validated['safety_officer_nama'],
                'staff_signature' => $validated['staff_signature'],
                'status' => WorkInspection::STATUS_APPROVED,
                'submitted_at' => now(),
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect('/admin/work-inspections/' . $inspection->id)
            ->with('success', 'Daftar Periksa berhasil disimpan dan ditandatangani.');
    }

    public function history(Request $request)
    {
        $this->authorizeSite();

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
        $this->authorizeSite();

        $inspection = WorkInspection::query()
            ->visibleTo(Auth::user())
            ->with('reviewer')
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
        $this->authorizeSite();

        return WorkInspection::query()
            ->visibleTo(Auth::user())
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
        $this->authorizeSite();

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
        $this->authorizeSite();

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

    private function authorizeSite(): string
    {
        $user = Auth::user();

        if (($user->role ?? null) !== 'staff' || ! in_array($user->site, WorkInspection::SITES, true)) {
            abort(403, 'Daftar Periksa hanya dapat diakses oleh Staff HSE site Madiun atau Banyuwangi.');
        }

        return $user->site;
    }
}
