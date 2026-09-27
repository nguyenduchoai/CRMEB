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

namespace app\api\controller\v2\user;


use app\services\user\UserInvoiceServices;
use think\Request;

/**
 * Class UserInvoiceController
 * @package app\api\controller\v2\user
 */
class UserInvoiceController
{
    /**
     * @var UserInvoiceServices
     */
    protected $services;

    /**
     * UserInvoiceController constructor.
     * @param UserInvoiceServices $services
     */
    public function __construct(UserInvoiceServices $services)
    {
        $this->services = $services;
    }

    /**
     * Lấy thông tin một hóa đơn
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function invoice($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($this->services->getInvoice((int)$id));
    }

    /**
     * Danh sách hóa đơn
     * @param Request $request
     * @return mixed
     */
    public function invoiceList(Request $request)
    {
        $data = $request->postMore([
            ['header_type', ''],
            ['type', '']
        ]);
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getUserList($uid, $data));
    }

    /**
     * Đặt hóa đơn mặc định
     * @param Request $request
     * @return mixed
     */
    public function setDefaultInvoice(Request $request)
    {
        list($id) = $request->getMore([['id', 0]], true);
        if (!$id || !is_numeric($id)) return app('json')->fail('Tham số không hợp lệ');
        $uid = (int)$request->uid();
        $this->services->setDefaultInvoice($uid, (int)$id);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Lấy hóa đơn mặc định
     * @param Request $request
     * @return mixed
     */
    public function getDefaultInvoice(Request $request)
    {
        [$type] = $request->postMore(['type', 1], true);
        $uid = (int)$request->uid();
        $defaultInvoice = $this->services->getUserDefaultInvoice($uid, (int)$type);
        if ($defaultInvoice) {
            $defaultInvoice = $defaultInvoice->toArray();
            return app('json')->success($defaultInvoice);
        }
        return app('json')->success([]);
    }

    /**
     * Sửa - Thêm hóa đơn
     * @param Request $request
     * @return mixed
     */
    public function saveInvoice(Request $request)
    {
        $data = $request->postMore([
            [['id', 'd'], 0],
            [['header_type', 'd'], 1],
            [['type', 'd'], 1],
            ['drawer_phone', ''],
            ['email', ''],
            ['name', ''],
            ['duty_number', ''],
            ['tell', ''],
            ['address', ''],
            ['bank', ''],
            ['card_number', ''],
            ['is_default', 0]
        ]);
        if (!$data['drawer_phone']) return app('json')->fail('Vui lòng nhập số điện thoại nhận hóa đơn');
        if (!check_phone($data['drawer_phone'])) return app('json')->fail('Số điện thoại không đúng định dạng');
        if (!$data['name']) return app('json')->fail('Vui lòng nhập tiêu đề hóa đơn (tên doanh nghiệp được xuất hóa đơn)');
        if (!in_array($data['header_type'], [1, 2])) {
            $data['header_type'] = empty($data['duty_number']) ? 1 : 2;
        }
        if ($data['header_type'] == 1 && !preg_match('/^[\x80-\xff]{2,60}$/', $data['name'])) {
            return app('json')->fail('Vui lòng nhập đúng tiêu đề hóa đơn (tên doanh nghiệp được xuất hóa đơn)');
        }
        if ($data['header_type'] == 2 && !preg_match('/^[0-9a-zA-Z&\(\)\（\）\x80-\xff]{2,150}$/', $data['name'])) {
            return app('json')->fail('Vui lòng nhập đúng tiêu đề hóa đơn (tên doanh nghiệp được xuất hóa đơn)');
        }
        if ($data['header_type'] == 2 && !$data['duty_number']) {
            return app('json')->fail('Vui lòng nhập mã số thuế trên hóa đơn');
        }
        if ($data['header_type'] == 2 && !preg_match('/^[A-Z0-9]{15}$|^[A-Z0-9]{17}$|^[A-Z0-9]{18}$|^[A-Z0-9]{20}$/', $data['duty_number'])) {
            return app('json')->fail('Vui lòng nhập đúng mã số thuế trên hóa đơn');
        }
        if ($data['card_number'] && !preg_match('/^[1-9]\d{11,19}$/', $data['card_number'])) {
            return app('json')->fail('Vui lòng nhập đúng số thẻ ngân hàng');
        }
        $uid = (int)$request->uid();
        $re = $this->services->saveInvoice($uid, $data);
        if ($re) {
            if ($re['type'] == 'edit') {
                return app('json')->success('Sửa thành công');
            } else {
                return app('json')->success('Thêm thành công', $re['data']);
            }
        } else {
            return app('json')->fail('Thao tác thất bại');
        }

    }

    /**
     * Xóa hóa đơn
     * @param Request $request
     * @return mixed
     */
    public function delInvoice(Request $request)
    {
        [$id] = $request->postMore([['id', 0]], true);
        if (!$id || !is_numeric($id)) return app('json')->fail('Tham số không hợp lệ');
        $uid = (int)$request->uid();
        $re = $this->services->delInvoice($uid, (int)$id);
        if ($re)
            return app('json')->success('Xóa thành công');
        else
            return app('json')->fail('Xóa thất bại');
    }
}
