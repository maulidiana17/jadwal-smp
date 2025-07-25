<?php

namespace App\Console;

use App\Console\Commands\GenerateDailyQR;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
      protected $commands = [
        GenerateDailyQR::class, // daftar command custom
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
          $schedule->command('qr:generate-daily')
                 ->everyThirtyMinutes();
                //  ->between('5:00', '8:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
