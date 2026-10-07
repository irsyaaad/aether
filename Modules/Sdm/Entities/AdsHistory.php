<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sdm\Entities\Ads;

class AdsHistory extends Model
{
    use HasFactory;

    protected $table = 'ad_histories';
    protected $fillable = [
        'ad_id',
        'action',
        'note',
        'changed_at'
    ];

    public function ad()
    {
        return $this->belongsTo(Ads::class);
    }
}
