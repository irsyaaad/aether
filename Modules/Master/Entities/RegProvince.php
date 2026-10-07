<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Model;

class RegProvince extends Model
{

    protected $table = 'reg_provinces';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name'];

    public function regencies()
    {
        return $this->hasMany(RegRegency::class, 'province_id');
    }
}
