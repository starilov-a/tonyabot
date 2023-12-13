<?php


namespace App\Services\SpaceToster;


use App\Services\SpaceToster\Behaviors\Passive\DailyStatisticBehavior;
use App\Services\SpaceToster\Behaviors\Reactive\DaBehavior;
use App\Services\SpaceToster\Behaviors\Reactive\HorrorStatusBehavior;
use App\Services\Telegram;

class SpaceToster
{
    private $behaviorMessage;
    public $behaviors;

    public function __construct()
    {
        //init behaviorsData;
        $this->behaviors['reactive'][] = new DaBehavior();
//        $this->behaviors['reactive'][] = new HorrorStatusBehavior();
        $this->behaviors['passive'][] = new DailyStatisticBehavior();
    }

    public function message(Telegram $telegram): void {
        $this->behaviorMessage->message($telegram);
    }

    public function setMessageBehavior(\App\Services\SpaceToster\Behaviors\MessageBehavior $sb) {
        $this->behaviorMessage = $sb;
    }

    public function getActiveBehavior()
    {
        $behaviors['reactive'] = [];
        $behaviors['passive'] = [];

        foreach ($this->behaviors as $type => $behaviorsOfType) {
            foreach ($behaviorsOfType as $behavior){
                if ($behavior->reasonToMessage())
                    $behaviors[$type][] = $behavior;
            }
        }

        $activeBehavior = false;

        if (!empty($behaviors['reactive']))
            $activeBehavior = $behaviors['reactive'][rand(0,count($behaviors['reactive'])-1)];
        elseif (!empty($behaviors['passive']))
            $activeBehavior = $behaviors['passive'][rand(0,count($behaviors['passive'])-1)];

        return $activeBehavior;
    }
}
