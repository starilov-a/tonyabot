<?php


namespace App\Services\SpaceToster\Behaviors;


use App\Services\SpaceToster\Cooldowns\CooldownDefault;
use App\Services\SpaceToster\Cooldowns\InterfaceCooldownStrategy;

abstract class Behavior
{
    protected $code = 'behavior';
    protected InterfaceCooldownStrategy $cooldown;
    protected $behaviorModel;
    protected $behaviorMessages;
    protected $checkLogicStatus = false;


    public function __construct()
    {
        $this->behaviorModel = \App\Models\Behavior::where('code', $this->code)->first();
        if (empty($cooldown))
            $this->setCooldown(new CooldownDefault($this));
    }

    protected function setCooldown(InterfaceCooldownStrategy $cooldown) {
        $this->cooldown = $cooldown;
    }

    public function getBehaviorModel() {
        return $this->behaviorModel;
    }

    abstract protected function checkLogic(): bool;
}
