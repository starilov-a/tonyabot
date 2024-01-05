<?php


namespace App\Services\SpaceToster\Cooldowns;


use App\Services\SpaceToster\Behaviors\Behavior;
use Carbon\Carbon;

class CooldownDailyStatus extends AbstractCooldown implements InterfaceCooldownStrategy
{
    protected $workHour = 19;

    public function __construct(Behavior $behavior, $workHour)
    {
        parent::__construct($behavior);
        $this->workHour = $workHour;
    }

    public function checkCooldown($update_id = 0): bool
    {
        $dt = Carbon::now();
        //сброс кулдауна через 22 часа после отработки
        if ($dt->hour == $this->workHour + 22 && !$this->behavior->status)
            $this->refreshCooldown($update_id, 1);
        return true;
    }
}
