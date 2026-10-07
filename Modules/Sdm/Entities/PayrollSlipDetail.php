<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollSlipDetail extends Model
{
    use HasFactory;

    protected $table = 'payroll_slip_details';

    protected $fillable = [
        'payroll_slip_id',
        'payroll_component_id',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    /**
     * Relationship dengan PayrollSlip
     */
    public function payrollSlip()
    {
        return $this->belongsTo(PayrollSlip::class, 'payroll_slip_id');
    }

    /**
     * Relationship dengan PayrollComponent
     */
    public function component()
    {
        return $this->belongsTo(PayrollComponent::class, 'payroll_component_id');
    }
    
    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\PayrollSlipDetailFactory::new();
    }
}