<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\Karyawan;

class Departemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departemens';

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'code',
        'description',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'parent_id'  => 'integer',
        'manager_id' => 'integer',
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
     * Relationship to Parent Department
     */
    public function parent()
    {
        return $this->belongsTo(Departemen::class, 'parent_id');
    }

    /**
     * Relationship to Sub Departments
     */
    public function children()
    {
        return $this->hasMany(Departemen::class, 'parent_id');
    }

    /**
     * Relationship to Manager (Karyawan)
     */
    public function manager()
    {
        return $this->belongsTo(Karyawan::class, 'manager_id', 'id');
    }

    /**
     * Relationship to Employees
     */
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'department_id');
    }

    /**
     * Get all recursive descendant IDs to prevent cyclical hierarchy
     */
    public function getAllDescendantIds(): array
    {
        $ids = [];
        $children = $this->children()->get();

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }

        return $ids;
    }
}
