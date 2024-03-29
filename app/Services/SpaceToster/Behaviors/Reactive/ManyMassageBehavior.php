<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Models\Message;
use App\Services\SpaceToster\Cooldowns\CooldownStandart5Min;
use App\Services\Telegram;
use Illuminate\Support\Facades\Storage;

class ManyMassageBehavior extends AbstarctReactiveBehavior implements \App\Services\SpaceToster\Behaviors\MessageBehavior
{
    protected $code = 'manymassage';
    protected $countTriggerMessage = 50;
    protected $timeAgo = 60*5;

    public function __construct()
    {
        $this->setCooldown(new CooldownStandart5Min($this->getBehaviorModel()));
        parent::__construct();
    }

    public function message(Telegram $telegram): void
    {
        $video = Storage::disk('local')->get('public/media/videos/wtf.mp4');
        $chat_id = $this->behaviorMessages->first()->chat_id;
        $update_id = $this->behaviorMessages->first()->telegram_update_id;;
        $telegram->sendVideo($video,'sueta.mp4', $chat_id);
        $this->cooldown->refreshCooldown($update_id);
    }

    protected function checkLogic(): bool
    {
        if ($this->behaviorMessages->count() > $this->countTriggerMessage)
            return true;
        return false;
    }

    protected function setBehaviorMessages()
    {
        $lastUserMessage = Message::orderBy('id', 'desc')->first();
        $minAgo = time() - $this->timeAgo;
        $this->behaviorMessages = Message::where('chat_id', '=', $lastUserMessage->chat_id)
            ->where('date', '>', $minAgo)->orderByDesc('id')->limit($this->countTriggerMessage + 1)->get();
    }

    protected function refreshCooldown($update_id): void
    {
        $this->behaviorModel->update(['date' => time(), 'telegram_update_id' => $update_id]);
    }
}
