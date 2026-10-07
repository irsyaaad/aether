<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sdm\Entities\Karyawan;

class HandoverRecord extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'handover_records';

    protected $fillable = [
        'handover_date',
        'from_employee_id',
        'to_employee_id',
        'item_name',
        'has_physical_form',
        'quantity',
        'condition_note',
        'remarks',
        'verified_by',
        'verified_at',
        'created_by',
    ];

    protected $casts = [
        'handover_date'     => 'date',
        'verified_at'       => 'datetime',
        'has_physical_form' => 'boolean',
        'quantity'          => 'integer',
    ];

    /** RELATIONS */
    public function fromEmployee()
    {
        return $this->belongsTo(Karyawan::class, 'from_employee_id', 'id');
    }

    public function toEmployee()
    {
        return $this->belongsTo(Karyawan::class, 'to_employee_id', 'id');
    }

    public function verifier()
    {
        return $this->belongsTo(Karyawan::class, 'verified_by', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
