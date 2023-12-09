<?php


namespace App\Http\Controllers;


use App\Http\Controllers\BotController\BotController;
use App\Services\TelegramUpdates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TelegramController extends Controller
{
    public function index(TelegramUpdates $telegram, BotController $spaceTosterController) {
        $telegram->getUpdates();

        $spaceTosterController = App::make(BotController::class);
        $spaceTosterController->index($telegram);
    }
}
