<?php


namespace App\Services\SpaceToster\Cooldowns;


use App\Services\SpaceToster\Behaviors\Behavior;

interface InterfaceCooldownStrategy
{
    public function checkCooldown($update_id = 0): bool;
    public function refreshCooldown($update_id = 0, $status = 1): void;
}
