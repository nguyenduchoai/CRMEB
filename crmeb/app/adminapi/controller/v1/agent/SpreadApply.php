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
namespace app\adminapi\controller\v1\agent;

use app\adminapi\controller\AuthController;
use app\services\agent\SpreadApplyServices;
use think\facade\App;

class SpreadApply extends AuthController
{
    /**
     * @var SpreadApplyServices
     */
    protected $services;

    /**
     * SpreadApply constructor.
     * @param App $app
     * @param SpreadApplyServices $services
     */
    public function __construct(App $app, SpreadApplyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách yêu cầu
     * @return mixed
     */
    public function applyList()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['keyword', ''],
        ]);
        return app('json')->success($this->services->applyList($where));
    }

    /**
     * Duyệt đơn đăng ký
     * @param $id
     * @param $uid
     * @param $status
     * @return mixed
     */
    public function applyExamine($id, $uid, $status)
    {
        [$refusal_reason] = $this->request->postMore([
            ['refusal_reason', ''],
        ], true);
        $this->services->applyExamine($id, $uid, $status, $refusal_reason);
        return app('json')->success($status == 1 ? 'Đã duyệt' : 'Từ chối thành công');
    }

    /**
     * xóa đơn đăng ký
     * @param $id
     * @return mixed
     */
    public function applyDelete($id)
    {
        $this->services->applyDelete($id);
        return app('json')->success('Xóa thành công');
    }
}
