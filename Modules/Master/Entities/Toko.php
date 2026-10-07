<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Toko extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tokos';

    public const CATEGORY_BANGUNAN = 'bangunan';
    public const CATEGORY_PACKAGING = 'packaging';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'category',
        'phone',
        'email',
        'address',
        'pic_name',
        'is_active',
    ];

    /**
     * Get available toko categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_BANGUNAN => 'Bangunan',
            self::CATEGORY_PACKAGING => 'Packaging',
        ];
    }

    /**
     * Accessor for category label
     */
    public function getCategoryLabelAttribute(): string
    {
        $categories = self::getCategories();
        $cat = strtolower($this->category ?? '');
        return $categories[$cat] ?? ($this->category ? ucfirst($this->category) : '-');
    }

    /**
     * Alias for category attribute (Indonesian)
     */
    public function getKategoriAttribute(): ?string
    {
        return $this->category;
    }

    protected $casts = [
        'company_id' => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Scope for active tokos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope filter by category
     */
    public function scopeCategory($query, ?string $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope for bangunan tokos
     */
    public function scopeBangunan($query)
    {
        return $query->where('category', self::CATEGORY_BANGUNAN);
    }

    /**
     * Scope for packaging tokos
     */
    public function scopePackaging($query)
    {
        return $query->where('category', self::CATEGORY_PACKAGING);
    }
}
