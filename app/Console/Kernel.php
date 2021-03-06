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
        \App\Console\Commands\GiftCount::class,
        \App\Console\Commands\BattleWar::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('GiftCount noPlayCheck')
            ->sendOutputTo('logger')
            ->cron('0 20 * * *');

        $schedule->command('GiftCount sunday')
            ->sendOutputTo('logger')
            ->cron('0 20 * * 0');

        $schedule->command('BattleWar discord')
            ->sendOutputTo('logger')
            ->cron('0 8 * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
