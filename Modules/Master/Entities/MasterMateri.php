<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterMateri extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_materis';

    protected $fillable = [
        'kelas_id',
        'urutan',
        'judul_materi',
        'tipe_materi', // Video, Dokumen PDF, Dataset Praktik, Kuis/Tugas
        'durasi_menit',
        'link_video',
        'file_materi',
        'deskripsi',
        'is_free_preview',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'durasi_menit' => 'integer',
        'is_free_preview' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }
}
