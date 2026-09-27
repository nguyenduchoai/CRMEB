<?php

namespace app\services\system;

use app\dao\system\SystemEventDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use think\facade\Db;

class SystemEventServices extends BaseServices
{
    public function __construct(SystemEventDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách kịch bản (scene)
     * @return \string[][]
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function getMarkList()
    {
//        $data = [
//            [
//                'label' => 'Đăng ký người dùng',
//                'value' => 'user_register',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'add_time' => 'Thời gian đăng ký người dùng',
//                    'user_type' => 'Nguồn người dùng',
//                ]
//            ],
//            [
//                'label' => 'Đăng nhập người dùng',
//                'value' => 'user_login',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'add_time' => 'Thời gian đăng ký người dùng',
//                    'login_time' => 'Thời gian đăng nhập người dùng',
//                    'user_type' => 'Nguồn người dùng',
//                ]
//            ],
//            [
//                'label' => 'Hủy tài khoản người dùng',
//                'value' => 'user_cancel',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'add_time' => 'Thời gian đăng ký người dùng',
//                    'cancel_time' => 'Thời gian hủy tài khoản người dùng',
//                    'user_type' => 'Nguồn người dùng',
//                ]
//            ],
//            [
//                'label' => 'Người dùng sửa thông tin',
//                'value' => 'user_change_info',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'avatar' => 'Ảnh đại diện người dùng',
//                    'add_time' => 'Thời gian đăng ký người dùng',
//                    'user_type' => 'Nguồn người dùng',
//                ]
//            ],
//            [
//                'label' => 'Liên kết quan hệ giới thiệu',
//                'value' => 'user_spread',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'nickname' => 'Biệt danh người dùng',
//                    'spread_uid' => 'uid người dùng cấp trên',
//                    'spread_time' => 'Thời gian liên kết người dùng',
//                    'user_type' => 'Nguồn người dùng',
//                ]
//            ],
//            [
//                'label' => 'Người dùng điểm danh',
//                'value' => 'user_sign',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'sign_point' => 'Điểm thưởng điểm danh',
//                    'sign_exp' => 'Điểm kinh nghiệm điểm danh',
//                    'sign_time' => 'Thời gian điểm danh',
//                ]
//            ],
//            [
//                'label' => 'Người dùng nạp tiền',
//                'value' => 'user_recharge',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hàng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'price' => 'Số tiền nạp',
//                    'give_price' => 'Số tiền tặng',
//                    'now_money' => 'Số dư hiện tại',
//                    'recharge_time' => 'Thời gian nạp tiền',
//                ]
//            ],
//            [
//                'label' => 'Người dùng rút tiền',
//                'value' => 'user_extract',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'extract_type' => 'Loại rút tiền',
//                    'extract_price' => 'Số tiền rút',
//                    'extract_fee' => 'Phí rút tiền',
//                    'extract_time' => 'Thời gian rút tiền',
//                ]
//            ],
//            [
//                'label' => 'Người dùng xem sản phẩm',
//                'value' => 'user_product_visit',
//                'data' => [
//                    'product_id' => 'ID sản phẩm',
//                    'uid' => 'uid người dùng',
//                    'visit_time' => 'Thời gian truy cập',
//                ]
//            ],
//            [
//                'label' => 'Người dùng yêu thích sản phẩm',
//                'value' => 'user_product_collect',
//                'data' => [
//                    'product_id' => 'ID sản phẩm',
//                    'uid' => 'uid người dùng',
//                    'collect_time' => 'Thời gian truy cập',
//                ]
//            ],
//            [
//                'label' => 'Người dùng thêm vào giỏ hàng',
//                'value' => 'user_add_cart',
//                'data' => [
//                    'product_id' => 'ID sản phẩm',
//                    'uid' => 'uid người dùng',
//                    'cart_num' => 'Số lượng sản phẩm',
//                    'add_time' => 'Thời gian thêm',
//                ]
//            ],
//            [
//                'label' => 'Người dùng quay thưởng',
//                'value' => 'user_lottery',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'lottery_id' => 'ID quay thưởng',
//                    'prize_id' => 'ID giải thưởng',
//                    'record_id' => 'ID bản ghi trúng thưởng',
//                    'lottery_time' => 'Thời gian quay thưởng',
//                ]
//            ],
//            [
//                'label' => 'Tạo đơn hàng',
//                'value' => 'order_create',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hàng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'total_num' => 'Tổng số sản phẩm',
//                    'pay_price' => 'Số tiền thanh toán',
//                    'pay_postage' => 'Phí vận chuyển đã thanh toán',
//                    'deduction_price' => 'Số tiền khấu trừ bằng điểm thưởng',
//                    'coupon_price' => 'Số tiền khấu trừ bằng phiếu giảm giá',
//                    'store_name' => 'Tên sản phẩm',
//                    'add_time' => 'Thời gian tạo đơn hàng',
//                ]
//            ],
//            [
//                'label' => 'Hủy đơn hàng',
//                'value' => 'order_cancel',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hàng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'total_num' => 'Tổng số sản phẩm',
//                    'pay_price' => 'Số tiền thanh toán',
//                    'deduction_price' => 'Số tiền khấu trừ bằng điểm thưởng',
//                    'coupon_price' => 'Số tiền khấu trừ bằng phiếu giảm giá',
//                    'cancel_time' => 'Thời gian hủy đơn hàng',
//                ]
//            ],
//            [
//                'label' => 'Thanh toán đơn hàng',
//                'value' => 'order_pay',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hàng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'total_num' => 'Tổng số sản phẩm',
//                    'pay_price' => 'Số tiền thanh toán',
//                    'pay_postage' => 'Phí vận chuyển đã thanh toán',
//                    'deduction_price' => 'Số tiền khấu trừ bằng điểm thưởng',
//                    'coupon_price' => 'Số tiền khấu trừ bằng phiếu giảm giá',
//                    'store_name' => 'Tên sản phẩm',
//                    'add_time' => 'Thời gian tạo đơn hàng',
//                ]
//            ],
//            [
//                'label' => 'Nhận hàng/xác nhận sử dụng đơn hàng',
//                'value' => 'order_take',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hàng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'total_num' => 'Tổng số sản phẩm',
//                    'pay_price' => 'Số tiền thanh toán',
//                    'pay_postage' => 'Phí vận chuyển đã thanh toán',
//                    'deduction_price' => 'Số tiền khấu trừ bằng điểm thưởng',
//                    'coupon_price' => 'Số tiền khấu trừ bằng phiếu giảm giá',
//                    'store_name' => 'Tên sản phẩm',
//                    'add_time' => 'Thời gian tạo đơn hàng',
//                ]
//            ],
//            [
//                'label' => 'Đơn hàng yêu cầu hoàn tiền',
//                'value' => 'order_initiated_refund',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'refund_order_id' => 'order_id của đơn hoàn tiền',
//                    'order_id' => 'order_id của đơn hàng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'refund_num' => 'Số lượng hoàn tiền',
//                    'refund_price' => 'Số tiền hoàn trả',
//                    'refund_time' => 'Thời gian yêu cầu hoàn tiền',
//                ]
//            ],
//            [
//                'label' => 'Người dùng hủy hoàn tiền',
//                'value' => 'order_refund_cancel',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hoàn tiền',
//                    'store_order_id' => 'ID đơn hàng gốc tương ứng',
//                    'order_id' => 'order_id của đơn hoàn tiền',
//                    'refund_num' => 'Số lượng hoàn tiền',
//                    'refund_price' => 'Số tiền hoàn trả',
//                    'cancel_time' => 'Thời gian từ chối',
//                ]
//            ],
//            [
//                'label' => 'Hoa hồng vào tài khoản',
//                'value' => 'order_brokerage',
//                'data' => [
//                    'uid' => 'uid người giới thiệu',
//                    'order_id' => 'order_id của đơn hàng',
//                    'phone' => 'Số điện thoại người giới thiệu',
//                    'brokeragePrice' => 'Số tiền hoa hồng',
//                    'goodsName' => 'Tên sản phẩm',
//                    'goodsPrice' => 'Số tiền đơn hàng',
//                    'add_time' => 'Thời gian vào tài khoản',
//                ]
//            ],
//            [
//                'label' => 'Điểm thưởng vào tài khoản',
//                'value' => 'order_point',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'storeTitle' => 'Tên sản phẩm',
//                    'give_integral' => 'Điểm thưởng tặng',
//                    'integral' => 'Tổng điểm thưởng',
//                    'add_time' => 'Thời gian tặng',
//                ]
//            ],
//            [
//                'label' => 'Yêu cầu xuất hóa đơn',
//                'value' => 'order_invoice',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'invoice_id' => 'ID hóa đơn',
//                    'add_time' => 'Thời gian xuất hóa đơn',
//                ]
//            ],
//            [
//                'label' => 'Đánh giá đơn hàng',
//                'value' => 'order_comment',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'oid' => 'ID đơn hàng',
//                    'unique' => 'Giá trị duy nhất của phân loại sản phẩm',
//                    'suk' => 'Phân loại sản phẩm',
//                    'product_id' => 'ID sản phẩm',
//                    'add_time' => 'Thời gian đánh giá',
//                ]
//            ],
//            [
//                'label' => 'Quản trị viên đăng nhập',
//                'value' => 'admin_login',
//                'data' => [
//                    'id' => 'ID quản trị viên',
//                    'account' => 'Tài khoản quản trị viên',
//                    'head_pic' => 'Ảnh đại diện quản trị viên',
//                    'real_name' => 'Tên quản trị viên',
//                    'login_time' => 'Thời gian đăng nhập',
//                ]
//            ],
//
//            [
//                'label' => 'Admin: rút tiền thành công',
//                'value' => 'admin_extract_success',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'price' => 'Số tiền rút',
//                    'pay_type' => 'Loại rút tiền',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'success_time' => 'Thời gian thành công'
//                ]
//            ],
//            [
//                'label' => 'Admin: rút tiền thất bại',
//                'value' => 'admin_extract_fail',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'price' => 'Số tiền rút',
//                    'pay_type' => 'Loại rút tiền',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'fail_time' => 'Thời gian thất bại'
//                ]
//            ],
//            [
//                'label' => 'Admin: hoàn tiền nạp',
//                'value' => 'admin_recharge_refund',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'refund_price' => 'Số tiền hoàn trả',
//                    'now_money' => 'Số dư còn lại',
//                    'nickname' => 'Biệt danh người dùng',
//                    'phone' => 'Số điện thoại người dùng',
//                    'refund_time' => 'Thời gian hoàn tiền',
//                ]
//            ],
//            [
//                'label' => 'Admin: đổi giá đơn hàng',
//                'value' => 'admin_order_change',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'pay_price' => 'Số tiền đơn hàng sau khi sửa',
//                    'gain_integral' => 'Điểm thưởng tặng của đơn hàng sau khi sửa',
//                    'change_time' => 'Thời gian sửa',
//                ]
//            ],
//            [
//                'label' => 'Admin: giao hàng đơn hàng',
//                'value' => 'admin_order_express',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'delivery_name' => 'Tên đơn vị vận chuyển/tên người giao hàng',
//                    'delivery_id' => 'Mã vận đơn/số điện thoại người giao hàng',
//                    'express_time' => 'Sự kiện giao hàng',
//                ]
//            ],
//            [
//                'label' => 'Admin: hoàn tiền đơn hàng',
//                'value' => 'admin_order_refund_success',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'order_id' => 'order_id của đơn hàng',
//                    'real_name' => 'Tên người dùng',
//                    'user_phone' => 'Số điện thoại người dùng',
//                    'user_address' => 'Địa chỉ người dùng',
//                    'total_num' => 'Tổng số sản phẩm',
//                    'pay_price' => 'Số tiền thanh toán',
//                    'refund_reason_wap' => 'Loại lý do hoàn tiền',
//                    'refund_reason_wap_explain' => 'Lý do hoàn tiền',
//                    'refund_price' => 'Số tiền hoàn trả thực tế',
//                    'refund_time' => 'Thời gian hoàn tiền',
//                ]
//            ],
//            [
//                'label' => 'Admin: từ chối hoàn tiền đơn hàng',
//                'value' => 'admin_order_refund_fail',
//                'data' => [
//                    'uid' => 'uid người dùng',
//                    'id' => 'ID đơn hoàn tiền',
//                    'store_order_id' => 'ID đơn hàng gốc tương ứng',
//                    'order_id' => 'order_id của đơn hoàn tiền',
//                    'refund_num' => 'Số lượng hoàn tiền',
//                    'refund_price' => 'Số tiền hoàn trả',
//                    'refuse_reason' => 'Lý do từ chối hoàn tiền',
//                    'refuse_time' => 'Thời gian từ chối',
//                ]
//            ],
//        ];
//        foreach ($data as &$item){
//            $item['data'] = json_encode($item['data']);
//        }
//        app()->make(SystemEventDataServices::class)->saveAll($data);

        $data = app()->make(SystemEventDataServices::class)->selectList([])->toArray();

        foreach ($data as &$item) {
            $str = '$data = ' . var_export(json_decode($item['data'], true), true);
            $item['data'] = str_replace(['array (', ')'], ['[', ']'], $str);
        }
        return $data;
    }

    /**
     * Lấy danh sách sự kiện
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function getEventList()
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->selectList(['is_del' => 0], 'id,name,mark,content,add_time,is_open', $page, $limit, 'id desc')->toArray();
        $count = $this->dao->getCount(['is_del' => 0]);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            foreach ($this->getMarkList() as $markItem) {
                if ($markItem['value'] == $item['mark']) {
                    $item['mark_name'] = $markItem['label'];
                }
            }
        }
        return compact('list', 'count');
    }

    /**
     * Lấy chi tiết sự kiện
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function getEventInfo($id)
    {
        $info = $this->dao->get($id);
        if (!$info) throw new AdminException('Sự kiện không tồn tại');
        $info = $info->toArray();
        $info['add_time'] = date('Y-m-d H:i:s', $info['add_time']);
        $info['customCode'] = "<?php\n\n" . json_decode($info['customCode'], true);
        return $info;
    }

    public function saveEvent($data)
    {
        $data['add_time'] = time();
        $data['customCode'] = json_encode(preg_replace('/<\?php\s*\n/', '', $data['customCode']));
        if (!$data['id']) {
            unset($data['id']);
            $res = $this->dao->save($data);
        } else {
            $res = $this->dao->update(['id' => $data['id']], $data);
        }
        if (!$res) throw new AdminException(100006);
        return true;
    }

    /**
     * Xóa sự kiện
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function eventDel($id)
    {
        $info = $this->dao->get($id);
        if (!$info) throw new AdminException('Sự kiện không tồn tại');
        $info->is_del = 1;
        $info->save();
        return true;
    }

    /**
     * Đặt trạng thái sự kiện
     * @param $id
     * @param $is_open
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/6/7
     */
    public function setEventStatus($id, $is_open)
    {
        $res = $this->dao->update(['id' => $id], ['is_open' => $is_open]);
        if (!$res) throw new AdminException(100014);
        return true;
    }
}