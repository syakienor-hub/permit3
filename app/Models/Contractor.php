<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    public const STATUS_WAITING = 'Menunggu HSE Plan';
    public const STATUS_REVIEW = 'Review Manager';
    public const STATUS_FORM_SUBMITTED = 'HSE Plan Diajukan';

    protected $fillable = [
        'token',
        'vendor',
        'pic',
        'jenis_pekerjaan',
        'telepon',
        'nama_pekerjaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_pekerja',
        'lokasi',
        'site',
        'status',
        'hse_plan_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(ContractorDocument::class);
    }

    public function hsePlan()
    {
        return $this->belongsTo(HsePlan::class);
    }
}
