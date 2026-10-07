<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produks';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'category',
        'unit',
        'price',
        'cost_price',
        'stock',
        'min_stock',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'price'      => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock'      => 'integer',
        'min_stock'  => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Scope for active produks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
