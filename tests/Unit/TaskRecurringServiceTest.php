<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Modules\Task\Services\TaskRecurringService;
use PHPUnit\Framework\TestCase;

class TaskRecurringServiceTest extends TestCase
{
    /**
     * Test available recurring types
     */
    public function test_recurring_types_are_available()
    {
        $types = TaskRecurringService::getTypes();

        $this->assertArrayHasKey(TaskRecurringService::TYPE_HARIAN, $types);
        $this->assertArrayHasKey(TaskRecurringService::TYPE_BULANAN, $types);
        $this->assertArrayHasKey(TaskRecurringService::TYPE_INSIDENTIL, $types);

        $this->assertEquals('Harian', $types[TaskRecurringService::TYPE_HARIAN]);
        $this->assertEquals('Bulanan', $types[TaskRecurringService::TYPE_BULANAN]);
        $this->assertEquals('Insidentil', $types[TaskRecurringService::TYPE_INSIDENTIL]);
    }

    /**
     * Test target auto progress calculation logic
     */
    public function test_target_auto_progress_calculation()
    {
        $task = new \Modules\Task\Entities\Task([
            'output_type'     => 'target',
            'target_amount'   => 5000000,
            'achieved_amount' => 2500000,
            'progress'        => 0,
        ]);

        $this->assertEquals(50, $task->calculateAutoProgress());

        $task->achieved_amount = 5000000;
        $this->assertEquals(100, $task->calculateAutoProgress());

        $task->achieved_amount = 7500000;
        $this->assertEquals(100, $task->calculateAutoProgress());
    }
}
