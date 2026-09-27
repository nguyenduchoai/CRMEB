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
namespace app\adminapi\controller\v1\order;

use app\adminapi\controller\AuthController;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderInvoiceServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;
use app\services\serve\ServeServices;
use app\services\system\store\SystemStoreServices;
use app\services\user\UserServices;
use think\facade\App;

/**
 * Quản lý hóa đơn
 * Class StoreOrderInvoice
 * @package app\adminapi\controller\v1\order
 */
class StoreOrderInvoice extends AuthController
{

    /**
     * StoreOrderInvoice constructor.
     * @param App $app
     * @param StoreOrderInvoiceServices $services
     */
    public function __construct(App $app, StoreOrderInvoiceServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy số lượng theo loại đơn hàng
     * @return mixed
     */
    public function chart()
    {
        $where = $this->request->getMore([
            ['data', '', '', 'time'],
            ['real_name', ''],
            ['field_key', ''],
            [['type', 'd'], 0],
        ]);
        $data = $this->services->chart($where);
        return app('json')->success($data);
    }

    /**
     * Truy vấn danh sách hóa đơn
     * @return mixed
     */
    public function list()
    {
        $where = $this->request->getMore([
            ['status', 0],
            ['real_name', ''],
            ['header_type', ''],
            ['type', ''],
            ['data', '', '', 'time'],
            ['field_key', ''],
        ]);
        return app('json')->success($this->services->getList($where));
    }


    /**
     * Đặt trạng thái hóa đơn
     * @param string $id
     * @return mixed
     */
    public function set_invoice($id = '')
    {
        if ($id == '') return app('json')->fail('Tham số không hợp lệ');
        $data = $this->request->postMore([
            ['is_invoice', 0],
            ['invoice_number', 0],
            ['remark', '']
        ]);
        if ($data['is_invoice'] == 1 && !$data['invoice_number']) {
            return app('json')->fail('Vui lòng điền số hóa đơn');
        }
        $this->services->setInvoice((int)$id, $data);
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Chi tiết đơn hàng
     * @param $id ID đơn hàng
     * @return mixed
     */
    public function orderInfo(StoreProductServices $productServices, StoreOrderServices $orderServices, $id)
    {
        if (!$id || !($orderInfo = $orderServices->get($id))) {
            return app('json')->fail('Đơn hàng không tồn tại');
        }
        /** @var UserServices $services */
        $services = app()->make(UserServices::class);
        $userInfo = $services->get($orderInfo['uid']);
        if (!$userInfo) {
            return app('json')->fail('Thông tin người dùng không tồn tại');
        }
        $userInfo = $userInfo->hidden(['pwd', 'add_ip', 'last_ip', 'login_type']);
        $userInfo['spread_name'] = '';
        if ($userInfo['spread_uid'])
            $userInfo['spread_name'] = $services->value(['uid' => $userInfo['spread_uid']], 'nickname');
        $orderInfo = $orderServices->tidyOrder($orderInfo->toArray(), true, true);
        //Tính toán số tiền ưu đãi
        $vipTruePrice = array_column($orderInfo['cartInfo'], 'vip_sum_truePrice');
        $vipTruePrice = array_sum($vipTruePrice);
        $orderInfo['vip_true_price'] = $vipTruePrice ?: 0;

        $orderInfo['add_time'] = $orderInfo['_add_time'] ?? '';
        $productId = array_column($orderInfo['cartInfo'], 'product_id');
        $cateData = $productServices->productIdByProductCateName($productId);
        foreach ($orderInfo['cartInfo'] as &$item) {
            $item['class_name'] = $cateData[$item['product_id']] ?? '';
        }
        if ($orderInfo['store_id'] && $orderInfo['shipping_type'] == 2) {
            /** @var  $storeServices */
            $storeServices = app()->make(SystemStoreServices::class);
            $orderInfo['_store_name'] = $storeServices->value(['id' => $orderInfo['store_id']], 'name');
        } else {
            $orderInfo['_store_name'] = '';
        }
        $userInfo = $userInfo->toArray();
        $invoice = $this->services->getOne(['order_id' => $id]);
        return app('json')->success(compact('orderInfo', 'userInfo', 'invoice'));
    }

    /**
     * Lấy thông tin cấu hình hóa đơn điện tử
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function elecInvoiceConfig()
    {
        $data = [
            'elec_invoice' => (int)sys_config('elec_invoice'),
            'auto_invoice' => (int)sys_config('auto_invoice'),
            'elec_invoice_cate' => (int)sys_config('elec_invoice_cate'),
            'elec_invoice_cate_name' => sys_config('elec_invoice_cate_name'),
            'elec_invoice_tax_rate' => (int)sys_config('elec_invoice_tax_rate')
        ];
        return app('json')->success($data);
    }

    /**
     * Lấy địa chỉ iframe của trang xuất hóa đơn
     * @param $id
     * @return \think\Response
     * @throws \ReflectionException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/13
     */
    public function invoiceIssuanceUrl($id)
    {
        if (sys_config('elec_invoice', 1) != 1) {
            return app('json')->fail('Chức năng hóa đơn điện tử chưa được bật, vui lòng bật trong Yihaotong và bật trong mục cấu hình Yihaotong ở trang quản trị cửa hàng');
        }
        $info = $this->services->getOne(['id' => $id]);
        $unique = app()->make(StoreOrderServices::class)->value(['id' => $info['order_id']], 'order_id');
        $cartInfo = app()->make(StoreOrderCartInfoServices::class)->getOrderCartInfo($info['order_id']);
        $goods = [];
        foreach ($cartInfo as $item) {
            $goods[] = [
                'store_name' => $item['cart_info']['productInfo']['store_name'],
                'unit_price' => bcadd($item['cart_info']['truePrice'], $item['cart_info']['postage_price'], 2),
                'num' => $item['cart_info']['cart_num']
            ];
        }
        $data = [];
        $data['unique'] = $unique;
        $data['goods'] = $goods;
        $data['account_name'] = $info['name'];
        $data['email'] = $info['email'];
        if ($info['header_type'] == 1) {
            $data['invoice_type'] = 82;
            $data['is_enterprise'] = 0;
        } else {
            $data['invoice_type'] = $info['type'] == 1 ? 82 : 81;
            $data['tax_id'] = $info['duty_number'];
            $data['is_enterprise'] = 1;
        }
        $invoice = app()->make(ServeServices::class)->invoice();
        return app('json')->success($invoice->invoiceIssuanceUrl($data));
    }

    /**
     * Lưu thông tin hóa đơn
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/14
     */
    public function saveInvoiceInfo($id)
    {
        $data = $this->request->postMore([
            ['invoice_num', ''],
            ['invoice_type', ''],
            ['invoice_serial_number', ''],
        ]);
        $info = $this->services->getOne(['id' => $id]);
        $data['unique_num'] = app()->make(StoreOrderServices::class)->value(['id' => $info['order_id']], 'order_id');
        $data['is_invoice'] = 1;
        $data['invoice_time'] = time();
        $this->services->update($id, $data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Xem chi tiết hóa đơn
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/14
     */
    public function invoiceInfo($id)
    {
        $info = $this->services->getOne(['id' => $id]);
        $invoice = app()->make(ServeServices::class)->invoice();
        return app('json')->success($invoice->invoiceInfo($info['invoice_num']));
    }

    /**
     * Tải xuống hóa đơn
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/14
     */
    public function downInvoice($id)
    {
        $info = $this->services->getOne(['id' => $id]);
        $invoice = app()->make(ServeServices::class)->invoice();
        return app('json')->success($invoice->downloadInvoice($info['invoice_num']));
    }

    /**
     * Danh mục hóa đơn điện tử
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/15
     */
    public function invoiceCategory()
    {
        $where = $this->request->getMore([
            ['name', ''],
            ['page', 1],
            ['limit', 100],
        ]);
        $invoice = app()->make(ServeServices::class)->invoice();
        return app('json')->success($invoice->category($where));
    }

    /**
     * Xuất hóa đơn
     * @param $id
     * @return \think\Response
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
        $this->services->invoiceIssuance($id);
        return app('json')->success('Xuất hóa đơn thành công');
    }

    /**
     * Xuất hóa đơn số âm
     * @param $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/16
     */
    public function redInvoiceIssuance($id)
    {
        $this->services->redInvoiceIssuance($id);
        return app('json')->success('Xuất hóa đơn điều chỉnh giảm thành công');
    }
}
