<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //$schedule->command('tarik:absensi')->twiceDaily(9, 12);
        $schedule->command('tarik:absensi')->twiceDaily('09:15', '17:00');
        $schedule->command('insert:absensi')->twiceDaily('09:20', '17:05');
        // $schedule->command('tarik:absensi')->everyFiveMinutes();
        // $schedule->command('insert:absensi')->everyFiveMinutes();
        
        // Generate Daily Top Up setiap hari jam 00:01
        $schedule->command('daily-top-up:generate')->dailyAt('00:01');

        // Generate Daily & Monthly Recurring Tasks setiap hari jam 00:01
        $schedule->command('tasks:generate-recurring')->dailyAt('00:01');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
