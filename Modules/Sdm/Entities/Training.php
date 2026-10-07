<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sdm\Entities\Karyawan;

class Training extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'trainings';
    protected $fillable = [
        'employee_id',
        'material',
        'trainer_id',
        'completed_at',
        'duration',
        'score',
        'status',
        'note',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function employee()
    {
        return $this->belongsTo(Karyawan::class, 'employee_id', 'id');
    }


    public function trainer()
    {
        return $this->belongsTo(Karyawan::class, 'trainer_id', 'id');
    }
}
