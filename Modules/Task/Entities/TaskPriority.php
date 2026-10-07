<?php

namespace Modules\Task\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Master\Entities\Company;

class TaskPriority extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_priorities';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'company_id' => 'integer',
        'level'      => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'priority_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level', 'asc');
    }

    /**
     * Get Tailwind badge classes based on code / level
     */
    public function getBadgeClassAttribute(): string
    {
        $code = strtoupper($this->code);
        return match ($code) {
            'URGENT' => 'bg-red-50 text-red-700 border-red-200',
            'HIGH'   => 'bg-amber-50 text-amber-700 border-amber-200',
            'MEDIUM' => 'bg-blue-50 text-blue-700 border-blue-200',
            'LOW'    => 'bg-slate-50 text-slate-700 border-slate-200',
            default  => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
