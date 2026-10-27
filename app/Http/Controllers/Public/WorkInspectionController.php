<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Permit;
use App\Models\WorkInspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class WorkInspectionController extends Controller
{
    public function create()
    {
        return view('public.work-inspections.create');
    }

    /** Form daftar periksa yang terhubung ke permit yang menunggu daftar periksa. */
    public function createForPermit(Permit $permit)
    {
        if (! $permit->needsWorkInspection()) {
            return redirect(URL::signedRoute('public.permits.success', ['permit' => $permit->id]))
                ->with('info', 'Permit ini tidak menunggu daftar periksa.');
        }

        $activeRequest = $permit->requestedInspections()
            ->whereIn('status', [WorkInspection::STATUS_PENDING, WorkInspection::STATUS_APPROVED])
            ->latest()
            ->first();

        if ($activeRequest) {
            return redirect(URL::signedRoute('public.work-inspections.success', ['workInspection' => $activeRequest->id]))
                ->with('info', 'Daftar periksa untuk permit ini sudah diajukan.');
        }

        return view('public.work-inspections.create', compact('permit'));
    }

    public function store(Request $request)
    {
        return $this->storeInspection($request);
    }

    /** Simpan daftar periksa yang terhubung ke permit tertentu. */
    public function storeForPermit(Request $request, Permit $permit)
    {
        if (! $permit->needsWorkInspection()) {
            throw ValidationException::withMessages([
                'permit' => 'Permit ini tidak menunggu daftar periksa.',
            ]);
        }

        return $this->storeInspection($request, $permit);
    }

    private function storeInspection(Request $request, ?Permit $permit = null)
    {
        $validated = $request->validate([
            'divisi_pengaju' => 'required|string|max:255',
            'tipe' => 'required|in:Internal,Eksternal',
            'site' => 'required|in:Madiun,Banyuwangi',
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
        ]);

        if ($permit && ($permit->site !== $validated['site'] || $permit->tipe !== $validated['tipe'])) {
            throw ValidationException::withMessages([
                'site' => 'Site dan tipe daftar periksa harus sama dengan Safety Permit yang diajukan.',
            ]);
        }

        $answers = [];

        foreach (array_keys(WorkInspection::ANSWER_LABELS) as $key) {
            $answers[$key] = $request->boolean('answers.' . $key);
        }

        $answers['izin_lain_ket'] = $validated['answers']['izin_lain_ket'] ?? null;
        $answers['apd_khusus_ket'] = $validated['answers']['apd_khusus_ket'] ?? null;

        $inspection = DB::transaction(function () use ($validated, $answers, $permit) {
            $year = date('Y');
            $sequence = WorkInspection::whereYear('created_at', $year)->count() + 1;

            return WorkInspection::create([
                'no_periksa' => sprintf('%03d/DPIKBT/INKA/%d', $sequence, $year),
                'site' => $validated['site'],
                'requested_permit_id' => $permit?->id,
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
                'status' => WorkInspection::STATUS_PENDING,
                'submitted_at' => now(),
            ]);
        });

        return redirect(URL::signedRoute('public.work-inspections.success', ['workInspection' => $inspection->id]));
    }

    public function success(WorkInspection $workInspection)
    {
        $permit = $workInspection->requestedPermit;

        return view('public.work-inspections.success', [
            'inspection' => $workInspection,
            'requestedPermit' => $permit,
            'permitSuccessUrl' => $permit
                ? URL::signedRoute('public.permits.success', ['permit' => $permit->id])
                : null,
            'statusUrl' => URL::signedRoute('public.work-inspections.status.form'),
            'successUrl' => URL::signedRoute('public.work-inspections.success', ['workInspection' => $workInspection->id]),
        ]);
    }

    public function statusForm()
    {
        return view('public.work-inspections.status');
    }

    public function statusShow(Request $request)
    {
        $validated = $request->validate([
            'no_periksa' => 'required|string|max:100',
            'telepon' => 'required|string|max:50',
        ]);

        $inspection = WorkInspection::where('no_periksa', $validated['no_periksa'])
            ->where('telepon', $validated['telepon'])
            ->first();

        if (! $inspection) {
            return back()
                ->withInput()
                ->withErrors([
                    'no_periksa' => 'Data daftar periksa tidak ditemukan. Periksa kembali nomor daftar periksa dan nomor telepon yang dipakai saat mengajukan.',
                ]);
        }

        return view('public.work-inspections.status', compact('inspection'));
    }

    /** Unduh PDF daftar periksa (hanya bila sudah disetujui). Route bertanda tangan (signed). */
    public function downloadPdf(WorkInspection $workInspection)
    {
        if ($workInspection->status !== WorkInspection::STATUS_APPROVED) {
            abort(403, 'Daftar periksa belum disetujui, PDF belum dapat diunduh.');
        }

        return Pdf::loadView('admin.work-inspections.pdf', ['inspection' => $workInspection])
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif'])
            ->download('Daftar-Periksa-' . str_replace('/', '-', $workInspection->no_periksa) . '.pdf');
    }
}
