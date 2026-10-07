<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Modules\Task\Services\TaskStatusService;
use PHPUnit\Framework\TestCase;

class TaskStatusServiceTest extends TestCase
{
    /**
     * Test all available statuses are valid
     */
    public function test_available_statuses()
    {
        $statuses = TaskStatusService::getAvailableStatuses();
        $this->assertArrayHasKey('draft', $statuses);
        $this->assertArrayHasKey('assigned', $statuses);
        $this->assertArrayHasKey('in_progress', $statuses);
        $this->assertArrayHasKey('on_hold', $statuses);
        $this->assertArrayHasKey('completed', $statuses);
        $this->assertArrayHasKey('cancelled', $statuses);

        $this->assertTrue(TaskStatusService::isValidStatus('draft'));
        $this->assertTrue(TaskStatusService::isValidStatus('completed'));
        $this->assertFalse(TaskStatusService::isValidStatus('overdue')); // Overdue is NOT a database status!
    }

    /**
     * Test overdue computation:
     * Overdue is: due_date < today AND status NOT IN ('completed', 'cancelled')
     */
    public function test_overdue_logic_when_due_date_past_and_in_progress()
    {
        $task = (object)[
            'due_date' => Carbon::yesterday(),
            'status'   => 'in_progress',
        ];

        $this->assertTrue(TaskStatusService::isOverdue($task));
    }

    public function test_overdue_logic_when_due_date_past_but_completed()
    {
        $task = (object)[
            'due_date' => Carbon::yesterday(),
            'status'   => 'completed',
        ];

        $this->assertFalse(TaskStatusService::isOverdue($task));
    }

    public function test_overdue_logic_when_due_date_past_but_cancelled()
    {
        $task = (object)[
            'due_date' => Carbon::yesterday(),
            'status'   => 'cancelled',
        ];

        $this->assertFalse(TaskStatusService::isOverdue($task));
    }

    public function test_overdue_logic_when_due_date_today()
    {
        $task = (object)[
            'due_date' => Carbon::today(),
            'status'   => 'in_progress',
        ];

        $this->assertFalse(TaskStatusService::isOverdue($task));
    }

    public function test_overdue_logic_when_due_date_future()
    {
        $task = (object)[
            'due_date' => Carbon::tomorrow(),
            'status'   => 'in_progress',
        ];

        $this->assertFalse(TaskStatusService::isOverdue($task));
    }

    public function test_overdue_logic_when_no_due_date()
    {
        $task = (object)[
            'due_date' => null,
            'status'   => 'in_progress',
        ];

        $this->assertFalse(TaskStatusService::isOverdue($task));
    }

    public function test_status_badges_and_labels()
    {
        $this->assertEquals('Selesai', TaskStatusService::getStatusLabel('completed'));
        $this->assertEquals('Sedang Berjalan', TaskStatusService::getStatusLabel('in_progress'));
        $this->assertStringContainsString('emerald', TaskStatusService::getStatusBadgeClass('completed'));
        $this->assertStringContainsString('rose', TaskStatusService::getStatusBadgeClass('cancelled'));
    }
}
