<?php

namespace Modules\Bimbingan\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\MasterProgram;

class TransaksiProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi_programs';

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'program_id',
        'nama_program',
        'nama_pemesan',
        'email_pemesan',
        'telepon_pemesan',
        'universitas',
        'judul_skripsi',
        'biaya',
        'metode_pembayaran',
        'kode_pembayaran',
        'bukti_pembayaran',
        'admin_fee',
        'total_bayar',
        'total_sesi',
        'sisa_sesi',
        'sesi_digunakan',
        'status',
        'status_pembayaran',
        'tanggal_pembelian',
        'tanggal_berakhir',
        'catatan',
    ];

    protected $casts = [
        'biaya'             => 'decimal:2',
        'admin_fee'         => 'decimal:2',
        'total_bayar'       => 'decimal:2',
        'total_sesi'        => 'integer',
        'sisa_sesi'         => 'integer',
        'sesi_digunakan'    => 'integer',
        'tanggal_pembelian' => 'datetime',
        'tanggal_berakhir'  => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    protected $appends = [
        'formatted_biaya',
        'bukti_pembayaran_url',
    ];

    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya ?? 0, 0, ',', '.');
    }

    public function getBuktiPembayaranUrlAttribute(): ?string
    {
        if ($this->bukti_pembayaran) {
            if (str_starts_with($this->bukti_pembayaran, 'http://') || str_starts_with($this->bukti_pembayaran, 'https://')) {
                return $this->bukti_pembayaran;
            }
            return asset('storage/' . $this->bukti_pembayaran);
        }
        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function program()
    {
        return $this->belongsTo(MasterProgram::class, 'program_id');
    }

    public function bimbingans()
    {
        return $this->hasMany(TransaksiBimbingan::class, 'program_id', 'program_id')
            ->where('user_id', $this->user_id);
    }
}
