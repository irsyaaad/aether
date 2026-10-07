<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class KpiTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi_target';

    protected $fillable = [
        'kpi_id',
        'tahun',
        'periode',
        'target_value',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'tahun' => 'integer',
        'periode' => 'integer',
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
    }

    /**
     * Relationship dengan KPI
     */
    public function kpi()
    {
        return $this->belongsTo(Kpi::class, 'kpi_id');
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
     * Relationship dengan KPI Realisasi
     */
    public function realisasi()
    {
        return $this->hasMany(KpiRealisasi::class, 'kpi_target_id');
    }

    /**
     * Get nama periode berdasarkan tipe target
     */
    public function getNamaPeriodeAttribute()
    {
        $tipeTarget = $this->kpi->tipe_target ?? 'bulanan';
        
        switch ($tipeTarget) {
            case 'bulanan':
                $bulan = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                return $bulan[$this->periode] ?? 'Unknown';
                
            case 'triwulan':
                return 'Triwulan ' . $this->periode;
                
            case 'semester':
                return 'Semester ' . $this->periode;
                
            case 'tahunan':
                return 'Tahunan';
                
            default:
                return 'Periode ' . $this->periode;
        }
    }
}