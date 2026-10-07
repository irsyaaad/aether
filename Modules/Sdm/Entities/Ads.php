<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sdm\Entities\AdsHistory;
use Illuminate\Support\Str;

class Ads extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ads';
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'status',
        'remarks',
        'created_by',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(AdsHistory::class);
    }
}
