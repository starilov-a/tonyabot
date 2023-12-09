<?php


namespace App\Services\SpaceToster\Behaviors\Passive;


use App\Services\SpaceToster\Behaviors\Behavior;

abstract class AbstractPassiveBehavior extends Behavior
{
    protected function refreshCooldown($status): void
    {
        $this->behaviorModel->update(['date' => time(), 'status' => $status]);
    }
}
