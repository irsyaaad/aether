<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Model;

class RegVillage extends Model
{

    protected $table = 'reg_villages';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'district_id', 'name'];

    public function district()
    {
        return $this->belongsTo(RegDistrict::class, 'district_id');
    }

    public function regency()
    {
        return $this->belongsTo(RegRegency::class, 'regency_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(RegProvince::class, 'province_id', 'id');
    }
}
