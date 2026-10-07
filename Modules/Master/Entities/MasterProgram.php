<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_programs';

    protected $fillable = [
        'kode_program',
        'nama_program',
        'kategori',
        'jenjang',
        'deskripsi',
        'biaya_standar',
        'durasi_sesi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'biaya_standar' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
