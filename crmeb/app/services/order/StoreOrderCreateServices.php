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

namespace app\services\order;


use app\services\activity\advance\StoreAdvanceServices;
use app\services\activity\combination\StorePinkServices;
use app\services\agent\AgentLevelServices;
use app\services\activity\coupon\StoreCouponUserServices;
use app\services\agent\DivisionServices;
use app\services\product\product\StoreCategoryServices;
use app\services\shipping\ShippingTemplatesFreeServices;
use app\services\shipping\ShippingTemplatesRegionServices;
use app\services\shipping\ShippingTemplatesServices;
use app\services\user\UserInvoiceServices;
use app\services\wechat\WechatUserServices;
use app\services\BaseServices;
use crmeb\exceptions\ApiException;
use crmeb\exceptions\ApiStatusException;
use crmeb\services\CacheService;
use app\dao\order\StoreOrderDao;
use app\services\user\UserServices;
use app\services\user\UserBillServices;
use app\services\user\UserAddressServices;
use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\system\store\SystemStoreServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\product\product\StoreProductServices;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;

/**
 * Tạo đơn hàng
 * Class StoreOrderCreateServices
 * @package app\services\order
 */
class StoreOrderCreateServices extends BaseServices
{
    /**
     * StoreOrderCreateServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Dùng thuật toán Snowflake để tạo ID đơn hàng
     * @return string
     * @throws \Exception
     */
    public function getNewOrderId(string $prefix = 'wx')
    {
        $snowflake = new \Godruoyi\Snowflake\Snowflake();

        if (Config::get('cache.default') == 'file') {
            //32-bit
            if (PHP_INT_SIZE == 4) {
                $id = abs($snowflake->id());
            } else {
                $id = $snowflake->setStartTimeStamp(strtotime('2022-01-01') * 1000)->id();
            }
            $replace = '';
            $chars = '0123456789';
            for ($i = 0; $i < 6; $i++) {
                $replace .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            $id = substr_replace($id, $replace, -6);
        } else {
            $is_callable = function ($currentTime) {
                $redis = Cache::store('redis');
                $swooleSequenceResolver = new \Godruoyi\Snowflake\RedisSequenceResolver($redis->handler());
                return $swooleSequenceResolver->sequence($currentTime);
            };
            //32-bit
            if (PHP_INT_SIZE == 4) {
                $id = abs($snowflake->setSequenceResolver($is_callable)->id());
            } else {
                $id = $snowflake->setStartTimeStamp(strtotime('2022-01-01') * 1000)->setSequenceResolver($is_callable)->id();
            }
        }

        return $prefix . $id;
    }

    /**
     * Tạo mã xác nhận cho đơn hàng cần xác nhận sử dụng
     * @return false|string
     */
    public function getStoreCode()
    {
        list($msec, $sec) = explode(' ', microtime());
        $num = time() + mt_rand(10, 999999) . '' . substr($msec, 2, 3);//Tạo số ngẫu nhiên
        if (strlen($num) < 12)
            $num = str_pad((string)$num, 12, 0, STR_PAD_RIGHT);
        else
            $num = substr($num, 0, 12);
        if ($this->dao->count(['verify_code' => $num])) {
            return $this->getStoreCode();
        }
        return $num;
    }

    /**
     * Tạo đơn hàng
     * @param $uid
     * @param $key
     * @param $userInfo
     * @param $addressId
     * @param $payType
     * @param false $useIntegral
     * @param int $couponId
     * @param string $mark
     * @param int $combinationId
     * @param int $pinkId
     * @param int $seckillId
     * @param int $bargainId
     * @param int $shippingType
     * @param string $real_name
     * @param string $phone
     * @param int $storeId
     * @param false $news
     * @param int $advanceId
     * @param array $customForm
     * @param int $invoice_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/03/01
     */
    public function createOrder($uid, $key, $userInfo, $addressId, $payType, $useIntegral = false, $couponId = 0, $mark = '', $combinationId = 0, $pinkId = 0, $seckillId = 0, $bargainId = 0, $shippingType = 1, $real_name = '', $phone = '', $storeId = 0, $news = false, $advanceId = 0, $customForm = [], $invoice_id = 0, $is_gift = 0, $gift_mark = '')
    {
        /** @var StoreOrderServices $orderService */
        $storeOrderServices = app()->make(StoreOrderServices::class);
        $bargainServices = app()->make(StoreBargainServices::class);
        $cartGroup = $storeOrderServices->getCacheOrderInfo($uid, $key);
        if (!$cartGroup) {
            throw new ApiException('Đơn hàng đã hết hạn, vui lòng tải lại trang hiện tại');
        }
        //Kiểm tra săn giảm giá trước khi đặt hàng
        if ($bargainId) {
            $bargainServices->checkBargainUser((int)$bargainId, $uid);
        }

        if ($pinkId) {
            $pinkId = (int)$pinkId;
            /** @var StorePinkServices $pinkServices */
            $pinkServices = app()->make(StorePinkServices::class);
            if ($pinkServices->isPink($pinkId, $uid))
                throw new ApiStatusException('ORDER_EXIST', 'Tạo đơn hàng thất bại, bạn đã ở trong nhóm mua chung này, không thể tham gia lại', ['orderId' => $storeOrderServices->getStoreIdPink($pinkId, $uid)]);
            if ($storeOrderServices->getIsOrderPink($pinkId, $uid))
                throw new ApiStatusException('ORDER_EXIST', 'Tạo đơn hàng thất bại, bạn đã tham gia nhóm mua chung này, vui lòng thanh toán đơn hàng trước', ['orderId' => $storeOrderServices->getStoreIdPink($pinkId, $uid)]);
        }
        $virtual_type = $cartGroup['cartInfo'][0]['productInfo']['virtual_type'] ?? 0;

        //Kiểm tra hóa đơn trước khi đặt hàng
        if ($invoice_id) {
            app()->make(UserInvoiceServices::class)->checkInvoice((int)$invoice_id, $uid);
        }

        /** @var StoreOrderComputedServices $computedServices */
        $computedServices = app()->make(StoreOrderComputedServices::class);
        $priceData = $computedServices->computedOrder($uid, $userInfo, $cartGroup, $addressId, $payType, $useIntegral, $couponId, true, $shippingType, $is_gift);
        /** @var WechatUserServices $wechatServices */
        $wechatServices = app()->make(WechatUserServices::class);
        /** @var UserAddressServices $addressServices */
        $addressServices = app()->make(UserAddressServices::class);
        if ($is_gift == 0) {
            if ($shippingType == 1 && $virtual_type == 0) {
                if (!$addressId) {
                    throw new ApiException('Vui lòng chọn địa chỉ nhận hàng');
                }
                if (!$addressInfo = $addressServices->getOne(['uid' => $uid, 'id' => $addressId, 'is_del' => 0]))
                    throw new ApiException('Địa chỉ đã chọn không hợp lệ');
                $addressInfo = $addressInfo->toArray();
            } else {
                if ((!$real_name || !$phone) && $virtual_type == 0) {
                    throw new ApiException('Vui lòng nhập họ tên và số điện thoại');
                }
                $addressInfo['real_name'] = $real_name;
                $addressInfo['phone'] = $phone;
                $addressInfo['province'] = '';
                $addressInfo['city'] = '';
                $addressInfo['district'] = '';
                $addressInfo['detail'] = '';
            }
        } else {
            $addressInfo['real_name'] = '';
            $addressInfo['phone'] = '';
            $addressInfo['province'] = '';
            $addressInfo['city'] = '';
            $addressInfo['district'] = '';
            $addressInfo['detail'] = '';
        }

        $cartInfo = $cartGroup['cartInfo'];
        $priceGroup = $cartGroup['priceGroup'];
        $cartIds = [];
        $totalNum = 0;
        $gainIntegral = 0;
        foreach ($cartInfo as $cart) {
            $cartIds[] = $cart['id'];
            $totalNum += $cart['cart_num'];
            if (!$seckillId) $seckillId = $cart['seckill_id'];
            if (!$bargainId) $bargainId = $cart['bargain_id'];
            if (!$combinationId) $combinationId = $cart['combination_id'];
            if (!$advanceId) $advanceId = $cart['advance_id'];
            $cartInfoGainIntegral = isset($cart['productInfo']['give_integral']) ? bcmul((string)$cart['cart_num'], (string)$cart['productInfo']['give_integral'], 0) : 0;
            $gainIntegral = bcadd((string)$gainIntegral, (string)$cartInfoGainIntegral, 0);
        }
        if (count($cartInfo) == 1 && isset($cartInfo[0]['productInfo']['presale']) && $cartInfo[0]['productInfo']['presale'] == 1) {
            $advance_id = $cartInfo[0]['product_id'];
        } else {
            $advance_id = 0;
        }
        $deduction = $seckillId || $bargainId || $combinationId;
        if ($deduction) {
            $couponId = 0;
            $gainIntegral = 0;
            $useIntegral = false;
        }
        //$shipping_type = 1 giao hàng qua vận chuyển, $shipping_type = 2 nhận tại cửa hàng
        $storeSelfMention = sys_config('store_self_mention') ?? 0;
        if (!$storeSelfMention) $shippingType = 1;
        if ($is_gift == 1) $shippingType = 0;

        $orderInfo = [
            'uid' => $uid,
            'order_id' => $this->getNewOrderId('cp'),
            'real_name' => $addressInfo['real_name'],
            'user_phone' => $addressInfo['phone'],
            'user_address' => $addressInfo['province'] . ' ' . $addressInfo['city'] . ' ' . $addressInfo['district'] . ' ' . $addressInfo['detail'],
            'cart_id' => $cartIds,
            'total_num' => $totalNum,
            'total_price' => $priceGroup['totalPrice'],
            'total_postage' => $shippingType == 1 ? $priceGroup['storePostage'] : 0,
            'coupon_id' => $couponId,
            'coupon_price' => $priceData['coupon_price'],
            'pay_price' => $priceData['pay_price'],
            'pay_postage' => $priceData['pay_postage'],
            'deduction_price' => $priceData['deduction_price'],
            'gift_price' => $priceData['gift_price'],
            'paid' => 0,
            'pay_type' => $payType,
            'use_integral' => $priceData['usedIntegral'],
            'gain_integral' => $gainIntegral,
            'mark' => htmlspecialchars($mark),
            'combination_id' => $combinationId,
            'pink_id' => $pinkId,
            'seckill_id' => $seckillId,
            'bargain_id' => $bargainId,
            'advance_id' => $advance_id,
            'cost' => $priceGroup['costPrice'],
            'add_time' => time(),
            'unique' => $key,
            'shipping_type' => $shippingType,
            'channel_type' => $userInfo['user_type'],
            'province' => strval($userInfo['user_type'] == 'wechat' || $userInfo['user_type'] == 'routine' ? $wechatServices->value(['uid' => $uid, 'user_type' => $userInfo['user_type']], 'province') : ''),
            'spread_uid' => 0,
            'spread_two_uid' => 0,
            'virtual_type' => $virtual_type,
            'is_gift' => $is_gift,
            'gift_mark' => $gift_mark,
            'pay_uid' => $uid,
            'custom_form' => json_encode($customForm),
            'division_id' => $userInfo['division_id'],
            'agent_id' => $userInfo['agent_id'],
            'staff_id' => $userInfo['staff_id'],
        ];

        if ($shippingType == 2) {
            $orderInfo['verify_code'] = $this->getStoreCode();
            /** @var SystemStoreServices $storeServices */
            $storeServices = app()->make(SystemStoreServices::class);
            $orderInfo['store_id'] = $storeServices->getStoreDispose($storeId, 'id');
            if (!$orderInfo['store_id']) {
                throw new ApiException('Chưa có cửa hàng, không thể chọn nhận tại cửa hàng');
            }
        }
        /** @var StoreOrderCartInfoServices $cartServices */
        $cartServices = app()->make(StoreOrderCartInfoServices::class);
        $priceData['coupon_id'] = $couponId;
        $order = $this->transaction(function () use ($cartIds, $orderInfo, $cartInfo, $key, $userInfo, $useIntegral, $priceData, $combinationId, $seckillId, $bargainId, $cartServices, $uid, $addressId, $advanceId) {
            //Tạo đơn hàng
            $order = $this->dao->save($orderInfo);
            if (!$order) {
                throw new ApiException('Tạo đơn hàng thất bại');
            }
            //Ghi lại số điện thoại và họ tên người nhận tại cửa hàng
            /** @var UserServices $userService */
            $userService = app()->make(UserServices::class);
            $realName = $userService->value(['uid' => $uid], 'real_name');
            if ($realName == '') $userService->update(['uid' => $uid], ['real_name' => $orderInfo['real_name'], 'record_phone' => $orderInfo['user_phone']]);
            //Khấu trừ bằng điểm thưởng
            if ($priceData['usedIntegral'] > 0) {
                $this->deductIntegral($userInfo, $useIntegral, $priceData, (int)$userInfo['uid'], $order['id']);
            }
            //Trừ tồn kho
            $this->decGoodsStock($cartInfo, $combinationId, $seckillId, $bargainId, $advanceId);
            //Lưu thông tin sản phẩm trong giỏ hàng
            $cartServices->setCartInfo($order['id'], $uid, $cartInfo);
            return $order;
        });

        //Tạo dữ liệu xuất hóa đơn
        if ($invoice_id) {
            app()->make(StoreOrderInvoiceServices::class)->makeUp($uid, $order['order_id'], (int)$invoice_id);
        }

        // Sự kiện sau khi tạo đơn hàng thành công
        event('OrderCreateAfterListener', [$order, compact('cartInfo', 'priceData', 'addressId', 'cartIds', 'news'), $uid, $key, $combinationId, $seckillId, $bargainId]);
        // Đẩy đơn hàng
        event('OutPushListener', ['order_create_push', ['order_id' => (int)$order['id']]]);

        //Sự kiện tùy chỉnh - sự kiện tạo đơn hàng
        event('CustomEventListener', ['order_create', [
            'uid' => $uid,
            'id' => (int)$order['id'],
            'order_id' => $order['order_id'],
            'real_name' => $order['real_name'],
            'user_phone' => $order['user_phone'],
            'user_address' => $order['user_address'],
            'total_num' => $order['total_num'],
            'pay_price' => $order['pay_price'],
            'pay_postage' => $order['pay_postage'],
            'deduction_price' => $order['deduction_price'],
            'coupon_price' => $order['coupon_price'],
            'store_name' => app()->make(StoreOrderCartInfoServices::class)->getCarIdByProductTitle((int)$order['id']),
            'add_time' => date('Y-m-d H:i:s', $order['add_time']),
        ]]);

        return $order;
    }


    /**
     * Khấu trừ điểm thưởng
     * @param array $userInfo
     * @param bool $useIntegral
     * @param array $priceData
     * @param int $uid
     * @param string $key
     */
    public function deductIntegral(array $userInfo, bool $useIntegral, array $priceData, int $uid, $orderId)
    {
        $res2 = true;
        if ($useIntegral && $userInfo['integral'] > 0) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            if (!$priceData['SurplusIntegral']) {
                $res2 = false !== $userServices->update($uid, ['integral' => 0]);
            } else {
                $res2 = false !== $userServices->bcDec($userInfo['uid'], 'integral', $priceData['usedIntegral'], 'uid');
            }
            /** @var UserBillServices $userBillServices */
            $userBillServices = app()->make(UserBillServices::class);
            $res3 = $userBillServices->income('deduction', $uid, [
                'number' => $priceData['usedIntegral'],
                'deductionPrice' => $priceData['deduction_price']
            ], $userInfo['integral'] - $priceData['usedIntegral'], $orderId);

            $res2 = $res2 && false != $res3;
        }
        if (!$res2) {
            throw new ApiException('Dùng điểm thưởng để khấu trừ thất bại');
        }
    }

    /**
     * Trừ tồn kho
     * @param array $cartInfo
     * @param int $combinationId
     * @param int $seckillId
     * @param int $bargainId
     */
    public function decGoodsStock(array $cartInfo, int $combinationId, int $seckillId, int $bargainId, int $advanceId)
    {
        $res5 = true;
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
        try {
            foreach ($cartInfo as $cart) {
                //Giảm tồn kho, tăng lượt bán
                if ($combinationId) $res5 = $res5 && $pinkServices->decCombinationStock((int)$cart['cart_num'], $combinationId, isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
                else if ($seckillId) $res5 = $res5 && $seckillServices->decSeckillStock((int)$cart['cart_num'], $seckillId, isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
                else if ($bargainId) $res5 = $res5 && $bargainServices->decBargainStock((int)$cart['cart_num'], $bargainId, isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
                else if ($advanceId) $res5 = $res5 && $advanceServices->decAdvanceStock((int)$cart['cart_num'], $advanceId, isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
                else $res5 = $res5 && $services->decProductStock((int)$cart['cart_num'], (int)$cart['productInfo']['id'], isset($cart['productInfo']['attrInfo']) ? $cart['productInfo']['attrInfo']['unique'] : '');
            }
            if (!$res5) {
                throw new ApiException('Phân loại đã chọn không đủ tồn kho');
            }
        } catch (\Throwable $e) {
            throw new ApiException('Phân loại đã chọn không đủ tồn kho');
        }
    }

    /**
     * Sau khi tạo dữ liệu đơn hàng: tính số tiền thực tế sản phẩm, tính hoa hồng, tính chiết khấu ưu đãi, thiết lập địa chỉ mặc định, dọn giỏ hàng
     * @param $order
     * @param array $group
     * @param $activity
     */
    public function orderCreateAfter($order, array $group, $activity)
    {
        /** @var UserAddressServices $addressServices */
        $addressServices = app()->make(UserAddressServices::class);
        //Đặt địa chỉ mặc định của người dùng
        if (!$addressServices->be(['is_default' => 1, 'uid' => $order['uid']])) {
            $addressServices->setDefaultAddress($group['addressId'], $order['uid']);
            $province = $addressServices->value(['id' => $group['addressId']], 'province') ?? '';
            app()->make(WechatUserServices::class)->update(['uid' => $order['uid']], ['province' => $province]);
        }
        //Xóa giỏ hàng
        if ($group['news']) {
            array_map(function ($key) {
                CacheService::delete($key);
            }, $group['cartIds']);
        } else {
            /** @var StoreCartServices $cartServices */
            $cartServices = app()->make(StoreCartServices::class);
            $cartServices->deleteCartStatus($group['cartIds']);
        }
        $uid = (int)$order['uid'];
        $orderId = (int)$order['id'];
        try {
            $cartInfo = $group['cartInfo'] ?? [];
            $priceData = $group['priceData'] ?? [];
            $addressId = $group['addressId'] ?? 0;
            $spread_ids = [];
            /** @var StoreOrderCreateServices $createService */
            $createService = app()->make(StoreOrderCreateServices::class);
            if ($cartInfo && $priceData) {
                /** @var StoreOrderCartInfoServices $cartServices */
                $cartServices = app()->make(StoreOrderCartInfoServices::class);
                [$cartInfo, $spread_ids] = $createService->computeOrderProductTruePrice($cartInfo, $priceData, $addressId, $uid, $order);
                $cartServices->updateCartInfo($orderId, $cartInfo);
            }

            $orderData = [];
            $spread_uid = $spread_two_uid = 0;
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            if ($spread_ids) {
                [$spread_uid, $spread_two_uid] = $spread_ids;
                $orderData['spread_uid'] = $spread_uid;
                $orderData['spread_two_uid'] = $spread_two_uid;
            } else {
                $spread_uid = $userServices->getSpreadUid($uid);
                $orderData = ['spread_uid' => 0, 'spread_two_uid' => 0];
                if ($spread_uid) {
                    $orderData['spread_uid'] = $spread_uid;
                }
                if ($spread_uid > 0 && sys_config('brokerage_level') == 2) {
                    $spread_two_uid = $userServices->getSpreadUid($spread_uid, [], false);
                    if ($spread_two_uid) {
                        $orderData['spread_two_uid'] = $spread_two_uid;
                    }
                }
            }
            $isCommission = 0;
            if ($order['combination_id']) {
                //Kiểm tra mua chung có tham gia trả hoa hồng không
                $isCommission = app()->make(StoreCombinationServices::class)->value(['id' => $order['combination_id']], 'is_commission');
            }
            if ($order['seckill_id']) {
                //Kiểm tra flash sale có tham gia trả hoa hồng không
                $isCommission = app()->make(StoreSeckillServices::class)->value(['id' => $order['seckill_id']], 'is_commission');
            }
            if ($order['bargain_id']) {
                //Kiểm tra săn giảm giá có tham gia trả hoa hồng không
                $isCommission = app()->make(StoreBargainServices::class)->value(['id' => $order['bargain_id']], 'is_commission');
            }
            if ($cartInfo && (!$activity || $isCommission)) {
                /** @var StoreOrderComputedServices $orderComputed */
                $orderComputed = app()->make(StoreOrderComputedServices::class);
                if ($userServices->checkUserPromoter($spread_uid)) $orderData['one_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'one_brokerage', false);
                if ($userServices->checkUserPromoter($spread_two_uid)) $orderData['two_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'two_brokerage', false);
                $orderData['staff_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'staff_brokerage', false);
                $orderData['agent_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'agent_brokerage', false);
                $orderData['division_brokerage'] = $orderComputed->getOrderSumPrice($cartInfo, 'division_brokerage', false);
            }
            $createService->update(['id' => $orderId], $orderData);
        } catch (\Throwable $e) {
            throw new ApiException('Tính ưu đãi thực tế, điểm thưởng, phí vận chuyển, hoa hồng của đơn hàng thất bại, nguyên nhân:' . $e->getMessage());
        }
    }

    /**
     * Tính giá thanh toán thực tế của từng sản phẩm trong đơn hàng
     * @param array $cartInfo
     * @param array $priceData
     * @param $addressId
     * @param int $uid
     * @return array
     */
    public function computeOrderProductTruePrice(array $cartInfo, array $priceData, $addressId, int $uid, $orderInfo)
    {
        //Đưa vào dữ liệu mặc định thống nhất
        foreach ($cartInfo as &$cart) {
            $cart['use_integral'] = 0;
            $cart['integral_price'] = 0.00;
            $cart['coupon_price'] = 0.00;
        }
        try {
            $cartInfo = $this->computeOrderProductCoupon($cartInfo, $priceData);
            $cartInfo = $this->computeOrderProductIntegral($cartInfo, $priceData);
        } catch (\Throwable $e) {
            Log::error('Thanh toán sản phẩm trong đơn hàng thất bại, File:' . $e->getFile() . ',Line：' . $e->getLine() . ',Message：' . $e->getMessage());
            throw new ApiException('Thanh toán sản phẩm trong đơn hàng thất bại');
        }
        //truePice đơn giá thanh toán thực tế (nếu có)
        //Tổng ưu đãi của nhiều sản phẩm và số tiền khấu trừ điểm thưởng
        foreach ($cartInfo as &$cart) {
            $coupon_price = $cart['coupon_price'] ?? 0;
            $integral_price = $cart['integral_price'] ?? 0;
            $cart['sum_true_price'] = bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 2);
            if ($coupon_price) {
                $cart['sum_true_price'] = bcsub((string)$cart['sum_true_price'], (string)$coupon_price, 2);
                $uni_coupon_price = (string)bcdiv((string)$coupon_price, (string)$cart['cart_num'], 4);
                $cart['truePrice'] = $cart['truePrice'] > $uni_coupon_price ? bcsub((string)$cart['truePrice'], $uni_coupon_price, 2) : 0;
            }
            if ($integral_price) {
                $cart['sum_true_price'] = bcsub((string)$cart['sum_true_price'], (string)$integral_price, 2);
                $uni_integral_price = (string)bcdiv((string)$integral_price, (string)$cart['cart_num'], 4);
                $cart['truePrice'] = $cart['truePrice'] > $uni_integral_price ? bcsub((string)$cart['truePrice'], $uni_integral_price, 2) : 0;
            }
            if ($cart['sum_true_price'] < 0) $cart['sum_true_price'] = '0.00';
        }
        try {
            [$cartInfo, $spread_ids] = $this->computeOrderProductBrokerage($uid, $cartInfo);
        } catch (\Throwable $e) {
            Log::error('Thanh toán sản phẩm trong đơn hàng thất bại, File:' . $e->getFile() . ',Line：' . $e->getLine() . ',Message：' . $e->getMessage());
            throw new ApiException('Thanh toán sản phẩm trong đơn hàng thất bại');
        }
        return [$cartInfo, $spread_ids];
    }

    /**
     * Tính phí vận chuyển thanh toán thực tế của từng sản phẩm
     * @param array $cartInfo
     * @param array $priceData
     * @return array
     */
    public function computeOrderProductPostage(array $cartInfo, array $priceData, $addressId)
    {
        $storePostage = $priceData['pay_postage'] ?? 0;
        if ($storePostage) {
            /** @var UserAddressServices $addressServices */
            $addressServices = app()->make(UserAddressServices::class);
            $addr = $addressServices->getAddress($addressId);
            if ($addr) {
                $addr = $addr->toArray();
                //Theo mẫu phí vận chuyển, tính số lượng/khối lượng/thể tích và tổng số tiền sản phẩm trong từng mẫu, sắp xếp giảm dần theo khối lượng đầu tiên
                $cityId = $addr['city_id'] ?? 0;
                $tempIds[] = 1;
                foreach ($cartInfo as $key_c => $item_c) {
                    $tempIds[] = $item_c['productInfo']['temp_id'];
                }
                $tempIds = array_unique($tempIds);
                /** @var ShippingTemplatesServices $shippServices */
                $shippServices = app()->make(ShippingTemplatesServices::class);
                $temp = $shippServices->getShippingColumn(['id' => $tempIds], 'type,appoint', 'id');
                /** @var ShippingTemplatesRegionServices $regionServices */
                $regionServices = app()->make(ShippingTemplatesRegionServices::class);
                $regions = $regionServices->getTempRegionList($tempIds, [$cityId, 0], 'temp_id,first,first_price,continue,continue_price', 'temp_id');
                $temp_num = [];
                foreach ($cartInfo as $cart) {
                    $tempId = $cart['productInfo']['temp_id'] ?? 1;
                    $type = $temp[$tempId]['type'] ?? $temp[1]['type'];
                    if ($type == 1) {
                        $num = $cart['cart_num'];
                    } elseif ($type == 2) {
                        $num = $cart['cart_num'] * $cart['productInfo']['attrInfo']['weight'];
                    } else {
                        $num = $cart['cart_num'] * $cart['productInfo']['attrInfo']['volume'];
                    }
                    $region = $regions[$tempId] ?? $regions[1];
                    if (!isset($temp_num[$cart['productInfo']['temp_id']])) {
                        $temp_num[$cart['productInfo']['temp_id']]['cart_id'][] = $cart['id'];
                        $temp_num[$cart['productInfo']['temp_id']]['number'] = $num;
                        $temp_num[$cart['productInfo']['temp_id']]['type'] = $type;
                        $temp_num[$cart['productInfo']['temp_id']]['price'] = bcmul($cart['cart_num'], $cart['truePrice'], 2);
                        $temp_num[$cart['productInfo']['temp_id']]['first'] = $region['first'];
                        $temp_num[$cart['productInfo']['temp_id']]['first_price'] = $region['first_price'];
                        $temp_num[$cart['productInfo']['temp_id']]['continue'] = $region['continue'];
                        $temp_num[$cart['productInfo']['temp_id']]['continue_price'] = $region['continue_price'];
                        $temp_num[$cart['productInfo']['temp_id']]['temp_id'] = $cart['productInfo']['temp_id'];
                        $temp_num[$cart['productInfo']['temp_id']]['city_id'] = $addr['city_id'];
                    } else {
                        $temp_num[$cart['productInfo']['temp_id']]['cart_id'][] = $cart['id'];
                        $temp_num[$cart['productInfo']['temp_id']]['number'] += $num;
                        $temp_num[$cart['productInfo']['temp_id']]['price'] += bcmul($cart['cart_num'], $cart['truePrice'], 2);
                    }
                }
                $cartInfo = array_combine(array_column($cartInfo, 'id'), $cartInfo);
                /** @var ShippingTemplatesFreeServices $freeServices */
                $freeServices = app()->make(ShippingTemplatesFreeServices::class);
                foreach ($temp_num as $k => $v) {
                    if (isset($temp[$v['temp_id']]['appoint']) && $temp[$v['temp_id']]['appoint']) {
                        if ($freeServices->isFree($v['temp_id'], $v['city_id'], $v['number'], $v['price'], $v['type'])) {
                            //Miễn phí vận chuyển
                            foreach ($v['cart_id'] as $c_id) {
                                if (isset($cartInfo[$c_id])) $cartInfo[$c_id]['postage_price'] = 0.00;
                            }
                        }
                    }
                }
                $count = 0;
                $compute_price = 0.00;
                $total_price = 0;
                $postage_price = 0.00;
                foreach ($cartInfo as &$cart) {
                    if (isset($cart['postage_price'])) {//Miễn phí vận chuyển
                        continue;
                    }
                    $total_price = bcadd((string)$total_price, (string)bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 4), 2);
                    $count++;
                }
                foreach ($cartInfo as &$cart) {
                    if (isset($cart['postage_price'])) {//Miễn phí vận chuyển
                        continue;
                    }
                    if ($count > 1) {
                        $postage_price = bcmul((string)bcdiv((string)bcmul((string)$cart['cart_num'], (string)$cart['truePrice'], 4), (string)$total_price, 4), (string)$storePostage, 2);
                        $compute_price = bcadd((string)$compute_price, (string)$postage_price, 2);
                    } else {
                        $postage_price = bcsub((string)$storePostage, $compute_price, 2);
                    }
                    $cart['postage_price'] = $postage_price;
                    $count--;
                }
                $cartInfo = array_merge($cartInfo);
            }
        }
        //Đảm bảo trường postage_price có giá trị đối với sản phẩm trong giỏ hàng không tính theo mẫu phí vận chuyển
        foreach ($cartInfo as &$item) {
            if (!isset($item['postage_price'])) $item['postage_price'] = 0.00;
        }
        return $cartInfo;
    }

    /**
     * Tính số tiền khấu trừ điểm thưởng thực tế của sản phẩm trong đơn hàng
     * @param array $cartInfo
     * @param array $priceData
     * @return array
     */
    public function computeOrderProductIntegral(array $cartInfo, array $priceData)
    {
        $usedIntegral = $priceData['usedIntegral'] ?? 0;
        $deduction_price = $priceData['deduction_price'] ?? 0;
        if ($deduction_price) {
            $count = 0;
            $total_price = 0.00;
            $compute_price = 0.00;
            $integral_price = 0.00;
            $use_integral = 0;
            $compute_integral = 0;
            foreach ($cartInfo as $cart) {
                $total_price = bcadd((string)$total_price, (string)bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 4), 2);
                $count++;
            }
            foreach ($cartInfo as &$cart) {
                if ($count > 1) {
                    $integral_price = bcmul((string)bcdiv((string)bcmul((string)$cart['cart_num'], (string)$cart['truePrice'], 4), (string)$total_price, 4), (string)$deduction_price, 2);
                    $compute_price = bcadd((string)$compute_price, (string)$integral_price, 2);
                    $use_integral = bcmul((string)bcdiv((string)bcmul((string)$cart['cart_num'], (string)$cart['truePrice'], 4), (string)$total_price, 4), (string)$usedIntegral, 0);
                    $compute_integral = bcadd((string)$compute_integral, $use_integral, 0);
                } else {
                    $integral_price = bcsub((string)$deduction_price, $compute_price, 2);
                    $use_integral = bcsub((string)$usedIntegral, $compute_integral, 0);
                }
                $count--;
                $cart['integral_price'] = $integral_price;
                $cart['use_integral'] = $use_integral;
            }
        }
        return $cartInfo;
    }

    /**
     * Tính số tiền khấu trừ phiếu giảm giá thực tế của sản phẩm trong đơn hàng
     * @param array $cartInfo
     * @param array $priceData
     * @return array
     */
    public function computeOrderProductCoupon(array $cartInfo, array $priceData)
    {
        if ($priceData['coupon_id'] && $priceData['coupon_price'] ?? 0) {
            $count = 0;
            $total_price = 0.00;
            $compute_price = 0.00;
            $coupon_price = 0.00;
            /** @var StoreCouponUserServices $couponServices */
            $couponServices = app()->make(StoreCouponUserServices::class);
            $couponInfo = $couponServices->getOne(['id' => $priceData['coupon_id']], '*', ['issue']);
            if ($couponInfo) {
                $type = $couponInfo['applicable_type'] ?? 0;
                $counpon_id = $couponInfo['id'];
                switch ($type) {
                    case 0:
                    case 3:
                        foreach ($cartInfo as $cart) {
                            $total_price = bcadd((string)$total_price, (string)bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 4), 2);
                            $count++;
                        }
                        foreach ($cartInfo as &$cart) {
                            if ($count > 1) {
                                $coupon_price = bcmul((string)bcdiv((string)bcmul((string)$cart['cart_num'], (string)$cart['truePrice'], 4), (string)$total_price, 4), (string)$couponInfo['coupon_price'], 2);
                                $compute_price = bcadd((string)$compute_price, (string)$coupon_price, 2);
                            } else {
                                $coupon_price = bcsub((string)$couponInfo['coupon_price'], $compute_price, 2);
                            }
                            $cart['coupon_price'] = $coupon_price;
                            $cart['coupon_id'] = $counpon_id;
                            $count--;
                        }
                        break;
                    case 1://Phiếu theo danh mục
                        /** @var StoreCategoryServices $storeCategoryServices */
                        $storeCategoryServices = app()->make(StoreCategoryServices::class);
                        $coupon_category = explode(',', (string)$couponInfo['category_id']);
                        $category_ids = $storeCategoryServices->getAllById($coupon_category);
                        if ($category_ids) {
                            $cateIds = array_column($category_ids, 'id');
                            foreach ($cartInfo as $cart) {
                                if (isset($cart['productInfo']['cate_id']) && array_intersect(explode(',', $cart['productInfo']['cate_id']), $cateIds)) {
                                    $total_price = bcadd((string)$total_price, (string)bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 4), 2);
                                    $count++;
                                }
                            }
                            foreach ($cartInfo as &$cart) {
                                $cart['coupon_id'] = 0;
                                $cart['coupon_price'] = 0;
                                if (isset($cart['productInfo']['cate_id']) && array_intersect(explode(',', $cart['productInfo']['cate_id']), $cateIds)) {
                                    if ($count > 1) {
                                        $coupon_price = bcmul((string)bcdiv((string)bcmul((string)$cart['cart_num'], (string)$cart['truePrice'], 4), (string)$total_price, 4), (string)$couponInfo['coupon_price'], 2);
                                        $compute_price = bcadd((string)$compute_price, (string)$coupon_price, 2);
                                    } else {
                                        $coupon_price = bcsub((string)$couponInfo['coupon_price'], $compute_price, 2);
                                    }
                                    $cart['coupon_id'] = $counpon_id;
                                    $cart['coupon_price'] = $coupon_price;
                                    $count--;
                                }
                            }
                        }
                        break;
                    case 2://Phiếu giảm giá sản phẩm
                        foreach ($cartInfo as $cart) {
                            if (isset($cart['product_id']) && in_array($cart['product_id'], explode(',', $couponInfo['product_id']))) {
                                $total_price = bcadd((string)$total_price, (string)bcmul((string)$cart['truePrice'], (string)$cart['cart_num'], 4), 2);
                                $count++;
                            }
                        }
                        foreach ($cartInfo as &$cart) {
                            $cart['coupon_id'] = 0;
                            $cart['coupon_price'] = 0;
                            if (isset($cart['product_id']) && in_array($cart['product_id'], explode(',', $couponInfo['product_id']))) {
                                if ($count > 1) {
                                    $coupon_price = bcmul((string)bcdiv((string)bcmul((string)$cart['cart_num'], (string)$cart['truePrice'], 4), (string)$total_price, 4), (string)$couponInfo['coupon_price'], 2);
                                    $compute_price = bcadd((string)$compute_price, (string)$coupon_price, 2);
                                } else {
                                    $coupon_price = bcsub((string)$couponInfo['coupon_price'], $compute_price, 2);
                                }
                                $cart['coupon_id'] = $counpon_id;
                                $cart['coupon_price'] = $coupon_price;
                                $count--;
                            }
                        }
                        break;
                }
            }
        }
        return $cartInfo;
    }

    /**
     * Tính hoa hồng thực tế
     * @param int $uid
     * @param array $cartInfo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function computeOrderProductBrokerage(int $uid, array $cartInfo)
    {

        [$storeBrokerageRatio, $storeBrokerageTwo, $spread_one_uid, $spread_two_uid] = $this->getSpreadDate($uid);

        /** @var DivisionServices $divisionService */
        $divisionService = app()->make(DivisionServices::class);
        [$storeBrokerageRatio, $storeBrokerageTwo, $staffPercent, $agentPercent, $divisionPercent] = $divisionService->getDivisionPercent($uid, $storeBrokerageRatio, $storeBrokerageTwo, sys_config('is_self_brokerage', 0));

        foreach ($cartInfo as &$cart) {
            $oneBrokerage = '0';//Số tiền trả hoa hồng cấp 1
            $twoBrokerage = '0';//Số tiền trả hoa hồng cấp 2
            $staffBrokerage = '0';//Số tiền trả hoa hồng nhân viên cửa hàng
            $agentBrokerage = '0';//Số tiền hoa hồng của đại lý
            $divisionBrokerage = '0';//Số tiền hoa hồng của đại lý khu vực
            $cartNum = (string)$cart['cart_num'] ?? '0';
            if (isset($cart['productInfo'])) {
                $productInfo = $cart['productInfo'];

                //Tính số tiền sản phẩm
                if (sys_config('user_brokerage_type') == 1) {
                    //Trả hoa hồng theo giá thanh toán thực tế
                    $price = bcmul((string)bcadd((string)$cart['truePrice'], (string)$cart['postage_price'], 2), $cartNum, 4);
                } else {
                    //Trả hoa hồng theo giá sản phẩm
                    if (isset($productInfo['attrInfo'])) {
                        $price = bcmul((string)($productInfo['attrInfo']['price'] ?? '0'), $cartNum, 4);
                    } else {
                        $price = bcmul((string)($productInfo['price'] ?? '0'), $cartNum, 4);
                    }
                }

                //Số tiền trả hoa hồng chỉ định
                if (isset($productInfo['is_sub']) && $productInfo['is_sub'] == 1) {
                    $oneBrokerage = bcmul((string)($productInfo['attrInfo']['brokerage'] ?? '0'), $cartNum, 2);
                    $twoBrokerage = bcmul((string)($productInfo['attrInfo']['brokerage_two'] ?? '0'), $cartNum, 2);
                } else {
                    if ($price) {
                        //Tỷ lệ trả hoa hồng cấp 1, nếu nhỏ hơn hoặc bằng 0 thì trả về ngay, không trả hoa hồng
                        if ($storeBrokerageRatio > 0) {
                            //Tính và lấy tỷ lệ trả hoa hồng cấp 1
                            $brokerageRatio = bcdiv($storeBrokerageRatio, 100, 4);
                            $oneBrokerage = bcmul((string)$price, (string)$brokerageRatio, 2);
                        }
                        //Tỷ lệ trả hoa hồng cấp 2 nhỏ hơn hoặc bằng 0 thì trả về ngay
                        if ($storeBrokerageTwo > 0) {
                            //Tính và lấy tỷ lệ trả hoa hồng cấp 2
                            $brokerageTwo = bcdiv($storeBrokerageTwo, 100, 4);
                            $twoBrokerage = bcmul((string)$price, (string)$brokerageTwo, 2);
                        }
                    }
                    $staffBrokerage = bcmul((string)$price, (string)bcdiv($staffPercent, 100, 4), 2);
                    $agentBrokerage = bcmul((string)$price, (string)bcdiv($agentPercent, 100, 4), 2);
                    $divisionBrokerage = bcmul((string)$price, (string)bcdiv($divisionPercent, 100, 4), 2);
                }
            }

            $cart['one_brokerage'] = $oneBrokerage;
            $cart['two_brokerage'] = $twoBrokerage;
            $cart['staff_brokerage'] = $staffBrokerage;
            $cart['agent_brokerage'] = $agentBrokerage;
            $cart['division_brokerage'] = $divisionBrokerage;
        }

        return [$cartInfo, [$spread_one_uid, $spread_two_uid]];
    }


    /**
     * Lấy tỷ lệ hoa hồng đã tính và uid người được trả hoa hồng
     * @param int $uid
     * @return array|int[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/10/8
     */
    public function getSpreadDate(int $uid)
    {
        //Phân phối của cửa hàng có mở không, uid người dùng có tồn tại không, nếu không thì trả về 0 hết
        if (!sys_config('brokerage_func_status') || !$uid) {
            return [0, 0, 0, 0];
        }

        //Lấy thông tin người dùng, nếu không lấy được thì trả về 0 hết
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        if (!$userInfo) {
            return [0, 0, 0, 0];
        }

        //Lấy tỷ lệ phân chia hoa hồng cấp 1, cấp 2 của hệ thống
        $storeBrokerageRatio = sys_config('store_brokerage_ratio') != '' ? sys_config('store_brokerage_ratio') : 0;
        $storeBrokerageTwo = sys_config('store_brokerage_two') != '' ? sys_config('store_brokerage_two') : 0;

        //Lấy uid của cấp trên và cấp trên của cấp trên, nếu mở tự mua thì lấy uid của bản thân và cấp trên
        $spread_one_uid = $userServices->getSpreadUid($uid, $userInfo);
        $spread_two_uid = 0;
        if ($spread_one_uid > 0 && $one_user_info = $userServices->getUserInfo($spread_one_uid)) {
            $spread_two_uid = $userServices->getSpreadUid($spread_one_uid, $one_user_info, false);
        }

        //Tính tỷ lệ hoa hồng sau khi áp dụng hạng phân phối
        [$storeBrokerageRatio, $storeBrokerageTwo] = app()->make(AgentLevelServices::class)->getAgentLevelBrokerage($storeBrokerageRatio, $storeBrokerageTwo, $spread_one_uid, $spread_two_uid);

        //Nếu tầng trả hoa hồng là cấp 1 thì đổi uid người dùng cấp 2 và tỷ lệ phân hoa hồng cấp 2 thành 0
        if (sys_config('brokerage_level') == 1) {
            $storeBrokerageTwo = $spread_two_uid = 0;
        }
        return [$storeBrokerageRatio, $storeBrokerageTwo, $spread_one_uid, $spread_two_uid];
    }
}
