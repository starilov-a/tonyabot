<?php

namespace App\Console\Commands;

use App\Http\Controllers\BotController\BotController;
use App\Http\Controllers\TelegramController;
use App\Services\TelegramUpdates;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class checkchat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:checkchat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление чатов';

    /**
     * Execute the console command.
     */
    public function handle(TelegramUpdates $telegram, BotController $spaceTosterController)
    {
        $i = 0;
        while ($i <= 12) {
            $controller = App::make(TelegramController::class);
            $controller->index($telegram, $spaceTosterController);
            $i++;
            sleep(5);
        }

    }
}
