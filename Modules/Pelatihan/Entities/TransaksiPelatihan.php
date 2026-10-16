<?php

namespace Modules\Pelatihan\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\MasterKelas;

class TransaksiPelatihan extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_pelatihans';

    protected $fillable = [
        'nomor_registrasi',
        'user_id',
        'kelas_id',
        'judul_kelas',
        'instruktur',
        'kode_kelas',
        'link_zoom',
        'progress',
        'total_modul',
        'modul_selesai',
        'status',
        'nama_pemesan',
        'email_pemesan',
        'telepon_pemesan',
        'universitas',
        'biaya',
        'admin_fee',
        'total_bayar',
        'metode_pembayaran',
        'kode_pembayaran',
        'bukti_pembayaran',
        'status_pembayaran',
        'tanggal_pembelian',
        'catatan',
    ];

    protected $casts = [
        'progress'          => 'integer',
        'total_modul'       => 'integer',
        'modul_selesai'     => 'integer',
        'biaya'             => 'decimal:2',
        'admin_fee'         => 'decimal:2',
        'total_bayar'       => 'decimal:2',
        'tanggal_pembelian' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function getBuktiPembayaranUrlAttribute()
    {
        if (!$this->bukti_pembayaran) {
            return null;
        }
        return asset('storage/' . $this->bukti_pembayaran);
    }

    public function getNomorSertifikatAttribute()
    {
        $year = $this->updated_at ? $this->updated_at->format('Y') : date('Y');
        $padId = str_pad($this->id, 5, '0', STR_PAD_LEFT);
        $cleanKode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->kode_kelas ?? 'WKS'));
        $prefix = substr($cleanKode, 0, 4) ?: 'WKS';
        return "AETH-CERT/{$year}/{$prefix}-{$padId}";
    }

    public function getVerifyUrlAttribute()
    {
        $code = $this->nomor_registrasi ?: $this->id;
        return route('pelatihan.sertifikat.verifikasi', $code);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }
}
