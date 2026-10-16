<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_kelas';

    protected $fillable = [
        'kode_kelas',
        'judul',
        'thumbnail',
        'program_id',
        'mentor_id',
        'level', // Pemula, Menengah, Mahir, Semua Level
        'kategori', // Kuantitatif, Kualitatif, Ekonometrika, Penulisan Akademik
        'deskripsi',
        'silabus_singkat',
        'harga',
        'kuota_maksimal',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe_kelas', // Online Webinar, Bootcamp Intensif, Self-Paced Video
        'link_meeting',
        'badge', // Terpopuler, Best Seller, Baru, Intensif
        'status', // Dibuka, Berlangsung, Selesai, Ditutup
        'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'kuota_maksimal' => 'integer',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'thumbnail_url',
        'harga_formatted',
    ];

    /**
     * Get thumbnail URL accessor
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return $this->thumbnail;
            }
            return asset('storage/' . $this->thumbnail);
        }
        return null;
    }

    /**
     * Get formatted price accessor
     */
    public function getHargaFormattedAttribute(): string
    {
        if ($this->harga && (float)$this->harga > 0) {
            return 'Rp ' . number_format((float)$this->harga, 0, ',', '.');
        }
        return 'Gratis';
    }

    public function program()
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function mentor()
    {
        return $this->belongsTo(MasterMentor::class, 'mentor_id');
    }

    public function materis()
    {
        return $this->hasMany(MasterMateri::class, 'kelas_id')->orderBy('urutan');
    }

    public function transaksis()
    {
        return $this->hasMany(\Modules\Pelatihan\Entities\TransaksiPelatihan::class, 'kelas_id');
    }
}
