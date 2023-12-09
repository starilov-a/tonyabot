<?php

namespace App\Http\Controllers\BotController;

use App\Http\Controllers\Controller;
use App\Services\SpaceToster\SpaceToster;
use App\Services\Telegram;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function index(Telegram $telegram): void
    {
        $spaceToster = new SpaceToster;

        $behavior = $spaceToster->getActiveBehavior();

        if (!empty($behavior)){
            $spaceToster->setMessageBehavior($behavior);
            $spaceToster->message($telegram);
        }
    }
}
