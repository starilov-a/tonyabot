<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'telegram_users';
    protected $fillable = ['id', 'first_name', 'username', 'language_code', 'is_bot'];

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
