<?php

namespace App\Console;

use App\Console\Commands\GatherNotify;
use App\Console\Commands\PushTaskDueAlert;
use App\Console\Commands\RunMerchAPIMonitor;
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
        RunMerchAPIMonitor::class,
        PushTaskDueAlert::class,
        GatherNotify::class,
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
        $schedule->command('merch:api-monitor')->everyFiveMinutes();
//        $schedule->command('ip-login:destroy')->everyMinute();
        $schedule->command('push:due-tasks')->everyMinute();
        $schedule->command('notify:gather')->everyMinute();
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
