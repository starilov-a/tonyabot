<?php

namespace App\Providers;

use App\Services\SpaceToster\SpaceToster;
use App\Services\Telegram;
use App\Services\TelegramUpdates;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('App\Services\Telegram', function($app){
            return new Telegram(new Http(), config('spacetosterbot.apikey'));
        });
        $this->app->bind('App\Services\TelegramUpdates', function($app){
            return new TelegramUpdates(new Http(), config('spacetosterbot.apikey'));
        });
        $this->app->bind('App\Services\SpaceToster\SpaceToster', function($app){
            return new SpaceToster();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
