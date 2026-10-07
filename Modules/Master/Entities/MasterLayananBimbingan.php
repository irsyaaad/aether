<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterLayananBimbingan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_layanan_bimbingans';

    protected $fillable = [
        'kode_layanan',
        'nama_layanan',
        'jenis_bimbingan',
        'target_output',
        'durasi_menit',
        'jumlah_pertemuan',
        'fasilitas',
        'harga',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
