<?php


namespace App\Services\SpaceToster\Cooldowns;


use App\Models\Behavior;

abstract class AbstractCooldown
{
    protected $behavior;

    public function __construct(Behavior $behavior)
    {
        $this->behavior = $behavior;
    }

    public function refreshCooldown($update_id = 0, $status = 1): void
    {
        $this->behavior->update(['date' => time(), 'telegram_update_id' => $update_id, 'status' => $status]);
    }
}
