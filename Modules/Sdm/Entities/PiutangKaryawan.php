<?php
namespace Modules\Sdm\Entities;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PiutangKaryawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'piutang_karyawan';

    protected $fillable = [
        'nomor_piutang',
        'karyawan_id',
        'tanggal_piutang',
        'jumlah_piutang',
        'jumlah_terbayar',
        'sisa_piutang',
        'jenis_piutang',
        'status',
        'keterangan',
        'jatuh_tempo',
        'ac_debet',
        'ac_kredit',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_piutang' => 'date',
        'jatuh_tempo'     => 'date',
        'jumlah_piutang'  => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'sisa_piutang'    => 'decimal:2',
        'jumlah_piutang'  => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->id_user;
                $model->updated_by = Auth::user()->id_user;
            }

            // Generate nomor piutang otomatis
            if (empty($model->nomor_piutang)) {
                $model->nomor_piutang = self::generateNomorPiutang();
            }

            // Set sisa piutang sama dengan jumlah piutang saat pertama kali dibuat
            $model->sisa_piutang = $model->jumlah_piutang;
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::user()->id_user;
            }

            // Update sisa piutang
            $model->sisa_piutang = $model->jumlah_piutang - $model->jumlah_terbayar;

            // Update status berdasarkan sisa piutang
            if ($model->sisa_piutang <= 0) {
                $model->status = 'lunas';
            } elseif ($model->status == 'lunas' && $model->sisa_piutang > 0) {
                $model->status = 'aktif';
            }
        });
    }

    /**
     * Generate nomor piutang otomatis
     */
    public static function generateNomorPiutang()
    {
        return DB::transaction(function () {
            $now    = now();
            $prefix = 'PIUT/' . $now->format('y.m') . '-';

            // Lock table dan cari nomor terakhir HANYA untuk bulan ini
            $lastRecord = self::where('nomor_piutang', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('nomor_piutang')
                ->first();

            $nextNumber = 1; // Reset ke 1 setiap bulan baru

            if ($lastRecord) {
                // Extract nomor dari nomor_piutang terakhir dalam bulan ini
                $lastNomor  = $lastRecord->nomor_piutang;
                $parts      = explode('-', $lastNomor);
                $lastNumber = intval(end($parts));
                $nextNumber = $lastNumber + 1;
            }

            $nomorPiutang = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Double check untuk memastikan nomor belum ada dalam bulan ini
            $exists = self::where('nomor_piutang', $nomorPiutang)->exists();
            if ($exists) {
                // Jika masih ada duplikat, cari nomor kosong berikutnya dalam bulan ini
                do {
                    $nextNumber++;
                    $nomorPiutang = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    $exists       = self::where('nomor_piutang', $nomorPiutang)->exists();
                } while ($exists);
            }

            return $nomorPiutang;
        });
    }

    /**
     * Relationship dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
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
     * Relationship dengan Pembayaran
     */
    public function pembayaran()
    {
        return $this->hasMany(PiutangKaryawanPembayaran::class, 'piutang_karyawan_id');
    }

    /**
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif'      => '<span class="badge bg-warning">Aktif</span>',
            'lunas'      => '<span class="badge bg-success">Lunas</span>',
            'dihapuskan' => '<span class="badge bg-danger">Dihapuskan</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Accessor untuk jenis piutang badge
     */
    public function getJenisPiutangBadgeAttribute()
    {
        $badges = [
            'kasbon'   => '<span class="badge bg-primary">Kasbon</span>',
            'pinjaman' => '<span class="badge bg-info">Pinjaman</span>',
            'lainnya'  => '<span class="badge bg-secondary">Lainnya</span>',
        ];

        return $badges[$this->jenis_piutang] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Check if piutang can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['aktif']);
    }

    /**
     * Check if piutang can be deleted
     */
    public function canBeDeleted()
    {
        return in_array($this->status, ['aktif']) && $this->jumlah_terbayar == 0;
    }

    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\PiutangKaryawanFactory::new ();
    }
}
