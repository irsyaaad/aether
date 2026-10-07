<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Model;

class RegDistrict extends Model
{

    protected $table = 'reg_districts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'regency_id', 'name'];

    public function regency()
    {
        return $this->belongsTo(RegRegency::class, 'regency_id');
    }

    public function province()
    {
        return $this->belongsTo(RegProvince::class, 'province_id', 'id');
    }

    public function villages()
    {
        return $this->hasMany(RegVillage::class, 'district_id');
    }
}
