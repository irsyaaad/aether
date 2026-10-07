<?php
namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table    = 'perusahaan';
    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'kota',
        'telp',
        'email',
        'npwp',
        'pemilik',
        'keterangan',
        'status',
        'company_profile',
        'akta_perusahaan',
        'visi_misi',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
