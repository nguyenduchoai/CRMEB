<?php


namespace app\listener\user;


use app\services\product\product\StoreVisitServices;
use crmeb\interfaces\ListenerInterface;

/**
 * Ghi lượt truy cập người dùng
 * Class UserVisitListener
 * @package app\listener\user
 */
class UserVisitListener implements ListenerInterface
{
    public function handle($event): void
    {
        [$uid, $product_id, $product_type, $cate, $type] = $event;

        //Ghi lịch sử truy cập người dùng
        /** @var StoreVisitServices $storeVisit */
        $storeVisit = app()->make(StoreVisitServices::class);
        $storeVisit->setView($uid, $product_id, $product_type, $cate, $type);
    }
}