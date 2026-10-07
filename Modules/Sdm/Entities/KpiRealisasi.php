<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class KpiRealisasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi_realisasi';

    protected $fillable = [
        'kpi_target_id',
        'karyawan_id',
        'realisasi_value',
        'pencapaian_persen',
        'skor',
        'catatan',
        'status',
        'tanggal_input',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'realisasi_value' => 'decimal:2',
        'pencapaian_persen' => 'decimal:2',
        'skor' => 'decimal:2',
        'tanggal_input' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::saving(function ($model) {
            // Auto calculate pencapaian_persen dan skor
            if ($model->kpiTarget && $model->kpiTarget->target_value > 0) {
                $model->pencapaian_persen = ($model->realisasi_value / $model->kpiTarget->target_value) * 100;
                
                // Calculate weighted score
                if ($model->kpiTarget->kpi) {
                    $bobot = $model->kpiTarget->kpi->bobot / 100; // convert percentage to decimal
                    $model->skor = min($model->pencapaian_persen, 100) * $bobot;
                }
            }
        });
    }

    /**
     * Relationship dengan KPI Target
     */
    public function kpiTarget()
    {
        return $this->belongsTo(KpiTarget::class, 'kpi_target_id');
    }

    /**
     * Relationship dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Relationship dengan User yang approve
     */
    public function approver()
    {
        return $this->belongsTo(\App\User::class, 'approved_by', 'id_user');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id_user');
    }

    /**
     * Relationship dengan User yang mengupdate
     */
    public function updater()
    {
        return $this->belongsTo(\App\User::class, 'updated_by', 'id_user');
    }

    /**
     * Scope untuk realisasi yang sudah diapprove
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'submitted' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}