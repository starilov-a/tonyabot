<?php


namespace App\Services\SpaceToster\Cooldowns;


class CooldownDefault extends AbstractCooldown implements InterfaceCooldownStrategy
{

    public function checkCooldown($update_id = 0): bool
    {
        $cooldown = 60*60*24;

        if (time() > ($this->behavior->date + $cooldown))
            return true;
        return false;
    }
}
