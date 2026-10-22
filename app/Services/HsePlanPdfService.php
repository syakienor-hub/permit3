<?php

namespace App\Services;

use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class HsePlanPdfService
{
    public function make(HsePlan $hsePlan)
    {
        $hsePlan->loadMissing('reviewer');

        return Pdf::loadView('admin.hse-plans.pdf', compact('hsePlan'))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);
    }

    public function output(HsePlan $hsePlan): string
    {
        return $this->make($hsePlan)->output();
    }

    public function filename(HsePlan $hsePlan): string
    {
        return 'HSE-Plan-' . str_replace('/', '-', $hsePlan->no_hse_plan) . '.pdf';
    }

    public function storeFinal(HsePlan $hsePlan, ?Contractor $contractor = null): HsePlanDocument
    {
        $pdf = $this->output($hsePlan);
        $path = 'hse-plans/' . $hsePlan->id . '/' . $this->filename($hsePlan);

        Storage::disk('local')->put($path, $pdf);

        $document = HsePlanDocument::updateOrCreate(
            ['hse_plan_id' => $hsePlan->id, 'jenis' => 'hse_plan_generated'],
            [
                'nama_dokumen' => 'HSE Plan Final',
                'file_path' => $path,
                'file_type' => 'pdf',
                'file_size' => strlen($pdf),
            ]
        );

        if (! $contractor) {
            return $document;
        }

        $existing = $contractor->documents()->where('jenis', 'hse_plan_generated')->get();
        $contractorDocument = $existing->shift();

        $attributes = [
            'nama_dokumen' => 'HSE Plan Final',
            'file_path' => $path,
            'file_type' => 'pdf',
            'file_size' => strlen($pdf),
        ];

        if ($contractorDocument) {
            if ($contractorDocument->file_path !== $path && Storage::disk('local')->exists($contractorDocument->file_path)) {
                Storage::disk('local')->delete($contractorDocument->file_path);
            }

            $contractorDocument->update($attributes);
        } else {
            ContractorDocument::create([
                'contractor_id' => $contractor->id,
                'jenis' => 'hse_plan_generated',
            ] + $attributes);
        }

        foreach ($existing as $oldDocument) {
            if ($oldDocument->file_path !== $path && Storage::disk('local')->exists($oldDocument->file_path)) {
                Storage::disk('local')->delete($oldDocument->file_path);
            }

            $oldDocument->delete();
        }

        return $document;
    }
}
