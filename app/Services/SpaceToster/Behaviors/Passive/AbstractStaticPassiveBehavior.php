<?php


namespace App\Services\SpaceToster\Behaviors\Passive;


abstract class AbstractStaticPassiveBehavior extends \App\Services\SpaceToster\Behaviors\Behavior
{
    protected function refreshCooldown($status): void
    {
        $this->behaviorModel->update(['date' => time(), 'status' => $status]);
    }
}
