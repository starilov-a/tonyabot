<?php


namespace App\Services\SpaceToster\Behaviors\Passive;


abstract class AbstractStaticPassiveBehavior extends AbstractPassiveBehavior
{
    public function reasonToMessage(): bool
    {
        if (!$this->checkCooldown())
            return false;
        if (!$this->checkLogic())
            return false;

        return true;
    }
}
