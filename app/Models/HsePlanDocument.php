<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsePlanDocument extends Model
{
    protected $fillable = [
        'hse_plan_id',
        'jenis',
        'nama_dokumen',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function hsePlan()
    {
        return $this->belongsTo(HsePlan::class);
    }
}
