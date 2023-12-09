<?php


namespace App\Services\SpaceToster\Behaviors\Passive;


use App\Models\Chat;
use App\Models\Message;
use App\Models\TelegramUser;
use App\Services\SpaceToster\Behaviors\Behavior;
use App\Services\SpaceToster\Behaviors\MessageBehavior;
use App\Services\Telegram;
use Carbon\Carbon;

class DailyStatisticBehavior extends AbstractStaticPassiveBehavior implements MessageBehavior
{
    protected $code = 'dailystat';
    protected $workHour = 0;
    protected $refreshHour = 0;

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $chats = Chat::all();
        foreach ($chats as $chat) {
            $messageOutput = 'Я умен, так что прикинув кибер мозгами, выяснилось вот что:'."\r\n\r\n";

            //подсчет кол-во сообщений
            $messages = Message::whereDate('created_at', Carbon::today())->where('chat_id', $chat->id)->get();
            $messageOutput .= 'Сообщений за сегодня: '. $messages->count()."\r\n";

            //топ слов
            $messageOutput .= 'Топ слов: '."\r\n";
            $wordStats = [];
            foreach ($messages as $message) {
                $words = explode(' ', $message->text);
                foreach ($words as $word){
                    if(!isset($wordStats[$word]))
                        $wordStats[$word] = 0;
                    $wordStats[$word]++;
                }
            }
            if (!empty($wordStats)) {
                arsort($wordStats);
                $i = 0;
                foreach ($wordStats as $word => $count) {
                    $messageOutput .= $i+1 . '. ' . $word . ' - ' . $count ."\r\n";
                    $i++;
                    if ($i == 5)
                        break;
                }
            } else {
                $messageOutput .= '«Среди черных дыр и квазаров распространяется звук тишины...»'."\r\n\r\n";
            }


            //самый активный участник беседы
            $userTopMessage = [];
            foreach ($messages as $message) {
                if(!isset($userTopMessage[$message->telegram_user_id]))
                    $userTopMessage[$message->telegram_user_id] = 1;
                $userTopMessage[$message->telegram_user_id]++;
            }
            if (!empty($userTopMessage)) {
                arsort($userTopMessage);
                $userId = array_key_first($userTopMessage);
                $user = TelegramUser::find($userId);
                $messageOutput .= 'Самый активный: @'.$user->username."\r\n\r\n";
            }

            //Клоун беседы

            $users = $chat->telegramUsers()->get();

            if ($users->isNotEmpty()) {
                $userRand = $users->random();
                $messageOutput .= 'Ипостер сегодня: @'.$userRand->username."\r\n";
            }

            $telegram->sendMessage($messageOutput, $chat->id);
            $messageOutput = '';
        }

        $this->refreshCooldown(0);
    }

    public function reasonToMessage(): bool
    {
        if (!$this->checkCooldown())
            return false;
        if (!$this->checkLogic())
            return false;

        return true;
    }

    protected function checkLogic(): bool
    {
        $dt = Carbon::now();
        if ($this->behaviorModel->status && $dt->hour == $this->workHour)
            return $this->checkLogicStatus = true;
        //TODO перенести сброс кулдауна
        if ($dt->hour == $this->refreshHour && !$this->behaviorModel->status)
            $this->refreshCooldown(1);

        return false;
    }
}
