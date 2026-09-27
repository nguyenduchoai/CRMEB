<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\listener\user;


use app\jobs\AgentJob;
use app\services\activity\coupon\StoreCouponIssueServices;
use app\services\user\UserBillServices;
use app\services\user\UserServices;
use app\services\user\UserSpreadServices;
use crmeb\interfaces\ListenerInterface;

/**
 * Event sau khi hoàn tất đăng ký
 * Class RegisterListener
 * @package app\listener\user
 */
class RegisterListener implements ListenerInterface
{
    /**
     * Event sau khi hoàn tất đăng ký
     * @param $event
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle($event): void
    {
        [$spreadUid, $userType, $name, $uid, $isNew] = $event;

        if ($spreadUid) {
            if ($isNew) {
                //Tăng điểm kinh nghiệm khi mời người dùng mới
                /** @var UserBillServices $userBill */
                $userBill = app()->make(UserBillServices::class);
                $userBill->inviteUserIncExp((int)$spreadUid);
                //Tăng hoa hồng giới thiệu
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $userServices->addBrokeragePrice($uid, $spreadUid);

                //Giới thiệu người mới, xử lý nâng hạng cộng tác viên cho bản thân và cấp trên
                AgentJob::dispatch([$uid]);
            }
            //Ghi lại quan hệ liên kết giới thiệu
            /** @var UserSpreadServices $userSpreadServices */
            $userSpreadServices = app()->make(UserSpreadServices::class);
            $res = $userSpreadServices->setSpread($uid, $spreadUid);

            //Tin nhắn tùy chỉnh - liên kết cấp dưới thành công
            if ($res) {
                $phone = app()->make(UserServices::class)->value($spreadUid, 'phone');
                event('CustomNoticeListener', [$spreadUid, ['nickname' => $name, 'time' => date('Y-m-d H:i:s'), 'phone' => $phone], 'spread_success']);
            }
        }

        if ($isNew) {
            //Gửi phiếu giảm giá cho người mới
            /**@var StoreCouponIssueServices $storeCoupon */
            $storeCoupon = app()->make(StoreCouponIssueServices::class);
            $storeCoupon->userFirstSubGiveCoupon((int)$uid);

            //Mở quyền giới thiệu khi ai cũng có thể làm cộng tác viên
            if (sys_config('brokerage_func_status') && sys_config('store_brokerage_statu') == 2) {
                /** @var UserServices $userServices */
                $userServices = app()->make(UserServices::class);
                $userServices->update($uid, ['is_promoter' => 1]);
            }
        }
    }
}
