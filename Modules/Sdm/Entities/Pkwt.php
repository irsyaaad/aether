<?php
namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Pkwt extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pkwt';

    protected $fillable = [
        'karyawan_id',
        'nomor_pkwt',
        'judul_pkwt',
        'jenis_kontrak',
        'tanggal_mulai',
        'tanggal_berakhir',
        'deskripsi',
        'jabatan',
        'gaji_pokok',
        'ketentuan_khusus',
        'nama_file',
        'path_file',
        'ukuran_file',
        'format_file',
        'status_kontrak',
        'auto_reminder',
        'reminder_days',
        'tanggal_reminder',
        'catatan',
        'uploaded_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_mulai'    => 'date',
        'tanggal_berakhir' => 'date',
        'tanggal_reminder' => 'date',
        'auto_reminder'    => 'boolean',
        'gaji_pokok'       => 'decimal:2',
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
     * Relationship with Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
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
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        if (! $this->path_file) {
            return null;
        }

        if (Storage::disk('public')->exists($this->path_file)) {
            return asset('storage/' . $this->path_file);
        }

        return asset('storage/' . $this->path_file);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        if (! $this->ukuran_file) {
            return '-';
        }

        $bytes = (int) $this->ukuran_file;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get contract type badge class
     */
    public function getJenisPkwtBadgeClass()
    {
        $classes = [
            'pkwt'      => 'bg-primary',
            'pkwtt'     => 'bg-success',
            'magang'    => 'bg-info',
            'freelance' => 'bg-warning',
            'konsultan' => 'bg-secondary',
        ];

        return $classes[$this->jenis_kontrak] ?? 'bg-secondary';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            'aktif'        => 'bg-success',
            'berakhir'     => 'bg-danger',
            'diperpanjang' => 'bg-info',
            'dibatalkan'   => 'bg-secondary',
        ];

        return $classes[$this->status_kontrak] ?? 'bg-secondary';
    }

    /**
     * Get contract duration in days
     */
    public function getDurasiKontrakAttribute()
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_berakhir);
    }

    /**
     * Get remaining days
     */
    public function getSisaHariAttribute()
    {
        if ($this->status_kontrak !== 'aktif') {
            return 0;
        }

        $today   = now()->startOfDay();
        $endDate = $this->tanggal_berakhir;

        if ($today->gt($endDate)) {
            return 0; // Sudah berakhir
        }

        return $today->diffInDays($endDate);
    }

    /**
     * Check if contract is expiring soon (within 30 days)
     */
    public function getIsExpiringSoonAttribute()
    {
        return $this->status_kontrak === 'aktif' && $this->sisa_hari <= 30 && $this->sisa_hari > 0;
    }

    /**
     * Check if contract is expired
     */
    public function getIsExpiredAttribute()
    {
        return $this->status_kontrak === 'aktif' && $this->sisa_hari <= 0;
    }

    /**
     * Get formatted salary
     */
    public function getFormattedGajiAttribute()
    {
        if (! $this->gaji_pokok) {
            return '-';
        }

        return 'Rp ' . number_format($this->gaji_pokok, 0, ',', '.');
    }

    /**
     * Scope for active contracts
     */
    public function scopeAktif($query)
    {
        return $query->where('status_kontrak', 'aktif');
    }

    /**
     * Scope for expiring contracts
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status_kontrak', 'aktif')
            ->whereDate('tanggal_berakhir', '<=', now()->addDays($days))
            ->whereDate('tanggal_berakhir', '>=', now());
    }

    /**
     * Scope for expired contracts
     */
    public function scopeExpired($query)
    {
        return $query->where('status_kontrak', 'aktif')
            ->whereDate('tanggal_berakhir', '<', now());
    }
}
