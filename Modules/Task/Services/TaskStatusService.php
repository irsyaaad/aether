<?php

namespace Modules\Task\Services;

use Carbon\Carbon;

class TaskStatusService
{
    public const STATUS_DRAFT       = 'draft';
    public const STATUS_ASSIGNED    = 'assigned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_ON_HOLD     = 'on_hold';
    public const STATUS_COMPLETED   = 'completed';
    public const STATUS_CANCELLED   = 'cancelled';
    public const STATUS_EXPIRED     = 'expired';

    /**
     * Get all available task statuses with their Indonesian labels
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_DRAFT       => 'Draft',
            self::STATUS_ASSIGNED    => 'Ditugaskan',
            self::STATUS_IN_PROGRESS => 'Sedang Berjalan',
            self::STATUS_ON_HOLD     => 'Ditunda',
            self::STATUS_COMPLETED   => 'Selesai',
            self::STATUS_CANCELLED   => 'Dibatalkan',
            self::STATUS_EXPIRED     => 'Kadaluarsa',
        ];
    }

    /**
     * Check if a status string is valid
     */
    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::getAvailableStatuses());
    }

    /**
     * Get Indonesian label for a given status
     */
    public static function getStatusLabel(?string $status): string
    {
        $statuses = self::getAvailableStatuses();
        return $statuses[$status] ?? ucfirst(str_replace('_', ' ', $status ?? '-'));
    }

    /**
     * Get Tailwind badge classes for status
     */
    public static function getStatusBadgeClass(?string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT       => 'bg-gray-100 text-gray-700 border-gray-200',
            self::STATUS_ASSIGNED    => 'bg-sky-50 text-sky-700 border-sky-200',
            self::STATUS_IN_PROGRESS => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_ON_HOLD     => 'bg-purple-50 text-purple-700 border-purple-200',
            self::STATUS_COMPLETED   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_CANCELLED   => 'bg-rose-50 text-rose-700 border-rose-200',
            self::STATUS_EXPIRED     => 'bg-slate-100 text-slate-600 border-slate-200',
            default                  => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    /**
     * Determine if a task is overdue.
     * Rule: due_date < today AND status NOT IN ('completed', 'cancelled', 'expired')
     */
    public static function isOverdue($task): bool
    {
        if (empty($task->due_date)) {
            return false;
        }

        if (in_array($task->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_EXPIRED])) {
            return false;
        }

        $dueDate = $task->due_date instanceof Carbon ? $task->due_date : Carbon::parse($task->due_date);
        return $dueDate->startOfDay()->lt(now()->startOfDay());
    }

    /**
     * Query scope for overdue tasks
     */
    public static function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_EXPIRED]);
    }

    /**
     * Query scope for non-overdue tasks
     */
    public static function scopeNotOverdue($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('due_date')
              ->orWhereDate('due_date', '>=', now()->toDateString())
              ->orWhereIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED, self::STATUS_EXPIRED]);
        });
    }
}
