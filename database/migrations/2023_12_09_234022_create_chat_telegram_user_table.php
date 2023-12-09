<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_telegram_user', function (Blueprint $table) {
            $table->BigInteger('chat_id');
            $table->BigInteger('telegram_user_id');
            $table->foreign('chat_id')->references('id')->on('chats');
            $table->foreign('telegram_user_id')->references('id')->on('telegram_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_telegram_user');
    }
};
