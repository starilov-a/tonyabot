<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUpdate extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'telegram_updates';
    protected $fillable = ['id'];

    public function message()
    {
        return $this->hasOne(Message::class,'message_id');
    }
}
