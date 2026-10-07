<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Platform extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'platforms';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'url',
        'logo',
        'fee_percentage',
        'description',
        'is_active',
    ];

    protected $casts = [
        'company_id'     => 'integer',
        'fee_percentage' => 'float',
        'is_active'      => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /**
     * Get URL for platform logo
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        return Storage::disk('public')->url($this->logo);
    }

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Scope for active platforms
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
