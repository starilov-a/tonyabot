<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\GPT\Actions\Horror\HorrorGPTAction;
use App\Models\Message;
use App\Services\GPT\Chats\Horror\HorrorGPTChat;
use App\Services\Telegram;

class HorrorStatusBehavior extends AbstarctReactiveBehavior implements \App\Services\SpaceToster\Behaviors\MessageBehavior
{
    protected $code = 'horrorstatus';

    public function message(Telegram $telegram): void
    {
        // TODO: Implement message() method.
    }

    protected function checkLogic(): bool
    {
        $messages = $this->behaviorMessages->implode('text','. ');
        HorrorGPTAction::make()->send($messages);
        dump(HorrorGPTAction::make()->send($messages));
        return false;
    }

    protected function setBehaviorMessages()
    {
        $lastUserMessage = Message::orderBy('id', 'desc')->first();
        $this->behaviorMessages = Message::where('chat_id', '=', $lastUserMessage->chat_id)->where('telegram_user_id', '=', $lastUserMessage->telegram_user_id)->orderByDesc('id')->take(10)->get();
    }
}
