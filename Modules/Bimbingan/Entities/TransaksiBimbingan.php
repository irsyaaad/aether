<?php

namespace Modules\Bimbingan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\MasterProgram;

class TransaksiBimbingan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_bimbingans';

    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'nama_mahasiswa',
        'nim',
        'universitas',
        'jurusan',
        'jenjang',
        'judul_skripsi',
        'topik_bidang',
        'metode_riset',
        'software_analisis',
        'paket_bimbingan',
        'program_id',
        'mentor_id',
        'jadwal_konsultasi',
        'link_zoom',
        'file_draft_skripsi',
        'keluhan_kendala',
        'catatan_mentor',
        'status',
        'biaya',
        'status_pembayaran',
    ];

    protected $casts = [
        'jadwal_konsultasi' => 'datetime',
        'biaya' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function mentor()
    {
        return $this->belongsTo(\Modules\Master\Entities\MasterMentor::class, 'mentor_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
