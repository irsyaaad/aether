<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'setting_perusahaan';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'kota',
        'telepon',
        'email',
        'website',
        'npwp',
        'pemilik',
        'visi_misi',
        'logo',
        'keterangan',
        'updated_by',
    ];

    /**
     * Get the user who last updated the settings.
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by');
    }
}
