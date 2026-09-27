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
declare (strict_types=1);

namespace app\services\order;


use app\jobs\OrderInvoiceJob;
use app\services\BaseServices;
use app\services\serve\ServeServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use app\dao\order\StoreOrderInvoiceDao;
use app\services\user\UserInvoiceServices;
use think\facade\Log;


/**
 * Class StoreOrderInvoiceServices
 * @package app\services\order
 */
class StoreOrderInvoiceServices extends BaseServices
{
    /**
     * LiveAnchorServices constructor.
     * @param StoreOrderInvoiceDao $dao
     */
    public function __construct(StoreOrderInvoiceDao $dao)
    {
        $this->dao = $dao;
    }

    public function chart(array $where)
    {
        $where['is_pay'] = 1;
        $where['is_del'] = 0;
        //Tất cả
        $data['all'] = (string)$this->dao->count($where);
        //Chờ xuất
        $where['type'] = 1;
        $data['noOpened'] = (string)$this->dao->count($where);
        //Đã xuất
        $where['type'] = 2;
        $data['opened'] = (string)$this->dao->count($where);
        //Hoàn tiền
        $where['type'] = 3;
        $data['refund'] = (string)$this->dao->count($where);
        $data['elec_invoice'] = (int)sys_config('elec_invoice', 0);
        return $data;
    }

    /**
     * Lấy danh sách xuất hóa đơn ở trang quản trị
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where)
    {
        [$page, $list] = $this->getPageValue();
        $field = 'id as invoice_id,order_id,header_type,type,name,duty_number,drawer_phone,email,tell,address,bank,card_number,is_invoice,invoice_number,remark as invoice_reamrk,invoice_time,add_time as invoice_add_time,unique_num,invoice_num,invoice_type,invoice_serial_number,red_invoice_num';
        $where['is_pay'] = 1;
        $where['is_del'] = 0;
        $list = $this->dao->getList($where, $field, ['order' => function ($query) {
            $query->field('id,order_id,pay_price,add_time,real_name,user_phone,status,refund_status');
        }], 'add_time desc', $page, $list);
        foreach ($list as &$item) {
            $item['id'] = $item['order']['id'] ?? 0;
            $item['order_id'] = $item['order']['order_id'] ?? '';
            $item['pay_price'] = $item['order']['pay_price'] ?? 0.00;
            $item['real_name'] = $item['order']['real_name'] ?? '';
            $item['user_phone'] = $item['order']['user_phone'] ?? '';
            $item['status'] = $item['order']['status'] ?? '';
            $item['refund_status'] = $item['order']['refund_status'] ?? 0;
            $item['add_time'] = date('Y-m-d H:i:s', $item['order']['add_time'] ?? $item['invoice_add_time'] ?? time());
            $item['invoice_add_time'] = date('Y-m-d H:i:s', $item['invoice_add_time']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy danh sách xuất hóa đơn ở phía người dùng (kèm thông tin sản phẩm)
     * @param $where
     * @return array
     */
    public function getOrderInvoiceList(array $where)
    {
        [$page, $list] = $this->getPageValue();
        $where['is_pay'] = 1;
        $where['is_del'] = 0;
        $list = $this->dao->getList($where, '*', ['order'], 'add_time desc', $page, $list);
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        foreach ($list as &$item) {
            if (isset($item['order']) && $item['order']) {
                $item['order'] = $storeOrderServices->tidyOrder($item['order'], true);
                if (isset($item['order']['_status']['_type']) && $item['order']['_status']['_type'] == 3) {
                    foreach ($item['order']['cartInfo'] ?: [] as $key => $product) {
                        $item['order']['cartInfo'][$key]['add_time'] = isset($product['add_time']) ? date('Y-m-d H:i', (int)$product['add_time']) : 'Thời gian không hợp lệ';
                    }
                }
            }
        }
        return $list;
    }

    /**
     * Đơn hàng yêu cầu xuất hóa đơn
     * @param int $uid
     * @param $order_id
     * @param int $invoice_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function makeUp(int $uid, $order_id, int $invoice_id)
    {
        if (!$order_id) throw new AdminException(100100);
        if (!$invoice_id) throw new AdminException(410325);

        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        /** @var UserInvoiceServices $userInvoiceServices */
        $userInvoiceServices = app()->make(UserInvoiceServices::class);
        $order = $storeOrderServices->getOne(['order_id|id' => $order_id, 'is_del' => 0]);
        if (!$order) {
            throw new AdminException(410173);
        }
        //Kiểm tra kèm truy vấn
        $invoice = $userInvoiceServices->checkInvoice($invoice_id, $uid);

        if ($this->dao->getOne(['order_id' => $order['id'], 'uid' => $uid])) {
            throw new AdminException(410249);
        }
        if ($order['refund_status'] == 2) {
            throw new AdminException(410226);
        }
        if ($order['refund_status'] == 1) {
            throw new AdminException(410250);
        }
        unset($invoice['id'], $invoice['add_time']);
        $data = [];
        $data['category'] = 'order';
        $data['order_id'] = $order['id'];
        $data['invoice_id'] = $invoice_id;
        $data['add_time'] = time();
        $data['is_pay'] = $order['paid'] == 1 ? 1 : 0;
        $data = array_merge($data, $invoice);
        if (!$re = $this->dao->save($data)) {
            throw new AdminException(410251);
        }
        if (sys_config('elec_invoice', 1) == 1 && sys_config('auto_invoice', 1) == 1 && $data['is_pay'] == 1) {
            //Tự động xuất hóa đơn
            OrderInvoiceJob::dispatchSecs(10, 'autoInvoice', [$re->id]);
        }

        //Sự kiện tùy chỉnh - yêu cầu xuất hóa đơn
        event('CustomEventListener', ['order_invoice', [
            'uid' => $uid,
            'order_id' => $order_id,
            'phone' => $order['user_phone'],
            'invoice_id' => $re->id,
            'add_time' => date('Y-m-d H:i:s'),
        ]]);

        return ['id' => $re->id];
    }

    public function setInvoice(int $id, array $data)
    {
        $orderInvoice = $this->dao->get($id);
        if (!$orderInvoice) {
            throw new AdminException(100026);
        }
        if ($data['is_invoice'] == 1) {
            $data['invoice_time'] = time();
        }
        if (!$this->dao->update($id, $data, 'id')) {
            throw new AdminException(100015);
        }
        return true;
    }

    /**
     * Đồng bộ tách bản ghi yêu cầu xuất hóa đơn khi tách đơn hàng
     * @param int $oid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function splitOrderInvoice(int $oid)
    {
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $orderInfo = $storeOrderServices->getOne(['id' => $oid, 'is_del' => 0]);
        if (!$orderInfo) {
            throw new AdminException(410173);
        }
        $pid = $orderInfo['pid'] > 0 ? $orderInfo['pid'] : $orderInfo['id'];
        //Truy vấn bản ghi xuất hóa đơn
        $orderInvoice = $this->dao->get(['order_id' => $oid]);
        //Truy vấn đơn hàng con
        $spliteOrder = $storeOrderServices->getColumn(['pid' => $pid, 'is_system_del' => 0], 'id,order_id');
        if ($spliteOrder && $orderInvoice) {
            $data = $orderInvoice->toArray();
            unset($data['id']);
            $data['add_time'] = strtotime($data['add_time']);
            $data_all = [];
            foreach ($spliteOrder as $order) {
                if (!$this->dao->count(['order_id' => $order['id']])) {
                    $data['order_id'] = $order['id'];
                    $data_all[] = $data;
                }
            }
            if ($data_all) {
                $this->transaction(function () use ($data_all, $orderInvoice, $orderInfo) {
                    $this->dao->saveAll($data_all);
                    if ($orderInfo['pid'] <= 0) {
                        $this->dao->update(['id' => $orderInvoice['id']], ['is_del' => 1]);
                    }
                });
            }
        }
        return true;
    }

    /**
     * Xuất hóa đơn
     * @param $id
     * @return bool
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/15
     */
    public function invoiceIssuance($id)
    {
        if (sys_config('elec_invoice', 1) != 1) {
            return app('json')->fail('Chức năng hóa đơn điện tử chưa được bật, vui lòng bật trong Yihaotong và bật trong mục cấu hình Yihaotong ở trang quản trị cửa hàng');
        }
        $info = $this->dao->getOne(['id' => $id]);
        $orderInfo = app()->make(StoreOrderServices::class)->get($info['order_id']);
        $cartInfo = app()->make(StoreOrderCartInfoServices::class)->getOrderCartInfo($orderInfo['id']);
        $data = [];
        $data['is_tax_inclusive'] = 1;
        $data['account_name'] = $info['name'];
        $data['bank_name'] = '';
        $data['bank_account'] = '';
        $data['telephone'] = $info['drawer_phone'];
        $data['company_address'] = '';
        $data['drawer'] = '';
        $data['amount'] = $orderInfo['pay_price'];
        if ($info['header_type'] == 1) {
            $data['invoice_type'] = 82;
            $data['tax_id'] = '';
            $data['is_enterprise'] = 0;
        } else {
            $data['invoice_type'] = $info['type'] == 1 ? 82 : 81;
            $data['tax_id'] = $info['duty_number'];
            $data['is_enterprise'] = 1;
        }
        $goods = [];
        foreach ($cartInfo as $item) {
            $goods[] = [
                'cate_id' => sys_config('elec_invoice_cate', 1799),
                'store_name' => $item['cart_info']['productInfo']['store_name'],
                'unit_price' => bcadd($item['cart_info']['truePrice'], $item['cart_info']['postage_price'], 2),
                'num' => $item['cart_info']['cart_num'],
                'tax_rate' => sys_config('elec_invoice_tax_rate', 13),
            ];
        }
        $data['goods'] = $goods;
        try {
            $invoice = app()->make(ServeServices::class)->invoice();
            $res = $invoice->invoiceIssuance($orderInfo['order_id'], $data);
            if ($res['status'] == 200) {
                $this->dao->update($id, [
                    'is_invoice' => 1,
                    'unique_num' => $orderInfo['order_id'],
                    'invoice_num' => $res['data']['invoice_num'],
                    'invoice_type' => $res['data']['invoice_type'],
                    'invoice_serial_number' => $res['data']['invoice_serial_number'],
                    'invoice_time' => time()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Tự động xuất hóa đơn thất bại, lý do:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Chưa xuất hóa đơn thì tự động xuất hóa đơn điện tử
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/15
     */
    public function autoInvoice()
    {
        if (sys_config('elec_invoice', 1) != 1) {
            return true;
        }
        if (sys_config('auto_invoice', 1) == 1) {
            $list = $this->dao->getColumn([
                ['is_pay', '=', 1],
                ['is_invoice', '=', 1],
                ['unique_num', '=', ''],
                ['is_del', '=', 0],
                ['add_time', '<', time() - 60],
            ], 'id');
            if ($list) {
                foreach ($list as $item) {
                    //Tự động xuất hóa đơn
                    OrderInvoiceJob::dispatchSecs(10, 'autoInvoice', [$item]);
                }
            }
        }
        return true;
    }

    /**
     * Đơn hàng hoàn tiền tự động lập hóa đơn điều chỉnh giảm
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/15
     */
    public function autoInvoiceRed()
    {
        if (sys_config('elec_invoice', 1) != 1) {
            return true;
        }
        if (sys_config('auto_invoice', 1) == 1) {
            $list = $this->dao->getColumn([
                ['is_pay', '=', 1],
                ['is_invoice', '=', 1],
                ['unique_num', '<>', ''],
                ['red_invoice_num', '=', ''],
                ['is_del', '=', 1],
                ['add_time', '<', time() - 60],
            ], 'id');
            if ($list) {
                foreach ($list as $item) {
                    //Tự động lập hóa đơn điều chỉnh giảm
                    OrderInvoiceJob::dispatchSecs(10, 'autoInvoiceRed', [$item]);
                }
            }
        }
        return true;
    }

    /**
     * Xuất hóa đơn số âm
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function redInvoiceIssuance($id)
    {
        $invoiceInfo = $this->dao->get($id);
        if ($invoiceInfo['is_pay'] == 0 || $invoiceInfo['is_invoice'] == 0 || $invoiceInfo['unique_num'] == '') {
            throw new AdminException('Trạng thái hóa đơn không đúng, vui lòng kiểm tra');
        }
        $invoice = app()->make(ServeServices::class)->invoice();
        $res = $invoice->redInvoiceIssuance(['invoice_num' => $invoiceInfo['invoice_num'], 'apply_type' => '01']);
        if ($res['status'] != 200) throw new AdminException('Xuất hóa đơn điều chỉnh giảm thất bại, vui lòng kiểm tra');
        $this->dao->update($id, ['red_invoice_num' => 1]);
        return true;
    }
}
