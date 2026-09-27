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
declare (strict_types=1);

namespace app\services\user;


use app\dao\user\UserInvoiceDao;
use app\services\BaseServices;
use crmeb\exceptions\ApiException;


/**
 * Class UserInvoiceServices
 * @package app\services\user
 */
class UserInvoiceServices extends BaseServices
{
    /**
     * LiveAnchorServices constructor.
     * @param UserInvoiceDao $dao
     */
    public function __construct(UserInvoiceDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Kiểm tra chức năng hóa đơn trong cài đặt hệ thống
     * @param bool $is_speclial
     * @return bool|array
     */
    public function invoiceFuncStatus(bool $is_speclial = true)
    {
        $invoice = (bool)sys_config('invoice_func_status', 0);
        if ($is_speclial) {
            $specialInvoice = sys_config('special_invoice_status', 0);
            return ['invoice_func' => $invoice, 'special_invoice' => $invoice && $specialInvoice];
        }
        return $invoice;
    }

    /**
     * Lấy thông tin một hóa đơn
     * @param int $id
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInvoice(int $id, int $uid = 0)
    {
        $invoice = $this->dao->getOne(['id' => $id, 'is_del' => 0]);
        if (!$invoice || ($uid && $invoice['uid'] != $uid)) {
            return [];
        }
        return $invoice->toArray();
    }

    /**
     * Kiểm tra hóa đơn này có dùng được không
     * @param int $id
     * @param int $uid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkInvoice(int $id, int $uid)
    {
        $invoice = $this->getInvoice($id, $uid);
        if (!$invoice) {
            throw new ApiException('Dữ liệu không tồn tại');
        }
        $invoice_func = $this->invoiceFuncStatus();
        if (!$invoice_func['invoice_func']) {
            throw new ApiException('Chưa bật tính năng hóa đơn');
        }
        //Hóa đơn chuyên dùng
        if ($invoice['type'] == 2) {
            if (!$invoice_func['special_invoice']) {
                throw new ApiException('Chưa bật tính năng hóa đơn chuyên dụng');
            }
        }
        return $invoice;
    }


    /**
     * Lấy danh sách hóa đơn của một người dùng
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserList(int $uid, $where)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $where['uid'] = $uid;
        return $this->dao->getList($where, '*', $page, $limit);
    }

    /**
     * Lấy hóa đơn mặc định của một người dùng
     * @param int $uid
     * @param string $field
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserDefaultInvoice(int $uid, int $type, string $field = '*')
    {
        return $this->dao->getOne(['uid' => $uid, 'is_default' => 1, 'is_del' => 0, 'type' => $type], $field);
    }

    /**
     * Thêm|Sửa
     * @param int $uid
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveInvoice(int $uid, array $data)
    {
        $id = (int)$data['id'];
        $data['uid'] = $uid;
        unset($data['id']);
        $invoice = $this->dao->get(['uid' => $uid, 'name' => $data['name'], 'drawer_phone' => $data['drawer_phone'], 'is_del' => 0]);
        if ($id) {
            if ($invoice && $id != $invoice['id']) {
                throw new ApiException('Hóa đơn này đã tồn tại');
            }
            if ($this->dao->update($id, $data, 'id')) {
                if ($data['is_default']) {
                    $this->setDefaultInvoice($uid, $id);
                }
                return ['type' => 'edit', 'msg' => 'Sửa hóa đơn thành công', 'data' => []];
            } else {
                throw new ApiException('Sửa thất bại');
            }
        } else {
            if ($invoice) {
                throw new ApiException('Hóa đơn này đã tồn tại');
            }
            if ($add_invoice = $this->dao->save($data)) {
                $id = (int)$add_invoice['id'];
                if ($data['is_default']) {
                    $this->setDefaultInvoice($uid, $id);
                }
                return ['type' => 'add', 'msg' => 'Thêm hóa đơn thành công', 'data' => ['id' => $id]];
            } else {
                throw new ApiException('Thêm thất bại');
            }
        }
    }

    /**
     * Đặt hóa đơn mặc định
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setDefaultInvoice(int $uid, int $id)
    {
        if (!$invoice = $this->getInvoice($id)) {
            throw new ApiException('Dữ liệu không tồn tại');
        }
        if ($invoice['uid'] != $uid) {
            throw new ApiException('Thao tác không hợp lệ');
        }
        if (!$this->dao->setDefault($uid, $id, $invoice['header_type'], $invoice['type'])) {
            throw new ApiException('Đặt hóa đơn mặc định thất bại');
        }
        return true;
    }

    /**
     * Xóa
     * @param $id
     * @throws \Exception
     */
    public function delInvoice(int $uid, int $id)
    {
        if ($invoice = $this->getInvoice($id)) {
            if ($invoice['uid'] != $uid) {
                throw new ApiException('Thao tác không hợp lệ');
            }
            if (!$this->dao->update($id, ['is_del' => 1])) {
                throw new ApiException('Xóa thất bại');
            }
        }
        return true;
    }

}
