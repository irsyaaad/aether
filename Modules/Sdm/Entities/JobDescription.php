<?php

namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class JobDescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_description';

    protected $fillable = [
        'jabatan_id',
        'kode_jobdesc',
        'judul_jobdesc',
        'deskripsi',
        'tugas_utama',
        'tanggung_jawab',
        'kualifikasi',
        'kompetensi',
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
     * Scope untuk Job Description aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate kode Job Description otomatis
     */
    public static function generateKodeJobdesc($jabatanId)
    {
        $jabatan = Jabatan::find($jabatanId);
        $kodeJabatan = $jabatan ? $jabatan->kode_jabatan : 'JBT';
        
        $lastJobdesc = self::where('jabatan_id', $jabatanId)
            ->where('kode_jobdesc', 'like', "JD/{$kodeJabatan}/%")
            ->orderBy('kode_jobdesc', 'desc')
            ->first();
        
        if ($lastJobdesc) {
            $lastNumber = (int) substr($lastJobdesc->kode_jobdesc, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "JD/{$kodeJabatan}/" . date('y') . $newNumber;
    }
}