<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMetodeRiset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_metode_risets';

    protected $fillable = [
        'kode_metode',
        'nama_metode',
        'kategori', // Kuantitatif, Kualitatif, Mixed Method, Ekonometrika, Data Science
        'software_rekomendasi', // SPSS, SmartPLS, EViews, Stata, Python, R, NVivo
        'deskripsi',
        'uji_statistik_terkait',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
