<?php


namespace App\Services\SpaceToster\Behaviors;


use App\Models\Message;
use App\Services\Telegram;

abstract class Behavior
{
    protected $code = 'behavior';
    protected $behaviorModel;
    protected $behaviorMessages;
    protected $checkLogicStatus = false;


    public function __construct()
    {
        $this->behaviorModel = \App\Models\Behavior::where('code', $this->code)->first();
    }

    protected function checkCooldown(): bool
    {
        if (time() > ($this->behaviorModel->date + $this->behaviorModel->cooldown))
            return true;

        return false;
    }

    protected function refreshCooldown($update_id): void
    {
        dump($update_id);
        $this->behaviorModel->update(['date' => time(), 'telegram_update_id' => $update_id]);
    }

    abstract protected function checkLogic(): bool;
}
