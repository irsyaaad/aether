<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class KandidatJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kandidat_job';
    protected $fillable = [
        'kandidat_id',
        'jabatan_id',
        'divisi_id',
        'lokasi_kerja_id',
        'status_kerja_id',
        'tanggal_mulai_kerja',
        'tanggal_akhir_kontrak',
        'atasan_langsung',
        'shift_kerja',
    ];
}
