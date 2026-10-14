<?php

namespace Modules\Olahdata\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiOlahdata extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_olahdatas';

    protected $fillable = [
        'nomor_order',
        'user_id',
        'nama_mahasiswa',
        'no_whatsapp',
        'email',
        'universitas',
        'jurusan',
        'judul_penelitian',
        'software_digunakan',
        'metode_analisis',
        'jumlah_sampel',
        'jumlah_variabel',
        'file_dataset',
        'file_kuesioner',
        'file_output_hasil',
        'catatan_khusus',
        'status_pengerjaan',
        'total_biaya',
        'admin_fee',
        'total_bayar',
        'metode_pembayaran',
        'kode_pembayaran',
        'bukti_pembayaran',
        'status_pembayaran',
        'tanggal_pembelian',
        'catatan_pembayaran',
    ];

    protected $casts = [
        'total_biaya'       => 'decimal:2',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
