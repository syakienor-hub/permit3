<?php

use App\Models\Contractor;
use App\Models\ContractorDocument;
use App\Models\HsePlan;
use App\Models\HsePlanDocument;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        foreach (HsePlanDocument::where('jenis', 'hse_plan_generated')->get() as $document) {
            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }

            $document->delete();
        }

        foreach (ContractorDocument::where('jenis', 'hse_plan_generated')->get() as $document) {
            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }

            $document->delete();
        }

        HsePlan::where('status', 'Final')->update([
            'status' => HsePlan::STATUS_REVIEW,
            'manager_signature' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        Contractor::where('status', Contractor::STATUS_FINAL)->get()->each(function (Contractor $contractor) {
            if (! $contractor->hse_plan_id) {
                $status = Contractor::STATUS_WAITING;
            } elseif ($contractor->documents()->where('jenis', 'hse_plan_existing')->exists()) {
                $status = Contractor::STATUS_REVIEW;
            } else {
                $status = Contractor::STATUS_FORM_SUBMITTED;
            }

            $contractor->forceFill(['status' => $status])->save();
        });
    }

    public function down(): void
    {
        //
    }
};
