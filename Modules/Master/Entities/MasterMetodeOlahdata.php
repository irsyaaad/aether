<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMetodeOlahdata extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_metode_olahdatas';

    protected $fillable = [
        'kode_metode',
        'nama_metode',
        'kategori',
        'software_rekomendasi',
        'deskripsi',
        'estimasi_hari',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
