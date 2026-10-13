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
        'banner',
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

    protected $appends = [
        'banner_url',
        'formatted_biaya',
        'ringkasan_sesi',
        'fasilitas_html',
    ];

    /**
     * Get banner URL accessor
     */
    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner) {
            if (str_starts_with($this->banner, 'http://') || str_starts_with($this->banner, 'https://')) {
                return $this->banner;
            }
            return asset('storage/' . $this->banner);
        }
        return null;
    }

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

        // If HTML containing <li> tags
        if (stripos($this->fasilitas, '<li') !== false) {
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $this->fasilitas, $matches);
            if (!empty($matches[1])) {
                $items = array_map(function ($item) {
                    return trim(html_entity_decode(strip_tags($item)));
                }, $matches[1]);
                return array_values(array_filter($items, fn($item) => $item !== ''));
            }
        }

        // If HTML containing paragraphs, divs or line breaks
        if (stripos($this->fasilitas, '<p') !== false || stripos($this->fasilitas, '<div') !== false || stripos($this->fasilitas, '<br') !== false) {
            $formatted = preg_replace('/<(?:br|\/p|\/div)\s*\/?>/i', "\n", $this->fasilitas);
            $clean = strip_tags($formatted);
            $lines = preg_split('/\r\n|\r|\n/', $clean);
            return array_values(array_filter(array_map('trim', array_map('html_entity_decode', $lines)), fn($item) => $item !== ''));
        }

        // Multiline string
        $lines = preg_split('/\r\n|\r|\n/', $this->fasilitas);
        return array_values(array_filter(array_map('trim', $lines), fn($item) => $item !== ''));
    }

    /**
     * Get facilities formatted as HTML (for rich text editors like CKEditor)
     */
    public function getFasilitasHtmlAttribute(): string
    {
        if (empty($this->fasilitas)) {
            return '';
        }

        // If already HTML
        if (stripos($this->fasilitas, '<') !== false) {
            return $this->fasilitas;
        }

        // Convert plain multiline to <ul><li>...</li></ul>
        $items = $this->fasilitas_array;
        if (empty($items)) {
            return '';
        }

        $html = '<ul>';
        foreach ($items as $item) {
            $html .= '<li>' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $html .= '</ul>';
        return $html;
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
