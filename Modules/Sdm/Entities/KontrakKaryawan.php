<?php
namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KontrakKaryawan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table   = 'kontrak_karyawan';
    protected $guarded = ['id'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }
}
