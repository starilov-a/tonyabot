<?php


namespace App\Services;


use Illuminate\Http\Request;

class Telegram
{
    protected $url = 'https://api.telegram.org/bot';
    protected $apikey;
    protected $http;
    private $request;
    private $chatId;
    private $message;

    public function __construct($http ,$key)
    {
        $this->apikey = $key;
        $this->http = $http;
    }

    public function setRequest(Request $request)
    {
        $this->request = json_decode($request->getContent());
        $this->chatId = $this->request->message->chat->id;
        $this->message =  $this->request->message->text;
    }

    public function sendMessage($message, $chat, $replyTo = false,$buttons = false)
    {
        $data = [
            'chat_id' => $chat,
            'text' => $message,
            'parse_mode' => 'html',
        ];
        if ($replyTo)
            $data['reply_to_message_id'] = $replyTo;
        if($buttons)
            $data['reply_markup'] = $buttons;

        return $this->http::post($this->url.$this->apikey.'/sendMessage', $data);
    }
}
