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
use app\services\message\MessageSystemServices;


/**
 * Lớp địa chỉ người dùng
 * Class UserController
 * @package app\api\controller\store
 */
class MessageSystemController
{
    protected $services = NUll;

    /**
     * MessageSystemController constructor.
     * @param MessageSystemServices $services
     */
    public function __construct(MessageSystemServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách thông báo nội bộ
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function message_list(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getMessageSystemList($uid));
    }

    /**
     * Chi tiết thông báo nội bộ
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(Request $request, $id)
    {
        if (!$id) {
            app('json')->fail('Tham số không hợp lệ');
        }
        $uid = (int)$request->uid();
        $where['uid'] = $uid;
        $where['id'] = $id;
        return app('json')->success($this->services->getInfo($where));
    }

    /**
     * Sửa trường trong danh sách thông báo
     * @param Request $request
     * @return mixed
     */
    public function edit_message(Request $request)
    {
        $data = $request->getMore([
            ['id', 0],
            ['key', ''],
            ['value', ''],
            ['all', 0]
        ]);
        $all = (int)$data['all'];
        if ($all === 1) {
            $this->services->update(['uid' => $request->uid()], [$data['key'] => $data['value']]);
        } else {
            $this->services->update($data['id'], [$data['key'] => $data['value']]);
        }
        return app('json')->success('Cài đặt thành công');
    }
}
