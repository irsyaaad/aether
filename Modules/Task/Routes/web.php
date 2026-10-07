<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\TaskCategoryController;
use Modules\Task\Http\Controllers\TaskController;
use Modules\Task\Http\Controllers\TaskPriorityController;

Route::group(['middleware' => ['auth']], function () {

    // My Tasks (Tugas Saya - Workspace Pengerjaan Tugas Karyawan)
    Route::get('my-tasks/tokos-select2', [\Modules\Task\Http\Controllers\MyTaskController::class, 'getTokosSelect2'])->name('tasks.my-tasks.tokos-select2');
    Route::get('my-tasks/produks-select2', [\Modules\Task\Http\Controllers\MyTaskController::class, 'getProduksSelect2'])->name('tasks.my-tasks.produks-select2');
    Route::get('my-tasks/get-data', [\Modules\Task\Http\Controllers\MyTaskController::class, 'getData'])->name('tasks.my-tasks.get-data');
    Route::get('my-tasks', [\Modules\Task\Http\Controllers\MyTaskController::class, 'index'])->name('tasks.my-tasks.index');
    Route::get('my-tasks/{id}', [\Modules\Task\Http\Controllers\MyTaskController::class, 'show'])->name('tasks.my-tasks.show');
    Route::post('my-tasks/{id}/complete', [\Modules\Task\Http\Controllers\MyTaskController::class, 'completeTask'])->name('tasks.my-tasks.complete');
    Route::post('my-tasks/{id}/target', [\Modules\Task\Http\Controllers\MyTaskController::class, 'updateTarget'])->name('tasks.my-tasks.update-target');
    Route::post('my-tasks/{id}/data-entries', [\Modules\Task\Http\Controllers\MyTaskController::class, 'storeDataEntry'])->name('tasks.my-tasks.data-entries.store');
    Route::match(['put', 'post'], 'my-tasks/data-entries/{entryId}', [\Modules\Task\Http\Controllers\MyTaskController::class, 'updateDataEntry'])->name('tasks.my-tasks.data-entries.update');
    Route::delete('my-tasks/data-entries/{entryId}', [\Modules\Task\Http\Controllers\MyTaskController::class, 'destroyDataEntry'])->name('tasks.my-tasks.data-entries.destroy');
    Route::post('my-tasks/{id}/stock-updates', [\Modules\Task\Http\Controllers\MyTaskController::class, 'storeStockUpdate'])->name('tasks.my-tasks.stock-updates.store');
    Route::delete('my-tasks/stock-updates/{updateId}', [\Modules\Task\Http\Controllers\MyTaskController::class, 'destroyStockUpdate'])->name('tasks.my-tasks.stock-updates.destroy');
    Route::post('my-tasks/{id}/stock-matrix', [\Modules\Task\Http\Controllers\MyTaskController::class, 'updateStockMatrix'])->name('tasks.my-tasks.stock-matrix');

    // Tasks Routes
    Route::get('tasks/tokos-select2', [TaskController::class, 'getTokosSelect2'])->name('tasks.tokos-select2');
    Route::get('tasks/get-data', [TaskController::class, 'getData'])->name('tasks.get-data');
    Route::post('tasks/{id}/progress', [TaskController::class, 'updateProgress'])->name('tasks.update-progress');
    Route::post('tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('tasks/{id}/target', [TaskController::class, 'updateTarget'])->name('tasks.update-target');
    Route::get('tasks/{id}/data-entries/get-data', [TaskController::class, 'getDataEntries'])->name('tasks.data-entries.get-data');
    Route::post('tasks/{id}/data-entries', [TaskController::class, 'storeDataEntry'])->name('tasks.data-entries.store');
    Route::delete('tasks/data-entries/{entryId}', [TaskController::class, 'destroyDataEntry'])->name('tasks.data-entries.destroy');
    Route::post('tasks/{id}/stock-updates', [TaskController::class, 'storeStockUpdate'])->name('tasks.stock-updates.store');
    Route::delete('tasks/stock-updates/{updateId}', [TaskController::class, 'destroyStockUpdate'])->name('tasks.stock-updates.destroy');
    Route::get('tasks/{id}/employee-recap/get-data', [TaskController::class, 'getEmployeeRecap'])->name('tasks.employee-recap.get-data');
    Route::resource('tasks', TaskController::class)->names('tasks');

    // Task Assignment / Plotting Routes (CRUD)
    Route::get('task-assignment/get-data', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'getData'])->name('tasks.assignment.get-data');
    Route::get('task-assignment/create', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'create'])->name('tasks.assignment.create');
    Route::post('task-assignment', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'store'])->name('tasks.assignment.store');
    Route::get('task-assignment', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'index'])->name('tasks.assignment.index');
    Route::get('task-assignment/submission/{id}', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'showSubmission'])->name('tasks.assignment.submission');
    Route::get('task-assignment/{employeeId}/detail', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'detail'])->name('tasks.assignment.detail');
    Route::get('task-assignment/{employeeId}/edit', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'edit'])->name('tasks.assignment.edit');
    Route::match(['put', 'post'], 'task-assignment/{employeeId}', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'update'])->name('tasks.assignment.update');
    Route::delete('task-assignment/{employeeId}', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'destroy'])->name('tasks.assignment.destroy');
    // Legacy aliases for backward compatibility
    Route::get('task-assignment/plot/{employeeId}', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'plot'])->name('tasks.assignment.plot');
    Route::post('task-assignment/plot/{employeeId}', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'savePlot'])->name('tasks.assignment.save-plot');
    Route::get('task-assignment/employee-tasks/{employeeId}', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'getEmployeeTasks'])->name('tasks.assignment.employee-tasks');
    Route::post('task-assignment/assign', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'assignTask'])->name('tasks.assignment.assign');
    Route::post('task-assignment/unassign', [\Modules\Task\Http\Controllers\TaskAssigneeController::class, 'unassignTask'])->name('tasks.assignment.unassign');
    // Task Monitoring / Submission Tracking Routes
    Route::get('task-monitoring/get-data', [\Modules\Task\Http\Controllers\TaskMonitoringController::class, 'getData'])->name('tasks.monitoring.get-data');
    Route::get('task-monitoring/{id}/detail', [\Modules\Task\Http\Controllers\TaskMonitoringController::class, 'detail'])->name('tasks.monitoring.detail');
    Route::get('task-monitoring', [\Modules\Task\Http\Controllers\TaskMonitoringController::class, 'index'])->name('tasks.monitoring.index');

    // Task Categories Routes
    Route::get('task-categories/get-data', [TaskCategoryController::class, 'getData'])->name('task-categories.get-data');
    Route::get('task-categories/by-company/{companyId}', [TaskCategoryController::class, 'byCompany'])->name('task-categories.by-company');
    Route::resource('task-categories', TaskCategoryController::class)->names('task-categories')->except(['create', 'show', 'edit']);

    // Task Priorities Routes
    Route::get('task-priorities/get-data', [TaskPriorityController::class, 'getData'])->name('task-priorities.get-data');
    Route::get('task-priorities/by-company/{companyId}', [TaskPriorityController::class, 'byCompany'])->name('task-priorities.by-company');
    Route::resource('task-priorities', TaskPriorityController::class)->names('task-priorities')->except(['create', 'show', 'edit']);

});

