<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Artisan;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SmsSendCron::class,
        //Commands\TransferCron::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        for($i=0;$i<11; $i++){
            Artisan::call('smsSend:cron');
            // Artisan::call('transfer:cron');
            sleep(5);
        }
        
        // 
        
        
        
    }

    // protected function shortSchedule(\Spatie\ShortSchedule\ShortSchedule $shortSchedule)
    // {
        
    //     // this command will run every 30 seconds
    //     $shortSchedule->command('smsSend:cron')->everySeconds(5);
        
    //     // this command will run every half a second
    //     $shortSchedule->command('transfer:cron')->everySeconds(15);
    // }

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
