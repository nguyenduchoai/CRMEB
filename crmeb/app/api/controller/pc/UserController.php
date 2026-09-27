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

namespace app\api\controller\pc;


use app\Request;
use app\services\pc\UserServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserMoneyServices;

class UserController
{
    protected $services;

    public function __construct(UserServices $services)
    {
        $this->services = $services;
    }

    /**
     * Chi tiết lịch sử tài chính người dùng
     * @param Request $request
     * @param $type 0 Tất cả  1 chi tiêu  2 nạp tiền  3 trả hoa hồng  4 rút tiền
     * @return mixed
     */
    public function getBalanceRecord(Request $request, $type)
    {
        $uid = (int)$request->uid();
        $data = [];
        switch ($type) {
            case 0:
            case 1:
            case 2:
                /** @var UserMoneyServices $moneyService */
                $moneyService = app()->make(UserMoneyServices::class);
                $data = $moneyService->getMoneyList($uid, $type);
                break;
            case 3:
            case 4:
                /** @var UserBrokerageServices $brokerageService */
                $brokerageService = app()->make(UserBrokerageServices::class);
                $data = $brokerageService->getBrokerageList($uid, $type);
                break;
        }
        return app('json')->success($data);
    }

    /**
     * Lấy danh sách yêu thích
     * @param Request $request
     * @return mixed
     */
    public function getCollectList(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getCollectList($uid));
    }
}
