<?php

namespace Modules\Task\Services;

use Illuminate\Support\Facades\DB;
use Modules\Task\Entities\Task;

class TaskNumberService
{
    /**
     * Generate unique task number for a company in format: TASK-{YEAR}-{SEQUENCE}
     * Example: TASK-2026-000001
     *
     * @param int $companyId
     * @param int|null $year
     * @return string
     */
    public static function generate(int $companyId, ?int $year = null): string
    {
        $year = $year ?: (int) date('Y');
        $prefix = "TASK-{$year}-";

        // Query the latest task number for this company and year
        // We look directly into tasks table (including soft deleted tasks to prevent duplicate constraint violation)
        $latest = Task::withTrashed()
            ->where('company_id', $companyId)
            ->where('task_number', 'like', $prefix . '%')
            ->orderByDesc('task_number')
            ->lockForUpdate()
            ->value('task_number');

        $nextSeq = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $seqStr = end($parts);
            if (is_numeric($seqStr)) {
                $nextSeq = ((int) $seqStr) + 1;
            }
        }

        do {
            $formatted = sprintf('%s%06d', $prefix, $nextSeq);
            $exists = Task::withTrashed()
                ->where('company_id', $companyId)
                ->where('task_number', $formatted)
                ->exists();

            if ($exists) {
                $nextSeq++;
            }
        } while ($exists);

        return $formatted;
    }
}
