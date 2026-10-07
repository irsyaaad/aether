<?php

namespace Modules\Task\Console;

use Illuminate\Console\Command;
use Modules\Task\Services\TaskRecurringService;

class GenerateRecurringTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily and monthly recurring tasks and inherit plotted employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recurring tasks generation...');

        $results = TaskRecurringService::generateAllDueRecurringTasks();

        $this->info("Generated {$results['daily']} daily tasks and {$results['monthly']} monthly tasks.");
        $this->info("Total: {$results['total']} tasks generated.");

        return 0;
    }
}
