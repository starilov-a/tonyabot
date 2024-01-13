<?php


namespace App\Services\SpaceToster\Cooldowns;


use Illuminate\Support\Facades\Log;

class CooldownStandart extends AbstractCooldown implements InterfaceCooldownStrategy
{

    public function checkCooldown($update_id = 0): bool
    {
        $rand = rand(1,100);

        if ($rand <= 30) {
            //сразу 30%
            $cooldown = 0;
        } elseif ($rand <= 60) {
            //день 30%
            $cooldown = 60*60*24;
        } elseif ($rand <= 90) {
            //3 дня 30%
            $cooldown = 60*60*24*3;
        } else {
            //7 дней 10%
            $cooldown = 60*60*24*7;
        }

        Log::channel('cooldowns')->info('Cooldown:"' . $cooldown);

        if (time() > ($this->behavior->date + $cooldown))
            return true;
        return false;
    }
}
