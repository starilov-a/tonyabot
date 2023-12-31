<?php


namespace App\Services\SpaceToster\Behaviors\Passive;


use App\Models\Chat;
use App\Models\Message;
use App\Models\TelegramUser;
use App\Services\SpaceToster\Behaviors\MessageBehavior;
use App\Services\SpaceToster\Cooldowns\CooldownDailyStatus;
use App\Services\Telegram;
use Carbon\Carbon;

class DailyStatisticBehavior extends AbstractPassiveBehavior implements MessageBehavior
{
    protected $code = 'dailystat';
    protected $workHour = 19;
    protected $refreshHour = 4;
    private $dictionary = [
        //Предлоги
        "в", "на", "под", "над", "за", "перед", "после", "между", "из",
        "при", "о", "об", "обо", "к", "ко", "с", "со", "без", "для", "через", "вокруг",
        "около", "вдоль", "возле", "внутри", "среди", "у", "передо", "впереди", "вглубь",
        "вперёд", "вперёди", "вдоль", "вслед", "взамен", "взамен", "ввиду", "вследствие",
        "вместо", "вне", "внутри", "впереди", "вразрез", "вслед", "вместо", "внизу", "внутри",
        "впереди", "вразрез", "вроде", "от",
        //Союзы
        "и", "да", "или", "но", "а", "как", "чтобы", "что", "если", "когда", "пока", "так как",
        "потому что", "либо", "ежели", "иначе как", "то есть", "однако", "зато", "тоже", "также",
        "впрочем", "вполне", "будто", "точно", "словно", "как будто", "будто бы", "если б", "едва ли",
        "раз", "как", "поскольку", "тогда как", "кабы", "ибо", "лишь только", "да и", "нежели",
        "из-за того что",
        //Союзы
        "бы", "же", "ли", "не", "так", "тоже", "вот", "лишь", "да", "всего лишь", "всё же",
        "разве", "то", "вон", "ведь", "вроде", "как бы", "как", "еще", "уж", "почти", "как бы то ни было",
        "что угодно", "хоть", "чуть", "лишь бы", "коль", "однако", "однажды", "когда-то",
        "наконец", "как ни странно", "например", "пожалуй", "пожалуйста", "кстати", "впрочем",
        "на самом деле", "даже", "уже", "ещё бы", "конечно", "пусть", "давай", "давайте", "что ж",
        "ну", "так вот", "как видите", "напротив", "вперед", "доколе", "затем", "поэтому",
        "таким образом", "вот так",
        //Междометия
        "о", "а", "эх", "увы", "ой", "ой-ой", "ах", "ох", "о-го", "ого-го", "ух", "ух ты",
        "ура", "фу", "пф", "ага", "та-да", "тсс", "эй", "ну", "ба", "псс", "гм", "гм-м", "тихо",
        "громко", "погоди", "ой-ой-ой", "господи", "исус", "так точно", "ой-ей", "ой-ей-ей",
        "сорри", "прости", "слушай", "ну и ну", "ну-ка", "ну-ну", "ну и ладно", "че", "как же так",
        "как так", "ничего себе", "фигасе", "вау", "э-ге-ге", "честно", "правда", "действительно",
        "как так", "так точно", "например", "как бы", "как тебе такое", "браво", "брависсимо", "фи",
        "та-та", "та-да", "угощай", "на здоровье", "здорово", "замечательно", "вот так", "так себе",
        "что делать", "неужели", "невероятно", "интересно", "удивительно", "вот это да", "вот те на",
        "поздравляю", "успехов", "полегче", "счастья", "удачи", "какие новости",
        "тебе",
        //Местоимения
        "я", "ты", "он", "она", "оно", "мы", "вы", "они", "меня", "тебя", "его", "её", "его",
        "нас", "вас", "их", "себя", "свой", "мой", "твой", "наш", "ваш", "их", "кто", "что", "кого",
        "чего", "кому", "чему", "кем", "чем", "где", "куда", "откуда", "в который", "в какой",
        "в каком", "в какой", "каков", "какова", "каково", "сколько", "чей", "чья", "чьё", "чьи",
        "какой", "какая", "какое", "какие", "такой", "такая", "такое", "такие", "столько", "столько",
        "столько", "некоторый", "некоторая", "некоторое", "некоторые", "весь", "вся", "всё", "все",
        "каждый", "каждая", "каждое", "каждые", "всякий", "всякая", "всякое", "всякие", "любой", "любая",
        "любое", "любые", "сам", "сама", "само", "сами", "самый", "самая", "самое", "самые", "другой",
        "другая", "другое", "другие", "тот", "та", "то", "те", "этот", "эта", "это", "эти", "такой",
        "такая", "такое", "такие", "таков", "такова", "таково", "таковы", "столький", "столькая",
        "столькое", "столькие", "мне",
        //другое
    ];

    public function __construct()
    {
        $this->setCooldown(new CooldownDailyStatus($this, 19));
        parent::__construct();
    }

    public function message(Telegram $telegram): void
    {
        if (!$this->checkLogicStatus)
            return;

        $chats = Chat::all();
        foreach ($chats as $chat) {
//            $messageOutput = 'Я тут покумекала и вот что выяснила:'."\r\n";

            //подсчет кол-во сообщений
            $messages = Message::whereDate('created_at', Carbon::today())->where('chat_id', $chat->id)->get();
//            $messageOutput .= 'Сообщений за сегодня: '. $messages->count()."\r\n";

            //топ слов
//            $messageOutput .= 'Топ слов: '."\r\n";
//            $wordStats = [];
//            foreach ($messages as $message) {
//
//                //удаление знаков припинания
//                $message->text = str_replace($message->text, '',['.', '!', '?', ',', ';', ':', '(', ')', '{', '}', '[', ']', '/', '\'', '|', '%']);
//
//                $words = explode(' ', $message->text);
//
//                foreach ($words as $word){
//                    //длина слова больше 1 символа
//                    if (!(strlen($word) > 2))
//                        continue;
//
//                    //Не входит в словарь
//                    if (in_array($word,$this->dictionary))
//                        continue;
//
//                    if(!isset($wordStats[$word]))
//                        $wordStats[$word] = 0;
//                    $wordStats[$word]++;
//                }
//            }
//            if (!empty($wordStats)) {
//                arsort($wordStats);
//                $i = 0;
//                foreach ($wordStats as $word => $count) {
//                    $messageOutput .= $i+1 . '. ' . $word . ' - ' . $count ."\r\n";
//                    $i++;
//                    if ($i == 5)
//                        break;
//                }
//            } else {
//                $messageOutput .= '«А чо так тихо то?...»'."\r\n\r\n";
//            }


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

                $login = '@'.$user->username;
                if ($login == '@unknown')
                    $login = $user->first_name;

                $messageOutput .= 'Сегодня моё прощение заслужил '.$login."\r\n";
            }

            $telegram->sendMessage($messageOutput, $chat->id);
            $messageOutput = '';
        }

        $this->cooldown->refreshCooldown($messages->last()->telegram_update_id, 0);
    }

    protected function checkLogic(): bool
    {
        $dt = Carbon::now();
        if ($this->behaviorModel->status && $dt->hour == $this->workHour)
            return $this->checkLogicStatus = true;
        return false;
    }
}
