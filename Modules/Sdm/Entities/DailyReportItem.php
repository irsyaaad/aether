<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Operasional\Entities\Armada;
use Modules\Operasional\Entities\DoModel;

class DailyReportItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'daily_report_id',
        'type',
        'reference_id',
        'description',
        'status',
        'notes',
    ];

    /**
     * Relationship with DailyReport
     */
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    /**
     * Polymorphic relationship for reference
     * This is a dynamic relationship based on type
     */
    public function reference()
    {
        // Since we don't have a proper polymorphic setup, we'll use a custom approach
        return $this->belongsTo(DoModel::class, 'reference_id')->when($this->type === 'armada', function ($query) {
            return $this->belongsTo(Armada::class, 'reference_id');
        });
    }

    /**
     * Get DO reference when type is 'do'
     */
    public function doReference()
    {
        return $this->belongsTo(DoModel::class, 'reference_id');
    }

    /**
     * Get Armada reference when type is 'armada'
     */
    public function armadaReference()
    {
        return $this->belongsTo(Armada::class, 'reference_id');
    }

    /**
     * Get reference object based on type
     */
    public function getReferenceAttribute()
    {
        if (!$this->reference_id) return null;

        switch ($this->type) {
            case 'do':
                return $this->doReference;
            case 'armada':
                return $this->armadaReference;
            default:
                return null;
        }
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'completed':
                return 'bg-success';
            case 'in_progress':
                return 'bg-warning';
            case 'pending':
                return 'bg-secondary';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-info';
        }
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        switch ($this->status) {
            case 'completed':
                return 'Selesai';
            case 'in_progress':
                return 'Proses';
            case 'pending':
                return 'Berangkat';
            case 'cancelled':
                return 'Batal';
            default:
                return 'Baru';
        }
    }
}