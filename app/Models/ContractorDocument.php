<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorDocument extends Model
{
    protected $fillable = [
        'contractor_id',
        'jenis',
        'nama_dokumen',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
