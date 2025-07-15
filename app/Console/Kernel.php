<?php

namespace App\Console;

use App\Models\PropertySetting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Определение расписания команд.
     */
    protected function schedule(Schedule $schedule)
    {
        $minutes = PropertySetting::getValue('async_period_minutes', 5);

        $schedule->command('sync:planyo-users')->everyXMinutes($minutes)->withoutOverlapping(); // Каждые 5 минут
        $schedule->command('sync:planyo-reservations')->everyXMinutes($minutes)->withoutOverlapping();
    }

    /**
     * Регистрация команд Artisan.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
