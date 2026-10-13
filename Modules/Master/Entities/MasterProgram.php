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
        'jumlah_sesi',
        'fasilitas',
        'badge',
        'target_bab',
        'is_populer',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_populer' => 'boolean',
        'biaya_standar' => 'decimal:2',
        'durasi_sesi' => 'integer',
        'jumlah_sesi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get list of facilities as an array
     */
    public function getFasilitasArrayAttribute(): array
    {
        if (empty($this->fasilitas)) {
            return [];
        }

        // If JSON format
        if (str_starts_with(trim($this->fasilitas), '[') || str_starts_with(trim($this->fasilitas), '{')) {
            $decoded = json_decode($this->fasilitas, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Multiline string
        $lines = preg_split('/\r\n|\r|\n/', $this->fasilitas);
        return array_values(array_filter(array_map('trim', $lines), fn($item) => $item !== ''));
    }

    /**
     * Formatted price attribute
     */
    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_standar, 0, ',', '.');
    }

    /**
     * Formatted session summary
     */
    public function getRingkasanSesiAttribute(): string
    {
        $sesi = $this->jumlah_sesi ?: 1;
        $durasi = $this->durasi_sesi ?: 60;
        return "{$sesi} Sesi Pertemuan (@{$durasi} Menit)";
    }
}
