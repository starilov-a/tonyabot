<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Services\SpaceToster\Behaviors\MessageBehavior;
use App\Services\SpaceToster\Cooldowns\CooldownStandart;
use App\Services\Telegram;

class DaBehavior extends AbstarctReactiveBehavior implements MessageBehavior
{
    protected $code = 'da';
    private $dictionary = ['да','да.','да?','da','da.','da?'];

    public function __construct()
    {
        $this->setCooldown(new CooldownStandart($this));
        parent::__construct();
    }

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $messageOutput = 'Меня заставили это делать...';
        foreach ($this->behaviorMessages as $message) {
            //TODO переделать поиск "да"
            $text = mb_strtolower($message->text);
            if (in_array($text,$this->dictionary)) {
                $chat = $message->chat->id;
                $message_id = $message->message_id;
                $update_id = $message->telegram_update_id;
                $messageOutput = 'пизда';
                break;
            }
        }
        $telegram->sendMessage($messageOutput, $chat, $message_id);
        $this->cooldown->refreshCooldown($update_id);
    }

    protected function checkLogic(): bool
    {
        foreach ($this->behaviorMessages as $message) {
            if (in_array($message->text, $this->dictionary)) {
                return $this->checkLogicStatus = true;
            }
        }
        return false;
    }
}
