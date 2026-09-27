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
namespace app\adminapi\controller\v1\application\wechat;

use app\services\other\QrcodeServices;
use EasyWeChat\Core\Exceptions\HttpException;
use app\adminapi\controller\AuthController;
use app\services\wechat\WechatReplyServices;
use app\services\wechat\WechatKeyServices;
use think\facade\App;

/**
 * Controller quản lý từ khóa
 * Class Reply
 * @package app\admin\controller\wechat
 */
class Reply extends AuthController
{
    /**
     * Phương thức khởi tạo
     * Menus constructor.
     * @param App $app
     * @param WechatReplyServices $services
     */
    public function __construct(App $app, WechatReplyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Trả lời khi theo dõi
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reply()
    {
        $where = $this->request->getMore([
            ['key', ''],
        ]);
        if ($where['key'] == '') return app('json')->fail('Tham số không hợp lệ');
        $info = $this->services->getDataByKey($where['key']);
        return app('json')->success(compact('info'));
    }

    /**
     * Danh sách trả lời theo từ khóa
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['key', ''],
            ['type', ''],
        ]);
        $where['key_type'] = 0;
        $list = $this->services->getKeyAll($where);
        return app('json')->success($list);
    }

    /**
     * Chi tiết từ khóa
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read($id)
    {
        $info = $this->services->getKeyInfo($id);
        return app('json')->success(compact('info'));
    }

    /**
     * Lưu từ khóa
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            'key',
            'type',
            ['status', 0],
            ['data', []],
        ]);
        try {
            if (!isset($data['key']) && empty($data['key']))
                return app('json')->fail('Vui lòng nhập từ khóa');
            if (!isset($data['type']) && empty($data['type']))
                return app('json')->fail('Vui lòng chọn loại trả lời');
            if (!in_array($data['type'], $this->services->replyType()))
                return app('json')->fail('Loại trả lời không hợp lệ');

            if (!isset($data['data']) || !is_array($data['data']))
                return app('json')->fail('Tham số tin nhắn trả lời không hợp lệ');
            $res = $this->services->redact($data['data'], $id, $data['key'], $data['type'], $data['status']);
            if (!$res)
                return app('json')->fail('Lưu thất bại');
            else
                return app('json')->success('Lưu thành công', $data);
        } catch (HttpException $e) {
            return app('json')->fail($e->getMessage());
        }
    }

    /**
     * Xóa từ khóa
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$this->services->delete($id)) {
            return app('json')->fail('Xóa thất bại');
        } else {
            /** @var WechatKeyServices $keyServices */
            $keyServices = app()->make(WechatKeyServices::class);
            $res = $keyServices->delete($id, 'reply_id');
            if (!$res) {
                return app('json')->fail('Xóa thất bại');
            }
        }
        return app('json')->success('Xóa thành công');
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) return app('json')->fail('Tham số không hợp lệ');
        $this->services->update($id, ['status' => $status], 'id');
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Tạo mã QR trả lời khi theo dõi
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function code_reply($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        /** @var QrcodeServices $qrcode */
        $qrcode = app()->make(QrcodeServices::class);
        $code = $qrcode->getForeverQrcode('reply', $id);
        if (!$code['ticket']) {
            return app('json')->fail('Tạo mã QR thất bại');
        }
        return app('json')->success($code->toArray());
    }

}
