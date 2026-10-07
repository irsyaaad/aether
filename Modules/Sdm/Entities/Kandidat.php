<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Sdm\Entities\KandidatJob;

class Kandidat extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'kandidat';

    protected $fillable = [
        'ads_id',
        'nama_lengkap',
        'nama_panggilan',
        'nik_ktp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_pernikahan',
        'alamat_ktp',
        'alamat_domisili',
        'nomor_hp',
        'email',
        'nomor_npwp',
        'nomor_bpjs_kesehatan',
        'nomor_bpjs_ketenagakerjaan',
        'nomor_sim',
        'jabatan',
        'divisi',
        'lokasi_kerja',
        'status_kerja',
        'tanggal_mulai_kerja',
        'tanggal_akhir_kontrak',
        'atasan_langsung',
        'shift_kerja',
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_makan',
        'tunjangan_transport',
        'nomor_rekening',
        'nama_bank',
        'golongan_darah',
        'kontak_darurat_nama',
        'kontak_darurat_nomor',
        'kontak_darurat_hubungan',
        'dokumen_ktp',
        'dokumen_kk',
        'dokumen_sim',
        'dokumen_npwp',
        'dokumen_ijazah',
        'foto_diri',
        'id_spx',
        'rekap_absen',
        'rekap_lembur',
        'saldo_cuti',
        'catatan_kinerja',
        'training',
        'status_seleksi',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id_user');
    }

    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by', 'id_user');
    }

    public function job_history()
    {
        return $this->hasMany(KandidatJob::class, 'kandidat_id', 'id');
    }

    public function ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id', 'id');
    }
}
