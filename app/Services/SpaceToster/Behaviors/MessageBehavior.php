<?php


namespace App\Services\SpaceToster\Behaviors;


use App\Services\Telegram;

interface MessageBehavior
{
    public function message(Telegram $telegram): void;
    public function reasonToMessage(): bool;
}
