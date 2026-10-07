<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
        'kode_jabatan',
        'deskripsi',
        'level',
        'departemen',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
     * Relationship dengan Karyawan
     */
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'jabatan_id');
    }

    /**
     * Relationship dengan SOP
     */
    public function sop()
    {
        return $this->hasMany(Sop::class, 'jabatan_id');
    }

    /**
     * Relationship dengan Job Description
     */
    public function jobDescription()
    {
        return $this->hasMany(JobDescription::class, 'jabatan_id');
    }

    /**
     * Relationship dengan KPI
     */
    public function kpi()
    {
        return $this->hasMany(Kpi::class, 'jabatan_id');
    }

    /**
     * Scope untuk jabatan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}