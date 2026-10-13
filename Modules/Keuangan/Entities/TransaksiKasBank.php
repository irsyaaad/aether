<?php

namespace Modules\Keuangan\Entities;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\MasterCoa;

class TransaksiKasBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_kas_banks';

    protected $fillable = [
        'nomor_transaksi',
        'jenis',
        'tanggal',
        'kas_bank_id',
        'lawan_coa_id',
        'nominal',
        'penerima_pembayar',
        'nomor_referensi',
        'metode_pembayaran',
        'keterangan',
        'lampiran',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship dengan Master CoA untuk Akun Kas / Bank (Debit di Kas Masuk / Kredit di Kas Keluar)
     */
    public function kasBank()
    {
        return $this->belongsTo(MasterCoa::class, 'kas_bank_id');
    }

    /**
     * Relationship dengan Master CoA untuk Akun Lawan (Kredit di Kas Masuk / Debit di Kas Keluar)
     */
    public function lawanCoa()
    {
        return $this->belongsTo(MasterCoa::class, 'lawan_coa_id');
    }

    /**
     * Relationship dengan User pencatat transaksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Scope untuk transaksi Kas Masuk
     */
    public function scopeMasuk($query)
    {
        return $query->where('jenis', 'masuk');
    }

    /**
     * Scope untuk transaksi Kas Keluar
     */
    public function scopeKeluar($query)
    {
        return $query->where('jenis', 'keluar');
    }

    /**
     * Helper untuk generate nomor transaksi otomatis (KM-YYYYMM-0001 / KK-YYYYMM-0001)
     */
    public static function generateNomorTransaksi(string $jenis, ?string $tanggal = null): string
    {
        $prefix = ($jenis === 'masuk') ? 'KM' : 'KK';
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $yearMonth = $date->format('Ym');
        $codePrefix = "{$prefix}-{$yearMonth}-";

        $lastRecord = self::withTrashed()
            ->where('nomor_transaksi', 'like', "{$codePrefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d{4})$/', $lastRecord->nomor_transaksi, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return $codePrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
