<?php

namespace Modules\Master\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'karyawan';

    protected $fillable = [
        'company_id',
        'employee_number',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'address',
        'department_id',
        'position_id',
        'manager_id',
        'join_date',
        'employment_status',
        'avatar',
        'is_active',
        // Kolom legacy untuk kompatibilitas backward
        'nama_lengkap',
        'nomor_hp',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat_ktp',
        'foto_diri',
        'jabatan',
        'status',
    ];

    protected $casts = [
        'company_id'    => 'integer',
        'department_id' => 'integer',
        'position_id'   => 'integer',
        'manager_id'    => 'integer',
        'birth_date'    => 'date',
        'join_date'     => 'date',
        'is_active'     => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $avatar = $this->avatar ?: $this->foto_diri;
        if (!$avatar) {
            return null;
        }

        if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
            return $avatar;
        }

        if (Storage::disk('public')->exists($avatar)) {
            return Storage::disk('public')->url($avatar);
        }

        if (file_exists(public_path('storage/' . $avatar))) {
            return asset('storage/' . $avatar);
        }

        return asset($avatar);
    }

    /**
     * Get display name (name or nama_lengkap)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: ($this->nama_lengkap ?: '-');
    }

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relationship to Department
     */
    public function department()
    {
        return $this->belongsTo(Departemen::class, 'department_id');
    }

    /**
     * Relationship to Position
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Relationship to Manager (Direct Supervisor)
     */
    public function manager()
    {
        return $this->belongsTo(Karyawan::class, 'manager_id');
    }

    /**
     * Relationship to Subordinates
     */
    public function subordinates()
    {
        return $this->hasMany(Karyawan::class, 'manager_id');
    }

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relationship to Tasks assigned to this employee
     */
    public function assignedTasks()
    {
        return $this->belongsToMany(\Modules\Task\Entities\Task::class, 'task_assignees', 'employee_id', 'task_id')
            ->withPivot(['assigned_at', 'assigned_by', 'start_date', 'due_date', 'status', 'progress', 'completed_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Relationship to TaskAssignee records
     */
    public function taskAssignees()
    {
        return $this->hasMany(\Modules\Task\Entities\TaskAssignee::class, 'employee_id');
    }

    /**
     * Relationship to Tasks followed by this employee
     */
    public function followedTasks()
    {
        return $this->belongsToMany(\Modules\Task\Entities\Task::class, 'task_followers', 'employee_id', 'task_id')
            ->withPivot(['followed_at'])
            ->withTimestamps();
    }
}
