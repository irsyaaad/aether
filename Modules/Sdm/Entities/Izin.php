<?php

namespace Modules\Sdm\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class Izin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'izin';

    protected $fillable = [
        'kode_izin',
        'karyawan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'jenis_izin',
        'alasan',
        'dokumen_pendukung',
        'status',
        'catatan_atasan',
        'disetujui_oleh',
        'tanggal_disetujui',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_disetujui',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->id_user;
                $model->updated_by = Auth::user()->id_user;
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::user()->id_user;
            }
        });
    }

    /**
     * Generate kode izin otomatis
     */
    public static function generateKodeIzin()
    {
        $now = Carbon::now();
        $prefix = 'IZN/' . $now->format('y.m.d') . '-';
        
        // Ambil nomor urut terakhir di hari yang sama
        $lastNumber = self::whereDate('created_at', $now->toDateString())
            ->where('kode_izin', 'like', $prefix . '%')
            ->select(DB::raw("CAST(SUBSTRING_INDEX(kode_izin, '-', -1) AS UNSIGNED) as nomor"))
            ->orderByDesc('nomor')
            ->value('nomor');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    /**
     * Relationship dengan User yang menyetujui
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh', 'id_user');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Relationship dengan User yang mengupdate
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id_user');
    }

    /**
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Accessor untuk jenis izin label
     */
    public function getJenisIzinLabelAttribute()
    {
        $labels = [
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
            'izin_khusus' => 'Izin Khusus',
            'dinas_luar' => 'Dinas Luar',
        ];

        return $labels[$this->jenis_izin] ?? $this->jenis_izin;
    }
    
    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\IzinFactory::new();
    }
}
