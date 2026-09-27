<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1\user;

use app\Request;
use app\services\activity\coupon\StoreCouponUserServices;
use app\services\order\OtherOrderServices;
use app\services\other\AgreementServices;
use app\services\user\member\MemberCardServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/** Thẻ thành viên
 * Class MemberCardController
 * @package app\api\controller\v1\user
 */
class MemberCardController
{
    protected $services = NUll;

    protected $channelType = ['weixin' => 'wechat', 'weixinh5' => 'weixinh5', 'routine' => 'routine', 'h5' => 'h5'];

    /** Khởi tạo handle tầng service
     * MemberCardController constructor.
     * @param MemberCardServices $memberCardServices
     */
    public function __construct(MemberCardServices $memberCardServices)
    {
        $this->services = $memberCardServices;
    }

    /**
     * API dữ liệu trang chủ thẻ thành viên
     * @param Request $request
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index(Request $request)
    {
        $member_rights = $this->services->getMemberRightsInfo();
        /** @var AgreementServices $agreementService */
        $agreementService = app()->make(AgreementServices::class);
        $member_explain = $agreementService->getAgreementBytype(1);
        $member_explain = (isset($member_explain['status']) && $member_explain['status'] == 1) ? $member_explain : "";
        $member_type = $this->services->DoMemberType();
        /** @var OtherOrderServices $OtherOrderServices */
        $OtherOrderServices = app()->make(OtherOrderServices::class);
        $is_get_free = $OtherOrderServices->isCanGetFree((int)$request->uid());
        /** @var StoreCouponUserServices $couponUserService */
        $couponUserService = app()->make(StoreCouponUserServices::class);
        $coupons = $couponUserService->getMemberCoupon($request->uid());
        return app('json')->success([
            'member_rights' => $member_rights['member_right'],
            'is_get_free' => $is_get_free,
            'member_explain' => $member_explain,
            'member_type' => $member_type,
            'member_coupons' => $coupons
        ]);
    }

    /**
     * Nhận thẻ thành viên bằng mã thẻ
     * @param Request $request
     * @return mixed
     */
    public function draw_member_card(Request $request)
    {
        $data = $request->postMore([
            ['member_card_code', ''],
            ['member_card_pwd', ''],
            ['from', 'weixin'],
        ]);
        $data['from'] = strtolower(trim($data['from']));
        if (!array_key_exists($data['from'], $this->channelType)) return app('json')->fail(100101);
        $data['from'] = $this->channelType[$data['from']];
        $uid = (int)$request->uid();
        $this->services->drawMemberCard($data, $uid);
        return app('json')->success(410165);
    }

    /**
     * API phiếu giảm giá thành viên
     * @param Request $request
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function memberCouponList(Request $request)
    {
        /** @var StoreCouponUserServices $couponUserService */
        $couponUserService = app()->make(StoreCouponUserServices::class);
        $coupons = $couponUserService->getMemberCoupon($request->uid());
        return app('json')->success($coupons);
    }

    /**
     * Tính số ngày thành viên
     * @param Request $request
     * @return mixed
     */
    public function getOverdueTime(Request $request)
    {
        $params = $request->getMore([
            ['member_type', ''],
            ['vip_day', ''],
            ['mc_id', 0]
        ]);
        $member_type = $params['member_type'];
        $vip_day = $params['vip_day'];
        /** @var \app\services\user\UserServices $userServices */
        $userServices = app()->make(\app\services\user\UserServices::class);
        $user_info = $userServices->getUserInfo($request->uid());
        if ($member_type == 'ever') {
            $overdue_time = 0;
            $is_ever_level = 1;
        } else {
            if ($user_info['is_money_level'] == 0) {
                $overdue_time = bcadd(bcmul($vip_day, 86400, 0), time(), 0);
            } else {
                $overdue_time = bcadd(bcmul($vip_day, 86400, 0), $user_info['overdue_time'], 0);
            }
            $is_ever_level = 0;
        }
        if ($is_ever_level == 1 || $user_info['is_ever_level']) {
            $res = "Thành viên vĩnh viễn";
        } else {
            $res = date('Y-m-d', $overdue_time);
        }

        return app('json')->success(['data' => $res]);
    }


}
