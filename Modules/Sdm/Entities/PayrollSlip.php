<?php

namespace Modules\Sdm\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use Modules\Sdm\Entities\Karyawan;

class PayrollSlip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payroll_slips';

    protected $fillable = [
        'nomor_slip',
        'payroll_period_id',
        'karyawan_id',
        'gaji_pokok',
        'total_tunjangan',
        'total_potongan',
        'gaji_kotor',
        'gaji_bersih',
        'hari_kerja',
        'hari_hadir',
        'hari_alpha',
        'jam_lembur',
        'status',
        'tanggal_bayar',
        'catatan',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_kotor' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'jam_lembur' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'approved_at' => 'datetime',
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
     * Generate nomor slip otomatis
     */
    public static function generateNomorSlip($periodId)
    {
        $period = PayrollPeriod::find($periodId);
        if (!$period) return null;

        $prefix = 'SLIP/' . $period->tanggal_mulai->format('y.m') . '-';
        
        $lastNumber = self::where('payroll_period_id', $periodId)
            ->where('nomor_slip', 'like', $prefix . '%')
            ->select(DB::raw("CAST(SUBSTRING_INDEX(nomor_slip, '-', -1) AS UNSIGNED) as nomor"))
            ->orderByDesc('nomor')
            ->value('nomor');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship dengan PayrollPeriod
     */
    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    /**
     * Relationship dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    /**
     * Relationship dengan PayrollSlipDetail
     */
    public function details()
    {
        return $this->hasMany(PayrollSlipDetail::class, 'payroll_slip_id');
    }

    /**
     * Relationship dengan User yang approve
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
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
            'approved' => '<span class="badge bg-success">Approved</span>',
            'paid' => '<span class="badge bg-primary">Paid</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Calculate totals from details
     */
    public function calculateTotals()
    {
        $allowances = $this->details()
            ->whereHas('component', function($q) {
                $q->where('jenis', 'allowance');
            })->sum('nilai');

        $deductions = $this->details()
            ->whereHas('component', function($q) {
                $q->where('jenis', 'deduction');
            })->sum('nilai');

        $this->update([
            'total_tunjangan' => $allowances,
            'total_potongan' => $deductions,
            'gaji_kotor' => $this->gaji_pokok + $allowances,
            'gaji_bersih' => $this->gaji_pokok + $allowances - $deductions,
        ]);
    }

    /**
     * Check if slip can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['draft']);
    }
    
    protected static function newFactory()
    {
        return \Modules\Sdm\Database\factories\PayrollSlipFactory::new();
    }
}