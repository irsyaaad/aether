<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Kpi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi';

    protected $fillable = [
        'jabatan_id',
        'kode_kpi',
        'nama_kpi',
        'deskripsi',
        'bobot',
        'satuan',
        'tipe_target',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
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
     * Relationship dengan Jabatan
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
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
     * Relationship dengan KPI Target
     */
    public function targets()
    {
        return $this->hasMany(KpiTarget::class, 'kpi_id');
    }

    /**
     * Scope untuk KPI aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate kode KPI otomatis
     */
    public static function generateKodeKpi($jabatanId)
    {
        $jabatan = Jabatan::find($jabatanId);
        $kodeJabatan = $jabatan ? $jabatan->kode_jabatan : 'JBT';
        
        $lastKpi = self::where('jabatan_id', $jabatanId)
            ->where('kode_kpi', 'like', "KPI/{$kodeJabatan}/%")
            ->orderBy('kode_kpi', 'desc')
            ->first();
        
        if ($lastKpi) {
            $lastNumber = (int) substr($lastKpi->kode_kpi, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "KPI/{$kodeJabatan}/" . date('y') . $newNumber;
    }
}