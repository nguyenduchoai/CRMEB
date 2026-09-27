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
namespace app\services\order;

use app\dao\order\StoreOrderRefundDao;
use app\jobs\ProductLogJob;
use app\services\activity\advance\StoreAdvanceServices;
use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\combination\StorePinkServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\BaseServices;
use app\services\activity\coupon\StoreCouponIssueUserServices;
use app\services\activity\coupon\StoreCouponUserServices;
use app\services\pay\PayServices;
use app\services\product\product\StoreProductServices;
use app\services\shipping\ExpressServices;
use app\services\statistic\CapitalFlowServices;
use app\services\user\UserBillServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserMoneyServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\AliPayService;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder as Form;
use crmeb\services\pay\Pay;
use crmeb\services\workerman\ChannelService;
use think\facade\Db;


/**
 * Hoàn tiền đơn hàng
 * Class StoreOrderRefundServices
 * @method getOrderRefundMoneyByWhere
 * @package app\services\order
 */
class StoreOrderRefundServices extends BaseServices
{
    /**
     * Service đơn hàng
     * @var StoreOrderServices
     */
    protected $storeOrderServices;

    /**
     * Phương thức khởi tạo
     * StoreOrderRefundServices constructor.
     * @param StoreOrderRefundDao $dao
     */
    public function __construct(StoreOrderRefundDao $dao, StoreOrderServices $storeOrderServices)
    {
        $this->dao = $dao;
        $this->storeOrderServices = $storeOrderServices;
    }

    /**
     * Biểu mẫu hoàn tiền đơn hàng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function refundOrderForm(int $id, $type = 'refund')
    {
        if ($type == 'refund') {//Đơn đổi trả
            $orderRefund = $this->dao->get($id);
            if (!$orderRefund) {
                throw new AdminException(100026);
            }
            $order = $this->storeOrderServices->get((int)$orderRefund['store_order_id']);
            if (!$order) {
                throw new AdminException(100026);
            }
            if (!$order['paid']) {
                throw new AdminException(400488);
            }
            if ($orderRefund['refund_price'] > 0 && in_array($orderRefund['refund_type'], [1, 5])) {
                if ($orderRefund['refund_price'] <= $orderRefund['refunded_price']) {
                    throw new AdminException(400485);
                }
            }
            $f[] = Form::input('order_id', 'Mã đơn hoàn tiền', $orderRefund->getData('order_id'))->disabled(true);
            $f[] = Form::number('refund_price', 'Số tiền hoàn', (float)bcsub((string)$orderRefund->getData('refund_price'), (string)$orderRefund->getData('refunded_price'), 2))->min(0)->required('Vui lòng nhập số tiền hoàn');
            return create_form('Xử lý hoàn tiền', $f, $this->url('/refund/refund/' . $id), 'PUT');
        } else {//Chủ động hoàn tiền đơn hàng
            $order = $this->storeOrderServices->get((int)$id);
            if (!$order) {
                throw new AdminException(100026);
            }
            if (!$order['paid']) {
                throw new AdminException(400488);
            }
            if ($order['pay_price'] > 0 && in_array($order['refund_status'], [0, 1])) {
                if ($order['pay_price'] <= $order['refund_price']) {
                    throw new AdminException(400485);
                }
            }
            $f[] = Form::input('order_id', 'Mã đơn hoàn tiền', $order->getData('order_id'))->disabled(true);
            $f[] = Form::number('refund_price', 'Số tiền hoàn', (float)bcsub((string)$order->getData('pay_price'), (string)$order->getData('refund_price'), 2))->precision(2)->required('Vui lòng nhập số tiền hoàn');
            return create_form('Xử lý hoàn tiền', $f, $this->url('/order/refund/' . $id), 'PUT');
        }
    }

    /**
     * Đồng ý hoàn tiền: tách đơn hoàn tiền, trả lại điểm thưởng, hoa hồng, v.v.
     * @param int $id
     * @param array $refundData
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function agreeRefund(int $id, array $refundData)
    {
        $order = $this->transaction(function () use ($id, $refundData) {
            //Tách hoàn tiền
            $orderRefundInfo = $this->dao->get($id);
            if (!$orderRefundInfo) throw new AdminException(100026);
            $cart_ids = [];
            if ($orderRefundInfo['cart_info']) {
                foreach ($orderRefundInfo['cart_info'] as $cart) {
                    $cart_ids[] = [
                        'cart_id' => $cart['id'],
                        'cart_num' => $cart['cart_num'],
                    ];
                }
            }
            if (!$cart_ids) return false;
            $orderInfo = $this->storeOrderServices->get($orderRefundInfo['store_order_id']);
            /** @var StoreOrderSplitServices $storeOrderSplitServices */
            $storeOrderSplitServices = app()->make(StoreOrderSplitServices::class);
            [$splitOrderInfo, $otherOrder] = $storeOrderSplitServices->equalSplit($orderRefundInfo['store_order_id'], $cart_ids, $orderInfo);


            //Hoàn trả điểm thưởng và phiếu giảm giá
            if (!$this->integralAndCouponBack($splitOrderInfo)) {
                throw new AdminException(400489);
            }
            //Hoàn tiền mua chung
            if ($splitOrderInfo['pid'] == 0 && $splitOrderInfo['pink_id'] > 0) {
                /** @var StorePinkServices $pinkServices */
                $pinkServices = app()->make(StorePinkServices::class);
                if (!$pinkServices->setRefundPink($splitOrderInfo)) {
                    throw new AdminException(400490);
                }
            }

            //Hoàn hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            if (!$userBrokerageServices->orderRefundBrokerageBack($splitOrderInfo)) {
                throw new AdminException(400491);
            }

            //Hoàn lại tồn kho
            if ($splitOrderInfo['status'] == 0) {
                /** @var StoreOrderStatusServices $services */
                $services = app()->make(StoreOrderStatusServices::class);
                if (!$services->count(['oid' => $splitOrderInfo['id'], 'change_type' => 'refund_price'])) {
                    /** @var StoreOrderServices $orderServices */
                    $orderServices = app()->make(StoreOrderServices::class);
                    $this->regressionStock($orderServices->get($splitOrderInfo['id']));
                }
            }

            //Hoàn số tiền
            if ($refundData['refund_price'] > 0) {
                if (!isset($refundData['refund_id']) || !$refundData['refund_id']) {
                    mt_srand();
                    $refundData['refund_id'] = $splitOrderInfo['order_id'] . rand(100, 999);
                }
                if ($splitOrderInfo['pid'] > 0) {//Đơn hàng con
                    $refundOrder = $this->storeOrderServices->get((int)$splitOrderInfo['pid']);
                    $refundData['pay_price'] = $refundOrder['pay_price'];
                } else {
                    $refundOrder = $splitOrderInfo;
                }
                switch ($refundOrder['pay_type']) {
                    case PayServices::WEIXIN_PAY:
                        $no = $refundOrder['order_id'];
                        if ($refundOrder['trade_no']) {
                            $no = $refundOrder['trade_no'];
                            $refundData['type'] = 'trade_no';
                        }
                        if (sys_config('pay_wechat_type')) {
                            $drivers = 'v3_wechat_pay';
                        } else {
                            $drivers = 'wechat_pay';
                        }
                        /** @var Pay $pay */
                        $pay = app()->make(Pay::class, [$drivers]);
                        if ($refundOrder['is_channel'] == 1) {
                            $refundData['trade_no'] = $refundOrder['trade_no'];
                            $refundData['pay_new_weixin_open'] = sys_config('pay_new_weixin_open');
                            //Hoàn tiền Mini Program
                            $pay->refund($no, $refundData);//Mini Program
                        } else {
                            //Hoàn tiền OA WeChat
                            $refundData['wechat'] = true;
                            $pay->refund($no, $refundData);//OA WeChat
                        }
                        break;
                    case PayServices::YUE_PAY:
                        //Hoàn tiền vào số dư
                        if (!$this->yueRefund($refundOrder, $refundData)) {
                            throw new AdminException(400492);
                        }
                        break;
                    case PayServices::ALIAPY_PAY:
                        mt_srand();
                        $refund_id = $refundData['refund_id'] ?? $refundOrder['order_id'] . rand(100, 999);
                        //Hoàn tiền Alipay
                        AliPayService::instance()->refund(strpos($refundOrder['trade_no'], '_') !== false ? $refundOrder['trade_no'] : $refundOrder['order_id'], floatval($refundData['refund_price']), $refund_id);
                        break;
                    case PayServices::ALLIN_PAY:
                        /** @var Pay $pay */
                        $pay = app()->make(Pay::class, ['allin_pay']);
                        /** @var StoreOrderServices $orderServices */
                        $orderServices = app()->make(StoreOrderServices::class);
                        $trade_no = $orderServices->value(['id' => $orderRefundInfo['store_order_id']], 'trade_no');
                        $pay->refund($trade_no, [
                            'order_id' => $refundOrder['order_id'],
                            'refund_price' => $refundData['refund_price']
                        ]);
                        break;
                }
            }
            //Lịch sử đơn hàng
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $statusService->save([
                'oid' => $splitOrderInfo['id'],
                'change_type' => 'refund_price',
                'change_message' => 'Hoàn tiền cho người dùng:' . $refundData['refund_price'] . 'đ',
                'change_time' => time()
            ]);
            $this->storeOrderServices->update($splitOrderInfo['id'], [
                'status' => -2,
                'refund_status' => 2,
                'refund_type' => 6,
                'refund_express' => $orderRefundInfo['refund_express'],
                'refund_express_name' => $orderRefundInfo['refund_express_name'],
                'refund_reason_wap_img' => $orderRefundInfo['refund_img'],
                'refund_reason_wap_explain' => $orderRefundInfo['refund_explain'],
                'refund_reason_time' => $orderRefundInfo['refunded_time'],
                'refund_reason_wap' => $orderRefundInfo['refund_reason'],
                'refund_price' => $refundData['refund_price'],
            ], 'id');
            $splitOrderInfo = $this->storeOrderServices->get($splitOrderInfo['id']);
            $this->dao->update($id, ['store_order_id' => $splitOrderInfo['id']]);
            if ($otherOrder['id'] != 0 && $orderInfo['id'] != $otherOrder['id']) {//Đã tách và tạo đơn hàng mới
                //Sửa đơn hoàn tiền đang yêu cầu của đơn hàng gốc
                $this->dao->update(['store_order_id' => $orderInfo['id']], ['store_order_id' => $otherOrder['id']]);
            }

            /** @var CapitalFlowServices $capitalFlowServices */
            $capitalFlowServices = app()->make(CapitalFlowServices::class);
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $userInfo = $userServices->get($splitOrderInfo['uid']);
            $splitOrderInfo['nickname'] = $userInfo['nickname'];
            $splitOrderInfo['phone'] = $userInfo['phone'];
            if (in_array($orderInfo['pay_type'], ['weixin', 'alipay', 'allinpay', 'offline'])) {
                $capitalFlowServices->setFlow($splitOrderInfo, 'refund');
            }

            return $splitOrderInfo;
        });
        //Xử lý xuất hóa đơn
        app()->make(StoreOrderInvoiceServices::class)->update(['order_id' => $order['id']], ['is_refund' => 1]);
        //Bản ghi hoàn tiền đơn hàng
        ProductLogJob::dispatch(['refund', ['uid' => $order['uid'], 'order_id' => $order['id']]]);
        event('NoticeListener', [['data' => $refundData, 'order' => $order], 'order_refund']);

        //Tin nhắn tùy chỉnh - hoàn tiền thành công
        $order['phone'] = $order['user_phone'];
        event('CustomNoticeListener', [$order['uid'], $order, 'order_refund_success']);

        //Sự kiện tùy chỉnh - hoàn tiền đơn hàng ở trang quản trị
        event('CustomEventListener', ['admin_order_refund_success', [
            'uid' => $order['uid'],
            'order_id' => $order['order_id'],
            'real_name' => $order['real_name'],
            'user_phone' => $order['user_phone'],
            'user_address' => $order['user_address'],
            'total_num' => $order['total_num'],
            'pay_price' => $order['pay_price'],
            'refund_reason_wap' => $order['refund_reason_wap'],
            'refund_reason_wap_explain' => $order['refund_reason_wap_explain'],
            'refund_price' => $order['refund_price'],
            'refund_time' => date('Y-m-d H:i:s'),
        ]]);

        return true;
    }

    /**
     * Cửa hàng đồng ý cho người dùng trả hàng
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/16
     */
    public function agreeExpress($id)
    {
        $order = $this->dao->get($id, ['refund_type']);
        if (!$order) throw new AdminException(100026);
        if ($order['refund_type'] == 4) {
            return true;
        }
        $this->dao->update($id, ['refund_type' => 4], 'id');
        return true;
    }

    /**
     * Xử lý hoàn tiền đơn hàng
     * @param int $type
     * @param $order
     * @param array $refundData
     * @return mixed
     */
    public function payOrderRefund(int $type, $order, array $refundData)
    {
        return $this->transaction(function () use ($type, $order, $refundData) {

            //Hoàn trả điểm thưởng và phiếu giảm giá
            if (!$this->integralAndCouponBack($order)) {
                throw new AdminException(400489);
            }
            //Xử lý hoàn tiền phiếu giảm giá cho sản phẩm ảo
            if ($order['virtual_type'] == 2) {
                /** @var StoreCouponUserServices $couponUser */
                $couponUser = app()->make(StoreCouponUserServices::class);
                $res = $couponUser->delUserCoupon(['cid' => $order['virtual_info'], 'uid' => $order['uid'], 'status' => 0]);
                if (!$res) throw new AdminException(400493);
                /** @var StoreCouponIssueUserServices $couponIssueUser */
                $couponIssueUser = app()->make(StoreCouponIssueUserServices::class);
                $couponIssueUser->delIssueUserCoupon(['issue_coupon_id' => $order['virtual_info'], 'uid' => $order['uid']]);
            }

            //Hoàn tiền mua chung
            if ($type == 1) {
                /** @var StorePinkServices $pinkServices */
                $pinkServices = app()->make(StorePinkServices::class);
                if (!$pinkServices->setRefundPink($order)) {
                    throw new AdminException(400490);
                }
            }

            //Hoàn hoa hồng
            /** @var UserBrokerageServices $userBrokerageServices */
            $userBrokerageServices = app()->make(UserBrokerageServices::class);
            if (!$userBrokerageServices->orderRefundBrokerageBack($order)) {
                throw new AdminException(400491);
            }


            //Hoàn lại tồn kho
            if ($order['status'] == 0) {
                /** @var StoreOrderStatusServices $services */
                $services = app()->make(StoreOrderStatusServices::class);
                if (!$services->count(['oid' => $order['id'], 'change_type' => 'refund_price'])) {
                    $this->regressionStock($order);
                }
            }

            //Hoàn số tiền
            if ($refundData['refund_price'] > 0) {
                if (!isset($refundData['refund_id']) || !$refundData['refund_id']) {
                    mt_srand();
                    $refundData['refund_id'] = $order['order_id'] . rand(100, 999);
                }
                if ($order['pid'] > 0) {//Đơn hàng con
                    $refundOrder = $this->storeOrderServices->get((int)$order['pid']);
                    $refundData['pay_price'] = $refundOrder['pay_price'];
                } else {
                    $refundOrder = $order;
                }
                switch ($refundOrder['pay_type']) {
                    case PayServices::WEIXIN_PAY:
                        $no = $refundOrder['order_id'];
                        if ($refundOrder['trade_no']) {
                            $no = $refundOrder['trade_no'];
                            $refundData['type'] = 'trade_no';
                        }
                        /** @var Pay $pay */
                        $pay = app()->make(Pay::class);
                        if ($refundOrder['is_channel'] == 1) {
                            //Hoàn tiền Mini Program
                            $pay->refund($no, $refundData);//Mini Program
                        } else {
                            //Hoàn tiền OA WeChat
                            $refundData['wechat'] = true;
                            $pay->refund($no, $refundData);//OA WeChat
                        }
                        break;
                    case PayServices::YUE_PAY:
                        //Hoàn tiền vào số dư
                        if (!$this->yueRefund($refundOrder, $refundData)) {
                            throw new AdminException(400492);
                        }
                        break;
                    case PayServices::ALIAPY_PAY:
                        mt_srand();
                        $refund_id = $refundData['refund_id'] ?? $refundOrder['order_id'] . rand(100, 999);
                        //Hoàn tiền Alipay
                        AliPayService::instance()->refund(strpos($refundOrder['trade_no'], '_') !== false ? $refundOrder['trade_no'] : $refundOrder['order_id'], floatval($refundData['refund_price']), $refund_id);
                        break;
                }
            }
            //Sửa trạng thái hoàn tiền của dữ liệu xuất hóa đơn
            $orderInvoiceServices = app()->make(StoreOrderInvoiceServices::class);
            $orderInvoiceServices->update(['order_id' => $order['id']], ['is_refund' => 1]);
        });
    }

    /**
     * Hoàn tiền vào số dư
     * @param $order
     * @param array $refundData
     * @return bool
     */
    public function yueRefund($order, array $refundData)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userMoney = $userServices->value(['uid' => $order['uid']], 'now_money');
        $res = $userServices->bcInc($order['uid'], 'now_money', $refundData['refund_price'], 'uid');
        /** @var UserMoneyServices $userMoneyServices */
        $userMoneyServices = app()->make(UserMoneyServices::class);
        return $res && $userMoneyServices->income('pay_product_refund', $order['uid'], $refundData['refund_price'], bcadd((string)$userMoney, (string)$refundData['refund_price'], 2), $order['id']);
    }

    /**
     * Hoàn trả điểm thưởng và phiếu giảm giá
     * @param $order
     * @param string $type
     * @return bool
     */
    public function integralAndCouponBack($order, $type = 'refund')
    {
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $res = true;
        //Đơn hàng bị hủy hoặc hoàn tiền thì trả lại phiếu giảm giá
        if ($order['coupon_id'] && $order['coupon_price']) {
            /** @var StoreCouponUserServices $couponUserServices */
            $couponUserServices = app()->make(StoreCouponUserServices::class);
            //Đơn hàng chưa thanh toán bị hủy, hoặc đơn hàng chính và đơn hàng con cuối cùng sau khi mở công tắc trả lại phiếu giảm giá thì trả lại phiếu giảm giá
            if ($type == 'cancel' || (sys_config('coupon_return_open', 1) && ($order['pid'] == 0 || $this->storeOrderServices->count(['pid' => $order['pid'], 'refund_status' => 0]) == 1))) {
                $res = $couponUserServices->recoverCoupon((int)$order['coupon_id']);
                $statusService->save([
                    'oid' => $order['id'],
                    'change_type' => 'coupon_back',
                    'change_message' => 'Hoàn phiếu giảm giá sản phẩm',
                    'change_time' => time()
                ]);
            }
        }

        //Hoàn lại điểm thưởng
        $order = $this->regressionIntegral($order);
        $statusService->save([
            'oid' => $order['id'],
            'change_type' => 'integral_back',
            'change_message' => 'Hoàn điểm thưởng sản phẩm',
            'change_time' => time()
        ]);
        return $res && $order->save();
    }

    /**
     * Hoàn lại điểm thưởng đã dùng và điểm thưởng đã tặng
     * @param $order
     * @return bool
     */
    public function regressionIntegral($order)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($order['uid'], ['integral']);
        if (!$userInfo) {
            $order->back_integral = $order->use_integral;
            return $order;
        }
        $integral = $userInfo['integral'];
        if ($order['status'] == -2 || $order['is_del']) {
            return $order;
        }

        $res1 = $res2 = $res3 = $res4 = true;
        //Điểm thưởng tặng của đơn hàng
        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $order_gain = $userBillServices->sum([
            'category' => 'integral',
            'type' => 'gain',
            'link_id' => $order['id'],
            'uid' => $order['uid']
        ], 'number');
        //Tặng theo sản phẩm
        $product_gain = $userBillServices->sum([
            'category' => 'integral',
            'type' => 'product_gain',
            'link_id' => $order['id'],
            'uid' => $order['uid']
        ], 'number');

        $give_integral = $order_gain + $product_gain;
        if ($give_integral) {
            //Kiểm tra đơn hàng đã hoàn lại điểm thưởng chưa
            $count = $userBillServices->count(['category' => 'integral', 'type' => 'integral_refund', 'link_id' => $order['id']]);
            if (!$count) {
                if ($integral > $give_integral) {
                    $res1 = $userServices->bcDec($order['uid'], 'integral', $give_integral);
                    //Ghi lại việc thu hồi điểm thưởng đã tặng
                    $integral = $integral - $give_integral;
                } else {
                    $res1 = $userServices->update($order['uid'], ['integral' => 0]);
                    //Ghi lại việc thu hồi điểm thưởng đã tặng
                    $integral = 0;
                }
                $res2 = $userBillServices->income('integral_refund', $order['uid'], $give_integral, $integral, $order['id']);
                //Xóa điểm thưởng bị đóng băng
                $userBillServices->update(['link_id' => $order['id']], ['frozen_time' => 0]);
            }
        }
        //Hoàn lại điểm thưởng đã dùng khi đặt hàng
        $use_integral = $order['use_integral'];
        if ($use_integral > 0) {
            $res3 = $userServices->bcInc($order['uid'], 'integral', $use_integral);
            //Ghi lại việc trả lại điểm thưởng đã dùng khi đặt hàng
            $res4 = $userBillServices->income('pay_product_integral_back', $order['uid'], (int)$use_integral, $integral + $use_integral, $order['id']);
        }
        if (!($res1 && $res2 && $res3 && $res4)) {
            throw new ApiException(400494);
        }
        if ($use_integral > $give_integral) {
            $order->back_integral = bcsub($use_integral, $give_integral, 2);
        }
        return $order;
    }

    /**
     * Hoàn lại tồn kho
     * @param $order
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function regressionStock($order)
    {
        if ($order['status'] == -2 || $order['is_del']) return true;
        $combination_id = $order['combination_id'];
        $seckill_id = $order['seckill_id'];
        $bargain_id = $order['bargain_id'];
        $res5 = true;
        /** @var StoreOrderCartInfoServices $cartServices */
        $cartServices = app()->make(StoreOrderCartInfoServices::class);
        /** @var StoreProductServices $services */
        $services = app()->make(StoreProductServices::class);
        /** @var StoreSeckillServices $seckillServices */
        $seckillServices = app()->make(StoreSeckillServices::class);
        /** @var StoreCombinationServices $pinkServices */
        $pinkServices = app()->make(StoreCombinationServices::class);
        /** @var StoreBargainServices $bargainServices */
        $bargainServices = app()->make(StoreBargainServices::class);
        /** @var StoreAdvanceServices $advanceServices */
        $advanceServices = app()->make(StoreAdvanceServices::class);
        $cartInfo = $cartServices->getCartInfoList(['cart_id' => $order['cart_id']], ['cart_info']);
        foreach ($cartInfo as $cart) {
            $cart['cart_info'] = is_array($cart['cart_info']) ? $cart['cart_info'] : json_decode($cart['cart_info'], true);
            //Tăng tồn kho, giảm lượt bán
            $unique = isset($cart['cart_info']['productInfo']['attrInfo']) ? $cart['cart_info']['productInfo']['attrInfo']['unique'] : '';
            $cart_num = (int)$cart['cart_info']['cart_num'];
            if ($combination_id) {
                $res5 = $res5 && $pinkServices->incCombinationStock($cart_num, (int)$combination_id, $unique);
            } else if ($seckill_id) {
                $res5 = $res5 && $seckillServices->incSeckillStock($cart_num, (int)$seckill_id, $unique);
            } else if ($bargain_id) {
                $res5 = $res5 && $bargainServices->incBargainStock($cart_num, (int)$bargain_id, $unique);
            } else {
                $res5 = $res5 && $services->incProductStock($cart_num, (int)$cart['cart_info']['productInfo']['id'], $unique);
            }
        }
        return $res5;
    }


    /**
     * Đồng ý hoàn tiền thành công thì gửi tin nhắn mẫu và ghi lại trạng thái đơn hàng
     * @param $data
     * @param $order
     * @param $refund_price
     * @param $id
     */
    public function storeProductOrderRefundY($data, $order, $refund_price)
    {
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $order['id'],
            'change_type' => 'refund_price',
            'change_message' => 'Hoàn tiền cho người dùng:' . $refund_price . 'đ',
            'change_time' => time()
        ]);

        /** @var CapitalFlowServices $capitalFlowServices */
        $capitalFlowServices = app()->make(CapitalFlowServices::class);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($order['uid']);
        $order['nickname'] = $userInfo['nickname'];
        $order['phone'] = $userInfo['phone'];
        if (in_array($order['pay_type'], ['weixin', 'alipay', 'allinpay', 'offline'])) {
            $order['refund_price'] = $refund_price;
            $capitalFlowServices->setFlow($order, 'refund');
        }

        event('NoticeListener', [['data' => $data, 'order' => $order], 'order_refund']);
    }

    /**
     * Đồng ý hoàn tiền nhưng hoàn tiền thất bại thì ghi vào bản ghi đơn hàng
     * @param int $id
     * @param $refund_price
     */
    public function storeProductOrderRefundYFasle(int $id, $refund_price)
    {
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $id,
            'change_type' => 'refund_price',
            'change_message' => 'Hoàn tiền cho người dùng:' . $refund_price . 'đ thất bại',
            'change_time' => time()
        ]);
    }

    /**
     * Không hoàn tiền thì ghi lại thay đổi trạng thái đơn hàng
     * @param int $id
     * @param string $refundReason
     */
    public function storeProductOrderRefundNo(int $id, string $refundReason)
    {
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $id,
            'change_type' => 'refund_n',
            'change_message' => 'Lý do từ chối hoàn tiền:' . $refundReason,
            'change_time' => time()
        ]);
    }


    /**
     * Form không hoàn tiền
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function noRefundForm(int $id)
    {
        $order = $this->dao->get($id);
        if (!$order) {
            throw new AdminException(100026);
        }
        $f[] = Form::input('order_id', 'Mã đơn từ chối hoàn tiền', $order->getData('order_id'))->disabled(true);
        $f[] = Form::input('refund_reason', 'Lý do từ chối hoàn tiền')->type('textarea')->required('Vui lòng nhập lý do từ chối hoàn tiền');
        return create_form('Lý do từ chối hoàn tiền', $f, $this->url('refund/no_refund/' . $id), 'PUT');
    }

    /**
     * Từ chối hoàn tiền
     * @param int $id
     * @param array $data
     * @param array $orderRefundInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refuseRefund(int $id, array $data, $orderRefundInfo = [])
    {
        if (!$orderRefundInfo) {
            $orderRefundInfo = $this->dao->get(['id' => $id, 'is_cancel' => 0]);
        }
        if (!$orderRefundInfo) {
            throw new ApiException(400495);
        }
        /** @var StoreOrderServices $storeOrderServices */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $this->transaction(function () use ($id, $data, $orderRefundInfo, $storeOrderServices) {
            //Xử lý đơn hậu mãi
            $this->dao->update($id, $data);
            //Xử lý đơn hàng
            $oid = (int)$orderRefundInfo['store_order_id'];

            $storeOrderServices->update($oid, ['refund_status' => 0, 'refund_type' => 3]);
            //Xử lý cart_info sản phẩm trong đơn hàng
            $this->cancelOrderRefundCartInfo($id, $oid, $orderRefundInfo, 'Lý do từ chối hoàn tiền:' . ($data['refuse_reason'] ?? ''));
            //Lịch sử
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $statusService->save([
                'oid' => $id,
                'change_type' => 'refund_n',
                'change_message' => 'Lý do từ chối hoàn tiền:' . ($data['refuse_reason'] ?? ''),
                'change_time' => time()
            ]);
        });
        $orderRefundInfo['refuse_reason'] = $data['refuse_reason'];
        event('NoticeListener', [['orderInfo' => $orderRefundInfo], 'send_order_refund_no_status']);

        //Tin nhắn tùy chỉnh - hoàn tiền thất bại
        event('CustomNoticeListener', [$orderRefundInfo['uid'], $orderRefundInfo->toArray(), 'order_refund_fail']);

        //Sự kiện tùy chỉnh - trang quản trị từ chối hoàn tiền đơn hàng
        event('CustomEventListener', ['admin_order_refund_fail', [
            'uid' => $orderRefundInfo['uid'],
            'id' => $orderRefundInfo['id'],
            'store_order_id' => $orderRefundInfo['store_order_id'],
            'order_id' => $orderRefundInfo['order_id'],
            'refund_num' => $orderRefundInfo['refund_num'],
            'refund_price' => $orderRefundInfo['refund_price'],
            'refuse_reason' => $orderRefundInfo['refuse_reason'],
            'refuse_time' => date('Y-m-d H:i:s'),
        ]]);

        return true;
    }


    /**
     * Tạo form hoàn điểm thưởng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function refundIntegralForm(int $id)
    {
        if (!$orderInfo = $this->dao->get($id))
            throw new AdminException(400118);
        if ($orderInfo->use_integral < 0 || $orderInfo->use_integral == $orderInfo->back_integral)
            throw new AdminException(400496);
        if (!$orderInfo->paid)
            throw new AdminException(400497);
        $f[] = Form::input('order_id', 'Mã đơn hoàn tiền', $orderInfo->getData('order_id'))->disabled(1);
        $f[] = Form::number('use_integral', 'Điểm thưởng đã dùng', (float)$orderInfo->getData('use_integral'))->min(0)->disabled(1);
        $f[] = Form::number('use_integrals', 'Điểm thưởng đã hoàn', (float)$orderInfo->getData('back_integral'))->min(0)->disabled(1);
        $f[] = Form::number('back_integral', 'Điểm thưởng có thể hoàn', (float)bcsub($orderInfo->getData('use_integral'), $orderInfo->getData('back_integral')))->min(0)->precision(0)->required('Vui lòng nhập điểm thưởng có thể hoàn');
        return create_form('Hoàn điểm thưởng', $f, $this->url('/order/refund_integral/' . $id), 'PUT');
    }

    /**
     * Xử lý hoàn điểm thưởng riêng
     * @param $orderInfo
     * @param $back_integral
     */
    public function refundIntegral($orderInfo, $back_integral)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $integral = $userServices->value(['uid' => $orderInfo['uid']], 'integral');
        return $this->transaction(function () use ($userServices, $orderInfo, $back_integral, $integral) {
            $res1 = $userServices->bcInc($orderInfo['uid'], 'integral', $back_integral, 'uid');
            /** @var UserBillServices $userBillServices */
            $userBillServices = app()->make(UserBillServices::class);
            $res2 = $userBillServices->income('pay_product_integral_back', $orderInfo['uid'], (int)$back_integral, $integral + $back_integral, $orderInfo['id']);
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $res3 = $statusService->save([
                'oid' => $orderInfo['id'],
                'change_type' => 'integral_back',
                'change_message' => 'Hoàn điểm thưởng sản phẩm:' . $back_integral,
                'change_time' => time()
            ]);
            $res4 = $orderInfo->save();
            $res = $res1 && $res2 && $res3 && $res4;
            if (!$res) {
                throw new AdminException(400498);
            }
            return true;
        });
    }

    /**
     * Yêu cầu hoàn tiền đơn hàng
     * @param $uni
     * @param $uid
     * @param string $refundReasonWap
     * @param string $refundReasonWapExplain
     * @param array $refundReasonWapImg
     * @return bool|void
     */
    public function orderApplyRefund($order, string $refundReasonWap = '', string $refundReasonWapExplain = '', array $refundReasonWapImg = [], int $refundType = 0, $cart_id = 0, $refund_num = 0)
    {
        if (!$order) {
            throw new ApiException(410173);
        }
        if ($order['refund_status'] == 2) {
            throw new ApiException(410226);
        }
        if ($order['refund_status'] == 1) {
            throw new ApiException(410250);
        }
        if ($order['total_num'] < $refund_num) {
            throw new ApiException(410252);
        }
        $this->transaction(function () use ($order, $refundReasonWap, $refundReasonWapExplain, $refundReasonWapImg, $refundType, $refund_num, $cart_id) {
            $status = 0;
            $order_id = (int)$order['id'];
            if ($cart_id) {
                /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
                $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
                $cart_ids = [];
                $cart_ids[0] = ['cart_id' => $cart_id, 'cart_num' => $refund_num];
                /** @var StoreOrderSplitServices $storeOrderSplitServices */
                $storeOrderSplitServices = app()->make(StoreOrderSplitServices::class);
                //Tách đơn
                $status = $order['status'];
                $order = $storeOrderSplitServices->split($order_id, $cart_ids, $order);
            } elseif (in_array($order['pid'], [0, -1]) && $this->storeOrderServices->count(['pid' => $order_id])) {
                /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
                $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
                $cart_info = $storeOrderCartInfoServices->getSplitCartList($order_id, 'cart_info');
                if (!$cart_info) {
                    throw new ApiException(410253);
                }
                $cart_ids = [];
                foreach ($cart_info as $key => $cart) {
                    $cart_ids[$key] = ['cart_id' => $cart['id'], 'cart_num' => $refund_num];
                }
                /** @var StoreOrderSplitServices $storeOrderSplitServices */
                $storeOrderSplitServices = app()->make(StoreOrderSplitServices::class);
                //Tách đơn
                $status = $order['status'];
                $order = $storeOrderSplitServices->split($order_id, $cart_ids, $order);
            }

            $data = [
                'refund_status' => 1,
                'refund_reason_time' => time(),
                'refund_reason_wap' => $refundReasonWap,
                'refund_reason_wap_explain' => $refundReasonWapExplain,
                'refund_reason_wap_img' => json_encode($refundReasonWapImg),
                'refund_type' => $refundType
            ];
            if ($status) $data['status'] = $status;

            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $res1 = false !== $statusService->save([
                    'oid' => $order['id'],
                    'change_type' => 'apply_refund',
                    'change_message' => 'Người dùng yêu cầu hoàn tiền, lý do:' . $refundReasonWap,
                    'change_time' => time()
                ]);
            $res2 = false !== $this->storeOrderServices->update(['id' => $order['id']], $data);
            $res = $res1 && $res2;
            if (!$res)
                throw new ApiException(410254);
            //Đơn hàng con yêu cầu hoàn tiền
            if ($order['pid'] > 0) {
                $p_order = $this->storeOrderServices->get((int)$order['pid']);
                $split_order = $this->storeOrderServices->count(['pid' => $order['pid'], 'refund_status' => 0]);
                if ($split_order || (!$split_order && $p_order['status'] == 4) || $cart_id) {
                    $this->storeOrderServices->update(['id' => $order['pid']], ['refund_status' => 3, 'refund_reason_time' => time()]);
                } else {
                    $this->storeOrderServices->update(['id' => $order['pid']], ['refund_status' => 4, 'refund_reason_time' => time()]);
                }
            } else {
                /** @var StoreOrderCartInfoServices $orderCartInfoService */
                $orderCartInfoService = app()->make(StoreOrderCartInfoServices::class);
//                if (!$orderCartInfoService->getSplitCartList()) {
//
//                }
            }
        });

        try {
            ChannelService::instance()->send('NEW_REFUND_ORDER', ['order_id' => $order['order_id']]);
        } catch (\Exception $e) {
        }
        //Đẩy thông báo nhắc nhở
        event('NoticeListener', [['order' => $order], 'send_order_apply_refund']);

        return true;

    }


    /**
     * Ghi mã vận đơn hoàn trả
     * @param $order
     * @param $express
     * @return bool
     */
    public function editRefundExpress($data)
    {
        $this->transaction(function () use ($data) {
            $id = $data['id'];
            $data['refund_type'] = 5;
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $res1 = false !== $statusService->save([
                    'oid' => $id,
                    'change_type' => 'refund_express',
                    'change_message' => 'Người dùng đã trả hàng, mã vận đơn:' . $data['refund_express'],
                    'change_time' => time()
                ]);
            if ($data['refund_img'] != '') unset($data['refund_img']);
            if ($data['refund_explain'] != '') unset($data['refund_explain']);
            $res2 = false !== $this->dao->update(['id' => $id], $data);
            $res = $res1 && $res2;
            if (!$res)
                throw new ApiException(100018);
        });
        return true;
    }

    /**
     * Yêu cầu hoàn tiền đơn hàng
     * @param int $id
     * @param int $uid
     * @param array $order
     * @param array $cart_ids
     * @param int $refundType
     * @param float $refundPrice
     * @param array $refundData
     * @param int $isPink
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function applyRefund(int $id, int $uid, $order = [], array $cart_ids = [], int $refundType = 0, float $refundPrice = 0.00, array $refundData = [], $isPink = 0)
    {
        /** Truy vấn đơn hàng có tồn tại không */
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        if (!$order) {
            $order = $orderServices->get($id);
        }
        if (!$order) {
            throw new ApiException(410173);
        }

        $is_now = $this->dao->getCount([
            ['store_order_id', '=', $id],
            ['refund_type', 'in', [1, 2, 4, 5]],
            ['is_cancel', '=', 0],
            ['is_del', '=', 0],
            ['is_pink_cancel', '=', 0]
        ]);
        if ($is_now) throw new ApiException(410255);

        $refund_num = $order['total_num'];
        $refund_price = $order['pay_price'];
        /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
        $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
        //Hoàn tiền một phần
        $cartInfo = [];
        $cartInfos = $storeOrderCartInfoServices->getCartColunm(['oid' => $id], 'id,cart_id,cart_num,refund_num,cart_info');
        if ($cart_ids) {
            $cartInfo = array_combine(array_column($cartInfos, 'cart_id'), $cartInfos);
            $refund_num = 0;
            foreach ($cart_ids as $cart) {
                if ($cart['cart_num'] + $cartInfo[$cart['cart_id']]['refund_num'] > $cartInfo[$cart['cart_id']]['cart_num']) {
                    throw new ApiException(410252);
                }
                $refund_num = bcadd((string)$refund_num, (string)$cart['cart_num'], 0);
            }
            //Tổng số lượng yêu cầu
            $total_num = array_sum(array_column($cart_ids, 'cart_num'));
            if ($total_num < $order['total_num']) {
                /** @var StoreOrderSplitServices $storeOrderSpliteServices */
                $storeOrderSpliteServices = app()->make(StoreOrderSplitServices::class);
                $cartInfos = $storeOrderSpliteServices->getSplitOrderCartInfo($id, $cart_ids, $order);
                $total_price = $pay_postage = 0;
                foreach ($cartInfos as $cart) {
                    $_info = is_string($cart['cart_info']) ? json_decode($cart['cart_info'], true) : $cart['cart_info'];
                    $total_price = bcadd((string)$total_price, bcmul((string)($_info['truePrice'] ?? 0), (string)$cart['cart_num'], 4), 2);
                    $pay_postage = bcadd((string)$pay_postage, (string)($_info['postage_price'] ?? 0), 2);
                }
                $refund_pay_price = bcadd((string)$total_price, (string)$pay_postage, 2);
                //Số tiền thanh toán thực tế của đơn hàng
                $order_pay_price = bcsub((string)bcadd((string)$order['total_price'], (string)$order['pay_postage'], 2), (string)bcadd((string)$order['deduction_price'], (string)$order['coupon_price'], 2), 2);
                if ($order_pay_price != $order['pay_price'] && $refund_pay_price != $order_pay_price) {//Có đổi giá
                    $refund_price = bcmul((string)bcdiv((string)$refund_pay_price, (string)$order_pay_price, 4), (string)$order['pay_price'], 2);
                } else {
                    $refund_price = $refund_pay_price;
                }
            }
        } else {
            foreach ($cartInfos as $cart) {
                if ($cart['refund_num'] > 0) {
                    throw new ApiException(410252);
                }
            }
        }
        foreach ($cartInfos as &$cart) {
            $cart['cart_info'] = is_string($cart['cart_info']) ? json_decode($cart['cart_info'], true) : $cart['cart_info'];
        }
        $refundData['uid'] = $uid;
        $refundData['store_id'] = $order['store_id'];
        $refundData['store_order_id'] = $id;
        $refundData['refund_num'] = $refund_num;
        $refundData['refund_type'] = $refundType;
        $refundData['refund_price'] = $refund_price;
        $refundData['order_id'] = $order['refund_no'] = app()->make(StoreOrderCreateServices::class)->getNewOrderId('');
        $refundData['add_time'] = time();
        $refundData['cart_info'] = json_encode(array_column($cartInfos, 'cart_info'));
        $refundData['is_pink_cancel'] = $isPink;
        $res = $this->transaction(function () use ($id, $order, $cart_ids, $refundData, $storeOrderCartInfoServices, $cartInfo, $orderServices, $cartInfos) {
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $res1 = false !== $statusService->save([
                    'oid' => $order['id'],
                    'change_type' => 'apply_refund',
                    'change_message' => 'Người dùng yêu cầu hoàn tiền, lý do:' . $refundData['refund_reason'],
                    'change_time' => time()
                ]);
            $res2 = true;
            //Thêm dữ liệu hoàn tiền
            /** @var StoreOrderRefundServices $storeOrderRefundServices */
            $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
            $res3 = $storeOrderRefundServices->save($refundData);
            if (!$res3) {
                throw new ApiException(410251);
            }
            $res4 = true;
            if ($cart_ids) {
                //Sửa thông tin hoàn tiền sản phẩm trong đơn hàng
                foreach ($cart_ids as $cart) {
                    $res4 = $res4 && $storeOrderCartInfoServices->update(['oid' => $id, 'cart_id' => $cart['cart_id']], ['refund_num' => (($cartInfo[$cart['cart_id']]['refund_num'] ?? 0) + $cart['cart_num'])]);
                }
            } else {
                foreach ($cartInfos as $cart) {
                    $res4 = $res4 && $storeOrderCartInfoServices->update(['oid' => $id, 'cart_id' => $cart['cart_id']], ['refund_num' => $cart['cart_num']]);
                }
            }
            return $res1 && $res2 && $res3 && $res4;
        });
        $storeOrderCartInfoServices->clearOrderCartInfo($order['id']);
        //Sự kiện yêu cầu hoàn tiền
        event('OrderRefundCreateAfterListener', [$order]);
        //Đẩy thông báo nhắc nhở
        event('NoticeListener', [['order' => $order], 'send_order_apply_refund']);
        //Đẩy đơn hàng
        event('OutPushListener', ['refund_create_push', ['order_id' => (int)$order['id']]]);

        //Sự kiện tùy chỉnh - đơn hàng yêu cầu hoàn tiền
        event('CustomEventListener', ['order_initiated_refund', [
            'uid' => $uid,
            'refund_order_id' => $refundData['order_id'],
            'order_id' => $order['order_id'],
            'real_name' => $order['real_name'],
            'user_phone' => $order['user_phone'],
            'user_address' => $order['user_address'],
            'refund_num' => $refundData['refund_num'],
            'refund_price' => $refundData['refund_price'],
            'refund_time' => date('Y-m-d H:i:s', $refundData['add_time']),
        ]]);

        try {
            ChannelService::instance()->send('NEW_REFUND_ORDER', ['order_id' => $order['order_id']]);
        } catch (\Exception $e) {
        }
        return $res;
    }

    /**
     * Lấy tổng số tiền của một trường
     * @param $cartInfo
     * @param string $key
     * @param bool $is_unit
     * @return int|string
     */
    public function getOrderSumPrice($cartInfo, $key = 'truePrice', $is_unit = true)
    {
        $SumPrice = 0;
        foreach ($cartInfo as $cart) {
            if (isset($cart['cart_info'])) $cart = $cart['cart_info'];
            if ($is_unit) {
                if ($key == 'level' || $key == 'member') {
                    if ($cart['price_type'] == $key) {
                        $SumPrice = bcadd($SumPrice, bcmul($cart['cart_num'] ?? 1, $cart['vip_truePrice'], 2), 2);
                    }
                } else {
                    $SumPrice = bcadd($SumPrice, bcmul($cart['cart_num'] ?? 1, $cart[$key] ?? 0, 2), 2);
                }
            } else {
                $SumPrice = bcadd($SumPrice, $cart[$key] ?? 0, 2);
            }
        }
        return $SumPrice;
    }

    /**
     * Danh sách đơn hoàn tiền
     * @param $where
     * @return array
     */
    public function refundList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        $count = $this->dao->count($where);
        $orderInfoList = $this->storeOrderServices->getColumn([['id', 'in', array_column($list, 'store_order_id')]], 'order_id,status', 'id');
        $store_order_status = [-2 => 'Đã hoàn tiền', -1 => 'Đang hoàn tiền', 'Chưa giao hàng', 'Chờ nhận hàng', 'Chờ đánh giá', 'Đã hoàn thành'];
        if ($list) {
            foreach ($list as &$item) {
                $item['paid'] = 1;
                $item['add_time'] = $item['_add_time'] = isset($item['add_time']) ? date('Y-m-d H:i:s', (int)$item['add_time']) : '';
                $item['cartInfo'] = $item['cart_info'];
                if (in_array($item['refund_type'], [1, 2, 4, 5])) {
                    $item['refund_status'] = 1;
                } elseif ($item['refund_type'] == 6) {
                    $item['refund_status'] = 2;
                } elseif ($item['refund_type'] == 3) {
                    $item['refund_status'] = 3;
                }
                foreach ($item['cart_info'] as $items) {
                    $item['_info'][]['cart_info'] = $items;
                }
                $item['total_num'] = $item['refund_num'];
                $item['pay_price'] = $item['refund_price'];
                $item['pay_postage'] = floatval($this->getOrderSumPrice($item['cart_info'], 'postage_price', false));

                if (in_array($item['refund_type'], [1, 2, 4, 5])) {
                    $_type = -1;
                    $_title = 'Đang yêu cầu hoàn tiền';
                } elseif ($item['refund_type'] == 3) {
                    $_type = -3;
                    $_title = 'Từ chối hoàn tiền';
                } else {
                    $_type = -2;
                    $_title = 'Đã hoàn tiền';
                }
                $item['_status'] = [
                    '_type' => $_type,
                    '_title' => $_title,
                ];
                $item['store_order_order_id'] = $orderInfoList[$item['store_order_id']]['order_id'] ?? '';
                $item['store_order_status'] = $store_order_status[$orderInfoList[$item['store_order_id']]['status'] ?? -3] ?? '';
            }
        }
        $data['list'] = $list;
        $data['count'] = $count;
        $del_where = ['is_cancel' => 0];
        $data['num'] = [
            0 => ['name' => 'Tất cả', 'num' => $this->dao->count($del_where)],
            1 => ['name' => 'Chỉ hoàn tiền', 'num' => $this->dao->count($del_where + ['refund_type' => 1])],
            2 => ['name' => 'Trả hàng hoàn tiền', 'num' => $this->dao->count($del_where + ['refund_type' => 2])],
            3 => ['name' => 'Từ chối hoàn tiền', 'num' => $this->dao->count($del_where + ['refund_type' => 3])],
            4 => ['name' => 'Chờ trả hàng', 'num' => $this->dao->count($del_where + ['refund_type' => 4])],
            5 => ['name' => 'Chờ nhận hàng trả', 'num' => $this->dao->count($del_where + ['refund_type' => 5])],
            6 => ['name' => 'Đã hoàn tiền', 'num' => $this->dao->count($del_where + ['refund_type' => 6])]
        ];
        return $data;
    }

    /**
     * Chi tiết đơn hoàn tiền
     * @param $uni
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/17
     */
    public function refundDetail($uni)
    {
        if (!strlen(trim($uni))) throw new ApiException(100100);
        $order = $this->dao->get(['order_id' => $uni], ['*']);
        if (!$order) throw new ApiException(410173);
        $order = $order->toArray();

        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $orderInfo = $orderServices->get($order['store_order_id']);

        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($order['uid']);

        $order['mapKey'] = sys_config('tengxun_map_key');
        $order['yue_pay_status'] = (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1 ? (int)1 : (int)2;//Thanh toán bằng số dư: 1 là bật, 2 là tắt
        $order['pay_weixin_open'] = (int)sys_config('pay_weixin_open') ?? 0;//WeChat Pay: 1 là bật, 0 là tắt
        $order['ali_pay_status'] = sys_config('ali_pay_status') ? true : false;//Thanh toán Alipay, 1 là mở, 0 là tắt
        $orderData = $order;
        $orderData['store_order_sn'] = $orderInfo['order_id'];
        $orderData['cartInfo'] = $orderData['cart_info'];
        $orderData['_pay_time'] = date('Y-m-d H:i:s', $orderInfo['pay_time']);
        $orderData['type'] = 0;
        if ($orderInfo['seckill_id'] || $orderInfo['bargain_id'] || $orderInfo['combination_id']) {
            if ($orderInfo['seckill_id']) $orderData['type'] = 1;
            if ($orderInfo['bargain_id']) $orderData['type'] = 2;
            if ($orderInfo['combination_id']) $orderData['type'] = 3;
        }
        //Tính toán số tiền ưu đãi
        $vipTruePrice = 0;
        $total_price = 0;
        $pay_postage = '0';
        foreach ($orderData['cartInfo'] ?? [] as $key => &$cart) {
            if (!isset($cart['sum_true_price'])) $cart['sum_true_price'] = bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
            $cart['vip_sum_truePrice'] = bcmul($cart['vip_truePrice'], $cart['cart_num'] ? $cart['cart_num'] : 1, 2);
            $vipTruePrice = bcadd((string)$vipTruePrice, (string)$cart['vip_sum_truePrice'], 2);
            if (isset($order['split']) && $order['split']) {
                $orderData['cartInfo'][$key]['cart_num'] = $cart['surplus_num'];
                if (!$cart['surplus_num']) unset($orderData['cartInfo'][$key]);
            }
            $total_price = bcadd($total_price, $cart['sum_true_price'], 2);
            $pay_postage = bcadd($cart['postage_price'], $pay_postage, 2);
        }
        $orderData['use_integral'] = $this->getOrderSumPrice($orderData['cartInfo'], 'use_integral', false);
        $orderData['integral_price'] = $this->getOrderSumPrice($orderData['cartInfo'], 'integral_price', false);
        $orderData['coupon_price'] = $this->getOrderSumPrice($orderData['cartInfo'], 'coupon_price', false);
        $orderData['deduction_price'] = $this->getOrderSumPrice($orderData['cartInfo'], 'integral_price', false);

        $total_price = bcadd((string)$total_price, (string)bcadd((string)$orderData['deduction_price'], (string)$orderData['coupon_price'], 2), 2);
        $orderData['vip_true_price'] = $vipTruePrice;
        $orderData['postage_price'] = 0;
        $orderData['pay_postage'] = $this->getOrderSumPrice($orderData['cart_info'], 'origin_postage_price', false);
        $orderData['member_price'] = 0;
        $orderData['routine_contact_type'] = sys_config('routine_contact_type', 0);
        $orderData['levelPrice'] = $this->getOrderSumPrice($orderData['cart_info'], 'level');//Lấy giảm giá theo hạng thành viên
        $orderData['memberPrice'] = $this->getOrderSumPrice($orderData['cart_info'], 'member');//Lấy giảm giá thành viên trả phí
        $orderData['pay_type'] = $orderInfo['pay_type'];

        switch ($orderInfo['pay_type']) {
            case PayServices::WEIXIN_PAY:
                $pay_type_name = 'WeChat Pay';
                break;
            case PayServices::YUE_PAY:
                $pay_type_name = 'Thanh toán bằng số dư';
                break;
            case PayServices::OFFLINE_PAY:
                $pay_type_name = 'Thanh toán ngoại tuyến';
                break;
            case PayServices::ALIAPY_PAY:
                $pay_type_name = 'Thanh toán Alipay';
                break;
            case PayServices::ALLIN_PAY:
                $pay_type_name = 'Allinpay';
                break;
            default:
                $pay_type_name = 'Thanh toán khác';
                break;
        }
        $orderData['_add_time'] = date('Y-m-d H:i:s', $orderData['add_time']);
        $orderData['add_time_y'] = date('Y-m-d', $orderData['add_time']);
        $orderData['add_time_h'] = date('H:i:s', $orderData['add_time']);
        if (in_array($orderData['refund_type'], [1, 2, 4, 5])) {
            $_type = -1;
            $_msg = 'Người bán đang xét duyệt, vui lòng kiên nhẫn chờ đợi';
            $_title = 'Đang yêu cầu hoàn tiền';
        } elseif ($orderData['refund_type'] == 3) {
            $_type = -3;
            $_title = 'Từ chối hoàn tiền';
            $_msg = 'Người bán từ chối hoàn tiền, vui lòng liên hệ người bán';
        } else {
            $_type = -2;
            $_title = 'Đã hoàn tiền';
            $_msg = 'Đã hoàn tiền cho bạn, cảm ơn bạn đã ủng hộ';
        }
        $refund_name = sys_config('refund_name', '');
        $refund_phone = sys_config('refund_phone', '');
        $refund_address = sys_config('refund_address', '');
        $orderData['_status'] = [
            '_type' => $_type,
            '_title' => $_title,
            '_msg' => $_msg ?? '',
            '_payType' => $pay_type_name,
            'refund_name' => $refund_name,
            'refund_phone' => $refund_phone,
            'refund_address' => $refund_address,
        ];
        $orderData['real_name'] = $orderInfo['real_name'];
        $orderData['user_phone'] = $orderInfo['user_phone'];
        $orderData['user_address'] = $orderInfo['user_address'];
        $orderData['nickname'] = $userInfo['nickname'] ?? '';
        $orderData['total_num'] = $orderData['refund_num'];
        $orderData['pay_price'] = $orderData['refund_price'];
        $orderData['refund_status'] = in_array($orderData['refund_type'], [1, 2, 4, 5]) ? 1 : 2;
        $orderData['total_price'] = $total_price;
        $orderData['paid'] = 1;
        $orderData['mark'] = $orderData['refund_explain'];
        $orderData['express_list'] = $orderData['refund_type'] == 4 ? app()->make(ExpressServices::class)->expressList(['is_show' => 1]) : [];
        $orderData['custom_form'] = [];
        $orderData['help_info'] = [
            'pay_uid' => $orderInfo['pay_uid'],
            'pay_nickname' => '',
            'pay_avatar' => '',
            'help_status' => 0
        ];
        if ($orderInfo['uid'] != $orderInfo['pay_uid']) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $payUser = $userServices->get($orderInfo['pay_uid']);
            $orderData['help_info'] = [
                'pay_uid' => $orderInfo['pay_uid'],
                'pay_nickname' => $payUser['nickname'],
                'pay_avatar' => $payUser['avatar'],
                'help_status' => 1
            ];
        }
        $orderData['pay_postage'] = $pay_postage;
        return $orderData;
    }

    /**
     * Hủy yêu cầu, trang quản trị từ chối thì xử lý dữ liệu cart_info refund_num
     * @param int $id
     * @param int $oid
     * @param array $orderRefundInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cancelOrderRefundCartInfo(int $id, int $oid, $orderRefundInfo = [], string $title = '')
    {
        if (!$orderRefundInfo) {
            $orderRefundInfo = $this->dao->get(['id' => $id, 'is_cancel' => 0]);
        }
        if (!$orderRefundInfo) {
            throw new ApiException(410173);
        }
        $cart_ids = array_column($orderRefundInfo['cart_info'], 'id');
        /** @var StoreOrderCartInfoServices $storeOrderCartInfoServices */
        $storeOrderCartInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $cartInfos = $storeOrderCartInfoServices->getColumn([['oid', '=', $oid], ['cart_id', 'in', $cart_ids]], 'cart_id,refund_num', 'cart_id');
        foreach ($orderRefundInfo['cart_info'] as $cart) {
            $cart_refund_num = $cartInfos[$cart['id']]['refund_num'] ?? 0;
            if ($cart['cart_num'] >= $cart_refund_num) {
                $refund_num = 0;
            } else {
                $refund_num = bcsub((string)$cart_refund_num, (string)$cart['cart_num'], 0);
            }
            $storeOrderCartInfoServices->update(['oid' => $oid, 'cart_id' => $cart['id']], ['refund_num' => $refund_num]);
        }
        $storeOrderCartInfoServices->clearOrderCartInfo($oid);

        //Ghi vào bảng lịch sử đơn hàng
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $oid,
            'change_type' => 'cancel_refund_order',
            'change_message' => $title ?: 'Hủy hoàn tiền',
            'change_time' => time()
        ]);

        //Sự kiện sau khi hủy đơn hậu mãi
        event('OrderRefundCancelAfterListener', [$orderRefundInfo]);
        // Đẩy đơn hàng
        event('OutPushListener', ['refund_cancel_push', ['order_id' => (int)$orderRefundInfo['id']]]);
        return true;
    }

    /**
     * Sửa ghi chú
     * @param int $id
     * @param string $remark
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateRemark(int $id, string $remark)
    {
        if (!$id) {
            throw new AdminException(100100);
        }
        if (!$remark) {
            throw new AdminException(410177);
        }

        if (!$order = $this->dao->get($id)) {
            throw new AdminException(410173);
        }
        $order->remark = $remark;
        if (!$order->save()) {
            throw new AdminException(100025);
        }
        return true;
    }

    /**
     * Từ chối hoàn tiền
     * @param int $id
     * @param string $refund_reason
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refuse(int $id, string $refund_reason)
    {
        if (!$refund_reason) {
            throw new AdminException(400499);
        }

        if (!$id || !($orderRefundInfo = $this->dao->get($id))) {
            throw new AdminException(400118);
        }

        $refundData = [
            'refuse_reason' => $refund_reason,
            'refund_type' => 3,
            'refunded_time' => time()
        ];
        //Xử lý từ chối hoàn tiền
        $this->refuseRefund($id, $refundData, $orderRefundInfo);
    }
}
