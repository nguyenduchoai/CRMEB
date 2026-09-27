<?php


namespace app\listener\user;


use app\services\user\UserLevelServices;
use crmeb\interfaces\ListenerInterface;

/**
 * Event nâng cấp người dùng
 * Class UserLevelListener
 * @package app\listener\user
 */
class UserLevelListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$uid] = $event;

        //Nâng hạng người dùng
        /** @var UserLevelServices $levelServices */
        $levelServices = app()->make(UserLevelServices::class);
        $levelServices->detection((int)$uid);
    }
}