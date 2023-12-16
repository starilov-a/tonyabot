<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $fillable = ['message_id', 'telegram_update_id', 'chat_id', 'telegram_user_id', 'text', 'date', 'photo_id'];

    protected $countMesseages = '10';

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function telegramUpdate()
    {
        return $this->hasOne(TelegramUpdate::class);
    }

    public function getLastMessages()
    {

    }
}
