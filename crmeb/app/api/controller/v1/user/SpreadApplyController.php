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
namespace app\api\controller\v1\user;

use app\Request;
use app\services\agent\SpreadApplyServices;
use crmeb\services\CacheService;

class SpreadApplyController
{
    public function __construct(SpreadApplyServices $services)
    {
        $this->services = $services;
    }

    public function applyInfo(Request $request)
    {
        $uid = $request->uid();
        $data = $this->services->applyInfo($uid);
        return app('json')->success($data);
    }

    public function applyPromoter(Request $request, $id)
    {
        $data = $request->postMore([
            ['uid', 0],
            ['nickname', ''],
            ['real_name', ''],
            ['phone', ''],
            ['content', ''],
            ['code', 0]
        ]);
        $data['uid'] = $request->uid();
        $userInfo = $request->user();
        $verifyCode = CacheService::get('code_' . $data['phone']);
        if (!$verifyCode) return app('json')->fail('Vui lòng lấy mã xác thực trước');
        if ($verifyCode != $data['code']) return app('json')->fail('Mã xác thực không đúng');
        unset($data['code']);
        $id = $this->services->applyPromoter($data, $id, $userInfo);
        return app('json')->success('Gửi yêu cầu thành công', ['id' => $id]);
    }
}
