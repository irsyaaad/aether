<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ArsipDokumentasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'arsip_dokumentasi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tipe_file',
        'nama_file',
        'path_file',
        'ukuran_file',
        'format_file',
        'kategori',
        'tanggal_dokumentasi',
        'lokasi',
        'tags',
        'status',
        'is_public',
        'uploaded_by',
        'updated_by',
    ];

    protected $casts = [
        'tags'                => 'array',
        'nama_file'           => 'array',
        'path_file'           => 'array',
        'ukuran_file'         => 'array',
        'format_file'         => 'array',
        'tanggal_dokumentasi' => 'date',
        'is_public'           => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->uploaded_by = Auth::id();
                $model->updated_by  = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Relationship with User (uploader)
     */
    public function uploader()
    {
        return $this->belongsTo(\App\User::class, 'uploaded_by', 'id_user');
    }

    /**
     * Relationship with User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by', 'id_user');
    }

    /**
     * Get file URLs
     */
    public function getFileUrlsAttribute()
    {
        if (empty($this->path_file)) {
            return [];
        }

        $urls = [];
        foreach ($this->path_file as $path) {
            $urls[] = asset('storage/' . $path);
        }

        return $urls;
    }

    /**
     * Get first file URL
     */
    public function getFileUrlAttribute()
    {
        $urls = $this->file_urls;
        return ! empty($urls) ? $urls[0] : null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        if (empty($this->ukuran_file)) {
            return '-';
        }

        $totalBytes = array_sum((array) $this->ukuran_file);

        $bytes = (int) $totalBytes;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get category badge class
     */
    public function getCategoryBadgeClass()
    {
        $classes = [
            'kegiatan'  => 'bg-primary',
            'fasilitas' => 'bg-info',
            'karyawan'  => 'bg-success',
            'produk'    => 'bg-warning',
            'acara'     => 'bg-danger',
            'lainnya'   => 'bg-secondary',
        ];

        return $classes[$this->kategori] ?? 'bg-secondary';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            'aktif'   => 'bg-success',
            'arsip'   => 'bg-warning',
            'dihapus' => 'bg-danger',
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    /**
     * Check if file is image
     */
    public function isImage()
    {
        return $this->tipe_file === 'foto';
    }

    /**
     * Check if file is video
     */
    public function isVideo()
    {
        return $this->tipe_file === 'video';
    }

    /**
     * Get thumbnail URL for videos
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->isVideo()) {
            // Return a default video thumbnail
            return asset('images/placeholder.png');
        }

        return $this->file_url;
    }
}
