<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'chats';
    protected $fillable = ['id', 'title', 'type'];

    public function telegramUsers()
    {
        return $this->belongsToMany(TelegramUser::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }


}
