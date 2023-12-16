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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('message_id')->unique();
            $table->BigInteger('telegram_update_id');
            $table->BigInteger('chat_id');
            $table->BigInteger('telegram_user_id');
            $table->longText('text');
            $table->integer('date');
            $table->integer('photo_id')->nullable();
            $table->timestamps();
        });
        Schema::table('messages', function(Blueprint $table) {
            $table->foreign('chat_id')->references('id')->on('chats')->cascadeOnDelete();
            $table->foreign('telegram_update_id')->references('id')->on('telegram_updates')->cascadeOnDelete();
            $table->foreign('telegram_user_id')->references('id')->on('telegram_users')->cascadeOnDelete();
        });
    }

    public function before()
    {
        $this->before = 'create_telegram_users_table';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
