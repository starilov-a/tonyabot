<?php


namespace App\Services\SpaceToster\Cooldowns;


class CooldownStandart5Min extends AbstractCooldown implements InterfaceCooldownStrategy
{

    public function checkCooldown($update_id = 0): bool
    {
        $rand = rand(1,100);

        if ($rand <= 30) {
            //через 5 минут 30%
            $cooldown = 60*5;
        } elseif ($rand <= 30) {
            //день 30%
            $cooldown = 60*60*24;
        } elseif ($rand <= 90) {
            //3 дня 30%
            $cooldown = 60*60*24*3;
        } else {
            //7 дней 10%
            $cooldown = 60*60*24*7;
        }

        if (time() > ($this->behavior->date + $cooldown))
            return true;
        return false;
    }
}
