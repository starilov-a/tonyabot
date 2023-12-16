<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Models\Message;
use App\Services\Telegram;
use Illuminate\Support\Facades\Storage;
use Orhanerday\OpenAi\OpenAi;

class HorrorStatusBehavior extends AbstarctReactiveBehavior implements \App\Services\SpaceToster\Behaviors\MessageBehavior
{
    protected $code = 'horrorstatus';
    protected $timeAgo = 30;

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $lastMessage = $this->behaviorMessages->first();
        $video = Storage::disk('local')->get('public/media/videos/tryska.mp4');
        $chat_id = $lastMessage->chat_id;

        $telegram->sendVideo($video,'tryska.mp4', $chat_id);

        $update_id = $lastMessage->telegram_update_id;
        $this->refreshCooldown($update_id);
    }

    protected function checkLogic(): bool
    {
        $message = $this->behaviorMessages->implode('text','. ');
        $open_ai = new OpenAi(config('gptapi.api_key'));

        $responce = $open_ai->chat([
            'model' => 'gpt-3.5-turbo-0301',
            'messages' => [
                [
                    "role" => "system",
                    "content" => "Определи эмоцию страха и дай один из ответов:3 - страшно, 2 - тревожно, 1 - нейтрально"
                ],
                [
                    "role" => "user",
                    "content" => $message
                ],
            ],
            'temperature' => 1,
            'max_tokens' => 15,
            'frequency_penalty' => -2.0,
            'presence_penalty' => -2.0,
        ]);

        $data = json_decode($responce);
        if (!isset($data->choices))
            return false;

        $status = strpos($data->choices[0]->message->content, '3');

        if ($status !== false)
            return $this->checkLogicStatus = true;

        return false;
    }

    protected function setBehaviorMessages()
    {
        $minAgo = time() - $this->timeAgo;
        $lastUserMessage = Message::orderBy('message_id', 'desc')->first();
        $this->behaviorMessages = Message::where('chat_id', '=', $lastUserMessage->chat_id)
            ->where('date', '>', $minAgo)
            ->where('telegram_user_id', '=', $lastUserMessage->telegram_user_id)
            ->orderByDesc('message_id')->take(3)->get();
    }
}
