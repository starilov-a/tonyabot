<?php


namespace App\Services;


use Illuminate\Http\Request;

class Telegram
{
    private $webhook = 'https://api.telegram.org/bot5558278236:AAHI-LWRav9eV33TX1As37Fq34hXSq5zlRI/setWebhook?url=https://rougebot.ru/webhook';
    private $url = 'https://api.telegram.org/bot';
    private $apikey;
    private $http;
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

    public function sendMessage($message, $buttons = false)
    {
        $data = [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'html'
        ];
        if($buttons)
            $data['reply_markup'] = $buttons;

        return $this->http::post($this->url.$this->apikey.'/sendMessage', $data);
    }

    public function getUpdates($chatId)
    {
        $data = [
            'chat_id' => $chatId
        ];

        return $this->http::get($this->url.$this->apikey.'/getUpdates', $data);
    }
}
