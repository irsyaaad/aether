<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMentor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_mentors';

    protected $fillable = [
        'nama_lengkap',
        'gelar',
        'spesialisasi',
        'email',
        'no_hp',
        'pendidikan_terakhir',
        'institusi',
        'keahlian_software',
        'tarif_per_sesi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tarif_per_sesi' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
