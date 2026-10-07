<?php
namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sdm\Entities\KaryawanJob;

class Karyawan extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'karyawan';

    protected $fillable = [
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
        'jenis_sim',
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
    ];

    public function job_history()
    {
        return $this->hasMany(KaryawanJob::class, 'karyawan_id', 'id');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id_user');
    }

    /**
     * Relationship dengan User yang mengupdate
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by', 'id_user');
    }

    /**
     * Relationship dengan Jabatan
     */
    public function jabatanRelation()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    /**
     * Relationship dengan KPI Realisasi
     */
    public function kpiRealisasi()
    {
        return $this->hasMany(KpiRealisasi::class, 'karyawan_id');
    }

    public function kontrak()
    {
        return $this->hasMany(KontrakKaryawan::class, 'id_karyawan');
    }
}
