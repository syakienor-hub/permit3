<?php

namespace App\Services;

use App\Models\HsePlan;
use Barryvdh\DomPDF\Facade\Pdf;

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
}
