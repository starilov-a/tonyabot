<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Models\Message;

abstract class AbstarctReactiveBehavior extends \App\Services\SpaceToster\Behaviors\Behavior
{
    public function __construct()
    {
        parent::__construct();
        $this->setBehaviorMessage();
    }

    protected function checkIssetMessages(): bool
    {
        if ($this->behaviorMessage->isEmpty())
            return false;
        return true;
    }

    protected function setBehaviorMessage()
    {
        $this->behaviorMessage = Message::where('telegram_update_id', '>', $this->behaviorModel->telegram_update_id)->orderByDesc('id')->take(10)->get();
    }

    public function reasonToMessage(): bool
    {
        if (!$this->checkCooldown())
            return false;
        if (!$this->checkIssetMessages())
            return false;
        if (!$this->checkLogic())
            return false;
        return true;
    }
}
