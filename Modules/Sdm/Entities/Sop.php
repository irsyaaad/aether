<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sop extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sop';

    protected $fillable = [
        'jabatan_id',
        'kode_sop',
        'judul_sop',
        'deskripsi',
        'prosedur',
        'versi',
        'tanggal_berlaku',
        'tanggal_review',
        'status',
        'dokumen_file',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
        'tanggal_review' => 'date',
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
     * Scope untuk SOP aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate kode SOP otomatis
     */
    public static function generateKodeSop($jabatanId)
    {
        $jabatan = Jabatan::find($jabatanId);
        $kodeJabatan = $jabatan ? $jabatan->kode_jabatan : 'JBT';
        
        $lastSop = self::where('jabatan_id', $jabatanId)
            ->where('kode_sop', 'like', "SOP/{$kodeJabatan}/%")
            ->orderBy('kode_sop', 'desc')
            ->first();
        
        if ($lastSop) {
            $lastNumber = (int) substr($lastSop->kode_sop, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "SOP/{$kodeJabatan}/" . date('y') . $newNumber;
    }
}