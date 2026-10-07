<?php
namespace Modules\Sdm\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tanggal_meeting',
        'peserta',
        'foto',
        'hasil_meeting',
        'jenis_meeting',
        'uploaded_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_meeting' => 'date',
        'peserta'         => 'array',
        'foto'            => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->uploaded_by = Auth::id();
                $model->updated_by  = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Relationship with User (uploader)
     */
    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by', 'id_user');
    }

    /**
     * Relationship with User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id_user');
    }

    /**
     * Get first photo URL
     */
    public function getFirstPhotoUrlAttribute()
    {
        if (empty($this->foto)) {
            return asset('images/placeholder.png');
        }
        return asset('storage/' . $this->foto[0]);
    }

    /**
     * Get all photo URLs
     */
    public function getPhotoUrlsAttribute()
    {
        if (empty($this->foto)) {
            return [];
        }

        $urls = [];
        foreach ($this->foto as $path) {
            $urls[] = asset('storage/' . $path);
        }
        return $urls;
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeClass()
    {
        $classes = [
            'Weekly'  => 'bg-info',
            'Monthly' => 'bg-primary',
            'Quartal' => 'bg-warning',
            'Yearly'  => 'bg-danger',
        ];

        return $classes[$this->jenis_meeting] ?? 'bg-secondary';
    }
}
