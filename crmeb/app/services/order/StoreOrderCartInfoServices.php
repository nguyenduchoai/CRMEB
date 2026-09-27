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

use crmeb\utils\Str;
use app\services\BaseServices;
use crmeb\services\CacheService;
use app\dao\order\StoreOrderCartInfoDao;

/**
 * Class StoreOrderCartInfoServices
 * @package app\services\order
 * @method array getCartColunm(array $where, string $field, ?string $key) Lấy thông tin giỏ hàng, trả về dạng mảng
 * @method array getCartInfoList(array $where, array $field) Lấy danh sách chi tiết giỏ hàng
 * @method getSplitCartNum(array $cart_id)
 * @method getOne(array $where, ?string $field = '*', array $with = []) Lấy một dữ liệu theo điều kiện
 */
class StoreOrderCartInfoServices extends BaseServices
{
    /**
     * StoreOrderCartInfoServices constructor.
     * @param StoreOrderCartInfoDao $dao
     */
    public function __construct(StoreOrderCartInfoDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Xóa bộ nhớ đệm sản phẩm đơn hàng
     * @param int $oid
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function clearOrderCartInfo(int $oid)
    {
        return CacheService::delete(md5('store_order_cart_info_' . $oid));
    }

    /**
     * Lấy chi tiết sản phẩm trong đơn hàng chỉ định
     * @param int $oid
     * @return array|bool|mixed
     * @throws \ReflectionException
     */
    public function getOrderCartInfo(int $oid)
    {
        $cartInfo = CacheService::get(md5('store_order_cart_info_' . $oid));
        if ($cartInfo) return $cartInfo;
        $cart_info = $this->dao->getColumn(['oid' => $oid], 'cart_info', 'cart_id');
        $info = [];
        foreach ($cart_info as $k => $v) {
            $_info = is_string($v) ? json_decode($v, true) : $v;
            if (!isset($_info['productInfo'])) $_info['productInfo'] = [];
            //Xử lý ảnh thu nhỏ
            if (isset($_info['productInfo']['attrInfo'])) {
                $_info['productInfo']['attrInfo'] = get_thumb_water($_info['productInfo']['attrInfo']);
            }
            $_info['productInfo'] = get_thumb_water($_info['productInfo']);
            $_info['refund_num'] = $this->dao->sum(['cart_id' => $_info['id']], 'refund_num');
            $info[$k]['cart_info'] = $_info;
            unset($_info);
        }
        CacheService::set(md5('store_order_cart_info_' . $oid), $info);
        return $info;
    }

    /**
     * Tìm tất cả tên sản phẩm trong giỏ hàng
     * @param int $oid
     * @param false $goodsNum
     * @return bool|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCarIdByProductTitle(int $oid, bool $goodsNum = false)
    {
        if ($goodsNum) {
            $key = md5('store_order_cart_product_title_num' . $oid);
        } else {
            $key = md5('store_order_cart_product_title_' . $oid);
        }
        $title = CacheService::get($key);
        if (!$title) {
            $orderCart = $this->dao->getCartInfoList(['oid' => $oid], ['cart_info']);
            foreach ($orderCart as $item) {
                if (isset($item['cart_info']['productInfo']['store_name'])) {
                    if ($goodsNum && isset($item['cart_info']['cart_num'])) {
                        $title .= $item['cart_info']['productInfo']['store_name'] . ' * ' . $item['cart_info']['cart_num'] . ' | ';
                    } else {
                        $title .= $item['cart_info']['productInfo']['store_name'] . '|';
                    }
                }
            }
            if ($title) {
                $title = substr($title, 0, strlen($title) - 1);
            }
            CacheService::set($key, $title);
        }
        return $title ?: '';
    }

    /**
     * Lấy thông tin sản phẩm để in đơn hàng
     * @param $oid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCartInfoPrintProduct($oid)
    {
        $cartInfo = $this->dao->getCartInfoList(['oid' => $oid], ['cart_info']);
        $product = [];
        foreach ($cartInfo as $item) {
            $value = is_string($item['cart_info']) ? json_decode($item['cart_info'], true) : $item['cart_info'];
            $value['productInfo']['store_name'] = $value['productInfo']['store_name'] ?? "";
            $value['productInfo']['store_name'] = Str::substrUTf8($value['productInfo']['store_name'], 10, 'UTF-8', '');
            $product[] = $value;
        }
        return $product;
    }

    /**
     * Lưu info giỏ hàng
     * @param $oid
     * @param $uid
     * @param array $cartInfo
     * @return \think\Collection
     * @throws \Exception
     */
    public function setCartInfo($oid, $uid, array $cartInfo)
    {
        $group = [];
        foreach ($cartInfo as $cart) {
            $group[] = [
                'oid' => $oid,
                'uid' => $uid,
                'cart_id' => $cart['id'],
                'product_id' => $cart['productInfo']['id'],
                'cart_info' => json_encode($cart),
                'cart_num' => $cart['cart_num'],
                'surplus_num' => $cart['cart_num'],
                'unique' => md5($cart['id'] . '' . $oid)
            ];
        }
        return $this->dao->saveAll($group);
    }

    /**
     * Sau khi tạo đơn hàng thành công, tính toán đơn hàng (giảm giá thực tế, điểm thưởng, hoa hồng, cấp trên, cấp trên của cấp trên)
     * @param $oid
     * @param array $cartInfo
     * @return bool
     */
    public function updateCartInfo($oid, array $cartInfo)
    {
        foreach ($cartInfo as $cart) {
            $group = [
                'cart_info' => json_encode($cart)
            ];
            $this->dao->update(['oid' => $oid, 'cart_id' => $cart['id']], $group);
        }
        return true;
    }

    /**
     * ID sản phẩm
     * @param $oid
     * @return array
     */
    public function getCartIdsProduct($oid)
    {
        return $this->dao->getColumn(['oid' => $oid], 'product_id', 'oid');
    }

    /**
     * Lấy sản phẩm còn có thể tách khỏi một đơn hàng, split_status 0: chưa tách 1: tách một phần 2: đã tách xong
     * @param int $oid
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getSplitCartList(int $oid, string $field = '*', string $key = 'cart_id')
    {
        $cartInfo = $this->dao->getColumn([['oid', '=', $oid], ['split_status', 'IN', [0, 1]]], $field, $key);
        foreach ($cartInfo as &$item) {
            if ($field == 'cart_info') {
                $item = is_string($item) ? json_decode($item, true) : $item;
            } else {
                if (isset($item['cart_info'])) $item['cart_info'] = is_string($item['cart_info']) ? json_decode($item['cart_info'], true) : $item['cart_info'];
                if (isset($item['cart_num']) && !$item['cart_num']) {//Tương thích dữ liệu cũ trước đây
                    $item['cart_num'] = $item['cart_info']['cart_num'] ?? 0;
                }
            }
            $item['num'] = $item['cart_num'];
        }
        return $cartInfo;
    }

    /**
     * Lấy sản phẩm có thể hoàn tiền
     * @param int $oid
     * @param string $field
     * @param string $key
     * @return array
     */
    public function getRefundCartList(int $oid, string $field = '*', string $key = '')
    {
        $cartInfo = array_merge($this->dao->getColumn(['oid' => $oid], $field, 'id'));
        foreach ($cartInfo as $key => &$item) {
            if ($field == 'cart_info') {
                $item = is_string($item) ? json_decode($item, true) : $item;
            } else {
                if (isset($item['cart_info'])) $item['cart_info'] = is_string($item['cart_info']) ? json_decode($item['cart_info'], true) : $item['cart_info'];
                if (isset($item['cart_num']) && !$item['cart_num']) {//Tương thích dữ liệu cũ trước đây
                    $item['cart_num'] = $item['cart_info']['cart_num'] ?? 0;
                }
            }
            $surplus = (int)bcsub((string)$item['cart_num'], (string)$item['refund_num'], 0);
            if ($surplus > 0) {
                $item['surplus_num'] = $surplus;
            } else {
                unset($cartInfo['key']);
            }
        }
        return $cartInfo;
    }
}
