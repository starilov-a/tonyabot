<?php


namespace App\Http\Controllers;


use App\Http\Controllers\SpaceTosterController\SpaceTosterController;
use App\Services\Telegram;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function index(Request $request, Telegram $telegram, SpaceTosterController $spaceTosterController) {
        $telegram->setRequest($request);

        $spaceTosterController->index($telegram);
    }
}
