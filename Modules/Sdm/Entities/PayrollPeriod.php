<?php

namespace Modules\Sdm\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class PayrollPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payroll_periods';

    protected $fillable = [
        'kode_periode',
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_gajian',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_gajian' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
     * Generate kode periode otomatis
     */
    public static function generateKodePeriode()
    {
        $now = Carbon::now();
        $prefix = 'PAY/' . $now->format('y.m') . '-';
        
        $lastNumber = self::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->where('kode_periode', 'like', $prefix . '%')
            ->select(DB::raw("CAST(SUBSTRING_INDEX(kode_periode, '-', -1) AS UNSIGNED) as nomor"))
            ->orderByDesc('nomor')
            ->value('nomor');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship dengan PayrollSlip
     */
    public function payrollSlips()
    {
        return $this->hasMany(PayrollSlip::class, 'payroll_period_id');
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
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'processed' => '<span class="badge bg-info">Processed</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'closed' => '<span class="badge bg-dark">Closed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Check if period can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['draft']);
    }

    /**
     * Check if period can be processed
     */
    public function canBeProcessed()
    {
        return in_array($this->status, ['draft']);
    }
    
    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\PayrollPeriodFactory::new();
    }
}