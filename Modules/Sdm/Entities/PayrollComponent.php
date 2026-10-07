<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\User;

class PayrollComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payroll_components';

    protected $fillable = [
        'kode_komponen',
        'nama_komponen',
        'jenis',
        'tipe_perhitungan',
        'nilai_default',
        'is_taxable',
        'is_active',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'nilai_default' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::user()->id_user;
                $model->updated_by = Auth::user()->id_user;
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::user()->id_user;
            }
        });
    }

    /**
     * Relationship dengan PayrollSlipDetail
     */
    public function payrollSlipDetails()
    {
        return $this->hasMany(PayrollSlipDetail::class, 'payroll_component_id');
    }

    /**
     * Relationship dengan User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Relationship dengan User yang mengupdate
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id_user');
    }

    /**
     * Accessor untuk jenis badge
     */
    public function getJenisBadgeAttribute()
    {
        $badges = [
            'allowance' => '<span class="badge bg-success">Tunjangan</span>',
            'deduction' => '<span class="badge bg-danger">Potongan</span>',
        ];

        return $badges[$this->jenis] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Accessor untuk tipe perhitungan label
     */
    public function getTipePerhitunganLabelAttribute()
    {
        $labels = [
            'fixed' => 'Tetap',
            'percentage' => 'Persentase',
            'formula' => 'Formula',
        ];

        return $labels[$this->tipe_perhitungan] ?? $this->tipe_perhitungan;
    }

    /**
     * Scope untuk komponen aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk tunjangan
     */
    public function scopeAllowances($query)
    {
        return $query->where('jenis', 'allowance');
    }

    /**
     * Scope untuk potongan
     */
    public function scopeDeductions($query)
    {
        return $query->where('jenis', 'deduction');
    }
    
    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\PayrollComponentFactory::new();
    }
}