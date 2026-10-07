<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    /**
     * Test that all core Task Management V1 routes are registered
     */
    public function test_task_routes_are_registered()
    {
        $this->assertTrue(Route::has('tasks.index'));
        $this->assertTrue(Route::has('tasks.create'));
        $this->assertTrue(Route::has('tasks.store'));
        $this->assertTrue(Route::has('tasks.show'));
        $this->assertTrue(Route::has('tasks.edit'));
        $this->assertTrue(Route::has('tasks.update'));
        $this->assertTrue(Route::has('tasks.destroy'));
        $this->assertTrue(Route::has('tasks.get-data'));
        $this->assertTrue(Route::has('tasks.update-progress'));
        $this->assertTrue(Route::has('tasks.update-status'));

        $this->assertTrue(Route::has('task-categories.index'));
        $this->assertTrue(Route::has('task-categories.get-data'));
        $this->assertTrue(Route::has('task-categories.by-company'));

        $this->assertTrue(Route::has('task-priorities.index'));
        $this->assertTrue(Route::has('task-priorities.get-data'));
        $this->assertTrue(Route::has('task-priorities.by-company'));

        $this->assertTrue(Route::has('tasks.assignment.index'));
        $this->assertTrue(Route::has('tasks.assignment.employee-tasks'));
        $this->assertTrue(Route::has('tasks.assignment.assign'));
        $this->assertTrue(Route::has('tasks.assignment.unassign'));
        $this->assertTrue(Route::has('tasks.assignment.batch-assign'));
    }
}
