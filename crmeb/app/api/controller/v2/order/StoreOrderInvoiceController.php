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

namespace app\api\controller\v2\order;


use app\services\order\StoreOrderInvoiceServices;
use app\services\order\StoreOrderServices;
use app\services\other\PosterServices;
use app\services\serve\ServeServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\system\store\SystemStoreServices;
use think\Request;

/**
 * Class StoreOrderInvoiceController
 * @package app\api\controller\v2\order
 */
class StoreOrderInvoiceController
{
    /**
     * @var StoreOrderInvoiceServices
     */
    protected $services;

    /**
     * StoreOrderInvoiceController constructor.
     * @param StoreOrderInvoiceServices $services
     */
    public function __construct(StoreOrderInvoiceServices $services)
    {
        $this->services = $services;
    }

    /**
     * Xuất hóa đơn cho đơn hàng
     * @param Request $request
     * @return mixed
     */
    public function makeUp(Request $request)
    {
        [$order_id, $invoice_id] = $request->postMore([
            ['order_id', 0],
            ['invoice_id', 0]
        ], true);
        $uid = (int)$request->uid;
        return app('json')->success($this->services->makeUp($uid, $order_id, (int)$invoice_id));
    }

    /**
     * Lịch sử xuất hóa đơn
     * @param Request $request
     * @return mixed
     */
    public function list(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getOrderInvoiceList(['uid' => $uid]));
    }

    /**
     * Chi tiết đơn hàng
     * @param \app\Request $request
     * @param $uni
     * @return mixed
     */
    public function detail(StoreOrderServices $services, Request $request, $uni)
    {
        if (!strlen(trim($uni))) return app('json')->fail(100100);
        $order = $services->getUserOrderDetail($uni, (int)$request->uid(), []);
        if (!$order) return app('json')->fail(410173);
        $order = $order->toArray();
        $orderInvoice = $this->services->getOne(['order_id' => $order['id']]);
        $order['invoice'] = $orderInvoice;
        //Bật nhận tại cửa hàng
        $store_self_mention = sys_config('store_self_mention');
        //Sau khi tắt nhận tại cửa hàng, ẩn thông tin cửa hàng trong đơn hàng
        if ($store_self_mention == 0) $order['shipping_type'] = 1;
        if ($order['verify_code']) {
            $verify_code = $order['verify_code'];
            $verify[] = substr($verify_code, 0, 4);
            $verify[] = substr($verify_code, 4, 4);
            $verify[] = substr($verify_code, 8);
            $order['_verify_code'] = implode(' ', $verify);
        }
        $order['add_time_y'] = date('Y-m-d', $order['add_time']);
        $order['add_time_h'] = date('H:i:s', $order['add_time']);
        /** @var SystemStoreServices $storeServices */
        $storeServices = app()->make(SystemStoreServices::class);
        $order['system_store'] = $storeServices->getStoreDispose($order['store_id']);
        if (($order['shipping_type'] === 2 || $order['delivery_uid'] != 0) && $order['verify_code']) {
            $name = $order['verify_code'] . '.jpg';
            /** @var SystemAttachmentServices $attachmentServices */
            $attachmentServices = app()->make(SystemAttachmentServices::class);
            $imageInfo = $attachmentServices->getInfo(['name' => $name]);
            $siteUrl = sys_config('site_url');
            if (!$imageInfo) {
                $imageInfo = PosterServices::getQRCodePath($order['verify_code'], $name);
                if (is_array($imageInfo)) {
                    $attachmentServices->attachmentAdd($imageInfo['name'], $imageInfo['size'], $imageInfo['type'], $imageInfo['dir'], $imageInfo['thumb_path'], 1, $imageInfo['image_type'], $imageInfo['time'], 2);
                    $url = $imageInfo['dir'];
                } else
                    $url = '';
            } else $url = $imageInfo['att_dir'];
            if (isset($imageInfo['image_type']) && $imageInfo['image_type'] == 1) $url = $siteUrl . $url;
            $order['code'] = $url;
        }
        $order['mapKey'] = sys_config('tengxun_map_key');
        $order['yue_pay_status'] = (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1 ? (int)1 : (int)2;//Thanh toán bằng số dư: 1 là bật, 2 là tắt
        $order['pay_weixin_open'] = (int)sys_config('pay_weixin_open') ?? 0;//WeChat Pay: 1 là bật, 0 là tắt
        $order['ali_pay_status'] = (bool)sys_config('ali_pay_status');//Thanh toán Alipay: 1 là bật, 0 là tắt
        return app('json')->success($services->tidyOrder($order, true, true));
    }

    /**
     * Tải hóa đơn điện tử ở frontend
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
}
