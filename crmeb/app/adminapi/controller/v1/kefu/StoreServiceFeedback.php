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

namespace app\adminapi\controller\v1\kefu;


use app\adminapi\controller\AuthController;
use app\services\kefu\service\StoreServiceFeedbackServices;
use think\facade\App;

/**
 * Lời nhắn phản hồi của người dùng gửi CSKH
 * Class StoreServiceFeedback
 * @package app\adminapi\controller\v1\application\wechat
 */
class StoreServiceFeedback extends AuthController
{

    /**
     * StoreServiceFeedback constructor.
     * @param App $app
     * @param StoreServiceFeedbackServices $services
     */
    public function __construct(App $app, StoreServiceFeedbackServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách lời nhắn
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', ''],
            ['time', '']
        ]);

        return app('json')->success($this->services->getFeedbackList($where));
    }

    /**
     * Lấy form sửa
     * @param $id
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id)
    {
        if (!$id) {
            return app('json')->fail(100100);
        }
        return app('json')->success($this->services->editForm((int)$id));
    }

    /**
     * Sửa
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $data = $this->request->postMore([
            ['make', ''],
            ['status', 0],
        ]);
        if (!$id || !($feedInfo = $this->services->get($id))) {
            return app('json')->fail(400268);
        }
        $feedInfo->make = $data['make'];
        if ($data['status']) {
            $feedInfo->status = $data['status'];
        }
        $feedInfo->save();
        return app('json')->success(100001);
    }

    /**
     * Xóa phản hồi
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        if (!$id) {
            return app('json')->fail(100100);
        }
        if ($this->services->delete($id)) {
            return app('json')->success(100002);
        } else {
            return app('json')->fail(100008);
        }
    }
}
