<?php


namespace App\Services\SpaceToster\Behaviors\Passive;


use App\Services\SpaceToster\Behaviors\Behavior;

abstract class AbstractPassiveBehavior extends Behavior
{
    public function reasonToMessage(): bool
    {
        if (!$this->cooldown->checkCooldown())
            return false;
        if (!$this->checkLogic())
            return false;

        return true;
    }
}
