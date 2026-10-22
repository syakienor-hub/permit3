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
