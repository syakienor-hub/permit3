<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use App\Services\HsePlanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HsePlanApprovalController extends Controller
{
    private const FILE_RULES = 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png';

    public function __construct(private HsePlanPdfService $pdfService)
    {
    }

    public function index(Request $request)
    {
        $query = HsePlan::query()
            ->with('reviewer')
            ->where('status', '!=', HsePlan::STATUS_FINAL);

        if ($request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        } elseif (! $request->filled('status')) {
            $query->where('status', HsePlan::STATUS_REVIEW);
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->search) . '%';
            $query->where(function ($sub) use ($term) {
                $sub->where('no_hse_plan', 'like', $term)
                    ->orWhere('no_po', 'like', $term)
                    ->orWhere('nama_kontraktor', 'like', $term)
                    ->orWhere('deskripsi_pekerjaan', 'like', $term);
            });
        }

        $hsePlans = $query->orderByRaw('COALESCE(submitted_at, created_at) ASC')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'review' => HsePlan::where('status', HsePlan::STATUS_REVIEW)->count(),
            'approved' => HsePlan::where('status', HsePlan::STATUS_APPROVED)->count(),
            'rejected' => HsePlan::where('status', HsePlan::STATUS_REJECTED)->count(),
        ];

        return view('admin.hse-plans.index', compact('hsePlans', 'counts'));
    }

    public function history(Request $request)
    {
        $query = HsePlan::query()
            ->with('reviewer')
            ->whereIn('status', [HsePlan::STATUS_APPROVED, HsePlan::STATUS_REJECTED]);

        if ($request->filled('site')) {
            $query->forSite($request->site);
        }

        if ($request->filled('status') && in_array($request->status, [HsePlan::STATUS_APPROVED, HsePlan::STATUS_REJECTED], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->search) . '%';
            $query->where(function ($sub) use ($term) {
                $sub->where('no_hse_plan', 'like', $term)
                    ->orWhere('no_po', 'like', $term)
                    ->orWhere('nama_kontraktor', 'like', $term)
                    ->orWhere('deskripsi_pekerjaan', 'like', $term);
            });
        }

        $hsePlans = $query
            ->orderByRaw('COALESCE(reviewed_at, updated_at) DESC')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'approved' => HsePlan::where('status', HsePlan::STATUS_APPROVED)->count(),
            'rejected' => HsePlan::where('status', HsePlan::STATUS_REJECTED)->count(),
        ];

        return view('admin.hse-plans.history', compact('hsePlans', 'counts'));
    }

    public function show($id)
    {
        $hsePlan = HsePlan::with(['documents', 'reviewer', 'contractor'])->findOrFail($id);

        return view('admin.hse-plans.show', compact('hsePlan'));
    }

    public function edit($id)
    {
        $hsePlan = HsePlan::with(['documents', 'contractor'])->findOrFail($id);

        return view('admin.hse-plans.edit', compact('hsePlan'));
    }

    public function update(Request $request, $id)
    {
        $hsePlan = HsePlan::with('contractor')->findOrFail($id);

        $validated = $request->validate([
            'no_po' => 'nullable|string|max:100',
            'site' => 'required|in:Madiun,Banyuwangi',
            'tipe' => 'required|in:Internal,Eksternal',
            'divisi_pengaju' => 'required|string|max:255',
            'nama_kontraktor' => 'required|string|max:255',
            'nama_pimpinan_proyek' => 'required|string|max:255',
            'telepon' => 'required|string|max:50',
            'deskripsi_pekerjaan' => 'required|string|max:2000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'personil' => 'required|array|min:1',
            'personil.*.nama' => 'required|string|max:255',
            'personil.*.tugas' => 'required|string|max:255',
            'apd' => 'required|array|min:1',
            'apd.*' => 'string|max:100',
            'apd_lainnya' => 'nullable|string|max:255',
            'peralatan' => 'required|array|min:1',
            'peralatan.*.nama' => 'required|string|max:255',
            'peralatan.*.jumlah' => 'required|string|max:50',
            'bahan_b3' => 'nullable|boolean',
            'limbah_b3' => 'nullable|boolean',
            'prosedur_kerja' => 'nullable|boolean',
            'rencana_inspeksi' => 'nullable|boolean',
            'tanggap_darurat' => 'nullable|boolean',
            'struktur_organisasi' => 'nullable|boolean',
            'jsa_file' => 'nullable|' . self::FILE_RULES,
            'sertifikat_personil' => 'nullable|array',
            'sertifikat_personil.*' => self::FILE_RULES,
            'sertifikat_peralatan' => 'nullable|array',
            'sertifikat_peralatan.*' => self::FILE_RULES,
            'msds_file' => 'nullable|' . self::FILE_RULES,
            'limbah_b3_file' => 'nullable|' . self::FILE_RULES,
            'prosedur_kerja_file' => 'nullable|' . self::FILE_RULES,
            'inspeksi_file' => 'nullable|' . self::FILE_RULES,
            'struktur_file' => 'nullable|' . self::FILE_RULES,
        ]);

        $hsePlan->forceFill([
            'no_po' => $validated['no_po'] ?? null,
            'site' => $validated['site'],
            'tipe' => $validated['tipe'],
            'divisi_pengaju' => $validated['divisi_pengaju'],
            'nama_kontraktor' => $validated['nama_kontraktor'],
            'nama_pimpinan_proyek' => $validated['nama_pimpinan_proyek'],
            'telepon' => $validated['telepon'],
            'deskripsi_pekerjaan' => $validated['deskripsi_pekerjaan'],
            'lama_pekerjaan' => $validated['tanggal_mulai'] . ' s/d ' . $validated['tanggal_selesai']
                . ' (' . $validated['jam_mulai'] . ' - ' . $validated['jam_selesai'] . ')',
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'personil' => $this->cleanRows($validated['personil']),
            'apd' => array_values($validated['apd']),
            'apd_lainnya' => $validated['apd_lainnya'] ?? null,
            'peralatan' => $this->cleanRows($validated['peralatan']),
            'bahan_b3' => $request->boolean('bahan_b3'),
            'limbah_b3' => $request->boolean('limbah_b3'),
            'prosedur_kerja' => $request->boolean('prosedur_kerja'),
            'rencana_inspeksi' => $request->boolean('rencana_inspeksi'),
            'tanggap_darurat' => $request->boolean('tanggap_darurat'),
            'struktur_organisasi' => $request->boolean('struktur_organisasi'),
        ])->save();

        $this->uploadDocuments($request, $hsePlan, 'jsa_file', 'jsa', 'Job Safety Analysis');
        $this->uploadDocuments($request, $hsePlan, 'sertifikat_personil', 'sertifikat_personil');
        $this->uploadDocuments($request, $hsePlan, 'sertifikat_peralatan', 'sertifikat_peralatan');
        $this->uploadDocuments($request, $hsePlan, 'msds_file', 'msds', 'MSDS Bahan B3');
        $this->uploadDocuments($request, $hsePlan, 'limbah_b3_file', 'limbah_b3', 'Rencana Pengelolaan Limbah B3');
        $this->uploadDocuments($request, $hsePlan, 'prosedur_kerja_file', 'prosedur_kerja', 'Prosedur / Instruksi Kerja');
        $this->uploadDocuments($request, $hsePlan, 'inspeksi_file', 'inspeksi', 'Rencana Pemantauan / Inspeksi');
        $this->uploadDocuments($request, $hsePlan, 'struktur_file', 'struktur_organisasi', 'Struktur Organisasi Proyek');

        $hasGenerated = $hsePlan->documents()->where('jenis', 'hse_plan_generated')->exists();

        if ($hsePlan->contractor || $hasGenerated) {
            $this->pdfService->storeFinal($hsePlan, $hsePlan->contractor);
        }

        return redirect('/admin/hse-plans/' . $hsePlan->id)
            ->with('success', 'HSE Plan berhasil diperbarui.');
    }

    public function viewPdf($id)
    {
        $hsePlan = HsePlan::with('reviewer')->findOrFail($id);

        return $this->pdfService->make($hsePlan)->stream($this->pdfService->filename($hsePlan));
    }

    public function downloadPdf($id)
    {
        $hsePlan = HsePlan::with('reviewer')->findOrFail($id);

        return $this->pdfService->make($hsePlan)->download($this->pdfService->filename($hsePlan));
    }

    public function approve(Request $request, $id)
    {
        $hsePlan = HsePlan::findOrFail($id);

        if ($hsePlan->status !== HsePlan::STATUS_REVIEW) {
            return redirect('/admin/hse-plans/' . $hsePlan->id)
                ->with('error', 'HSE Plan ini sudah diproses.');
        }

        $request->validate([
            'tanda_tangan' => 'required|string',
        ]);

        $hsePlan->forceFill([
            'status' => HsePlan::STATUS_APPROVED,
            'manager_signature' => $request->tanda_tangan,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ])->save();

        return redirect('/admin/hse-plans/' . $hsePlan->id)
            ->with('success', 'HSE Plan berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $hsePlan = HsePlan::findOrFail($id);

        if ($hsePlan->status !== HsePlan::STATUS_REVIEW) {
            return redirect('/admin/hse-plans/' . $hsePlan->id)
                ->with('error', 'HSE Plan ini sudah diproses.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $hsePlan->forceFill([
            'status' => HsePlan::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'manager_signature' => null,
        ])->save();

        return redirect('/admin/hse-plans/' . $hsePlan->id)
            ->with('success', 'HSE Plan ditolak dan alasan sudah dicatat.');
    }

    public function viewDocument($hsePlanId, $documentId)
    {
        $hsePlan = HsePlan::findOrFail($hsePlanId);
        $document = HsePlanDocument::where('hse_plan_id', $hsePlan->id)->findOrFail($documentId);

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404);
        }

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $filename = $document->nama_dokumen;

        if ($extension && ! str_ends_with(strtolower($filename), '.' . strtolower($extension))) {
            $filename .= '.' . $extension;
        }

        return Storage::disk('local')->response(
            $document->file_path,
            $filename,
            ['Content-Type' => $this->mimeTypeFor($document)]
        );
    }

    private function cleanRows(array $rows): array
    {
        return array_values(array_filter($rows, function ($row) {
            return is_array($row) && trim((string) ($row['nama'] ?? '')) !== '';
        }));
    }

    private function uploadDocuments(Request $request, HsePlan $hsePlan, string $field, string $jenis, ?string $defaultName = null): void
    {
        $files = $request->file($field);

        if (! $files) {
            return;
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
            $path = $file->storeAs('hse-plans/' . $hsePlan->id, $filename);

            HsePlanDocument::create([
                'hse_plan_id' => $hsePlan->id,
                'jenis' => $jenis,
                'nama_dokumen' => $defaultName ?? $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $ext,
                'file_size' => $file->getSize(),
            ]);
        }
    }

    private function mimeTypeFor(HsePlanDocument $document): string
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return $mimeTypes[strtolower($document->file_type)]
            ?? Storage::disk('local')->mimeType($document->file_path)
            ?: 'application/octet-stream';
    }
}
