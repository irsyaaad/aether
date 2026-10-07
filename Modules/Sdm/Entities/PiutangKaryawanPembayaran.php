<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use Modules\Keuangan\Entities\Ac4;

class PiutangKaryawanPembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'piutang_karyawan_pembayaran';

    protected $fillable = [
        'piutang_karyawan_id',
        'nomor_pembayaran',
        'tanggal_bayar',
        'jumlah_bayar',
        'keterangan',
        'bukti_bayar',
        'metode_bayar',
        'ac_debet',
        'ac_kredit',
        'do_id',
        'created_by',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->id_user;
            }
            
            // Generate nomor pembayaran otomatis
            if (empty($model->nomor_pembayaran)) {
                $model->nomor_pembayaran = self::generateNomorPembayaran();
            }
        });
    }

    /**
     * Generate nomor pembayaran otomatis
     */
    public static function generateNomorPembayaran()
    {
        return DB::transaction(function () {
            $now = now();
            $prefix = 'BYR/' . $now->format('y.m') . '-';
            
            // Mulai dari nomor 1 dan cek satu per satu (termasuk yang soft deleted)
            $nextNumber = 1;
            
            do {
                $nomorPembayaran = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                $exists = self::withTrashed()->where('nomor_pembayaran', $nomorPembayaran)->lockForUpdate()->exists();
                
                if (!$exists) {
                    return $nomorPembayaran;
                }
                
                $nextNumber++;
            } while ($nextNumber <= 9999); // Maksimal 9999 nomor per bulan
            
            throw new \Exception('Nomor pembayaran sudah habis untuk bulan ini');
        });
    }

    /**
     * Relationship dengan PiutangKaryawan
     */
    public function piutangKaryawan()
    {
        return $this->belongsTo(PiutangKaryawan::class, 'piutang_karyawan_id');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function acDebet()
    {
        return $this->belongsTo(Ac4::class, 'ac_debet', 'id_ac');
    }

    public function acKredit()
    {
        return $this->belongsTo(Ac4::class, 'ac_kredit', 'id_ac');
    }

    /**
     * Accessor untuk metode bayar badge
     */
    public function getMetodeBayarBadgeAttribute()
    {
        $badges = [
            'tunai' => '<span class="badge bg-success">Tunai</span>',
            'transfer' => '<span class="badge bg-primary">Transfer</span>',
            'potong_gaji' => '<span class="badge bg-warning">Potong Gaji</span>',
            'lainnya' => '<span class="badge bg-secondary">Lainnya</span>',
            '' => '<span class="badge bg-danger">Lainnya</span>',
        ];

        return $badges[$this->metode_bayar] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get bukti bayar URL
     */
    public function getBuktiBayarUrlAttribute()
    {
        if ($this->bukti_bayar) {
            return asset('storage/piutang-karyawan/bukti-bayar/' . $this->bukti_bayar);
        }
        return null;
    }

    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\PiutangKaryawanPembayaranFactory::new();
    }
}