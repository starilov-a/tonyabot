<?php


namespace App\Services;


use App\Models\Chat;
use App\Models\Message;
use App\Models\TelegramUpdate;
use App\Models\TelegramUser;

class TelegramUpdates extends Telegram
{
    private $updatedRequest;
    private $updateWebhook;
    public $telegramUpdates;

    public function __construct($http, $key)
    {
        parent::__construct($http, $key);
        $this->updateWebhook = $this->url.$key.'/getUpdates';
    }

    public function getUpdates()
    {
        $lastUpdate = TelegramUpdate::orderBy('id', 'desc')->first();
        $offset = !empty($lastUpdate) ? $lastUpdate->id+1 : 1;
        $this->telegramUpdates = json_decode($this->http::get($this->updateWebhook.'?offset='.$offset));
        $this->saveUpdatesData();
    }

    protected function saveUpdatesData() {
        if (!isset($this->telegramUpdates->result))
            return;

        foreach ($this->telegramUpdates->result as $update) {
            if (!isset($update->message)){
                continue;
            }
            TelegramUpdate::create([
                'id' => $update->update_id
            ]);
            if (isset($update->message->text) || isset($update->message->photo)) {
                $chat = Chat::firstOrCreate(['id' => $update->message->chat->id],
                    [
                        'id' => $update->message->chat->id,
                        'title' => $update->message->chat->title,
                        'type' => $update->message->chat->type
                    ]
                );
                $telegramUser = TelegramUser::firstOrCreate(['id' => $update->message->from->id],
                    [
                        'id' => $update->message->from->id,
                        'first_name' => $update->message->from->first_name,
                        'username' => $update->message->from->username ?? "unknown",
                        'language_code' => $update->message->from->language_code ?? null,
                        'is_bot' => $update->message->from->is_bot
                    ]
                );
                
                if (empty($chat->telegramUsers()->find($telegramUser->id)))
                    $chat->telegramUsers()->attach($telegramUser->id);

                Message::firstOrCreate(['id' => $update->message->message_id],
                    [
                        'id' => $update->message->message_id,
                        'telegram_update_id' => $update->update_id,
                        'chat_id' => $update->message->chat->id,
                        'telegram_user_id' => $update->message->from->id,
                        'text' => isset($update->message->photo) ? ($update->message->caption ?? '') : $update->message->text,
                        'date' => $update->message->date,
                    ]
                );
            }
        }
    }
}
