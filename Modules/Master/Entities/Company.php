<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'logo',
        'website',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get URL for company logo
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
     * Get departments belonging to this company
     */
    public function departments()
    {
        return $this->hasMany(Departemen::class, 'company_id');
    }

    /**
     * Get positions belonging to this company
     */
    public function positions()
    {
        return $this->hasMany(Position::class, 'company_id');
    }

    /**
     * Get employees belonging to this company
     */
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'company_id');
    }

    /**
     * Get stores (toko) belonging to this company
     */
    public function tokos()
    {
        return $this->hasMany(Toko::class, 'company_id');
    }

    /**
     * Get products belonging to this company
     */
    public function produks()
    {
        return $this->hasMany(Produk::class, 'company_id');
    }

    /**
     * Get platforms belonging to this company
     */
    public function platforms()
    {
        return $this->hasMany(Platform::class, 'company_id');
    }
}
