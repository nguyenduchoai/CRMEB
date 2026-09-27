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

namespace app\adminapi\controller\v1\finance;

use app\adminapi\controller\AuthController;
use app\services\user\UserExtractServices;
use think\facade\App;
use think\Request;

/**
 * Class UserExtract
 * @package app\adminapi\controller\v1\finance
 */
class UserExtract extends AuthController
{
    /**
     * UserExtract constructor.
     * @param App $app
     * @param UserExtractServices $services
     */
    public function __construct(App $app, UserExtractServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['extract_type', ''],
            ['nireid', '', '', 'like'],
            ['data', '', '', 'time'],
        ]);
        if (isset($where['extract_type']) && $where['extract_type'] == 'wx') {
            $where['extract_type'] = 'weixin';
        }
        return app('json')->success($this->services->index($where));
    }

    /**
     * Hiển thị trang form sửa resource
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        if (!$id) return app('json')->fail(100026);
        return app('json')->success($this->services->edit((int)$id));
    }

    /**
     * Lưu resource đã cập nhật
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        if (!$id) return app('json')->fail(100100);
        $id = (int)$id;
        $UserExtract = $this->services->getExtract($id);
        if (!$UserExtract) app('json')->fail(100026);
        if ($UserExtract['extract_type'] == 'alipay') {
            $data = $this->request->postMore([
                'real_name',
                'mark',
                'extract_price',
                'alipay_code',
            ]);
            if (!$data['real_name']) return app('json')->fail(400107);
            if ($data['extract_price'] <= -1) return app('json')->fail(400108);
            if (!$data['alipay_code']) return app('json')->fail(400109);
        } else if ($UserExtract['extract_type'] == 'weixin') {
            $data = $this->request->postMore([
                'real_name',
                'mark',
                'extract_price',
                'wechat',
            ]);
            if ($data['extract_price'] <= -1) return app('json')->fail(400108);
            if (!$data['wechat']) return app('json')->fail(400110);
        } else {
            $data = $this->request->postMore([
                'real_name',
                'extract_price',
                'mark',
                'bank_code',
                'bank_address',
            ]);
            if (!$data['real_name']) return app('json')->fail(400107);
            if ($data['extract_price'] <= -1) return app('json')->fail(400108);
            if (!$data['bank_code']) return app('json')->fail(400111);
            if (!$data['bank_address']) return app('json')->fail(400112);
        }
        return app('json')->success($this->services->update($id, $data) ? 100001 : 100007);
    }

    /**
     * Từ chối
     * @param $id
     * @return mixed
     */
    public function refuse($id)
    {
        if (!$id) app('json')->fail(100100);
        $data = $this->request->postMore([
            ['message', '']
        ]);
        if ($data['message'] == '') return app('json')->fail(400113);
        return app('json')->success($this->services->refuse((int)$id, $data['message']) ? 100014 : 100015);
    }

    /**
     * Duyệt
     * @param $id
     * @return mixed
     */
    public function adopt($id)
    {
        if (!$id) app('json')->fail(100100);
        $res = $this->services->adopt((int)$id);
        if ($res) {
            if ($res === 'v3_extract') {
                return app('json')->success('Rút tiền thành công, đang chờ người dùng xác nhận nhận tiền');
            } else {
                return app('json')->success('Rút tiền thành công');
            }
        } else {
            return app('json')->success('Thao tác thất bại');
        }
    }
}
