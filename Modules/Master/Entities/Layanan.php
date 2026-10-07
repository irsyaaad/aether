<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use SoftDeletes;

    protected $table = 'layanan';
    protected $primaryKey = 'kode_layanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kode_layanan', 'nama_layanan'];
}
