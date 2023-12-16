<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Models\Message;
use App\Services\Telegram;

class LargeMessageBehavior extends AbstarctReactiveBehavior implements \App\Services\SpaceToster\Behaviors\MessageBehavior
{

    protected $code = 'largemessage';

    protected function checkLogic(): bool
    {
        $message = $this->behaviorMessages->first();
        if (strlen($message) > 300)
            return $this->checkLogicStatus = true;

        return false;
    }

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $userMessage = $this->behaviorMessages->first();

        $messageOutput = 'ну ты и высрал';
        $message_id = $userMessage->message_id;
        $chat = $userMessage->chat_id;
        $update_id = $userMessage->telegram_update_id;

        $telegram->sendMessage($messageOutput, $chat, $message_id);
        $this->refreshCooldown($update_id);
    }

    protected function setBehaviorMessages()
    {
        $this->behaviorMessages = collect([Message::where('telegram_update_id', '>', $this->behaviorModel->telegram_update_id)->orderBy('message_id', 'desc')->first()]);
    }
}
