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

namespace app\jobs;

use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\activity\coupon\StoreCouponUserServices;
use app\services\kefu\service\StoreServiceServices;
use app\services\message\notice\SmsService;
use app\services\order\OutStoreOrderServices;
use app\services\order\StoreOrderCartInfoServices;
use app\services\order\StoreOrderEconomizeServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;
use app\services\user\member\MemberCardServices;
use app\services\user\UserLabelRelationServices;
use app\services\user\UserLevelServices;
use app\services\user\UserServices;
use app\services\wechat\WechatUserServices;
use crmeb\basic\BaseJobs;
use crmeb\services\app\WechatService;
use crmeb\services\workerman\ChannelService;
use crmeb\traits\QueueTrait;
use think\exception\ValidateException;
use think\facade\Log;

/**
 * Hàng đợi tin nhắn đơn hàng
 * Class OrderJob
 * @package crmeb\jobs
 */
class OrderJob extends BaseJobs
{
    use QueueTrait;

    /**
     * Thực hiện gửi tin nhắn khi đơn hàng thanh toán thành công
     * @param $order
     * @return bool
     */
    public function doJob($order)
    {
        //Tính số tiền tiết kiệm được của sản phẩm
        try {
            $this->setEconomizeMoney($order);
        } catch (\Throwable $e) {
            Log::error('Tính số tiền tiết kiệm thất bại, nguyên nhân:' . $e->getMessage());
        }
        //Cập nhật số đơn hàng đã thanh toán của người dùng
        try {
            $this->setUserPayCountAndPromoter($order);
        } catch (\Throwable $e) {
            Log::error('Cập nhật số đơn hàng của người dùng thất bại, nguyên nhân:' . $e->getMessage());
        }
        //Thêm nhãn người dùng
        try {
            $this->setUserLabel($order);
        } catch (\Throwable $e) {
            Log::error('Thêm nhãn người dùng thất bại, nguyên nhân:' . $e->getMessage());
        }
        try {
            if (in_array($order['is_channel'], [0, 2])) {//OA WeChat gửi tin nhắn mẫu
                $this->sendOrderPaySuccessCustomerService($order, 1);
            } else if (in_array($order['is_channel'], [1, 2])) {//Mini Program gửi tin nhắn mẫu
                $this->sendOrderPaySuccessCustomerService($order, 0);
            }
        } catch (\Exception $e) {
            throw new ValidateException('Gửi tin nhắn CSKH, tin nhắn SMS thất bại, nguyên nhân:' . $e->getMessage());
        }


        //In hóa đơn
//        $switch = sys_config('pay_success_printing_switch') ? true : false;
//        if ($switch) {
//            try {
//                /** @var StoreOrderServices $orderServices */
//                $orderServices = app()->make(StoreOrderServices::class);
//                $orderServices->orderPrint($order, $order['cart_id']);
//            } catch (\Throwable $e) {
//                Log::error('In hóa đơn xảy ra lỗi, nguyên nhân lỗi:' . $e->getMessage());
//            }
//        }

        //Kiểm tra hạng thành viên
        try {
            /** @var UserLevelServices $levelServices */
            $levelServices = app()->make(UserLevelServices::class);
            $levelServices->detection((int)$order['uid']);
        } catch (\Throwable $e) {
            Log::error('Nâng hạng thành viên thất bại, nguyên nhân:' . $e->getMessage());
        }
        //Gửi tin nhắn đơn hàng mới đến trang quản trị
        try {
            ChannelService::instance()->send('NEW_ORDER', ['order_id' => $order['order_id']]);
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo đơn hàng mới đến trang quản trị thất bại, nguyên nhân:' . $e->getMessage());
        }
        return true;
    }

    /**
     * Thiết lập số lần mua của người dùng và kiểm tra thời điểm trở thành người giới thiệu
     * @param $order
     */
    public function setUserPayCountAndPromoter($order)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($order['uid']);
        if ($userInfo) {
            $userInfo->pay_count = $userInfo->pay_count + 1;
            if (!$userInfo->is_promoter) {
                /** @var StoreOrderServices $orderServices */
                $orderServices = app()->make(StoreOrderServices::class);
                $price = $orderServices->sum(['paid' => 1, 'refund_status' => 0, 'uid' => $userInfo['uid']], 'pay_price');
                $status = is_brokerage_statu($price);
                if ($status) {
                    $userInfo->is_promoter = 1;
                }
            }
            $userInfo->save();
        }
    }

    /**
     * Thiết lập nhãn theo lượt mua của người dùng
     * @param $order
     */
    public function setUserLabel($order)
    {
        /** @var StoreOrderCartInfoServices $cartInfoServices */
        $cartInfoServices = app()->make(StoreOrderCartInfoServices::class);
        $productIds = $cartInfoServices->getCartColunm(['oid' => $order['id']], 'product_id', '');
        /** @var StoreProductServices $productServices */
        $productServices = app()->make(StoreProductServices::class);
        $label = $productServices->getColumn([['id', 'in', $productIds]], 'label_id');
        $labelIds = array_unique(explode(',', implode(',', $label)));
        /** @var UserLabelRelationServices $labelServices */
        $labelServices = app()->make(UserLabelRelationServices::class);
        $where = [
            ['label_id', 'in', $labelIds],
            ['uid', '=', $order['uid']]
        ];
        $data = [];
        $userLabel = $labelServices->getColumn($where, 'label_id');
        foreach ($labelIds as $item) {
            if (!in_array($item, $userLabel)) {
                $data[] = ['uid' => $order['uid'], 'label_id' => $item];
            }
        }
        $re = true;
        if ($data) {
            $re = $labelServices->saveAll($data);
        }
        return $re;
    }


    /**
     * Gửi tin nhắn cho CSKH sau khi đơn hàng thanh toán thành công
     * @param $order
     * @param int $type 1 OA WeChat 0 Mini Program
     * @return string
     */
    public function sendOrderPaySuccessCustomerService($order, $type = 0)
    {
        /** @var StoreServiceServices $services */
        $services = app()->make(StoreServiceServices::class);
        /** @var WechatUserServices $wechatUserServices */
        $wechatUserServices = app()->make(WechatUserServices::class);
        $serviceOrderNotice = $services->getStoreServiceOrderNotice();
        if (count($serviceOrderNotice)) {
            /** @var StoreProductServices $services */
            $services = app()->make(StoreProductServices::class);
            /** @var StoreSeckillServices $seckillServices */
            $seckillServices = app()->make(StoreSeckillServices::class);
            /** @var StoreCombinationServices $pinkServices */
            $pinkServices = app()->make(StoreCombinationServices::class);
            /** @var StoreBargainServices $bargainServices */
            $bargainServices = app()->make(StoreBargainServices::class);
            /** @var StoreOrderCartInfoServices $cartInfoServices */
            $cartInfoServices = app()->make(StoreOrderCartInfoServices::class);
            foreach ($serviceOrderNotice as $item) {
                $userInfo = $wechatUserServices->getOne(['uid' => $item['uid'], 'user_type' => 'wechat']);
                if ($userInfo) {
                    $userInfo = $userInfo->toArray();
                    if ($userInfo['subscribe'] && $userInfo['openid']) {
                        if ($item['customer']) {
                            // Thống kê quản lý mở  đẩy tin nhắn hình ảnh và văn bản
                            $head = 'Thông báo đơn hàng - Mã đơn hàng:' . $order['order_id'];
                            $url = sys_config('site_url') . '/pages/admin/orderDetail/index?id=' . $order['order_id'];
                            $description = '';
                            $image = sys_config('site_logo');
                            if (isset($order['seckill_id']) && $order['seckill_id'] > 0) {
                                $description .= 'Sản phẩm flash sale:' . $seckillServices->value(['id' => $order['seckill_id']], 'title');
                                $image = $seckillServices->value(['id' => $order['seckill_id']], 'image');
                            } else if (isset($order['combination_id']) && $order['combination_id'] > 0) {
                                $description .= 'Sản phẩm mua chung:' . $pinkServices->value(['id' => $order['combination_id']], 'title');
                                $image = $pinkServices->value(['id' => $order['combination_id']], 'image');
                            } else if (isset($order['bargain_id']) && $order['bargain_id'] > 0) {
                                $title = $bargainServices->value(['id' => $order['bargain_id']], 'title');
                                $description .= 'Sản phẩm săn giảm giá:' . $title;
                                $image = $bargainServices->value(['id' => $order['bargain_id']], 'image');
                            } else {
                                $productIds = $cartInfoServices->getCartIdsProduct($order['id']);
                                $storeProduct = $services->getProductArray([['id', 'in', $productIds]], 'image,store_name', 'id');
                                if (count($storeProduct)) {
                                    foreach ($storeProduct as $value) {
                                        $description .= $value['store_name'] . '  ';
                                        $image = $value['image'];
                                    }
                                }
                            }
                            $message = WechatService::newsMessage($head, $description, $url, $image);
                            try {
                                WechatService::staffService()->message($message)->to($userInfo['openid'])->send();
                            } catch (\Exception $e) {
                                Log::error($userInfo['nickname'] . 'Gửi thất bại' . $e->getMessage());
                            }
                        } else {
                            // Đẩy tin nhắn văn bản
                            $head = "Nhắc nhở CSKH: Bạn có một đơn hàng mới \r\nMã đơn hàng: {$order['order_id']}\r\nSố tiền thanh toán: ₫{$order['pay_price']}\r\nGhi chú: {$order['mark']}\r\nNguồn đơn hàng: Mini Program";
                            if ($type) $head = "Nhắc nhở CSKH: Bạn có một đơn hàng mới \r\nMã đơn hàng: {$order['order_id']}\r\nSố tiền thanh toán: ₫{$order['pay_price']}\r\nGhi chú: {$order['mark']}\r\nNguồn đơn hàng: OA WeChat";
                            try {
                                WechatService::staffService()->message($head)->to($userInfo['openid'])->send();
                            } catch (\Exception $e) {
                                Log::error($userInfo['nickname'] . 'Gửi thất bại' . $e->getMessage());
                            }
                        }
                    }
                }

            }
        }
    }

    /**
     * Tính số tiền tiết kiệm
     * @param $order
     * @return false|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setEconomizeMoney($order)
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        /** @var StoreOrderCartInfoServices $cartInfoService */
        $cartInfoService = app()->make(StoreOrderCartInfoServices::class);
        /** @var StoreCouponUserServices $couponService */
        $couponService = app()->make(StoreCouponUserServices::class);
        /** @var StoreOrderEconomizeServices $economizeService */
        $economizeService = app()->make(StoreOrderEconomizeServices::class);
        /** @var MemberCardServices $memberCardService */
        $memberCardService = app()->make(MemberCardServices::class);
        $getOne = $economizeService->getOne(['order_id' => $order['order_id']]);
        if ($getOne) return false;
        //Kiểm tra có phải là thành viên hay không
        $userInfo = $userService->getUserInfo($order['uid']);
        if ($userInfo && $userInfo['is_money_level'] > 0) {
            $save = [];
            $save['order_type'] = 1;
            $save['add_time'] = time();
            $save['pay_price'] = $order['pay_price'];
            $save['order_id'] = $order['order_id'];
            $save['uid'] = $order['uid'];
            //Tính số tiền tiết kiệm của sản phẩm
            $isOpenVipPrice = $memberCardService->isOpenMemberCard('vip_price');
            if ($isOpenVipPrice) {
                $cartInfo = $cartInfoService->getOrderCartInfo($order['id']);
                $memberPrice = 0.00;
                if ($cartInfo) {
                    foreach ($cartInfo as $k => $item) {
                        foreach ($item as $value) {
                            if (isset($value['price_type']) && $value['price_type'] == 'member') $memberPrice += bcmul($value['vip_truePrice'], $value['cart_num'] ?: 1, 2);
                        }
                    }
                }
                $save['member_price'] = $memberPrice;
            }
            //Tính số tiền tiết kiệm phí vận chuyển
            $isOpenExpress = $memberCardService->isOpenMemberCard('express');
            if ($isOpenExpress) {
                $expressTotalMoney = bcdiv($order['total_postage'], bcdiv($isOpenExpress, 100, 2), 2);
                $save['postage_price'] = bcsub($expressTotalMoney, $order['total_postage'], 2);
            }

            //Tính số tiền tiết kiệm từ phiếu giảm giá thành viên
            if ($order['coupon_id']) {
                $couponMoney = $couponService->get($order['coupon_id'], ['*'], ['issue']);
                if ($couponMoney && $couponMoney['receive_type']) {
                    $save['coupon_price'] = $couponMoney['coupon_price'];
                }
            }
            return $economizeService->addEconomize($save);
        }
        return false;

    }
}
