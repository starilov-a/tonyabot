<?php

namespace App\Http\Controllers\SpaceTosterController;

use App\Http\Controllers\Controller;
use App\Services\Telegram;
use Illuminate\Http\Request;

class SpaceTosterController extends Controller
{
    public function index(Telegram $telegram)
    {
        $telegram->sendMessage('Привет');
    }
}
