<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Post;
use App\Models\Feed;
use Carbon\Carbon;
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
        //      ->everyMinute()
        //      ->appendOutputTo(storage_path('logs/inspire.log'));
        // $schedule->command('inspire')->hourly();
        // $schedule->call(function () {
        //     Post::where('created_at', '<', Carbon::now()->subDays(7))->delete();
        // })->daily();
        $schedule->call(function () {
            Post::where('created_at', '<', Carbon::now()->subDays(5))->delete();
            Feed::where('created_at', '<', Carbon::now()->subDays(14))->delete();
        })->daily();
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
