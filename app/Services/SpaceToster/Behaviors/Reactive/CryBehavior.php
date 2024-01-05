<?php


namespace App\Services\SpaceToster\Behaviors\Reactive;


use App\Models\Message;
use App\Services\SpaceToster\Cooldowns\CooldownStandart5Min;
use App\Services\Telegram;
use Illuminate\Support\Facades\Log;
use Orhanerday\OpenAi\OpenAi;

class CryBehavior extends AbstarctReactiveBehavior implements \App\Services\SpaceToster\Behaviors\MessageBehavior
{

    protected $code = 'cryman';
    protected $timeAgo = 30;

    public function __construct()
    {
        $this->setCooldown(new CooldownStandart5Min($this));
        parent::__construct();
    }

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $lastMessage = $this->behaviorMessages->first();

        $messageOutput = '@' . $lastMessage->telegramUser->username.', что закибербулили тебя, да? ну я не знаю, поплачь что ли';
        $chat = $lastMessage->chat_id;
        $update_id = $lastMessage->telegram_update_id;

        $telegram->sendMessage($messageOutput, $chat);
        $this->cooldown->refreshCooldown($update_id);
    }

    protected function checkLogic(): bool
    {
        $message = $this->behaviorMessages->implode('text','. ');

        if (mb_strlen($message) < 40)
            return false;

        $open_ai = new OpenAi(config('gptapi.api_key'));
        $responce = $open_ai->chat([
            'model' => 'gpt-3.5-turbo-0301',
            'messages' => [
                [
                    "role" => "system",
                    "content" => "Определи эмоцию в сообщении и дай один из ответов: 3 - расстроен, 2 - испуган, 1 - не испуган, 0 - не грустит. Дай короткий ответ:"
                ],
                [
                    "role" => "user",
                    "content" => '"'.$message.'"'
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

        Log::channel('gptanswers')->info('GPT all answer for cryman:"' . $data->choices[0]->message->content .
            '"; User "' . $this->behaviorMessages->first()->telegramUser->username .
            '"; Message: "'.$message.'"');

        if (strpos($data->choices[0]->message->content, '3') !== false || strpos($data->choices[0]->message->content, 'расстроен'))
            return $this->checkLogicStatus = true;

        return false;
    }

    protected function setBehaviorMessages()
    {
        $minAgo = time() - $this->timeAgo;
        $lastUserMessage = Message::orderBy('id', 'desc')->first();
        $this->behaviorMessages = Message::where('chat_id', '=', $lastUserMessage->chat_id)
            ->where('date', '>', $minAgo)
            ->where('telegram_user_id', '=', $lastUserMessage->telegram_user_id)
            ->orderByDesc('message_id')->take(3)->get();
    }
}
