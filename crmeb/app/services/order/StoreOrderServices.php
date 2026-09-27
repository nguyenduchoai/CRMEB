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

use app\dao\order\StoreOrderDao;
use app\jobs\AutoCommentJob;
use app\services\activity\combination\StorePinkServices;
use app\services\activity\coupon\StoreCouponUserServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\BaseServices;
use app\services\other\PosterServices;
use app\services\other\QrcodeServices;
use app\services\other\UploadService;
use app\services\pay\OrderPayServices;
use app\services\pay\PayServices;
use app\services\product\product\StoreProductLogServices;
use app\services\serve\ServeServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\system\store\SystemStoreServices;
use app\services\system\SystemTicketServices;
use app\services\user\UserInvoiceServices;
use app\services\user\UserRechargeServices;
use app\services\user\UserServices;
use app\services\product\product\StoreProductReplyServices;
use app\services\user\UserAddressServices;
use app\services\user\UserBillServices;
use app\services\user\UserLevelServices;
use app\services\wechat\WechatUserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\exceptions\PayException;
use crmeb\services\app\MiniProgramService;
use crmeb\services\CacheService;
use crmeb\services\easywechat\orderShipping\MiniOrderService;
use crmeb\services\FormBuilder as Form;
use crmeb\services\printer\Printer;
use crmeb\services\SystemConfigService;
use crmeb\utils\Arr;
use Guzzle\Http\EntityBody;
use think\facade\Log;

/**
 * Class StoreOrderServices
 * @package app\services\order
 * @method getOrderIdsCount(array $ids) Lấy số lượng đơn hàng chưa xóa theo id đơn hàng
 * @method StoreOrderDao getUserOrderDetail(string $key, int $uid, array $with) Lấy chi tiết đơn hàng
 * @method chartTimePrice($start, $stop) Lấy số tiền thanh toán từ thời điểm hiện tại đến thời điểm chỉ định (quản trị viên)
 * @method chartTimeNumber($start, $stop) Lấy số đơn hàng đã thanh toán từ thời điểm hiện tại đến thời điểm chỉ định (quản trị viên)
 * @method together(array $where, string $field, string $together = 'sum') Truy vấn tổng hợp
 * @method getBuyCount($uid, $type, $typeId) Lấy số lượng sản phẩm của hoạt động này mà người dùng đã mua
 * @method getDistinctCount(array $where, $field, ?bool $search = true)
 * @method getTrendData($time, $type, $timeType, $str) Xu hướng người dùng
 * @method getRegion($time, $channelType) Thống kê theo khu vực
 * @method getProductTrend($time, $timeType, $field, $str) Xu hướng sản phẩm
 * @method getList(array $where, array $field, int $page = 0, int $limit = 0, array $with = [])
 */
class StoreOrderServices extends BaseServices
{

    /**
     * Hình thức giao hàng
     * @var string[]
     */
    public $deliveryType = [
        'send' => 'Cửa hàng tự giao',
        'express' => 'Giao qua đơn vị vận chuyển',
        'fictitious' => 'Giao hàng ảo',
        'delivery_part_split' => 'Tách đơn, đã giao một phần',
        'delivery_split' => 'Tách đơn, đã giao xong'
    ];

    /**
     * StoreOrderProductServices constructor.
     * @param StoreOrderDao $dao
     */
    public function __construct(StoreOrderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param array $field
     * @param array $with
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderList(array $where, array $field = ['*'], array $with = [])
    {
        [$page, $limit] = $this->getPageValue();
        $data = $this->dao->getOrderList($where, $field, $page, $limit, $with);
        $count = $this->dao->count($where, false);
        $data = $this->tidyOrderList($data);
        foreach ($data as &$item) {
            $refund_num = array_sum(array_column($item['refund'], 'refund_num'));
            $cart_num = 0;
            $vipTruePrice = 0;
            foreach ($item['_info'] as $items) {
                $cart_num += $items['cart_info']['cart_num'];
                $vipTruePrice = bcadd((string)$vipTruePrice, bcmul((string)$items['cart_info']['vip_truePrice'], (string)$items['cart_info']['cart_num'], 2), 2);
            }
            $item['total_price'] = bcadd($item['total_price'], $vipTruePrice, 2);
            $item['is_all_refund'] = $refund_num == $cart_num;
            $item['pay_price'] = (float)$item['pay_price'];
        }
        return compact('data', 'count');
    }

    /**
     * Danh sách đơn hàng phía trước
     * @param array $where
     * @param array|string[] $field
     * @param array $with
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderApiList(array $where, array $field = ['*'], array $with = [])
    {
        [$page, $limit] = $this->getPageValue();
        $data = $this->dao->getOrderList($where, $field, $page, $limit, $with);
        foreach ($data as &$item) {
            $item = $this->tidyOrder($item, true);
            foreach ($item['cartInfo'] ?: [] as $key => $product) {
                if ($item['_status']['_type'] == 3) {
                    $item['cartInfo'][$key]['add_time'] = isset($product['add_time']) ? date('Y-m-d H:i', (int)$product['add_time']) : 'Thời gian không hợp lệ';
                }
                $item['cartInfo'][$key]['productInfo']['price'] = $product['truePrice'] ?? 0;
            }
            if (count($item['refund'])) {
                $refund_num = array_sum(array_column($item['refund'], 'refund_num'));
                $cart_num = array_sum(array_column($item['cartInfo'], 'cart_num'));
                $item['is_all_refund'] = $refund_num == $cart_num ? true : false;
            } else {
                $item['is_all_refund'] = false;
            }
        }
        return $data;
    }

    /**
     * Lấy số lượng đơn hàng
     * @param int $uid
     * @return array
     * @throws \ReflectionException
     */
    public function getOrderData(int $uid = 0)
    {
        $data['order_count'] = (string)$this->dao->count(['uid' => $uid, 'refund_status' => [0, 3], 'pid' => 0, 'is_del' => 0, 'is_system_del' => 0]);
        $data['sum_price'] = (string)$this->dao->sum([
            ['uid', '=', $uid],
            ['paid', '=', 1],
            ['refund_status', '=', 0],
            ['pid', '>=', 0]
        ], 'pay_price', false);
        $countWhere = ['is_del' => 0, 'is_system_del' => 0];
        if ($uid) {
            $countWhere['uid'] = $uid;
        }
        $data['unpaid_count'] = (string)$this->dao->count(['status' => 0] + $countWhere);
        $data['unshipped_count'] = (string)$this->dao->count(['status' => 1] + $countWhere + ['pid' => 0]);
        $data['received_count'] = (string)$this->dao->count(['status' => 2] + $countWhere + ['pid' => 0]);
        $data['evaluated_count'] = (string)$this->dao->count(['status' => 3] + $countWhere + ['pid' => 0]);
        $data['complete_count'] = (string)$this->dao->count(['status' => 4] + $countWhere + ['pid' => 0]);

        /** @var StoreOrderRefundServices $storeOrderRefundServices */
        $storeOrderRefundServices = app()->make(StoreOrderRefundServices::class);
        $refund_where = ['is_cancel' => 0];
        if ($uid) $refund_where['uid'] = $uid;
        $data['refunding_count'] = (string)$storeOrderRefundServices->count($refund_where + ['refund_type' => [1, 2, 4, 5]]);
        $data['no_refund_count'] = (string)$storeOrderRefundServices->count($refund_where + ['refund_type' => 3]);
        $data['refunded_count'] = (string)$storeOrderRefundServices->count($refund_where + ['refund_type' => 6]);
        $data['refund_count'] = bcadd(bcadd($data['refunding_count'], $data['refunded_count'], 0), $data['no_refund_count'], 0);
        $data['yue_pay_status'] = (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1 ? (int)1 : (int)2;//Thanh toán bằng số dư: 1 là bật, 2 là tắt
        $data['pc_order_count'] = $data['order_count'] + $data['refunding_count'] + $data['refunded_count'];
        $data['pay_weixin_open'] = sys_config('pay_weixin_open', '0') != '0';//WeChat Pay: 1 là bật, 0 là tắt
        $data['ali_pay_status'] = sys_config('ali_pay_status', '0') != '0';//Thanh toán Alipay, 1 là mở, 0 là tắt
        $data['friend_pay_status'] = (int)sys_config('friend_pay_status') ?? 0;//Bạn bè thanh toán giúp, 1 là mở, 0 là tắt
        return $data;
    }

    /**
     * Định dạng dữ liệu chi tiết đơn hàng
     * @param $order
     * @param bool $detail Có cần chi tiết sản phẩm trong đơn hàng không
     * @param bool $isPic Có cần hình ảnh trạng thái đơn hàng không
     * @return mixed
     */
    public function tidyOrder($order, bool $detail = false, $isPic = false)
    {
        if ($detail == true && isset($order['id'])) {
            /** @var StoreOrderCartInfoServices $cartServices */
            $cartServices = app()->make(StoreOrderCartInfoServices::class);
            $cartInfos = $cartServices->getCartColunm(['oid' => $order['id']], 'cart_num,surplus_num,cart_info,refund_num', 'unique');
            $info = [];
            /** @var StoreProductReplyServices $replyServices */
            $replyServices = app()->make(StoreProductReplyServices::class);
            foreach ($cartInfos as $k => $cartInfo) {
                $cart = json_decode($cartInfo['cart_info'], true);
                $cart['cart_num'] = $cartInfo['cart_num'];
                $cart['surplus_num'] = $cartInfo['surplus_num'];
                $cart['refund_num'] = $cartInfo['refund_num'];
                $cart['surplus_refund_num'] = $cartInfo['surplus_num'] - $cartInfo['refund_num'];
                $cart['unique'] = $k;
                //Thêm trường có đánh giá hay không
                $cart['is_reply'] = $replyServices->count(['unique' => $k]);
                if (isset($cart['productInfo']['attrInfo'])) {
                    $cart['productInfo']['attrInfo'] = get_thumb_water($cart['productInfo']['attrInfo']);
                }
                $cart['productInfo'] = get_thumb_water($cart['productInfo']);
                //Mua nhiều sản phẩm cùng loại thì tính tổng ưu đãi
                $cart['vip_sum_truePrice'] = bcmul($cart['vip_truePrice'], $cart['cart_num'] ? $cart['cart_num'] : 1, 2);
                $cart['is_valid'] = 1;
                array_push($info, $cart);
                unset($cart);
            }
            $order['cartInfo'] = $info;
        }
        /** @var StoreOrderStatusServices $statusServices */
        $statusServices = app()->make(StoreOrderStatusServices::class);
        $status = [];
        if ($order['is_cancel']) {
            $status['_type'] = 4;
            $status['_title'] = 'Đã hủy';
            $status['_msg'] = 'Bạn đã hủy đơn hàng, cảm ơn bạn đã sử dụng';
            $status['_class'] = 'nobuy';
        } else {
            if (!$order['paid'] && $order['pay_type'] == 'offline' && !$order['status'] >= 2) {
                $status['_type'] = 9;
                $status['_title'] = 'Thanh toán ngoại tuyến, chưa thanh toán';
                $status['_msg'] = 'Chờ người bán xử lý, vui lòng kiên nhẫn chờ đợi';
                $status['_class'] = 'nobuy';
            } else if (!$order['paid']) {
                $status['_type'] = 0;
                $status['_title'] = 'Chưa thanh toán';
                //Khoảng thời gian hủy đơn hàng do hệ thống định trước
                $keyValue = ['order_cancel_time', 'order_activity_time', 'order_bargain_time', 'order_seckill_time', 'order_pink_time'];
                //Lấy cấu hình
                $systemValue = SystemConfigService::more($keyValue);
                //Định dạng dữ liệu
                $systemValue = Arr::setValeTime($keyValue, is_array($systemValue) ? $systemValue : []);
                if ($order['pink_id'] || $order['combination_id']) {
                    $order_pink_time = $systemValue['order_pink_time'] ?: $systemValue['order_activity_time'];
                    $time = $order['add_time'] + $order_pink_time * 3600;
                    $status['_msg'] = 'Vui lòng hoàn tất thanh toán trước' . date('m-d H:i:s', $time) . '!';
                } else if ($order['seckill_id']) {
                    $order_seckill_time = $systemValue['order_seckill_time'] ?: $systemValue['order_activity_time'];
                    $time = $order['add_time'] + $order_seckill_time * 3600;
                    $status['_msg'] = 'Vui lòng hoàn tất thanh toán trước' . date('m-d H:i:s', $time) . '!';
                } else if ($order['bargain_id']) {
                    $order_bargain_time = $systemValue['order_bargain_time'] ?: $systemValue['order_activity_time'];
                    $time = $order['add_time'] + $order_bargain_time * 3600;
                    $status['_msg'] = 'Vui lòng hoàn tất thanh toán trước' . date('m-d H:i:s', $time) . '!';
                } else {
                    $time = $order['add_time'] + $systemValue['order_cancel_time'] * 3600;
                    $status['_msg'] = 'Vui lòng hoàn tất thanh toán trước' . date('m-d H:i:s', (int)$time) . '!';
                }
                $status['_class'] = 'nobuy';
            } else if ($order['status'] == 4) {
                if ($order['delivery_type'] == 'send') {//TODO giao hàng
                    $status['_type'] = 1;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery'], 'change_time')) . 'người bán đã cử nhân viên giao hàng';
                    $status['_class'] = 'state-ysh';
                } elseif ($order['delivery_type'] == 'express') {//TODO giao hàng
                    $status['_type'] = 1;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery_goods'], 'change_time')) . 'người bán đã giao hàng';
                    $status['_class'] = 'state-ysh';
                } elseif ($order['delivery_type'] == 'split') {//Giao hàng theo phần tách đơn
                    $status['_type'] = 1;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery_part_split'], 'change_time')) . 'người bán đã tách thành nhiều kiện để giao hàng';
                    $status['_class'] = 'state-ysh';
                } else {
                    $status['_type'] = 1;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery_fictitious'], 'change_time')) . 'người bán đã giao hàng ảo';
                    $status['_class'] = 'state-ysh';
                }
            } else if ($order['refund_status'] == 1) {
                if (in_array($order['refund_type'], [0, 1, 2])) {
                    $status['_type'] = -1;
                    $status['_title'] = 'Đang yêu cầu hoàn tiền';
                    $status['_msg'] = 'Người bán đang xét duyệt, vui lòng kiên nhẫn chờ đợi';
                    $status['_class'] = 'state-sqtk';
                } elseif ($order['refund_type'] == 4) {
                    $status['_type'] = -1;
                    $status['_title'] = 'Đang yêu cầu hoàn tiền';
                    $status['_msg'] = 'Người bán đã đồng ý hoàn tiền, vui lòng nhập mã vận đơn trả hàng';
                    $status['_class'] = 'state-sqtk';
                    $status['refund_name'] = sys_config('refund_name', '');
                    $status['refund_phone'] = sys_config('refund_phone', '');
                    $status['refund_address'] = sys_config('refund_address', '');
                } elseif ($order['refund_type'] == 5) {
                    $status['_type'] = -1;
                    $status['_title'] = 'Đang yêu cầu hoàn tiền';
                    $status['_msg'] = 'Chờ người bán nhận hàng';
                    $status['_class'] = 'state-sqtk';
                    $status['refund_name'] = sys_config('refund_name', '');
                    $status['refund_phone'] = sys_config('refund_phone', '');
                    $status['refund_address'] = sys_config('refund_address', '');
                }
            } else if ($order['refund_status'] == 2 || $order['refund_type'] == 6) {
                $status['_type'] = -2;
                $status['_title'] = 'Đã hoàn tiền';
                $status['_msg'] = 'Đã hoàn tiền cho bạn, cảm ơn bạn đã ủng hộ';
                $status['_class'] = 'state-sqtk';
            } else if ($order['refund_status'] == 3) {
                $status['_type'] = -1;
                $status['_title'] = 'Hoàn tiền một phần (đơn con)';
                $status['_msg'] = 'Tách đơn giao hàng, hoàn tiền một phần';
                $status['_class'] = 'state-sqtk';
            } else if ($order['refund_status'] == 4) {
                $status['_type'] = -1;
                $status['_title'] = 'Tất cả đơn con đều đang yêu cầu hoàn tiền';
                $status['_msg'] = 'Tách đơn giao hàng, hoàn tiền toàn bộ';
                $status['_class'] = 'state-sqtk';
            } else if (!$order['status']) {
                if ($order['pink_id']) {
                    /** @var StorePinkServices $pinkServices */
                    $pinkServices = app()->make(StorePinkServices::class);
                    if ($pinkServices->getCount(['id' => $order['pink_id'], 'status' => 1])) {
                        $status['_type'] = 1;
                        $status['_title'] = 'Đang mua chung';
                        $status['_msg'] = 'Chờ người khác tham gia mua chung';
                        $status['_class'] = 'state-nfh';
                    } else {
                        $status['_type'] = 1;
                        $status['_title'] = 'Chưa giao hàng';
                        $status['_msg'] = 'Người bán chưa giao hàng, vui lòng kiên nhẫn chờ đợi';
                        $status['_class'] = 'state-nfh';
                    }
                } else {
                    if ($order['shipping_type'] === 1) {
                        $status['_type'] = 1;
                        $status['_title'] = 'Chưa giao hàng';
                        if ($order['advance_id']) {
                            $status['_msg'] = date('Y-m-d', $order['cartInfo'][0]['productInfo']['presale_end_time']) . 'Sau khi kết thúc đặt trước' . $order['cartInfo'][0]['productInfo']['presale_day'] . 'ngày sẽ giao hàng, vui lòng kiên nhẫn chờ đợi';
                        } else {
                            $status['_msg'] = 'Người bán chưa giao hàng, vui lòng kiên nhẫn chờ đợi';
                        }
                        $status['_class'] = 'state-nfh';
                    } elseif ($order['shipping_type'] === 2) {
                        $status['_type'] = 1;
                        $status['_title'] = 'Chờ xác nhận sử dụng';
                        $status['_msg'] = 'Chờ xác nhận sử dụng, vui lòng đến điểm nhận hàng để quét mã xác nhận';
                        $status['_class'] = 'state-nfh';
                    } else {
                        $status['_type'] = 1;
                        $status['_title'] = 'Chờ nhận quà';
                        $status['_msg'] = 'Chờ nhận quà, hãy tặng món quà này cho bạn bè nhé!';
                        $status['_class'] = 'state-nfh';
                    }
                }
            } else if ($order['status'] == 1) {
                if ($order['delivery_type'] == 'send') {//TODO giao hàng
                    $status['_type'] = 2;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery'], 'change_time')) . 'người bán đã cử nhân viên giao hàng';
                    $status['_class'] = 'state-ysh';
                } elseif ($order['delivery_type'] == 'express') {//TODO giao hàng
                    $status['_type'] = 2;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery_goods'], 'change_time')) . 'người bán đã giao hàng';
                    $status['_class'] = 'state-ysh';
                } elseif ($order['delivery_type'] == 'split') {//Giao hàng theo phần tách đơn
                    $status['_type'] = 2;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery_split'], 'change_time')) . 'người bán đã tách thành nhiều kiện để giao hàng';
                    $status['_class'] = 'state-ysh';
                } else {
                    $status['_type'] = 2;
                    $status['_title'] = 'Chờ nhận hàng';
                    $status['_msg'] = date('d/m H:i', $statusServices->value(['oid' => $order['id'], 'change_type' => 'delivery_fictitious'], 'change_time')) . 'người bán đã giao hàng ảo';
                    $status['_class'] = 'state-ysh';
                }
            } else if ($order['status'] == 2) {
                $status['_type'] = 3;
                $status['_title'] = 'Chờ đánh giá';
                $status['_msg'] = 'Đã nhận hàng, hãy đánh giá ngay nhé';
                $status['_class'] = 'state-ypj';
            } else if ($order['status'] == 3) {
                $status['_type'] = 4;
                $status['_title'] = 'Giao dịch hoàn tất';
                $status['_msg'] = 'Giao dịch hoàn tất, cảm ơn bạn đã ủng hộ';
                $status['_class'] = 'state-ytk';
            }
        }
        if (isset($order['pay_type']))
            $status['_payType'] = $status['_type'] == 0 ? '' : PayServices::PAY_TYPE[$order['pay_type']] ?? 'Phương thức khác';
        if (isset($order['delivery_type']))
            $status['_deliveryType'] = $this->deliveryType[$order['delivery_type']] ?? 'Phương thức khác';
        $order['_status'] = $status;
        $order['_pay_time'] = isset($order['pay_time']) && $order['pay_time'] != null ? date('Y-m-d H:i:s', $order['pay_time']) : '';
        $order['_add_time'] = isset($order['add_time']) ? (strstr((string)$order['add_time'], '-') === false ? date('Y-m-d H:i:s', $order['add_time']) : $order['add_time']) : '';

        //Khoảng thời gian hủy đơn hàng do hệ thống định trước
        $keyValue = ['order_cancel_time', 'order_activity_time', 'order_bargain_time', 'order_seckill_time', 'order_pink_time'];
        //Lấy cấu hình
        $systemValue = SystemConfigService::more($keyValue);
        //Định dạng dữ liệu
        $systemValue = Arr::setValeTime($keyValue, is_array($systemValue) ? $systemValue : []);
        if ($order['seckill_id']) {
            $secs = $systemValue['order_seckill_time'] ? $systemValue['order_seckill_time'] : $systemValue['order_activity_time'];
        } elseif ($order['bargain_id']) {
            $secs = $systemValue['order_bargain_time'] ? $systemValue['order_bargain_time'] : $systemValue['order_activity_time'];
        } elseif ($order['combination_id']) {
            $secs = $systemValue['order_pink_time'] ? $systemValue['order_pink_time'] : $systemValue['order_activity_time'];
        } else {
            $secs = $systemValue['order_cancel_time'];
        }
        $order['stop_time'] = $secs * 3600 + $order['add_time'];
        $order['status_pic'] = '';
        //Lấy hình ảnh trạng thái sản phẩm
        if ($isPic) {
            $order_details_images = sys_data('order_details_images') ?: [];
            foreach ($order_details_images as $image) {
                if (isset($image['order_status']) && $image['order_status'] == $order['_status']['_type']) {
                    $order['status_pic'] = $image['pic'];
                    break;
                }
            }
        }
        if ($order['seckill_id'] || $order['bargain_id'] || $order['combination_id'] || $order['advance_id']) {
            if ($order['seckill_id']) $order['type'] = 1;
            if ($order['bargain_id']) $order['type'] = 2;
            if ($order['combination_id']) $order['type'] = 3;
            if ($order['advance_id']) $order['type'] = 4;
        }
        $order['offlinePayStatus'] = (int)sys_config('offline_pay_status') ?? (int)2;
        $log = $statusServices->getColumn(['oid' => $order['id']], 'change_time', 'change_type');
        if (isset($log['delivery'])) {
            $delivery = date('Y-m-d', $log['delivery']);
        } elseif (isset($log['delivery_goods'])) {
            $delivery = date('Y-m-d', $log['delivery_goods']);
        } elseif (isset($log['delivery_fictitious'])) {
            $delivery = date('Y-m-d', $log['delivery_fictitious']);
        } else {
            $delivery = '';
        }
        $order['order_log'] = [
            'create' => isset($log['cache_key_create_order']) ? date('Y-m-d', $log['cache_key_create_order']) : '',
            'pay' => isset($log['pay_success']) ? date('Y-m-d', $log['pay_success']) : '',
            'delivery' => $delivery,
            'take' => isset($log['take_delivery']) ? date('Y-m-d', $log['take_delivery']) : '',
            'complete' => isset($log['check_order_over']) ? date('Y-m-d', $log['check_order_over']) : '',
        ];

        $order['gift_user_info'] = [
            'gift_uid' => $order['gift_uid'],
            'gift_nickname' => '',
            'gift_avatar' => '',
        ];
        if ($order['gift_uid'] != 0) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $giftUser = $userServices->get($order['gift_uid'], ['nickname', 'avatar']);
            $order['gift_user_info'] = [
                'gift_uid' => $order['gift_uid'],
                'gift_nickname' => $giftUser['nickname'],
                'gift_avatar' => $giftUser['avatar'],
            ];
        }
        return $order;
    }

    /**
     * Chuyển đổi dữ liệu
     * @param array $data
     * @return array
     */
    public function tidyOrderList(array $data)
    {
        /** @var StoreOrderCartInfoServices $services */
        $services = app()->make(StoreOrderCartInfoServices::class);
        foreach ($data as &$item) {
            $item['_info'] = $services->getOrderCartInfo((int)$item['id']);
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['_refund_time'] = isset($item['refund_reason_time']) && $item['refund_reason_time'] ? date('Y-m-d H:i:s', $item['refund_reason_time']) : '';
            $item['_pay_time'] = isset($item['pay_time']) && $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '';
            if (($item['pink_id'] || $item['combination_id']) && isset($item['pinkStatus'])) {
                switch ($item['pinkStatus']) {
                    case 1:
                        $item['pink_name'] = '[Đơn mua chung] Đang diễn ra';
                        $item['color'] = '#f00';
                        break;
                    case 2:
                        $item['pink_name'] = '[Đơn mua chung] Đã hoàn thành';
                        $item['color'] = '#00f';
                        break;
                    case 3:
                        $item['pink_name'] = '[Đơn mua chung] Chưa hoàn thành';
                        $item['color'] = '#f0f';
                        break;
                    default:
                        $item['pink_name'] = '[Đơn mua chung] Đơn hàng cũ';
                        $item['color'] = '#FF7D00';
                        break;
                }
            } elseif ($item['combination_id']) {
                $item['pink_name'] = '[Đơn mua chung]';
                $item['color'] = '#FF7D00';
            } elseif ($item['seckill_id']) {
                $item['pink_name'] = '[Đơn flash sale]';
                $item['color'] = '#3491FA';
            } elseif ($item['bargain_id']) {
                $item['pink_name'] = '[Đơn săn giảm giá]';
                $item['color'] = '#F7BA1E';
            } elseif ($item['advance_id']) {
                $item['pink_name'] = '[Đơn đặt trước]';
                $item['color'] = '#B27FEB';
            } else {
                if ($item['shipping_type'] == 1) {
                    $item['pink_name'] = '[Đơn thường]';
                    $item['color'] = '#333';
                } else if ($item['shipping_type'] == 2) {
                    $item['pink_name'] = '[Đơn xác nhận sử dụng]';
                    $item['color'] = '#8956E8';
                }
            }
            if ($item['paid'] == 1) {
                switch ($item['pay_type']) {
                    case PayServices::WEIXIN_PAY:
                        $item['pay_type_name'] = 'WeChat Pay';
                        break;
                    case PayServices::YUE_PAY:
                        $item['pay_type_name'] = 'Thanh toán bằng số dư';
                        break;
                    case PayServices::OFFLINE_PAY:
                        $item['pay_type_name'] = 'Thanh toán ngoại tuyến';
                        break;
                    case PayServices::ALIAPY_PAY:
                        $item['pay_type_name'] = 'Thanh toán Alipay';
                        break;
                    case PayServices::ALLIN_PAY:
                        $item['pay_type_name'] = 'Allinpay';
                        break;
                    default:
                        $item['pay_type_name'] = 'Thanh toán khác';
                        break;
                }
            } else {
                switch ($item['pay_type']) {
                    case 'offline':
                        $item['pay_type_name'] = 'Thanh toán ngoại tuyến';
                        $item['pay_type_info'] = 1;
                        break;
                    default:
                        $item['pay_type_name'] = '';
                        break;
                }
            }
            $status_name = ['status_name' => '', 'pics' => []];
            if ($item['paid'] == 0 && $item['status'] == 0) {
                $status_name['status_name'] = 'Chưa thanh toán';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['shipping_type'] == 1 && $item['refund_status'] == 0) {
                $status_name['status_name'] = $item['combination_id'] && isset($item['pinkStatus']) && $item['pinkStatus'] == 1 ? 'Chưa giao hàng (đang mua chung)' : 'Chưa giao hàng';
            } else if ($item['paid'] == 1 && $item['status'] == 4 && $item['shipping_type'] == 1 && $item['refund_status'] == 0) {
                $status_name['status_name'] = 'Giao một phần';
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['shipping_type'] == 2 && $item['refund_status'] == 0) {
                $status_name['status_name'] = 'Chưa xác nhận sử dụng';
            } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['shipping_type'] == 1 && $item['refund_status'] == 0) {
                $status_name['status_name'] = 'Chờ nhận hàng';
            } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['shipping_type'] == 2 && $item['refund_status'] == 0) {
                $status_name['status_name'] = 'Chưa xác nhận sử dụng';
            } else if ($item['paid'] == 1 && $item['status'] == 2 && $item['refund_status'] == 0) {
                $status_name['status_name'] = 'Chờ đánh giá';
            } else if ($item['paid'] == 1 && $item['status'] == 3 && $item['refund_status'] == 0) {
                $status_name['status_name'] = 'Đã hoàn thành';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                $refundReasonTime = date('Y-m-d H:i', $item['refund_reason_time']);
                $refundReasonWapImg = json_decode($item['refund_reason_wap_img'], true);
                $refundReasonWapImg = $refundReasonWapImg ?: [];
                $img = [];
                if (count($refundReasonWapImg)) {
                    foreach ($refundReasonWapImg as $itemImg) {
                        if (strlen(trim($itemImg)))
                            $img[] = $itemImg;
                    }
                }
                $status_name['status_name'] = 'Đang hoàn tiền';
                $status_name['pics'] = $img;
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $status_name['status_name'] = 'Đã hoàn tiền';
            } else if ($item['paid'] == 1 && $item['refund_status'] == 3) {
                $status_name['status_name'] = <<<HTML
<b style="color:#f124c7">Hoàn tiền một phần</b><br/>
HTML;
            } else if ($item['paid'] == 1 && $item['refund_status'] == 4) {
                $status_name['status_name'] = <<<HTML
<b style="color:#f124c7">Đang hoàn tiền</b><br/>
HTML;
            }
            $item['status_name'] = $status_name;
            if ($item['paid'] == 0 && $item['status'] == 0 && $item['refund_status'] == 0) {
                $item['_status'] = 1;//Chưa thanh toán
            } else if ($item['paid'] == 1 && $item['status'] == 0 && $item['refund_status'] == 0) {
                $item['_status'] = 2;//Đã thanh toán, chưa giao hàng
            } else if ($item['paid'] == 1 && $item['status'] == 4 && $item['refund_status'] == 0) {
                $item['_status'] = 8;//Đã thanh toán, giao hàng một phần
            } else if ($item['paid'] == 1 && $item['refund_status'] == 1) {
                $item['_status'] = 3;//Đã thanh toán, đang yêu cầu hoàn tiền
            } else if ($item['paid'] == 1 && $item['status'] == 1 && $item['refund_status'] == 0) {
                $item['_status'] = 4;//Đã thanh toán, chờ nhận hàng
            } else if ($item['paid'] == 1 && $item['status'] == 2 && $item['refund_status'] == 0) {
                $item['_status'] = 5;//Đã thanh toán, chờ đánh giá
            } else if ($item['paid'] == 1 && $item['status'] == 3 && $item['refund_status'] == 0) {
                $item['_status'] = 6;//Đã thanh toán, đã hoàn thành
            } else if ($item['paid'] == 1 && $item['refund_status'] == 2) {
                $item['_status'] = 7;//Đã thanh toán, đã hoàn tiền
            } else if ($item['paid'] == 1 && $item['refund_status'] == 3 && $item['status'] == 4) {
                $item['_status'] = 9;//Giao hàng theo tách đơn, yêu cầu hoàn tiền một phần
            } else if ($item['paid'] == 1 && $item['refund_status'] == 4) {
                $item['_status'] = 10;//Giao hàng theo tách đơn, đã yêu cầu hoàn tiền toàn bộ
            } else if ($item['paid'] == 1 && $item['refund_status'] == 3 && $item['status'] == 0) {
                $item['_status'] = 11;//Hoàn tiền theo tách đơn, chưa giao hàng
            }
            if ($item['clerk_id'] == 0 && !isset($item['clerk_name'])) {
                $item['clerk_name'] = 'Nền tảng chính';
            }

            if ($item['store_id']) {
                $store = app()->make(SystemStoreServices::class);
                $storeOne = $store->value(['id' => $item['store_id']], 'name');
                if ($storeOne) $item['store_name'] = $storeOne;
            }

            //Đổi store_name theo nhân viên xác nhận sử dụng
            if ($item['clerk_id'] && isset($item['staff_store_id']) && $item['staff_store_id']) {
                /** @var SystemStoreServices $store */
                $store = app()->make(SystemStoreServices::class);
                $storeOne = $store->value(['id' => $item['staff_store_id']], 'name');
                if ($storeOne) $item['store_name'] = $storeOne;
            }
        }
        return $data;
    }

    /**
     * Xử lý số tiền đơn hàng
     * @param $where
     * @return array
     */
    public function getOrderPrice($where)
    {
        if (isset($where['refund_type']) && $where['refund_type']) unset($where['refund_type']);
        $where['is_del'] = 0;//Đơn hàng đã xóa thì không thống kê
        $price['today_pay_price'] = 0;//Số tiền thanh toán hôm nay
        $price['pay_price'] = 0;//Số tiền thanh toán
        $price['refund_price'] = 0;//Số tiền hoàn
        $price['pay_price_wx'] = 0;//Số tiền thanh toán WeChat Pay
        $price['pay_price_yue'] = 0;//Số tiền thanh toán bằng số dư
        $price['pay_price_offline'] = 0;//Số tiền thanh toán ngoại tuyến
        $price['pay_price_other'] = 0;//Số tiền thanh toán khác
        $price['use_integral'] = 0;//Điểm thưởng người dùng đã dùng
        $price['back_integral'] = 0;//Tổng số điểm thưởng hoàn lại
        $price['deduction_price'] = 0;//Số tiền khấu trừ
        $price['total_num'] = 0; //Tổng số sản phẩm
        $price['today_count_sum'] = 0; //Tổng số đơn hàng hôm nay
        $price['count_sum'] = 0; //Tổng số đơn hàng
        $price['brokerage'] = 0;
        $price['pay_postage'] = 0;
        $whereData = ['is_del' => 0];
        if ($where['status'] == '' && $where['pay_type'] != 3) {
            $whereData['paid'] = 1;
        }
        $ids = $this->dao->column($where + $whereData, 'id');
        if (count($ids)) {
            /** @var UserBillServices $services */
            $services = app()->make(UserBillServices::class);
            $price['brokerage'] = $services->getBrokerageNumSum($ids);
        }
        $price['refund_price'] = $this->dao->together($where + ['is_del' => 0, 'paid' => 1, 'refund_status' => 2], 'refund_price');
        $sumNumber = $this->dao->search($where + $whereData)->field([
            'sum(total_num) as sum_total_num',
            'count(id) as count_sum',
            'sum(pay_price) as sum_pay_price',
            'sum(pay_postage) as sum_pay_postage',
            'sum(use_integral) as sum_use_integral',
            'sum(back_integral) as sum_back_integral',
            'sum(deduction_price) as sum_deduction_price'
        ])->find();
        if ($sumNumber) {
            $price['count_sum'] = $sumNumber['count_sum'];
            $price['total_num'] = $sumNumber['sum_total_num'];
            $price['pay_price'] = $sumNumber['sum_pay_price'];
            $price['pay_postage'] = $sumNumber['sum_pay_postage'];
            $price['use_integral'] = $sumNumber['sum_use_integral'];
            $price['back_integral'] = $sumNumber['sum_back_integral'];
            $price['deduction_price'] = $sumNumber['sum_deduction_price'];
        }
        $list = $this->dao->column($where + $whereData, 'sum(pay_price) as sum_pay_price,pay_type', 'id', 'pay_type');
        foreach ($list as $v) {
            if ($v['pay_type'] == 'weixin') {
                $price['pay_price_wx'] = $v['sum_pay_price'];
            } elseif ($v['pay_type'] == 'yue') {
                $price['pay_price_yue'] = $v['sum_pay_price'];
            } elseif ($v['pay_type'] == 'offline') {
                $price['pay_price_offline'] = $v['sum_pay_price'];
            } else {
                $price['pay_price_other'] = $v['sum_pay_price'];
            }
        }
        $where['time'] = 'today';
        $sumNumber = $this->dao->search($where + $whereData)->field([
            'count(id) as today_count_sum',
            'sum(pay_price) as today_pay_price',
        ])->find();
        if ($sumNumber) {
            $price['today_count_sum'] = $sumNumber['today_count_sum'];
            $price['today_pay_price'] = $where['status'] !== 0 ? $sumNumber['today_pay_price'] : 0;
        }
        return $price;
    }

    /**
     * Lấy dữ liệu thống kê trang danh sách đơn hàng
     * @param $where
     * @return array
     */
    public function getBadge($where)
    {
        $price = $this->getOrderPrice($where);
        return [
            [
                'name' => 'Số lượng đơn hàng',
                'field' => 'cái',
                'count' => $price['count_sum'],
                'className' => 'md-basket',
                'col' => 6
            ],
            [
                'name' => 'Số tiền đơn hàng',
                'field' => 'đ',
                'count' => $price['pay_price'],
                'className' => 'md-pricetags',
                'col' => 6
            ],
            [
                'name' => 'Số lượng đơn hàng hôm nay',
                'field' => 'cái',
                'count' => $price['today_count_sum'],
                'className' => 'ios-chatbubbles',
                'col' => 6
            ],
            [
                'name' => 'Số tiền thanh toán hôm nay',
                'field' => 'đ',
                'count' => $price['today_pay_price'],
                'className' => 'ios-cash',
                'col' => 6
            ],
        ];
    }

    /**
     *
     * @param array $where
     * @return mixed
     */
    /**
     * @param array $where
     * @return array
     * @throws \ReflectionException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/3/14
     */
    public function orderCount(array $where)
    {
        $where['is_system_del'] = 0;
        $where['pid'] = 0;
        $data['un_paid'] = $this->dao->count($where + ['status' => 0], false);
        $data['un_send'] = $this->dao->count($where + ['status' => 1, 'shipping_type' => 1], false);
        return $data;
    }

    /**
     * Tạo form sửa đơn hàng
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function updateForm(int $id)
    {
        $product = $this->dao->get($id);
        if (!$product) {
            throw new AdminException(100026);
        }
        $f = [];
        $f[] = Form::input('order_id', 'Mã đơn hàng', $product->getData('order_id'))->disabled(true);
        $f[] = Form::hidden('total_price', (float)$product->getData('total_price'));
        $f[] = Form::hidden('pay_postage', (float)$product->getData('pay_postage') ?: 0);
        $f[] = Form::number('pay_price', 'Số tiền thanh toán thực tế', (float)$product->getData('pay_price'))->min(0);
        $f[] = Form::number('gain_integral', 'Tặng điểm thưởng', (float)$product->getData('gain_integral') ?: 0)->min(0);
        return create_form('Chỉnh sửa đơn hàng', $f, $this->url('/order/update/' . $id), 'PUT');
    }

    /**
     * Chỉnh sửa đơn hàng
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function updateOrder(int $id, array $data)
    {
        $order = $this->dao->getOne(['id' => $id, 'is_del' => 0]);
        if (!$order) {
            throw new AdminException(400118);
        }
        /** @var StoreOrderCreateServices $createServices */
        $createServices = app()->make(StoreOrderCreateServices::class);
        $data['order_id'] = $createServices->getNewOrderId('cp');
        if (sys_config('user_brokerage_type') == 1) {
            $percent = $order['pay_price'] != 0 ? bcdiv((string)$data['pay_price'], (string)$order['pay_price'], 6) : $order['pay_price'];
            if ($order['one_brokerage'] > 0) {
                $data['one_brokerage'] = bcmul((string)$order['one_brokerage'], $percent, 2);
            }
            if ($order['two_brokerage'] > 0) {
                $data['two_brokerage'] = bcmul((string)$order['two_brokerage'], $percent, 2);
            }
            if ($order['staff_brokerage'] > 0) {
                $data['staff_brokerage'] = bcmul((string)$order['staff_brokerage'], $percent, 2);
            }
            if ($order['agent_brokerage'] > 0) {
                $data['agent_brokerage'] = bcmul((string)$order['agent_brokerage'], $percent, 2);
            }
            if ($order['division_brokerage'] > 0) {
                $data['division_brokerage'] = bcmul((string)$order['division_brokerage'], $percent, 2);
            }
        }
        /** @var StoreOrderStatusServices $services */
        $services = app()->make(StoreOrderStatusServices::class);
        return $this->transaction(function () use ($id, $data, $services) {
            $res = $this->dao->update($id, $data);
            $res = $res && $services->save([
                    'oid' => $id,
                    'change_type' => 'order_edit',
                    'change_time' => time(),
                    'change_message' => 'Sửa tổng giá sản phẩm thành:' . $data['total_price'] . ' số tiền thanh toán thực tế' . $data['pay_price']
                ]);
            if (isset($data['gain_integral'])) {
                $res = $res && $services->save([
                        'oid' => $id,
                        'change_type' => 'order_edit',
                        'change_time' => time(),
                        'change_message' => 'Sửa điểm thưởng tặng của đơn hàng thành:' . $data['gain_integral']
                    ]);
            }
            if ($res) {
                $order = $this->dao->getOne(['id' => $id, 'is_del' => 0]);
                //Nhắc nhở SMS khi đổi giá
                event('NoticeListener', [['order' => $order, 'pay_price' => $data['pay_price']], 'price_revision']);
                //Tin nhắn tùy chỉnh - đổi giá đơn hàng
                $order['change_price'] = $data['pay_price'];
                event('NoticeListener', [$order['uid'], $order, 'price_change_price']);

                //Sự kiện tùy chỉnh - đổi giá đơn hàng
                event('CustomEventListener', ['admin_order_change', [
                    'uid' => $order['uid'],
                    'order_id' => $data['order_id'],
                    'pay_price' => $data['pay_price'],
                    'gain_integral' => $data['gain_integral'] ?? $order['gain_integral'],
                    'change_time' => date('Y-m-d H:i:s'),
                ]]);

                return $data['order_id'];
            } else {
                throw new AdminException(100007);
            }
        });
    }

    /**
     * Biểu đồ đơn hàng
     * @param $cycle
     * @return array
     */
    public function orderCharts($cycle)
    {
        $datalist = [];
        switch ($cycle) {
            case 'thirtyday':
                $datebefor = date('Y-m-d', strtotime('-30 day'));
                $dateafter = date('Y-m-d 23:59:59');
                //Kỳ trước
                $pre_datebefor = date('Y-m-d', strtotime('-60 day'));
                $pre_dateafter = date('Y-m-d', strtotime('-30 day'));
                for ($i = -29; $i <= 0; $i++) {
                    $datalist[date('m-d', strtotime($i . ' day'))] = date('m-d', strtotime($i . ' day'));
                }
                $order_list = $this->dao->orderAddTimeList($datebefor, $dateafter, '30');
                if (empty($order_list)) return ['yAxis' => [], 'legend' => [], 'xAxis' => [], 'serise' => [], 'pre_cycle' => [], 'cycle' => []];
                foreach ($order_list as $k => &$v) {
                    $order_list[$v['day']] = $v;
                }
                $cycle_list = [];
                foreach ($datalist as $dk => $dd) {
                    if (!empty($order_list[$dd])) {
                        $cycle_list[$dd] = $order_list[$dd];
                    } else {
                        $cycle_list[$dd] = ['count' => 0, 'day' => $dd, 'price' => ''];
                    }
                }
                $chartdata = [];
                $data = [];//Tạm thời
                $chartdata['yAxis']['maxnum'] = 0;//Số lượng giá trị lớn nhất
                $chartdata['yAxis']['maxprice'] = 0;//Số tiền giá trị lớn nhất
                foreach ($cycle_list as $k => $v) {
                    $data['day'][] = $v['day'];
                    $data['count'][] = $v['count'];
                    $data['price'][] = round($v['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['count'])
                        $chartdata['yAxis']['maxnum'] = $v['count'];//Số đơn hàng tối đa trong ngày
                    if ($chartdata['yAxis']['maxprice'] < $v['price'])
                        $chartdata['yAxis']['maxprice'] = $v['price'];//Số tiền tối đa trong ngày
                }
                $chartdata['legend'] = ['Số tiền đơn hàng', 'Số đơn hàng'];//Danh mục
                $chartdata['xAxis'] = $data['day'];//Giá trị trục X
                $series1 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series2 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series1, 'data' => $data['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'line', 'itemStyle' => $series2, 'data' => $data['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2
                //Thống kê tổng số kỳ trước
                $pre_total = $this->dao->preTotalFind($pre_datebefor, $pre_dateafter);
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //Tổng số thống kê
                $total = $this->dao->preTotalFind($datebefor, $dateafter);
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return $chartdata;
            case 'week':
                $weekarray = array(['Chủ nhật'], ['Thứ 2'], ['Thứ 3'], ['Thứ 4'], ['Thứ 5'], ['Thứ 6'], ['Thứ 7']);
                $datebefor = date('Y-m-d', strtotime('-1 week Monday'));
                $dateafter = date('Y-m-d', strtotime('-1 week Sunday'));
//                $order_list = $this->dao->orderAddTimeList($datebefor, $dateafter, 'week');
                //Xử lý lại truy vấn dữ liệu
                $new_order_list = [];
//                foreach ($order_list as $k => $v) {
//                    $new_order_list[$v['day']] = $v;
//                }
                $now_datebefor = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                $now_dateafter = date('Y-m-d', strtotime("+1 day"));
                $now_order_list = $this->dao->nowOrderList($now_datebefor, $now_dateafter, 'week');
                //Xử lý lại truy vấn dữ liệu, key chuyển thành giá trị hiện tại
                $new_now_order_list = [];
                foreach ($now_order_list as $k => $v) {
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk => $dd) {
                    if (!empty($new_order_list[$dk])) {
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    } else {
                        $weekarray[$dk]['pre'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                    if (!empty($new_now_order_list[$dk])) {
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    } else {
                        $weekarray[$dk]['now'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                }
                $chartdata = [];
                $data = [];//Tạm thời
                $chartdata['yAxis']['maxnum'] = 0;//Số lượng giá trị lớn nhất
                $chartdata['yAxis']['maxprice'] = 0;//Số tiền giá trị lớn nhất
                foreach ($weekarray as $k => $v) {
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'], 2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']) {
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count'] > $v['now']['count'] ? $v['pre']['count'] : $v['now']['count'];//Số đơn hàng tối đa trong ngày
                    }
                    if ($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']) {
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price'] > $v['now']['price'] ? $v['pre']['price'] : $v['now']['price'];//Số tiền tối đa trong ngày
                    }
                }
                $chartdata['legend'] = ['Số tiền tuần trước', 'Số tiền tuần này', 'Số đơn hàng tuần trước', 'Số đơn hàng tuần này'];//Danh mục
                $chartdata['xAxis'] = $data['day'];//Giá trị trục X
                $series1 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series2 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $series3 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series4 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series1, 'data' => $data['pre']['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series2, 'data' => $data['now']['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series3, 'data' => $data['pre']['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series4, 'data' => $data['now']['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2

                //Thống kê tổng số kỳ trước
                $pre_total = $this->dao->preTotalFind($datebefor, $dateafter);
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //Tổng số thống kê
                $total = $this->dao->preTotalFind($now_datebefor, $now_dateafter);
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return $chartdata;
            case 'month':
                $weekarray = array('01' => ['1'], '02' => ['2'], '03' => ['3'], '04' => ['4'], '05' => ['5'], '06' => ['6'], '07' => ['7'], '08' => ['8'], '09' => ['9'], '10' => ['10'], '11' => ['11'], '12' => ['12'], '13' => ['13'], '14' => ['14'], '15' => ['15'], '16' => ['16'], '17' => ['17'], '18' => ['18'], '19' => ['19'], '20' => ['20'], '21' => ['21'], '22' => ['22'], '23' => ['23'], '24' => ['24'], '25' => ['25'], '26' => ['26'], '27' => ['27'], '28' => ['28'], '29' => ['29'], '30' => ['30'], '31' => ['31']);

                $datebefor = date('Y-m-01', strtotime('-1 month'));
                $dateafter = date('Y-m-d', strtotime(date('Y-m-01')));
                $order_list = $this->dao->orderAddTimeList($datebefor, $dateafter, "month");
                //Xử lý lại truy vấn dữ liệu
                $new_order_list = [];
                foreach ($order_list as $k => $v) {
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-m-01');
                $now_dateafter = date('Y-m-d', strtotime("+1 day"));
                $now_order_list = $this->dao->nowOrderList($now_datebefor, $now_dateafter, "month");
                //Xử lý lại truy vấn dữ liệu, key chuyển thành giá trị hiện tại
                $new_now_order_list = [];
                foreach ($now_order_list as $k => $v) {
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk => $dd) {
                    if (!empty($new_order_list[$dk])) {
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    } else {
                        $weekarray[$dk]['pre'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                    if (!empty($new_now_order_list[$dk])) {
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    } else {
                        $weekarray[$dk]['now'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                }
                $chartdata = [];
                $data = [];//Tạm thời
                $chartdata['yAxis']['maxnum'] = 0;//Số lượng giá trị lớn nhất
                $chartdata['yAxis']['maxprice'] = 0;//Số tiền giá trị lớn nhất
                foreach ($weekarray as $k => $v) {
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'], 2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']) {
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count'] > $v['now']['count'] ? $v['pre']['count'] : $v['now']['count'];//Số đơn hàng tối đa trong ngày
                    }
                    if ($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']) {
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price'] > $v['now']['price'] ? $v['pre']['price'] : $v['now']['price'];//Số tiền tối đa trong ngày
                    }

                }
                $chartdata['legend'] = ['Số tiền tháng trước', 'Số tiền tháng này', 'Số đơn hàng tháng trước', 'Số đơn hàng tháng này'];//Danh mục
                $chartdata['xAxis'] = $data['day'];//Giá trị trục X
                $series1 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series2 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $series3 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series4 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series1, 'data' => $data['pre']['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series2, 'data' => $data['now']['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series3, 'data' => $data['pre']['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series4, 'data' => $data['now']['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2

                //Thống kê tổng số kỳ trước
                $pre_total = $this->dao->preTotalFind($datebefor, $dateafter);
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //Tổng số thống kê
                $total = $this->dao->preTotalFind($now_datebefor, $now_dateafter);
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return $chartdata;
            case 'year':
                $weekarray = array('01' => ['Tháng 1'], '02' => ['Tháng 2'], '03' => ['Tháng 3'], '04' => ['Tháng 4'], '05' => ['Tháng 5'], '06' => ['Tháng 6'], '07' => ['Tháng 7'], '08' => ['Tháng 8'], '09' => ['Tháng 9'], '10' => ['Tháng 10'], '11' => ['Tháng 11'], '12' => ['Tháng 12']);
                $datebefor = date('Y-01-01', strtotime('-1 year'));
                $dateafter = date('Y-12-31', strtotime('-1 year'));
                $order_list = $this->dao->orderAddTimeList($datebefor, $dateafter, 'year');
                //Xử lý lại truy vấn dữ liệu
                $new_order_list = [];
                foreach ($order_list as $k => $v) {
                    $new_order_list[$v['day']] = $v;
                }
                $now_datebefor = date('Y-01-01');
                $now_dateafter = date('Y-12-31 23:59:59');
                $now_order_list = $this->dao->nowOrderList($now_datebefor, $now_dateafter, 'year');
                //Xử lý lại truy vấn dữ liệu, key chuyển thành giá trị hiện tại
                $new_now_order_list = [];
                foreach ($now_order_list as $k => $v) {
                    $new_now_order_list[$v['day']] = $v;
                }
                foreach ($weekarray as $dk => $dd) {
                    if (!empty($new_order_list[$dk])) {
                        $weekarray[$dk]['pre'] = $new_order_list[$dk];
                    } else {
                        $weekarray[$dk]['pre'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                    if (!empty($new_now_order_list[$dk])) {
                        $weekarray[$dk]['now'] = $new_now_order_list[$dk];
                    } else {
                        $weekarray[$dk]['now'] = ['count' => 0, 'day' => $weekarray[$dk][0], 'price' => '0'];
                    }
                }
                $chartdata = [];
                $data = [];//Tạm thời
                $chartdata['yAxis']['maxnum'] = 0;//Số lượng giá trị lớn nhất
                $chartdata['yAxis']['maxprice'] = 0;//Số tiền giá trị lớn nhất
                foreach ($weekarray as $k => $v) {
                    $data['day'][] = $v[0];
                    $data['pre']['count'][] = $v['pre']['count'];
                    $data['pre']['price'][] = round($v['pre']['price'], 2);
                    $data['now']['count'][] = $v['now']['count'];
                    $data['now']['price'][] = round($v['now']['price'], 2);
                    if ($chartdata['yAxis']['maxnum'] < $v['pre']['count'] || $chartdata['yAxis']['maxnum'] < $v['now']['count']) {
                        $chartdata['yAxis']['maxnum'] = $v['pre']['count'] > $v['now']['count'] ? $v['pre']['count'] : $v['now']['count'];//Số đơn hàng tối đa trong ngày
                    }
                    if ($chartdata['yAxis']['maxprice'] < $v['pre']['price'] || $chartdata['yAxis']['maxprice'] < $v['now']['price']) {
                        $chartdata['yAxis']['maxprice'] = $v['pre']['price'] > $v['now']['price'] ? $v['pre']['price'] : $v['now']['price'];//Số tiền tối đa trong ngày
                    }
                }
                $chartdata['legend'] = ['Số tiền năm ngoái', 'Số tiền năm nay', 'Số đơn hàng năm ngoái', 'Số đơn hàng năm nay'];//Danh mục
                $chartdata['xAxis'] = $data['day'];//Giá trị trục X
                $series1 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series2 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $series3 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#69cdff'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#3eb3f7'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#1495eb'
                        ]
                    ]
                ]]
                ];
                $series4 = ['normal' => ['color' => [
                    'x' => 0, 'y' => 0, 'x2' => 0, 'y2' => 1,
                    'colorStops' => [
                        [
                            'offset' => 0,
                            'color' => '#6fdeab'
                        ],
                        [
                            'offset' => 0.5,
                            'color' => '#44d693'
                        ],
                        [
                            'offset' => 1,
                            'color' => '#2cc981'
                        ]
                    ]
                ]]
                ];
                $chartdata['series'][] = ['name' => $chartdata['legend'][0], 'type' => 'bar', 'itemStyle' => $series1, 'data' => $data['pre']['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][1], 'type' => 'bar', 'itemStyle' => $series2, 'data' => $data['now']['price']];//Giá trị nhóm 1
                $chartdata['series'][] = ['name' => $chartdata['legend'][2], 'type' => 'line', 'itemStyle' => $series3, 'data' => $data['pre']['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2
                $chartdata['series'][] = ['name' => $chartdata['legend'][3], 'type' => 'line', 'itemStyle' => $series4, 'data' => $data['now']['count'], 'yAxisIndex' => 1];//Giá trị nhóm 2

                //Thống kê tổng số kỳ trước
                $pre_total = $this->dao->preTotalFind($datebefor, $dateafter);
                if ($pre_total) {
                    $chartdata['pre_cycle']['count'] = [
                        'data' => $pre_total['count'] ?: 0
                    ];
                    $chartdata['pre_cycle']['price'] = [
                        'data' => $pre_total['price'] ?: 0
                    ];
                }
                //Tổng số thống kê
                $total = $this->dao->preTotalFind($now_datebefor, $now_dateafter);
                if ($total) {
                    $cha_count = intval($pre_total['count']) - intval($total['count']);
                    $pre_total['count'] = $pre_total['count'] == 0 ? 1 : $pre_total['count'];
                    $chartdata['cycle']['count'] = [
                        'data' => $total['count'] ?: 0,
                        'percent' => round((abs($cha_count) / intval($pre_total['count']) * 100), 2),
                        'is_plus' => $cha_count > 0 ? -1 : ($cha_count == 0 ? 0 : 1)
                    ];
                    $cha_price = round($pre_total['price'], 2) - round($total['price'], 2);
                    $pre_total['price'] = $pre_total['price'] == 0 ? 1 : $pre_total['price'];
                    $chartdata['cycle']['price'] = [
                        'data' => $total['price'] ?: 0,
                        'percent' => round(abs($cha_price) / $pre_total['price'] * 100, 2),
                        'is_plus' => $cha_price > 0 ? -1 : ($cha_price == 0 ? 0 : 1)
                    ];
                }
                return $chartdata;
            default:
                break;
        }
    }

    /**
     * Lấy số lượng đơn hàng
     * @return int
     */
    public function storeOrderCount()
    {
        return $this->dao->storeOrderCount();
    }

    /**
     * ID đơn hàng mới
     * @param $status
     * @return array
     */
    public function newOrderId($status)
    {
        return $this->dao->search(['status' => $status, 'is_remind' => 0])->column('order_id', 'id');
    }

    /**
     * Sửa đơn hàng mới
     * @param $newOrderId
     * @return \crmeb\basic\BaseModel
     */
    public function newOrderUpdate($newOrderId)
    {
        return $this->dao->newOrderUpdates($newOrderId);
    }

    /**
     * Tỷ lệ tăng trưởng
     * @param $left
     * @param $right
     * @return int|string
     */
    public function growth($nowValue, $lastValue)
    {
        if ($lastValue == 0 && $nowValue == 0) return 0;
        if ($lastValue == 0) return bcmul((string)$nowValue, '100', 2);
        if ($nowValue == 0) return bcdiv(bcsub($nowValue, $lastValue, 2), $lastValue, 4) * 100;
        return bcmul(bcdiv((bcsub($nowValue, $lastValue, 2)), $lastValue, 4), 100, 2);
    }

    /**
     * Thống kê phần đầu trang chủ ở trang quản trị
     * @return array
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/03
     */
    public function homeStatics()
    {
        /** @var UserServices $userService */
        $userService = app()->make(UserServices::class);
        /** @var StoreProductLogServices $productLogServices */
        $productLogServices = app()->make(StoreProductLogServices::class);
        //TODO doanh số
        //Doanh số hôm nay
        $today_sales = $this->dao->todaySales('today');
        //Doanh số hôm qua
        $yesterday_sales = $this->dao->todaySales('yesterday');
        //So sánh cùng kỳ theo ngày
        $sales_today_ratio = $this->growth($today_sales, $yesterday_sales);
        //Tổng doanh thu
        $total_sales = $this->dao->totalSales('month');
        $sales = [
            'today' => $today_sales,
            'yesterday' => $yesterday_sales,
            'today_ratio' => $sales_today_ratio,
            'total' => $total_sales . 'đ',
            'date' => 'Hôm nay'
        ];
        //TODO: lượt truy cập người dùng
        //Lượt truy cập hôm nay
        $today_visits = $productLogServices->count(['time' => 'today', 'type' => 'visit']);
        //Lượt truy cập hôm qua
        $yesterday_visits = $productLogServices->count(['time' => 'yesterday', 'type' => 'visit']);
        //So sánh cùng kỳ theo ngày
        $visits_today_ratio = $this->growth($today_visits, $yesterday_visits);
        //Tổng lượt truy cập
        $total_visits = $productLogServices->count(['time' => 'month', 'type' => 'visit']);
        $visits = [
            'today' => $today_visits,
            'yesterday' => $yesterday_visits,
            'today_ratio' => $visits_today_ratio,
            'total' => $total_visits . 'Pv',
            'date' => 'Hôm nay'
        ];
        //TODO số lượng đơn hàng
        //Số đơn hàng hôm nay
        $today_order = $this->dao->todayOrderVisit('today', 1);
        //Số đơn hàng hôm qua
        $yesterday_order = $this->dao->todayOrderVisit('yesterday', 1);
        //So sánh cùng kỳ theo ngày của đơn hàng
        $order_today_ratio = $this->growth($today_order, $yesterday_order);
        //Tổng số đơn hàng
        $total_order = $this->dao->count(['time' => 'month', 'paid' => 1, 'refund_status' => 0, 'pid' => 0]);
        $order = [
            'today' => $today_order,
            'yesterday' => $yesterday_order,
            'today_ratio' => $order_today_ratio,
            'total' => $total_order . 'đơn',
            'date' => 'Hôm nay'
        ];
        //TODO người dùng
        //Người dùng mới hôm nay
        $today_user = $userService->todayAddVisits('today', 1);
        //Người dùng mới hôm qua
        $yesterday_user = $userService->todayAddVisits('yesterday', 1);
        //So sánh cùng kỳ theo ngày của người dùng mới
        $user_today_ratio = $this->growth($today_user, $yesterday_user);
        //Tất cả người dùng
        $total_user = $userService->count(['time' => 'month']);
        $user = [
            'today' => $today_user,
            'yesterday' => $yesterday_user,
            'today_ratio' => $user_today_ratio,
            'total' => $total_user . 'người',
            'date' => 'Hôm nay'
        ];
        $info = array_values(compact('sales', 'visits', 'order', 'user'));
        $info[0]['title'] = 'Doanh số';
        $info[1]['title'] = 'Lượt truy cập của người dùng';
        $info[2]['title'] = 'Số đơn hàng';
        $info[3]['title'] = 'Thêm người dùng';
        $info[0]['total_name'] = 'Doanh số tháng này';
        $info[1]['total_name'] = 'Lượt truy cập tháng này';
        $info[2]['total_name'] = 'Số đơn hàng tháng này';
        $info[3]['total_name'] = 'Người dùng mới tháng này';
        return $info;
    }

    /**
     * In biên nhận đơn hàng
     * @param int $id
     * @param bool $start
     * @return bool|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function orderPrintTicket(int $id, $print_type)
    {
        $order = $this->get($id);
        if (!$order) {
            throw new AdminException(400118);
        }
        /** @var StoreOrderCartInfoServices $cartServices */
        $cartServices = app()->make(StoreOrderCartInfoServices::class);
        $product = $cartServices->getCartInfoPrintProduct($order['id']);
        if (!$product) {
            throw new AdminException(400463);
        }
//        $switch = (bool)sys_config('pay_success_printing_switch');
//        if (!$switch) {
//            throw new AdminException(400464);
//        }

        app()->make(SystemTicketServices::class)->startPrint(
            is_object($order) ? $order->toArray() : $order,
            $product,
            $print_type
        );
        return true;

//        if (sys_config('print_type', 1) == 1) {
//            $name = 'yi_lian_yun';
//            $configData = [
//                'clientId' => sys_config('printing_client_id', ''),
//                'apiKey' => sys_config('printing_api_key', ''),
//                'partner' => sys_config('develop_id', ''),
//                'terminal' => sys_config('terminal_number', '')
//            ];
//            if (!$configData['clientId'] || !$configData['apiKey'] || !$configData['partner'] || !$configData['terminal']) {
//                throw new AdminException(400465);
//            }
//        } else {
//            $name = 'fei_e_yun';
//            $configData = [
//                'feyUser' => sys_config('fey_user', ''),
//                'feyUkey' => sys_config('fey_ukey', ''),
//                'feySn' => sys_config('fey_sn', '')
//            ];
//            if (!$configData['feyUser'] || !$configData['feyUkey'] || !$configData['feySn']) {
//                throw new AdminException(400465);
//            }
//        }
//        $printer = new Printer($name, $configData);
//        $res = $printer->setPrinterContent([
//            'name' => sys_config('site_name'),
//            'url' => sys_config('site_url'),
//            'orderInfo' => is_object($order) ? $order->toArray() : $order,
//            'product' => $product
//        ])->startPrinter();
//        if (!$res) {
//            throw new AdminException($printer->getError());
//        }
//        return true;
    }

    /**
     * Lấy dữ liệu xác nhận đơn hàng
     * @param array $user
     * @param $cartId
     * @param bool $new
     * @param int $addressId
     * @param int $shipping_type
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderConfirmData(array $user, $cartId, bool $new, int $addressId, int $shipping_type = 1, int $is_gift = 0)
    {
        $addr = [];
        /** @var UserAddressServices $addressServices */
        $addressServices = app()->make(UserAddressServices::class);
        if ($addressId) {
            $addr = $addressServices->getAddress($addressId);
        }
        //Không gửi address id hoặc địa chỉ đã xóa không tìm thấy || lấy địa chỉ mặc định
        if (!$addr) {
            $addr = $addressServices->getUserDefaultAddress((int)$user['uid']);
        }
        if ($addr) {
            $addr = $addr->toArray();
        } else {
            $addr = [];
        }
        if ($shipping_type == 2) $addr = [];
        if ($is_gift == 1) {
            $addr = [];
            $shipping_type = 0;
        }
        /** @var StoreCartServices $cartServices */
        $cartServices = app()->make(StoreCartServices::class);
        $cartGroup = $cartServices->getUserProductCartListV1($user['uid'], $cartId, $new, $addr, $shipping_type, $is_gift);
        $data = [];
        $data['storeFreePostage'] = $storeFreePostage = floatval(sys_config('store_free_postage')) ?: 0;//Số tiền đơn tối thiểu để miễn phí vận chuyển
        $validCartInfo = $cartGroup['valid'];
        /** @var StoreOrderComputedServices $computedServices */
        $computedServices = app()->make(StoreOrderComputedServices::class);
        $priceGroup = $computedServices->getOrderPriceGroup($storeFreePostage, $validCartInfo, $addr, $user, $shipping_type, $is_gift);
        $validCartInfo = $priceGroup['cartInfo'] ?? $validCartInfo;
        $other = [
            'offlinePostage' => sys_config('offline_postage'),
            'integralRatio' => sys_config('integral_ratio')
        ];
        $cartIdA = explode(',', $cartId);
        $seckill_id = 0;
        $combination_id = 0;
        $bargain_id = 0;
        $advance_id = 0;
        if (count($cartIdA) == 1) {
            $seckill_id = $cartGroup['deduction']['seckill_id'] ?? 0;
            $combination_id = $cartGroup['deduction']['combination_id'] ?? 0;
            $bargain_id = $cartGroup['deduction']['bargain_id'] ?? 0;
            $advance_id = $cartGroup['deduction']['advance_id'] ?? 0;
        }
        $data['valid_count'] = count($validCartInfo);
        $data['virtual_type'] = $data['valid_count'] ? (int)$validCartInfo[0]['productInfo']['virtual_type'] > 0 : 0;
        $data['deduction'] = $seckill_id || $combination_id || $bargain_id || $advance_id;
        $data['addressInfo'] = $addr;
        $data['seckill_id'] = $seckill_id;
        $data['combination_id'] = $combination_id;
        $data['bargain_id'] = $bargain_id;
        $data['advance_id'] = $advance_id;
        $data['cartInfo'] = $cartGroup['cartInfo'];
        $data['custom_form'] = json_decode($cartGroup['cartInfo'][0]['productInfo']['custom_form'], true) ?? [];
        if (!is_array($data['custom_form'])) $data['custom_form'] = [];
        $data['priceGroup'] = $priceGroup;
        $data['orderKey'] = $this->cacheOrderInfo($user['uid'], $validCartInfo, $priceGroup, $other);
        $data['offlinePostage'] = $other['offlinePostage'];
        /** @var UserLevelServices $levelServices */
        $levelServices = app()->make(UserLevelServices::class);
        $userLevel = $levelServices->getUerLevelInfoByUid($user['uid']);
        if (isset($user['pwd'])) unset($user['pwd']);
        $user['vip'] = $userLevel !== false;
        if ($user['vip']) {
            $user['vip_id'] = $userLevel['id'] ?? 0;
            $user['discount'] = $userLevel['discount'] ?? 0;
        }
        $data['userInfo'] = $user;
        $data['integralRatio'] = $other['integralRatio'];
        $data['offline_pay_status'] = (int)sys_config('offline_pay_status') ?? (int)2;
        $data['yue_pay_status'] = (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1 ? (int)1 : (int)2;//Thanh toán bằng số dư: 1 là bật, 2 là tắt
        $data['pay_weixin_open'] = sys_config('pay_weixin_open', '0') != '0';//WeChat Pay: 1 là bật, 0 là tắt
        $data['friend_pay_status'] = (int)sys_config('friend_pay_status') ?? 0;//Bạn bè thanh toán giúp, 1 là mở, 0 là tắt
        $data['store_self_mention'] = (int)sys_config('store_self_mention') ?? 0;//Nhận tại cửa hàng có mở không
        /** @var SystemStoreServices $systemStoreServices */
        $systemStoreServices = app()->make(SystemStoreServices::class);
        $store_count = $systemStoreServices->count(['type' => 0]);
        $data['store_self_mention'] = $data['store_self_mention'] && $store_count;

        $data['ali_pay_status'] = sys_config('ali_pay_status', '0') != '0';//Thanh toán Alipay, 1 là mở, 0 là tắt
        $data['system_store'] = [];//Thông tin cửa hàng
        /** @var UserInvoiceServices $userInvoice */
        $userInvoice = app()->make(UserInvoiceServices::class);
        $invoice_func = $userInvoice->invoiceFuncStatus();
        $data['invoice_func'] = $invoice_func['invoice_func'];
        $data['special_invoice'] = $invoice_func['special_invoice'];

        /** @var UserBillServices $userBillServices */
        $userBillServices = app()->make(UserBillServices::class);
        $data['usable_integral'] = bcsub((string)$user['integral'], (string)$userBillServices->getBillSum(['uid' => $user['uid'], 'is_frozen' => 1]), 0);
        $data['integral_open'] = sys_config('integral_ratio', 0) > 0;

        //Tự động nhận phiếu giảm giá
        app()->make(StoreCouponUserServices::class)->autoReceiveCoupon($user['uid'], $cartGroup);
        return $data;
    }

    /**
     * Lưu tạm thông tin đơn hàng
     * @param $uid
     * @param $cartInfo
     * @param $priceGroup
     * @param array $other
     * @param int $cacheTime
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cacheOrderInfo($uid, $cartInfo, $priceGroup, $other = [], $cacheTime = 600)
    {
        $key = $this->getCacheKey();
        CacheService::set('user_order_' . $uid . $key, compact('cartInfo', 'priceGroup', 'other'), $cacheTime);
        return $key;
    }

    /**
     * Dùng thuật toán Snowflake để tạo ID đơn hàng
     * @return string
     * @throws \Exception
     */
    public function getCacheKey(string $prefix = '')
    {
        $snowflake = new \Godruoyi\Snowflake\Snowflake();
        //32-bit
        if (PHP_INT_SIZE == 4) {
            $id = abs($snowflake->id());
        } else {
            $id = $snowflake->setStartTimeStamp(strtotime('2020-06-05') * 1000)->id();
        }
        return $prefix . $id;
    }

    /**Lấy số lần người dùng mua sản phẩm chương trình
     * @param $uid
     * @param $seckill_id
     * @return int
     */
    public function activityProductCount(array $where)
    {
        return $this->dao->count($where);
    }

    /**
     * Lấy thông tin đơn hàng đã lưu tạm
     * @param int $uid
     * @param string $key
     * @return |null
     */
    public function getCacheOrderInfo(int $uid, string $key)
    {
        $cacheName = 'user_order_' . $uid . $key;
        if (!CacheService::has($cacheName)) return null;
        return CacheService::get($cacheName);
    }

    /**
     * Lấy id đơn hàng mua chung
     * @param int $pid
     * @param int $uid
     * @return mixed
     */
    public function getStoreIdPink(int $pid, int $uid)
    {
        return $this->dao->value(['uid' => $uid, 'pink_id' => $pid, 'is_del' => 0], 'order_id');
    }

    /**
     * Kiểm tra đơn hàng hiện tại có mua chung không
     * @param int $pid
     * @param int $uid
     * @return int
     */
    public function getIsOrderPink($pid = 0, $uid = 0)
    {
        return $this->dao->count(['uid' => $uid, 'pink_id' => $pid, 'refund_status' => 0, 'is_del' => 0]);
    }

    /**
     * Kiểm tra phương thức thanh toán có mở không
     * @param $payType
     * @return bool
     */
    public function checkPaytype(string $payType)
    {
        $res = false;
        switch ($payType) {
            case PayServices::WEIXIN_PAY:
                $res = sys_config('pay_weixin_open', '0') != '0';
                break;
            case PayServices::YUE_PAY:
                $res = sys_config('balance_func_status') && sys_config('yue_pay_status') == 1;
                break;
            case 'offline':
                $res = sys_config('offline_pay_status') == 1;
                break;
            case PayServices::ALIAPY_PAY:
                $res = sys_config('ali_pay_status', '0') != '0';
                break;
            case PayServices::FRIEND:
                $res = sys_config('friend_pay_status', 1) == 1;
                break;
            case PayServices::ALLIN_PAY:
                $res = sys_config('allin_pay_status') == 1;
        }
        return $res;
    }


    /**
     * Đổi phương thức thanh toán thành thanh toán ngoại tuyến
     * @param string $orderId
     * @return bool|\crmeb\basic\BaseModel
     */
    public function setOrderTypePayOffline(string $orderId)
    {
        if (($count = strpos($orderId, '_')) !== false) {
            $orderId = substr($orderId, $count + 1);
        }
        if (sys_config('offline_postage', 0) == 1) {
            $orderInfo = $this->dao->get(['order_id' => $orderId]);
            $cartInfoService = app()->make(StoreOrderCartInfoServices::class);
            $cartInfo = $cartInfoService->getColumn(['oid' => $orderInfo['id']], 'cart_info', 'id');
            foreach ($cartInfo as $key => &$item) {
                $item_arr = json_decode($item, true);
                $item_arr['postage_price'] = $item_arr['origin_postage_price'] = 0;
                $cartInfoService->update(['id' => $key], ['cart_info' => json_encode($item_arr)]);
            }
            return $this->dao->update($orderId, [
                'pay_type' => 'offline',
                'pay_price' => bcsub((string)$orderInfo['pay_price'], (string)$orderInfo['pay_postage'], 2),
                'pay_postage' => 0
            ], 'order_id');
        }
        return $this->dao->update($orderId, ['pay_type' => 'offline'], 'order_id');
    }

    /**
     * Xóa đơn hàng
     * @param string $uni
     * @param int $uid
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function removeOrder(string $uni, int $uid)
    {
        $order = $this->getUserOrderDetail($uni, $uid);
        if (!$order) {
            throw new ApiException(410173);
        }
        $order = $this->tidyOrder($order);
        if ($order['_status']['_type'] != 0 && $order['_status']['_type'] != -2 && $order['_status']['_type'] != 4)
            throw new ApiException(410256);

        $order->is_del = 1;
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $res = $statusService->save([
            'oid' => $order['id'],
            'change_type' => 'remove_order',
            'change_message' => 'Xóa đơn hàng',
            'change_time' => time()
        ]);
        if ($order->save() && $res) {
            return true;
        } else
            throw new ApiException(100020);
    }

    /**
     * Hủy đơn hàng
     * @param $order_id
     * @param $uid
     * @return bool|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cancelOrder($order_id, int $uid)
    {
        $order = $this->dao->getOne(['order_id' => $order_id, 'uid' => $uid, 'is_del' => 0]);
        if (!$order) {
            throw new ApiException(410173);
        }
        if ($order->paid) {
            throw new ApiException(410257);
        }
        /** @var StoreOrderCartInfoServices $cartServices */
        $cartServices = app()->make(StoreOrderCartInfoServices::class);
        $cartInfo = $cartServices->getOrderCartInfo($order['id']);
        /** @var StoreOrderRefundServices $refundServices */
        $refundServices = app()->make(StoreOrderRefundServices::class);

        $this->transaction(function () use ($refundServices, $order) {
            $res = $refundServices->integralAndCouponBack($order, 'cancel') && $refundServices->regressionStock($order);
            $order->is_cancel = 1;
            if (!($res && $order->save())) {
                throw new ApiException(100020);
            }
        });

        //Sự kiện tùy chỉnh - hủy đơn hàng
        event('CustomEventListener', ['order_cancel', [
            'uid' => $uid,
            'id' => $order['id'],
            'order_id' => $order_id,
            'real_name' => $order['id'],
            'user_phone' => $order['id'],
            'user_address' => $order['id'],
            'total_num' => $order['id'],
            'pay_price' => $order['id'],
            'deduction_price' => $order['id'],
            'coupon_price' => $order['id'],
            'cancel_time' => date('Y-m-d H:i:s'),
        ]]);

        return true;
    }

    /**
     * Kiểm tra đơn hàng hoàn thành
     * @param StoreProductReplyServices $replyServices
     * @param array $uniqueList
     * @param $oid
     * @return mixed
     */
    public function checkOrderOver($replyServices, array $uniqueList, $oid)
    {
        //Tất cả sản phẩm trong đơn hàng đã đánh giá xong
        $replyServices->count(['unique' => $uniqueList, 'oid' => $oid]);
        if ($replyServices->count(['unique' => $uniqueList, 'oid' => $oid]) >= count($uniqueList)) {
            $res = $this->dao->update(['id' => $oid, 'status' => 2], ['status' => 3]);
            if (!$res) throw new ApiException(100007);
            /** @var StoreOrderStatusServices $statusService */
            $statusService = app()->make(StoreOrderStatusServices::class);
            $statusService->save([
                'oid' => $oid,
                'change_type' => 'check_order_over',
                'change_message' => 'Người dùng đánh giá',
                'change_time' => time()
            ]);
            $order = $this->dao->get((int)$oid, ['id,pid,status']);
            if ($order && $order['pid'] > 0) {
                $p_order = $this->dao->get((int)$order['pid'], ['id,pid,status']);
                //Đơn hàng chính đã nhận hàng toàn bộ, và đơn hàng con không có chờ đánh giá nhưng có đã hoàn thành
                if ($p_order['status'] == 2 && !$this->dao->count(['pid' => $order['pid'], 'status' => 3]) && $this->dao->count(['pid' => $order['pid'], 'status' => 4])) {
                    $this->dao->update($p_order['id'], ['status' => 3]);
                    $statusService->save([
                        'oid' => $p_order['id'],
                        'change_type' => 'check_order_over',
                        'change_message' => 'Người dùng đánh giá',
                        'change_time' => time()
                    ]);
                }
            }
        }
    }

    /**
     * Đơn hàng của một người dùng
     * @param int $uid
     * @param UserServices $userServices
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserOrderList(int $uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid, 'uid');
        if (!$user) {
            throw new AdminException(100026);
        }
        [$page, $limit] = $this->getPageValue();
        $where = ['uid' => $uid, 'paid' => 1, 'refund_status' => 0, 'pid' => 0];
        $list = $this->dao->getStairOrderList($where, 'order_id,real_name,total_num,total_price,pay_price,FROM_UNIXTIME(pay_time,"%Y-%m-%d") as pay_time,paid,pay_type,pink_id,seckill_id,bargain_id', $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }


    /**
     * Lấy danh sách đơn hàng giới thiệu
     * @param int $uid
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserStairOrderList(int $uid, $where)
    {
        $where_data = [];
        if (isset($where['type'])) {
            switch ((int)$where['type']) {
                case 1:
                    $where_data['spread_uid'] = $uid;
                    break;
                case 2:
                    $where_data['spread_two_uid'] = $uid;
                    break;
                case 3:
                    $where_data['division_id'] = $uid;
                    break;
                case 4:
                    $where_data['agent_id'] = $uid;
                    break;
                default:
                    $where_data['all_spread'] = $uid;
                    break;
            }
        }
        if (isset($where['data']) && $where['data']) {
            $where_data['time'] = $where['data'];
        }
        if (isset($where['order_id']) && $where['order_id']) {
            $where_data['order_id'] = $where['order_id'];
        }
        //Đơn hàng giới thiệu chỉ hiển thị đơn hàng đã thanh toán và chưa hoàn tiền
        $where_data['paid'] = 1;
        $where_data['refund_status'] = 0;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getStairOrderList($where_data, '*', $page, $limit);
        $count = $this->dao->count($where_data);
        return compact('list', 'count');
    }

    /**
     * Xuất đơn hàng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getExportList(array $where)
    {
        $list = $this->dao->search($where)->order('id desc')->select()->toArray();
        foreach ($list as &$item) {
            /** @var StoreOrderCartInfoServices $orderCart */
            $orderCart = app()->make(StoreOrderCartInfoServices::class);
            $_info = $orderCart->getCartColunm(['oid' => $item['id']], 'cart_info', 'unique');
            foreach ($_info as $k => $v) {
                $cart_info = is_string($v) ? json_decode($v, true) : $v;
                if (!isset($cart_info['productInfo'])) $cart_info['productInfo'] = [];
                $_info[$k] = $cart_info;
                unset($cart_info);
            }
            $item['_info'] = $_info;
            /** @var WechatUserServices $wechatUserService */
            $wechatUserService = app()->make(WechatUserServices::class);
            $item['sex'] = $wechatUserService->value(['uid' => $item['uid']], 'sex');
            if ($item['pink_id'] || $item['combination_id']) {
                /** @var StorePinkServices $pinkService */
                $pinkService = app()->make(StorePinkServices::class);
                $pinkStatus = $pinkService->value(['order_id_key' => $item['id']], 'status');
                switch ($pinkStatus) {
                    case 1:
                        $item['pink_name'] = '[Đơn mua chung] Đang diễn ra';
                        $item['color'] = '#f00';
                        break;
                    case 2:
                        $item['pink_name'] = '[Đơn mua chung] Đã hoàn thành';
                        $item['color'] = '#00f';
                        break;
                    case 3:
                        $item['pink_name'] = '[Đơn mua chung] Chưa hoàn thành';
                        $item['color'] = '#f0f';
                        break;
                    default:
                        $item['pink_name'] = '[Đơn mua chung] Đơn hàng cũ';
                        $item['color'] = '#457856';
                        break;
                }
            } elseif ($item['seckill_id']) {
                $item['pink_name'] = '[Đơn flash sale]';
                $item['color'] = '#32c5e9';
            } elseif ($item['bargain_id']) {
                $item['pink_name'] = '[Đơn săn giảm giá]';
                $item['color'] = '#12c5e9';
            } else {
                if ($item['shipping_type'] == 1) {
                    $item['pink_name'] = '[Đơn thường]';
                    $item['color'] = '#895612';
                } else if ($item['shipping_type'] == 2) {
                    $item['pink_name'] = '[Đơn xác nhận sử dụng]';
                    $item['color'] = '#8956E8';
                }
            }
        }
        return $list;
    }

    /**
     * Tự động hủy đơn hàng
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderUnpaidCancel()
    {
        //Khoảng thời gian hủy đơn hàng do hệ thống định trước
        $keyValue = ['order_cancel_time', 'order_activity_time', 'order_bargain_time', 'order_seckill_time', 'order_pink_time'];
        //Lấy cấu hình
        $systemValue = SystemConfigService::more($keyValue);
        //Định dạng dữ liệu
        $systemValue = Arr::setValeTime($keyValue, is_array($systemValue) ? $systemValue : []);
        $list = $this->dao->getOrderUnPaidList();
        /** @var StoreOrderRefundServices $refundServices */
        $refundServices = app()->make(StoreOrderRefundServices::class);
        foreach ($list as $order) {
            if ($order['pink_id'] || $order['combination_id']) {
                $secs = $systemValue['order_pink_time'] ?: $systemValue['order_activity_time'];
            } elseif ($order['seckill_id']) {
                $secs = $systemValue['order_seckill_time'] ?: $systemValue['order_activity_time'];
            } elseif ($order['bargain_id']) {
                $secs = $systemValue['order_bargain_time'] ?: $systemValue['order_activity_time'];
            } else {
                $secs = $systemValue['order_cancel_time'];
            }
            if ($secs == 0) return true;
            if (($order['add_time'] + bcmul($secs, '3600', 0)) < time()) {
                try {
                    $this->transaction(function () use ($order, $refundServices) {
                        //Hoàn trả điểm thưởng và phiếu giảm giá
                        $res = $refundServices->integralAndCouponBack($order, 'cancel');
                        //Hoàn trả tồn kho và lượt bán
                        $res = $res && $refundServices->regressionStock($order);
                        //Sửa trạng thái đơn hàng
                        $res = $res && $this->dao->update($order['id'], ['is_cancel' => 1, 'mark' => 'Đơn hàng chưa thanh toán đã quá thời gian hệ thống quy định']);
                        if (!$res) {
                            Log::error('Mã đơn hàng' . $order['order_id'] . 'tự động hủy đơn hàng thất bại');
                        }
                        return true;
                    });

                    /** @var StoreOrderCartInfoServices $cartServices */
                    $cartServices = app()->make(StoreOrderCartInfoServices::class);
                    $cartInfo = $cartServices->getOrderCartInfo((int)$order['id']);

                } catch (\Throwable $e) {
                    Log::error('Tự động hủy đơn hàng thất bại, nguyên nhân:' . $e->getMessage(), $e->getTrace());
                }
            }
        }
    }

    /**Lấy doanh thu đơn hàng của hôm nay hoặc hôm qua theo thời gian
     * @param array $where
     * @return float|int
     */
    public function getOrderMoneyByWhere(array $where, string $sum_field, string $selectType, string $group = "")
    {

        switch ($selectType) {
            case "sum" :
                return $this->dao->getDayTotalMoney($where, $sum_field);
            case "group" :
                return $this->dao->getDayGroupMoney($where, $sum_field, $group);
        }
    }

    /**Thống kê số đơn hàng theo khoảng thời gian
     * @param array $where
     * @param string $sum_field
     */
    public function getOrderCountByWhere(array $where)
    {
        return $this->dao->getDayOrderCount($where);
    }

    /**Thống kê số đơn hàng theo khoảng thời gian, nhóm theo
     * @param $where
     * @return mixed
     */
    public function getOrderGroupCountByWhere($where)
    {
        return $this->dao->getOrderGroupCount($where);
    }

    /** Số người thanh toán đơn hàng theo khoảng thời gian
     * @param $where
     * @return mixed
     */
    public function getPayOrderPeopleByWhere($where)
    {
        return $this->dao->getPayOrderPeople($where);
    }

    /**Thống kê số người thanh toán đơn hàng theo nhóm khoảng thời gian
     * @param $where
     * @return mixed
     */
    public function getPayOrderGroupPeopleByWhere($where)
    {
        return $this->dao->getPayOrderGroupPeople($where);
    }

    /**
     * Danh sách đơn hoàn tiền
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refundList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        if ($where['refund_reason_time'] != '') $where['refund_reason_time'] = explode('-', $where['refund_reason_time']);
        $data = $this->dao->getRefundList($where, $page, $limit);
        if ($data['list']) $data['list'] = $this->tidyOrderList($data['list']);
        $data['num'] = [
            0 => ['name' => 'Tất cả', 'num' => $this->dao->count(['refund_type' => 0, 'is_system_del' => 0])],
            1 => ['name' => 'Chỉ hoàn tiền', 'num' => $this->dao->count(['refund_type' => 1, 'is_system_del' => 0])],
            2 => ['name' => 'Trả hàng hoàn tiền', 'num' => $this->dao->count(['refund_type' => 2, 'is_system_del' => 0])],
            3 => ['name' => 'Từ chối hoàn tiền', 'num' => $this->dao->count(['refund_type' => 3, 'is_system_del' => 0])],
            4 => ['name' => 'Chờ trả hàng', 'num' => $this->dao->count(['refund_type' => 4, 'is_system_del' => 0])],
            5 => ['name' => 'Chờ nhận hàng trả', 'num' => $this->dao->count(['refund_type' => 5, 'is_system_del' => 0])],
            6 => ['name' => 'Đã hoàn tiền', 'num' => $this->dao->count(['refund_type' => 6, 'is_system_del' => 0])]
        ];
        return $data;
    }

    /**
     * Cửa hàng đồng ý hoàn tiền, chờ khách hàng trả hàng
     * @param $order_id
     * @return bool
     */
    public function agreeRefund($order_id)
    {
        $res = $this->dao->update(['id' => $order_id], ['refund_type' => 4]);
        /** @var StoreOrderStatusServices $statusService */
        $statusService = app()->make(StoreOrderStatusServices::class);
        $statusService->save([
            'oid' => $order_id,
            'change_type' => 'refund_express',
            'change_message' => 'Chờ người dùng trả hàng',
            'change_time' => time()
        ]);
        if ($res) return true;
        throw new AdminException(100005);
    }

    /**
     * @param array $where
     * @param array|string[] $field
     * @param array $with
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSplitOrderList(array $where, array $field = ['*'], array $with = [], $page = 0, $limit = 0, $order = 'pay_time DESC,id DESC')
    {
        $data = $this->dao->getOrderList($where, $field, $page, $limit, $with, $order);
        if ($data) {
            $data = $this->tidyOrderList($data);
        }
        return $data;
    }

    /**
     * Chi tiết thanh toán hộ
     * @param $orderId
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFriendDetail($orderId, $uid)
    {
        $orderInfo = $this->dao->getOne(['id' => $orderId, 'is_del' => 0]);
        if ($orderInfo) {
            $orderInfo = $orderInfo->toArray();
        } else {
            throw new ApiException(410173);
        }
        $orderInfo = $this->tidyOrder($orderInfo, true);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($orderInfo['uid']);
        $friendInfo = $userServices->get($orderInfo['pay_uid']);
        $info = [
            'id' => $orderInfo['id'],
            'order_id' => $orderInfo['order_id'],
            'uid' => $orderInfo['uid'],
            'avatar' => $userInfo['avatar'],
            'nickname' => $userInfo['nickname'],
            'cartInfo' => $orderInfo['cartInfo'],
            'paid' => $orderInfo['paid'],
            'total_num' => $orderInfo['total_num'],
            'pay_price' => $orderInfo['pay_price'],
            'type' => $uid == $orderInfo['uid'] ? 0 : 1,
            'pay_uid' => isset($friendInfo) ? $friendInfo['uid'] : 0,
            'pay_nickname' => isset($friendInfo) ? $friendInfo['nickname'] : '',
            'pay_avatar' => isset($friendInfo) ? $friendInfo['avatar'] : '',
        ];
        return $info;
    }

    /**
     * Lấy danh sách sản phẩm trả hàng
     * @param array $cart_ids
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function refundCartInfoList(array $cart_ids = [], int $id = 0)
    {
        $orderInfo = $this->dao->get($id);
        if (!$orderInfo) {
            throw new ApiException(410173);
        }
        $orderInfo = $this->tidyOrder($orderInfo, true);
        $cartInfo = $orderInfo['cartInfo'] ?? [];
        $data = [];
        if ($cart_ids) {
            foreach ($cart_ids as $cart) {
                if (!isset($cart['cart_id']) || !$cart['cart_id'] || !isset($cart['cart_num']) || !$cart['cart_num'] || $cart['cart_num'] <= 0) {
                    throw new ApiException(410223);
                }
            }
            $cart_ids = array_combine(array_column($cart_ids, 'cart_id'), $cart_ids);
            $i = 0;
            foreach ($cartInfo as $item) {
                if (isset($cart_ids[$item['id']])) {
                    $data['cartInfo'][$i] = $item;
                    if (isset($cart_ids[$item['id']]['cart_num'])) $data['cartInfo'][$i]['cart_num'] = $cart_ids[$item['id']]['cart_num'];
                    $i++;
                }
            }
        }
        $data['_status'] = $orderInfo['_status'] ?? [];
        $data['_status']['_is_back'] = $orderInfo['delivery_type'] != 'fictitious' && $orderInfo['virtual_type'] == 0;
        $data['cartInfo'] = $data['cartInfo'] ?? $cartInfo;
        return $data;
    }

    /**
     * Đặt hàng lại
     * @param string $uni
     * @param int $uid
     * @return array
     */
    public function againOrder(StoreCartServices $services, string $uni, int $uid): array
    {
        if (!$uni) throw new ApiException(100100);
        $order = $this->getUserOrderDetail($uni, $uid);
        if (!$order) throw new ApiException(410173);
        $order = $this->tidyOrder($order, true);
        $cateId = [];

        foreach ($order['cartInfo'] as $v) {
            if ($v['combination_id']) throw new ApiException(410258);
            elseif ($v['bargain_id']) throw new ApiException(410259);
            elseif ($v['seckill_id']) throw new ApiException(410260);
            elseif ($v['advance_id']) throw new ApiException(410261);
            else $cateId[] = $services->setCart($uid, (int)$v['product_id'], (int)$v['cart_num'], $v['productInfo']['attrInfo']['unique'] ?? '', '0', true);
        }
        if (!$cateId) throw new ApiException(410262);
        return $cateId;
    }

    /**
     * Thanh toán riêng bằng Alipay
     * @param OrderPayServices $payServices
     * @param OtherOrderServices $services
     * @param string $key
     * @param string $quitUrl
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function aliPayOrder(OrderPayServices $payServices, OtherOrderServices $services, string $key, string $quitUrl)
    {
        if (!$key) {
            throw new ApiException(100100);
        }
        if (!$quitUrl) {
            throw new ApiException(100100);
        }

        $orderCache = CacheService::get($key);
        if (!$orderCache || !isset($orderCache['order_id'])) {
            throw new ApiException(410263);
        }

        $payType = isset($orderCache['other_pay_type']) && $orderCache['other_pay_type'] == true;
        if ($payType) {
            $orderInfo = $services->getOne(['order_id' => $orderCache['order_id'], 'is_del' => 0, 'paid' => 0]);
        } else {
            $orderInfo = $this->get(['order_id' => $orderCache['order_id'], 'paid' => 0, 'is_del' => 0]);
        }

        if (!$orderInfo) {
            throw new ApiException(410264);
        }
        return $payServices->beforePay($orderInfo->toArray(), PayServices::ALIAPY_PAY, ['quitUrl' => $quitUrl]);
    }

    /**
     * Thông tin đơn hàng người dùng
     * @param StoreOrderEconomizeServices $services
     * @param string $uni
     * @param int $uid
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserOrderByKey(StoreOrderEconomizeServices $services, string $uni, int $uid): array
    {
        $order = $this->getUserOrderDetail($uni, $uid, ['split', 'invoice', 'user']);
        if (!$order) throw new ApiException(410294);
        $order = $order->toArray();
        $splitNum = [];
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
        $order['system_store'] = false;
        if ($order['store_id']) {
            /** @var SystemStoreServices $storeServices */
            $storeServices = app()->make(SystemStoreServices::class);
            $order['system_store'] = $storeServices->getStoreDispose($order['store_id']);
        }
        $order['code'] = '';
        if (($order['shipping_type'] === 2 || $order['delivery_uid'] != 0) && $order['verify_code']) {
//            $name = $order['verify_code'] . '.jpg';
//            /** @var SystemAttachmentServices $attachmentServices */
//            $attachmentServices = app()->make(SystemAttachmentServices::class);
//            $imageInfo = $attachmentServices->getInfo(['name' => $name]);
//            $siteUrl = sys_config('site_url');
//            if (!$imageInfo) {
//                $imageInfo = PosterServices::getQRCodePath($order['verify_code'], $name);
//                if (is_array($imageInfo)) {
//                    $attachmentServices->attachmentAdd($imageInfo['name'], $imageInfo['size'], $imageInfo['type'], $imageInfo['dir'], $imageInfo['thumb_path'], 1, $imageInfo['image_type'], $imageInfo['time'], 2);
//                    $url = $imageInfo['dir'];
//                } else
//                    $url = '';
//            } else $url = $imageInfo['att_dir'];
//            if (isset($imageInfo['image_type']) && $imageInfo['image_type'] == 1) $url = $siteUrl . $url;
//            $order['code'] = $url;
            try {
                $verifyName = 'verify_code_' . $order['verify_code'] . '.jpg';
                $data = 'verify_code=' . $order['verify_code'];
                /** @var SystemAttachmentServices $systemAttachmentService */
                $systemAttachmentService = app()->make(SystemAttachmentServices::class);
                $imageInfo = $systemAttachmentService->getOne(['name' => $verifyName]);
                $siteUrl = sys_config('site_url');
                if (!$imageInfo) {
                    $res = MiniProgramService::appCodeUnlimitService($data, 'pages/admin/order_cancellation/index', 280);
                    if (!$res) throw new ApiException('Tạo mã xác nhận Mini Program thất bại');
                    $uploadType = (int)sys_config('upload_type', 1);
                    $upload = UploadService::init();
                    $res = (string)EntityBody::factory($res);
                    $res = $upload->to('routine/product')->validate()->setAuthThumb(false)->stream($res, $verifyName);
                    if ($res === false) throw new ApiException('Tạo mã xác nhận Mini Program thất bại');
                    $imageInfo = $upload->getUploadInfo();
                    $imageInfo['image_type'] = $uploadType;
                    if ($imageInfo['image_type'] == 1) $remoteImage = PosterServices::remoteImage($siteUrl . $imageInfo['dir']);
                    else $remoteImage = PosterServices::remoteImage($imageInfo['dir']);
                    if (!$remoteImage['status']) throw new ApiException('Tạo mã xác nhận Mini Program thất bại');
                    $systemAttachmentService->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => time(),
                        'pid' => 1,
                        'type' => 2
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                if ($imageInfo['image_type'] == 1) $url = $siteUrl . $url;
                $order['code'] = $url;
            } catch (\Exception $e) {
            }
        }
        $order['mapKey'] = sys_config('tengxun_map_key');
        $order['yue_pay_status'] = (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1 ? (int)1 : (int)2;//Thanh toán bằng số dư: 1 là bật, 2 là tắt
        $order['pay_weixin_open'] = sys_config('pay_weixin_open') != '0';//WeChat Pay: 1 là bật, 0 là tắt
        $order['ali_pay_status'] = sys_config('ali_pay_status', '0') != '0';//Thanh toán Alipay, 1 là mở, 0 là tắt
        $order['friend_pay_status'] = (int)sys_config('friend_pay_status') ?? 0;//Bạn bè thanh toán giúp, 1 là mở, 0 là tắt
        $orderData = $this->tidyOrder($order, true, true);
        $vipTruePrice = $memberPrice = $levelPrice = 0;
        foreach ($orderData['cartInfo'] ?? [] as $key => $cart) {
            $vipTruePrice = bcadd((string)$vipTruePrice, (string)$cart['vip_sum_truePrice'], 2);
            if ($cart['price_type'] == 'member') $memberPrice = bcadd((string)$memberPrice, (string)$cart['vip_sum_truePrice'], 2);
            if ($cart['price_type'] == 'level') $levelPrice = bcadd((string)$levelPrice, (string)$cart['vip_sum_truePrice'], 2);
            if (isset($splitNum[$cart['id']])) {
                $orderData['cartInfo'][$key]['cart_num'] = $cart['cart_num'] - $splitNum[$cart['id']];
                if ($orderData['cartInfo'][$key]['cart_num'] == 0) unset($orderData['cartInfo'][$key]);
            }
        }
        $orderData['cartInfo'] = array_merge($orderData['cartInfo']);
        $orderData['vip_true_price'] = $vipTruePrice;
        $orderData['levelPrice'] = $levelPrice;
        $orderData['memberPrice'] = $memberPrice;
        $economize = $services->get(['order_id' => $order['order_id']], ['postage_price', 'member_price']);
        if ($economize) {
            $orderData['postage_price'] = $economize['postage_price'];
            $orderData['member_price'] = $economize['member_price'];
        } else {
            $orderData['postage_price'] = 0;
            $orderData['member_price'] = 0;
        }
        $orderData['routine_contact_type'] = sys_config('routine_contact_type', 0);
        /** @var UserInvoiceServices $userInvoice */
        $userInvoice = app()->make(UserInvoiceServices::class);
        $invoice_func = $userInvoice->invoiceFuncStatus();
        $orderData['invoice_func'] = $invoice_func['invoice_func'];
        $orderData['special_invoice'] = $invoice_func['special_invoice'];
        $orderData['refund_cartInfo'] = $orderData['cartInfo'];
        $orderData['refund_total_num'] = $orderData['total_num'];
        $orderData['refund_pay_price'] = $orderData['pay_price'];
        $orderData['is_apply_refund'] = true;
        $orderData['help_info'] = [
            'pay_uid' => $orderData['pay_uid'],
            'pay_nickname' => '',
            'pay_avatar' => '',
            'help_status' => 0
        ];
        $orderData['gift_user_info'] = [
            'gift_uid' => $orderData['gift_uid'],
            'gift_nickname' => '',
            'gift_avatar' => '',
        ];
        if ($orderData['uid'] != $orderData['pay_uid']) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $payUser = $userServices->get($orderData['pay_uid'], ['nickname', 'avatar']);
            $orderData['help_info'] = [
                'pay_uid' => $orderData['pay_uid'],
                'pay_nickname' => $payUser['nickname'],
                'pay_avatar' => $payUser['avatar'],
                'help_status' => 1
            ];
        }
        if ($orderData['gift_uid'] != 0) {
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $giftUser = $userServices->get($orderData['gift_uid'], ['nickname', 'avatar']);
            $orderData['gift_user_info'] = [
                'gift_uid' => $orderData['gift_uid'],
                'gift_nickname' => $giftUser['nickname'],
                'gift_avatar' => $giftUser['avatar'],
            ];
        }
        // Kiểm tra có mở quản lý đơn hàng Mini Program không
        $orderData['order_shipping_open'] = false;
        if (sys_config('order_shipping_open', 0) && $order['pay_price'] > 0 && $order['is_channel'] == 1 && $order['pay_type'] == 'weixin' && MiniOrderService::isManaged()) {
            // Kiểm tra có đơn hàng con chưa nhận hàng hay không
            if ($order['pid'] > 0) {
                if ($this->checkSubOrderNotTake((int)$order['pid'], (int)$order['id'])) {
                    $orderData['order_shipping_open'] = true;
                }
            } else {
                $orderData['order_shipping_open'] = true;
            }

        }
        $orderData['is_refund_available'] = $this->isRefundAvailable((int)$order['id']);

        $orderData['gift_key'] = $orderData['gift_code'] = '';
        if ($order['is_gift'] == 1) {
            $orderData['gift_key'] = md5($order['id'] . '_' . $order['order_id'] . '_' . $order['uid']);
            /** @var QrcodeServices $qrcodeService */
            $qrcodeService = app()->make(QrcodeServices::class);
            $orderData['gift_code'] = $qrcodeService->getRoutineQrcodePath($order['id'], $order['uid'], 7, ['gift_key' => $orderData['gift_key']]);
        }
        $orderData['avatar'] = set_file_url($orderData['avatar']);
        return $orderData;
    }

    /**
     * Kiểm tra đơn hàng có thể hoàn tiền không
     * @param $oid
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/10/11
     */
    public function isRefundAvailable($oid)
    {
        $refundTimeAvailable = (int)sys_config('refund_time_available');
        if ($refundTimeAvailable == 0) return true;
        $statusInfo = app()->make(StoreOrderStatusServices::class)->get(['oid' => $oid, 'change_type' => 'take_delivery']);
        if (!$statusInfo) return true;
        $changeTime = preg_match('/^\d+$/', $statusInfo['change_time']) ? intval($statusInfo['change_time']) : strtotime($statusInfo['change_time']);
        if (($changeTime + ($refundTimeAvailable * 86400)) < time()) {
            return false;
        }
        return true;
    }

    /**
     * Lấy trạng thái hiển thị giao hàng và nhận tại cửa hàng trên trang xác nhận đơn hàng
     * @param $uid
     * @param $cartIds
     * @param $new
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function checkShipping($uid, $cartIds, $new)
    {
        if ($new) {
            $cartIds = explode(',', $cartIds);
            $cartInfo = [];
            foreach ($cartIds as $key) {
                $info = CacheService::get($key);
                if ($info) {
                    $cartInfo[] = $info;
                }
            }
        } else {
            /** @var StoreCartServices $cartServices */
            $cartServices = app()->make(StoreCartServices::class);
            $cartInfo = $cartServices->getCartList(['uid' => $uid, 'status' => 1, 'id' => $cartIds], 0, 0, ['productInfo', 'attrInfo']);
        }
        if (!$cartInfo) {
            throw new ApiException(100026);
        }
        $arr = [];
        foreach ($cartInfo as $item) {
            $arr[] = $item['productInfo']['logistics'];
        }
        $res = array_unique(explode(',', implode(',', $arr)));
        if (count($res) == 2) {
            return ['type' => 0];
        } else {
            if ($res[0] == 2 && sys_config('store_self_mention') == 0) {
                return ['type' => 1];
            }
            return ['type' => (int)$res[0]];
        }
    }

    /**
     * Tự động đánh giá
     * @return bool
     */
    public function autoComment()
    {
        //Số ngày tự động đánh giá
        $systemCommentTime = sys_config('system_comment_time', 0);
        //0 là hủy chức năng tự động đánh giá tốt mặc định
        if ($systemCommentTime == 0) {
            return true;
        }
        $sevenDay = bcsub((string)time(), bcmul((string)$systemCommentTime, '86400'));
        /** @var StoreOrderStoreOrderStatusServices $service */
        $service = app()->make(StoreOrderStoreOrderStatusServices::class);
        $orderList = $service->getTakeOrderIds([
            'change_time' => $sevenDay,
            'is_del' => 0,
            'paid' => 1,
            'status' => 2,
            'change_type' => ['take_delivery', 'user_take_delivery']
        ], 30);
        foreach ($orderList as $item) {
            AutoCommentJob::dispatch([$item['id'], $item['cart_id']]);
        }
        return true;
    }

    /**
     * @param int $uid
     * @param string $orderId
     * @param string $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/13
     */
    public function getCashierInfo(int $uid, string $orderId, string $type)
    {
        //Công tắc loại thanh toán
        $data = [
            'ali_pay_status' => sys_config('ali_pay_status', '0') != '0',
            'wechat_pay_status' => sys_config('pay_weixin_open', '0') != '0',
            'offline_pay_status' => (int)sys_config('offline_pay_status') == 1,
            'friend_pay_status' => (int)sys_config('friend_pay_status') == 1,
            'yue_pay_status' => (int)sys_config('balance_func_status') && (int)sys_config('yue_pay_status') == 1,
        ];

        $data['order_id'] = $orderId;
        $data['pay_price'] = '0';
        $data['now_money'] = app()->make(UserServices::class)->value(['uid' => $uid], 'now_money');

        switch ($type) {
            case 'order':
                $info = $this->dao->get(['order_id' => $orderId], ['id', 'pay_price', 'add_time', 'combination_id', 'seckill_id', 'bargain_id', 'pay_postage', 'is_gift']);
                if (!$info) {
                    throw new PayException('Đơn hàng bạn thanh toán không tồn tại');
                }
                $orderCancelTime = sys_config('order_cancel_time', 0);
                $orderActivityTime = sys_config('order_activity_time', 0);
                if ($info->combination_id) {
                    $time = (sys_config('order_pink_time', 0) ?: $orderActivityTime) * 60 * 60 + ((int)$info->add_time);
                } else if ($info->seckill_id) {
                    $time = (sys_config('order_seckill_time', 0) ?: $orderActivityTime) * 60 * 60 + ((int)$info->add_time);
                } else if ($info->bargain_id) {
                    $time = (sys_config('order_bargain_time', 0) ?: $orderActivityTime) * 60 * 60 + ((int)$info->add_time);
                } else {
                    $time = $orderCancelTime * 60 * 60 + ((int)$info->add_time);
                }

                if ($time < 0) {
                    $time = 0;
                }

                $data['pay_price'] = $info['pay_price'];
                $data['pay_postage'] = $info['pay_postage'];
                $data['offline_postage'] = (int)sys_config('offline_postage', 0);
                $data['invalid_time'] = $time;
                $data['oid'] = $info['id'];
                $data['is_gift'] = $info['is_gift'];

                break;
            case 'svip':
                $info = app()->make(OtherOrderServices::class)->get(['order_id' => $orderId], ['id', 'pay_price', 'add_time']);
                if (!$info) {
                    throw new PayException('Đơn hàng bạn thanh toán không tồn tại');
                }
                $data['pay_price'] = $info['pay_price'];
                $data['invalid_time'] = $info->add_time + 86400;
                break;
            case 'recharge':
                $info = app()->make(UserRechargeServices::class)->get(['order_id' => $orderId], ['id', 'price', 'add_time']);
                if (!$info) {
                    throw new PayException('Đơn hàng bạn thanh toán không tồn tại');
                }
                $data['pay_price'] = $info['price'];
                $data['invalid_time'] = $info->add_time + 86400;
                break;
            default:
                throw new PayException('Tạm thời chưa hỗ trợ thanh toán cho loại đơn hàng khác');
        }

        return $data;
    }

    /**
     * Hủy yêu cầu gửi hàng của người bán
     * @param int $id
     * @param string $msg
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/15
     */
    public function shipmentCancelOrder(int $id, string $msg)
    {
        $orderInfo = $this->dao->get($id);
        if (!$orderInfo) {
            throw new AdminException('Đơn hàng cần hủy không tồn tại');
        }
        if (!$orderInfo->kuaidi_task_id || !$orderInfo->kuaidi_order_id) {
            throw new AdminException('Thông tin đơn người bán gửi hàng không tồn tại, không thể hủy');
        }
        if ($orderInfo->is_stock_up != 1) {
            throw new AdminException('Trạng thái đơn hàng không đúng, không thể hủy gửi hàng');
        }

        //Gửi yêu cầu hủy gửi hàng của cửa hàng
        app()->make(ServeServices::class)->express()->shipmentCancelOrder([
            'task_id' => $orderInfo->kuaidi_task_id,
            'order_id' => $orderInfo->kuaidi_order_id,
            'cancel_msg' => $msg,
        ]);

        //Đơn hàng trở về trạng thái gốc
        $this->transaction(function () use ($id, $msg, $orderInfo) {
            app()->make(StoreOrderStatusServices::class)->save([
                'oid' => $id,
                'change_time' => time(),
                'change_type' => 'delivery_goods_cancel',
                'change_message' => 'Đã hủy giao hàng, lý do hủy:' . $msg
            ]);

            $orderInfo->status = 0;
            $orderInfo->is_stock_up = 0;
            $orderInfo->kuaidi_task_id = '';
            $orderInfo->kuaidi_order_id = '';
            $orderInfo->express_dump = '';
            $orderInfo->kuaidi_label = '';
            $orderInfo->delivery_id = '';
            $orderInfo->delivery_code = '';
            $orderInfo->delivery_name = '';
            $orderInfo->delivery_type = '';
            $orderInfo->save();
        });

        return true;
    }

    /**
     * Kiểm tra đơn hàng đã giao hết hàng chưa
     * @param int $pid
     * @param int $order_id
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/31
     */
    public function checkSubOrderNotSend(int $pid, int $order_id)
    {
        $order_count = $this->dao->getSubOrderNotSend($pid, $order_id);
        if ($order_count > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Kiểm tra có đơn hàng con chưa nhận hàng hay không
     * @param int $pid
     * @param int $order_id
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/31
     */
    public function checkSubOrderNotTake(int $pid, int $order_id)
    {
        $order_count = $this->dao->getSubOrderNotTake($pid, $order_id);
        if ($order_count > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Dữ liệu phiếu soạn hàng
     * @param $oid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/10/11
     */
    public function printShippingData($order_id)
    {
        $orderInfo = $this->dao->get(['order_id' => $order_id]);
        if (!$orderInfo) {
            throw new AdminException('Đơn hàng không tồn tại');
        }
        $orderInfo = $this->tidyOrder($orderInfo->toArray(), true);
        $data['user_name'] = $orderInfo['real_name'];
        $data['user_phone'] = $orderInfo['user_phone'];
        $data['user_address'] = $orderInfo['user_address'];
        $data['order_id'] = $orderInfo['order_id'];
        $data['pay_time'] = $orderInfo['_pay_time'];
        $data['pay_type'] = $orderInfo['_status']['_payType'];
        $data['pay_price'] = $orderInfo['pay_price'];
        $data['pay_postage'] = $orderInfo['pay_postage'];
        $data['deduction_price'] = $orderInfo['deduction_price'];
        $data['coupon_price'] = $orderInfo['coupon_price'];
        $data['mark'] = $orderInfo['mark'];
        $data['product_info'] = [];
        $data['vip_price'] = 0;
        foreach ($orderInfo['cartInfo'] as $item) {
            $data['product_info'][] = [
                'name' => $item['productInfo']['store_name'],
                'sku' => $item['attrInfo']['suk'],
                'price' => $item['sum_price'],
                'num' => $item['cart_num'],
                'sum_price' => bcmul((string)$item['sum_price'], (string)$item['cart_num'], 2)
            ];
            $data['vip_price'] = bcadd((string)$data['vip_price'], $item['vip_sum_truePrice'], 2);
        }
        return $data;
    }

    public function giftDetail($oid)
    {
        $orderInfo = $this->dao->getOne(['id' => $oid, 'is_del' => 0]);
        if ($orderInfo) {
            $orderInfo = $orderInfo->toArray();
        } else {
            throw new ApiException('Đơn hàng không tồn tại');
        }
        $orderInfo = $this->tidyOrder($orderInfo, true);
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($orderInfo['uid']);
        $arr = [];
        foreach ($orderInfo['cartInfo'] as $cartInfo) {
            $arr[] = $cartInfo['productInfo']['logistics'];
        }
        $res = array_unique(explode(',', implode(',', $arr)));
        if (count($res) == 2) {
            $type = 0;
        } else {
            if ($res[0] == 2 && sys_config('store_self_mention') == 0) {
                $type = 1;
            } else {
                $type = (int)$res[0];
            }
        }
        return [
            'id' => $orderInfo['id'],
            'order_id' => $orderInfo['order_id'],
            'uid' => $orderInfo['uid'],
            'avatar' => set_file_url($userInfo['avatar']),
            'nickname' => $userInfo['nickname'],
            'cartInfo' => $orderInfo['cartInfo'],
            'paid' => $orderInfo['paid'],
            'total_num' => $orderInfo['total_num'],
            'pay_price' => $orderInfo['pay_price'],
            'gift_key' => md5($orderInfo['id'] . '_' . $orderInfo['order_id'] . '_' . $orderInfo['uid']),
            'gift_mark' => $orderInfo['gift_mark'],
            'gift_uid' => $orderInfo['gift_uid'],
            'refund_status' => $orderInfo['refund_status'],
            'type' => $type,
            'store_self_mention' => (int)sys_config('store_self_mention') ?? 0,//Nhận tại cửa hàng có mở không
        ];
    }

    public function receiveGift($uid, $oid, $gift_key, $shipping_type, $name, $phone, $address_id = 0, $store_id = 0)
    {
        $orderInfo = $this->dao->get($oid);
        if (!$orderInfo) {
            throw new AdminException('Đơn hàng không tồn tại');
        }
        if ($gift_key != md5($orderInfo['id'] . '_' . $orderInfo['order_id'] . '_' . $orderInfo['uid'])) {
            throw new AdminException('Nhận thất bại');
        }
        if ($orderInfo['refund_status'] != 0) {
            throw new AdminException('Đơn hàng đã được hoàn tiền');
        }
        if ($orderInfo['uid'] == $uid) {
            throw new AdminException('Không thể nhận quà của chính mình');
        }
        if ($orderInfo['gift_uid'] != 0 && $orderInfo['gift_uid'] != $uid) {
            return false;
        }
        $address = '';
        if ($shipping_type == 1 && $address_id) {
            $addressInfo = app()->make(UserAddressServices::class)->getOne(['uid' => $uid, 'id' => $address_id, 'is_del' => 0]);
            $name = $addressInfo['real_name'];
            $phone = $addressInfo['phone'];
            $address = $addressInfo['province'] . ' ' . $addressInfo['city'] . ' ' . $addressInfo['district'] . ' ' . $addressInfo['detail'];
        }
        $verify_code = '';
        if ($shipping_type == 2 && $store_id) {
            $store_id = app()->make(SystemStoreServices::class)->getStoreDispose($store_id, 'id');
            if (!$store_id) throw new ApiException('Cửa hàng được chọn không hợp lệ');
            $verify_code = app()->make(StoreOrderCreateServices::class)->getStoreCode();
        }
        $orderData = [
            'gift_uid' => $uid,
            'real_name' => $name,
            'user_phone' => $phone,
            'user_address' => $address,
            'shipping_type' => $shipping_type,
            'store_id' => $store_id,
            'verify_code' => $verify_code,
        ];
        $this->dao->update($oid, $orderData);
        return true;
    }
}
