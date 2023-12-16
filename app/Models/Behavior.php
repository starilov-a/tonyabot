<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    use HasFactory;

    protected $fillable = ['telegram_update_id', 'date', 'status', 'cooldown'];

    public function message()
    {
        return $this->hasOne(Message::class,'message_id');
    }
}
