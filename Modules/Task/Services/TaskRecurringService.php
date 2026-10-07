<?php

namespace Modules\Task\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Task\Entities\Task;

class TaskRecurringService
{
    public const TYPE_HARIAN     = 'harian';
    public const TYPE_BULANAN    = 'bulanan';
    public const TYPE_INSIDENTIL = 'insidentil';

    /**
     * Get all task types with labels
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_HARIAN     => 'Harian',
            self::TYPE_BULANAN    => 'Bulanan',
            self::TYPE_INSIDENTIL => 'Insidentil',
        ];
    }

    /**
     * Generate daily recurring tasks for a specific date (default: today).
     * Action disabled as per user request.
     *
     * @param Carbon|null $date
     * @return int Number of generated tasks
     */
    public static function generateDailyTasks(?Carbon $date = null): int
    {
        return 0;
    }

    /**
     * Generate monthly recurring tasks for a specific date/month (default: current month).
     * Action disabled as per user request.
     *
     * @param Carbon|null $date
     * @return int Number of generated tasks
     */
    public static function generateMonthlyTasks(?Carbon $date = null): int
    {
        return 0;
    }

    /**
     * Synchronize newly assigned employees from master template to active recurring instance
     */
    public static function syncAssigneesToInstance(Task $master, Task $instance, ?string $startDate = null, ?string $dueDate = null): void
    {
        // Action disabled as per user request
    }

    /**
     * Generate all due recurring tasks (Daily + Monthly)
     */
    public static function generateAllDueRecurringTasks(?Carbon $date = null): array
    {
        $dailyCount = self::generateDailyTasks($date);
        $monthlyCount = self::generateMonthlyTasks($date);

        return [
            'daily'   => $dailyCount,
            'monthly' => $monthlyCount,
            'total'   => $dailyCount + $monthlyCount,
        ];
    }
}
