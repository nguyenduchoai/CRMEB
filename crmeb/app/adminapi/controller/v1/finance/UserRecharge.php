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
namespace app\adminapi\controller\v1\finance;

use app\adminapi\controller\AuthController;
use app\services\user\UserRechargeServices;
use think\facade\App;

/**
 * Class UserRecharge
 * @package app\adminapi\controller\v1\finance
 */
class UserRecharge extends AuthController
{
    /**
     * UserRecharge constructor.
     * @param App $app
     * @param UserRechargeServices $services
     */
    public function __construct(App $app, UserRechargeServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     * @return \think\Response
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['data', ''],
            ['paid', ''],
            ['nickname', ''],
        ]);
        return app('json')->success($this->services->getRechargeList($where));
    }

    /**
     * Xóa resource được chỉ định
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        return app('json')->success($this->services->delRecharge((int)$id) ? 'Xóa thành công' : 'Xóa thất bại');
    }

    /**
     * Lấy dữ liệu nạp tiền của người dùng
     * @return array
     */
    public function user_recharge()
    {
        $where = $this->request->getMore([
            ['data', ''],
            ['paid', ''],
            ['nickname', ''],
        ]);
        return app('json')->success($this->services->user_recharge($where));
    }

    /**
     * Form hoàn tiền
     * @param $id
     * @return mixed|void
     */
    public function refund_edit($id)
    {
        if (!$id) return app('json')->fail('Dữ liệu không tồn tại');
        return app('json')->success($this->services->refund_edit((int)$id));
    }

    /**
     * Thao tác hoàn tiền
     * @param $id
     * @return mixed
     */
    public function refund_update($id)
    {
        $data = $this->request->postMore([
            'refund_price',
        ]);
        if (!$id) return app('json')->fail('Dữ liệu không tồn tại');
        return app('json')->success($this->services->refund_update((int)$id, $data['refund_price']) ? 'Hoàn tiền thành công' : 'Hoàn tiền thất bại');
    }
}
