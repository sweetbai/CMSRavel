<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Str;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //自定义命令
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 每天执行访客清理
        $schedule->command('visitor:clear')->daily();
        // 每周三清理操作日志
        $schedule->command('operate:clear')->weeklyOn(3);

        // $schedule->command('inspire')->hourly();
    }

    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);

        // 注册模块命令
        $list = glob(base_path('modules') . '/*/Console/*.php');
        foreach ($list as $file) {
            $file = str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file, base_path('modules').DIRECTORY_SEPARATOR)
                );
            $this->commands[] =  "\\Modules\\{$file}";
        }
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
