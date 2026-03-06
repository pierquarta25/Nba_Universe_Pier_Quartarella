<?php



namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
    protected $commands = [
        \App\Console\Commands\SyncGiocatori::class,
    ];

    
    protected function schedule(Schedule $schedule): void
    {
        // Ogni notte alle 4:00 → aggiorna i dati dei giocatori
        // Scelto alle 4:00 perché è fuori dagli orari di picco
        // e le partite NBA (fuso USA) sono già finite
        $schedule->command('sync:giocatori')
                 ->dailyAt('04:00')
                 ->withoutOverlapping()     // non parte se il precedente è ancora in corso
                 ->runInBackground()        // non blocca altri task
                 ->appendOutputTo(storage_path('logs/sync-giocatori.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}