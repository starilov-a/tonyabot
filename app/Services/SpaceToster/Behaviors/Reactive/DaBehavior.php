<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Models\Message;
use App\Services\SpaceToster\Behaviors\Behavior;
use App\Services\SpaceToster\Behaviors\MessageBehavior;
use App\Services\Telegram;

class DaBehavior extends AbstarctReactiveBehavior implements MessageBehavior
{
    protected $code = 'da';
    private $dictionary = ['да','Да','дА','DA','da','Da','dA','Да.','дА.','DA.','da.','Da.','dA.','да?'];

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $messageOutput = 'Меня заставляли это делать...';
        foreach ($this->behaviorMessage as $message) {
            if (in_array($message->text,$this->dictionary)) {
                $chat = $message->chat->id;
                $message_id = $message->id;
                $update_id = $message->telegram_update_id;
                $messageOutput = 'Пизда';
                break;
            }
        }
        $telegram->sendMessage($messageOutput, $chat, $message_id);
        $this->refreshCooldown($update_id);
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

    protected function checkLogic(): bool
    {
        foreach ($this->behaviorMessage as $message) {
            if (in_array($message->text, $this->dictionary)) {
                return $this->checkLogicStatus = true;
            }
        }
        return false;
    }
}
