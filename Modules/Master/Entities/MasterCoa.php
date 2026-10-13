<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCoa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_coas';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kategori',
        'tipe',
        'laporan',
        'posisi_normal',
        'saldo_awal',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'saldo_awal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function scopeNeraca($query)
    {
        return $query->where('laporan', 'Neraca');
    }

    public function scopeLabaRugi($query)
    {
        return $query->where('laporan', 'Laba Rugi');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeKasBank($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('kategori', 'ASET LANCAR')
                  ->where(function ($sub) {
                      $sub->whereIn('kode_akun', ['1101', '1102', '1103'])
                          ->orWhere('nama_akun', 'like', 'Kas%')
                          ->orWhere('nama_akun', 'like', 'Bank%');
                  })
                  ->orWhere('kategori', 'like', '%KAS%');
            });
    }

    public function scopeBeban($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('tipe', 'Beban')
                  ->orWhere('kategori', 'like', '%BEBAN%')
                  ->orWhere('kode_akun', 'like', '5%');
            });
    }

    public function scopePendapatan($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('tipe', 'Pendapatan')
                  ->orWhere('kategori', 'like', '%PENDAPATAN%')
                  ->orWhere('kode_akun', 'like', '4%');
            });
    }
}
