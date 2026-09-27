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

namespace app\services\message;

use app\dao\system\SystemNotificationDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 * Class quản lý tin nhắn
 * Class SystemNotificationServices
 * @package app\services\system
 * @method value($where, $value) Lấy giá trị một trường theo điều kiện
 */
class SystemNotificationServices extends BaseServices
{

    protected $messageData = [

        //Mã xác thực SMS
        'verify_code' => [
            ['label' => 'Mã xác thực', 'value' => 'code'],
            ['label' => 'Thời gian hiệu lực', 'value' => 'time'],
        ],

        //Người dùng đăng nhập
        'login_success' => [
            ['label' => 'Biệt danh người dùng', 'value' => 'nickname'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'phone'],
            ['label' => 'Thời gian đăng nhập gần nhất', 'value' => 'last_time'],
            ['label' => 'Số dư người dùng', 'value' => 'now_money'],
            ['label' => 'Hoa hồng người dùng', 'value' => 'brokerage_price'],
            ['label' => 'Điểm thưởng người dùng', 'value' => 'integral'],
            ['label' => 'Điểm kinh nghiệm người dùng', 'value' => 'exp'],
            ['label' => 'Thời gian đăng nhập', 'value' => 'time'],
        ],

        //Người dùng liên kết quan hệ
        'spread_success' => [
            ['label' => 'Biệt danh người dùng', 'value' => 'nickname'],
            ['label' => 'Thời gian liên kết', 'value' => 'time'],
        ],

        //Sửa số tiền đơn hàng chưa thanh toán
        'price_change_price' => [
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Số tiền ban đầu của đơn hàng', 'value' => 'pay_price'],
            ['label' => 'Số tiền sau khi sửa', 'value' => 'change_price'],
        ],

        //Thanh toán đơn hàng thành công
        'order_pay_success' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Tên người dùng', 'value' => 'real_name'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'user_phone'],
            ['label' => 'Địa chỉ người dùng', 'value' => 'user_address'],
            ['label' => 'Tổng số sản phẩm', 'value' => 'total_num'],
            ['label' => 'Số tiền thanh toán', 'value' => 'pay_price'],
            ['label' => 'Phí vận chuyển đã thanh toán', 'value' => 'pay_postage'],
            ['label' => 'Số tiền khấu trừ bằng điểm thưởng', 'value' => 'deduction_price'],
            ['label' => 'Số tiền giảm từ phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Loại thanh toán', 'value' => 'pay_type'],
            ['label' => 'Tên sản phẩm', 'value' => 'storeName'],
            ['label' => 'Thời gian đặt hàng', 'value' => 'time'],
        ],

        //Đơn hàng giao qua vận chuyển
        'order_express_success' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Tên người dùng', 'value' => 'real_name'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'user_phone'],
            ['label' => 'Địa chỉ người dùng', 'value' => 'user_address'],
            ['label' => 'Tổng số sản phẩm', 'value' => 'total_num'],
            ['label' => 'Số tiền thanh toán', 'value' => 'pay_price'],
            ['label' => 'Phí vận chuyển đã thanh toán', 'value' => 'pay_postage'],
            ['label' => 'Số tiền khấu trừ bằng điểm thưởng', 'value' => 'deduction_price'],
            ['label' => 'Số tiền giảm từ phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Loại thanh toán', 'value' => 'pay_type'],
            ['label' => 'Tên sản phẩm', 'value' => 'storeName'],
            ['label' => 'Đơn vị vận chuyển', 'value' => 'delivery_name'],
            ['label' => 'Mã vận đơn', 'value' => 'delivery_id'],
            ['label' => 'Thời gian giao hàng', 'value' => 'time'],
        ],

        //Người giao hàng giao đơn hàng
        'order_send_success' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Tên người dùng', 'value' => 'real_name'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'user_phone'],
            ['label' => 'Địa chỉ người dùng', 'value' => 'user_address'],
            ['label' => 'Tổng số sản phẩm', 'value' => 'total_num'],
            ['label' => 'Số tiền thanh toán', 'value' => 'pay_price'],
            ['label' => 'Phí vận chuyển đã thanh toán', 'value' => 'pay_postage'],
            ['label' => 'Số tiền khấu trừ bằng điểm thưởng', 'value' => 'deduction_price'],
            ['label' => 'Số tiền giảm từ phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Loại thanh toán', 'value' => 'pay_type'],
            ['label' => 'Tên sản phẩm', 'value' => 'storeName'],
            ['label' => 'Họ tên nhân viên giao hàng', 'value' => 'delivery_name'],
            ['label' => 'Số điện thoại nhân viên giao hàng', 'value' => 'delivery_id'],
            ['label' => 'Thời gian đi giao hàng', 'value' => 'time'],

        ],

        //Xác nhận đã nhận hàng
        'order_take' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Tên người dùng', 'value' => 'real_name'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'user_phone'],
            ['label' => 'Địa chỉ người dùng', 'value' => 'user_address'],
            ['label' => 'Tổng số sản phẩm', 'value' => 'total_num'],
            ['label' => 'Số tiền thanh toán', 'value' => 'pay_price'],
            ['label' => 'Phí vận chuyển đã thanh toán', 'value' => 'pay_postage'],
            ['label' => 'Số tiền khấu trừ bằng điểm thưởng', 'value' => 'deduction_price'],
            ['label' => 'Số tiền giảm từ phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Loại thanh toán', 'value' => 'pay_type'],
            ['label' => 'Tên sản phẩm', 'value' => 'storeTitle'],
            ['label' => 'Họ tên nhân viên giao hàng', 'value' => 'delivery_name'],
            ['label' => 'Số điện thoại nhân viên giao hàng', 'value' => 'delivery_id'],
            ['label' => 'Thời gian ký nhận', 'value' => 'time'],
        ],

        //Đơn hàng yêu cầu hoàn tiền
        'order_initiated_refund' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Tên người dùng', 'value' => 'real_name'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'user_phone'],
            ['label' => 'Địa chỉ người dùng', 'value' => 'user_address'],
            ['label' => 'Tổng số sản phẩm', 'value' => 'total_num'],
            ['label' => 'Số tiền thanh toán', 'value' => 'pay_price'],
            ['label' => 'Phí vận chuyển đã thanh toán', 'value' => 'pay_postage'],
            ['label' => 'Số tiền khấu trừ bằng điểm thưởng', 'value' => 'deduction_price'],
            ['label' => 'Số tiền giảm từ phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Loại thanh toán', 'value' => 'pay_type'],
        ],

        //Đơn hàng hoàn tiền thành công
        'order_refund_success' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'order_id đơn hàng', 'value' => 'order_id'],
            ['label' => 'Tên người dùng', 'value' => 'real_name'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'user_phone'],
            ['label' => 'Địa chỉ người dùng', 'value' => 'user_address'],
            ['label' => 'Tổng số sản phẩm', 'value' => 'total_num'],
            ['label' => 'Số tiền thanh toán', 'value' => 'pay_price'],
            ['label' => 'Phí vận chuyển đã thanh toán', 'value' => 'pay_postage'],
            ['label' => 'Số tiền khấu trừ bằng điểm thưởng', 'value' => 'deduction_price'],
            ['label' => 'Số tiền giảm từ phiếu giảm giá', 'value' => 'coupon_price'],
            ['label' => 'Loại thanh toán', 'value' => 'pay_type'],
            ['label' => 'Loại lý do hoàn tiền', 'value' => 'refund_reason_wap'],
            ['label' => 'Lý do hoàn tiền', 'value' => 'refund_reason_wap_explain'],
            ['label' => 'Số tiền hoàn thực tế', 'value' => 'refund_price'],
        ],

        //Đơn hàng bị từ chối hoàn tiền
        'order_refund_fail' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Số tiền hoàn', 'value' => 'refund_price'],
            ['label' => 'Lý do từ chối hoàn tiền', 'value' => 'refuse_reason'],
            ['label' => 'Thời gian từ chối', 'value' => 'time'],
        ],

        //Nạp tiền người dùng
        'recharge_success' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Biệt danh người dùng', 'value' => 'nickname'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'phone'],
            ['label' => 'Số tiền nạp', 'value' => 'price'],
            ['label' => 'Số tiền tặng', 'value' => 'give_price'],
            ['label' => 'Số dư người dùng sau khi nạp', 'value' => 'now_money'],
            ['label' => 'Thời gian nạp tiền', 'value' => 'time'],
        ],

        //Hoàn tiền nạp của người dùng
        'recharge_refund' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Biệt danh người dùng', 'value' => 'nickname'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'phone'],
            ['label' => 'Số tiền hoàn', 'value' => 'price'],
            ['label' => 'Số dư người dùng sau khi hoàn tiền', 'value' => 'now_money'],
            ['label' => 'Thời gian hoàn tiền', 'value' => 'time'],
        ],

        //Yêu cầu rút tiền của người dùng được duyệt
        'extract_success' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Biệt danh người dùng', 'value' => 'nickname'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'phone'],
            ['label' => 'Số tiền rút', 'value' => 'price'],
            ['label' => 'Thời gian rút tiền', 'value' => 'time'],
        ],

        //Rút tiền của người dùng thất bại
        'extract_fail' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Biệt danh người dùng', 'value' => 'nickname'],
            ['label' => 'Lý do thất bại', 'value' => 'message'],
            ['label' => 'Số tiền rút', 'value' => 'price'],
            ['label' => 'Thời gian thất bại', 'value' => 'time'],
        ],

        //Hoa hồng đã về tài khoản
        'brokerage_received' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'phone'],
            ['label' => 'Số tiền nhận được', 'value' => 'brokeragePrice'],
            ['label' => 'Tên sản phẩm', 'value' => 'goodsName'],
            ['label' => 'Tiền hàng', 'value' => 'goodsPrice'],
            ['label' => 'Thời gian nhận tiền', 'value' => 'time'],
        ],

        //Điểm thưởng đã được cộng
        'point_received' => [
            ['label' => 'uid người dùng', 'value' => 'uid'],
            ['label' => 'Số điện thoại người dùng', 'value' => 'phone'],
            ['label' => 'Số điểm thưởng', 'value' => 'give_integral'],
            ['label' => 'Tên sản phẩm', 'value' => 'storeTitle'],
            ['label' => 'Tổng điểm thưởng', 'value' => 'integral'],
            ['label' => 'Thời gian nhận tiền', 'value' => 'time'],
        ],


    ];

    /**
     * SystemNotificationServices constructor.
     * @param SystemNotificationDao $dao
     */
    public function __construct(SystemNotificationDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Một cấu hình
     * @param int $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOneNotce(array $where)
    {
        return $this->dao->getOne($where);
    }

    /**
     * Lấy danh sách ở trang quản trị
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNotList(array $where)
    {
        return $this->dao->getList($where);
    }

    /**
     * Form thêm tin nhắn tùy chỉnh
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/2/19
     */
    public function getNotForm($id = 0)
    {
        if ($id) {
            $info = $this->dao->get($id);
            if ($info) $info = $info->toArray();
        } else {
            $info = [];
        }
        $data = [
            ['value' => 'login_success', 'label' => 'Khi người dùng đăng nhập thành công'],
            ['value' => 'spread_success', 'label' => 'Khi liên kết quan hệ giới thiệu thành công'],
            ['value' => 'price_change_price', 'label' => 'Khi sửa giá đơn hàng chưa thanh toán'],
            ['value' => 'order_pay_success', 'label' => 'Khi thanh toán đơn hàng thành công'],
            ['value' => 'order_express_success', 'label' => 'Khi giao hàng qua đơn vị vận chuyển thành công'],
            ['value' => 'order_send_success', 'label' => 'Khi nhân viên giao hàng bắt đầu giao đơn'],
            ['value' => 'order_take', 'label' => 'Khi đơn hàng được nhận thành công'],
            ['value' => 'order_initiated_refund', 'label' => 'Khi đơn hàng yêu cầu hoàn tiền'],
            ['value' => 'order_refund_success', 'label' => 'Khi hoàn tiền đơn hàng thành công'],
            ['value' => 'order_refund_fail', 'label' => 'Khi hoàn tiền đơn hàng thất bại'],
            ['value' => 'recharge_success', 'label' => 'Khi nạp tiền thành công'],
            ['value' => 'recharge_refund', 'label' => 'Khi hoàn tiền nạp'],
            ['value' => 'extract_success', 'label' => 'Khi rút tiền thành công'],
            ['value' => 'extract_fail', 'label' => 'Khi rút tiền thất bại'],
            ['value' => 'brokerage_received', 'label' => 'Khi nhận được hoa hồng'],
            ['value' => 'point_received', 'label' => 'Khi được cộng điểm thưởng'],
        ];
        $field = [];
        $field[] = Form::select('custom_trigger', 'Vị trí kích hoạt', $info['custom_trigger'] ?? '')->options($data);
        $field[] = Form::input('name', 'Tên', $info['name'] ?? '')->placeholder('Vui lòng nhập tên tin nhắn, ví dụ: Tin nhắn thanh toán thành công');
        $field[] = Form::input('mark', 'Mã định danh', $info['mark'] ?? '')->placeholder('Vui lòng nhập mã định danh tin nhắn, dùng chữ tiếng Anh và dấu gạch dưới, ví dụ: order_pay_success');
        return create_form('Thêm thông báo', $field, Url::buildUrl('/setting/notification/not_form_save/' . $id), 'POST');
    }

    /**
     * Lưu thông báo tùy chỉnh
     * @param $id
     * @param $data
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/2/20
     */
    public function notFormSave($id, $data)
    {
        if ($id) {
            $data['title'] = $data['name'];
            $res = $this->dao->update($id, $data);
        } else {
            $data['type'] = 3;
            $data['title'] = $data['name'];
            $data['is_system'] = $data['is_wechat'] = $data['is_routine'] = $data['is_sms'] = $data['is_ent_wechat'] = 2;
            $data['add_time'] = time();
            $res = $this->dao->save($data);
        }
        if ($res) return true;
        throw new AdminException(100006);
    }

    /**
     * Lấy một dòng dữ liệu
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNotInfo(array $where)
    {
        $type = $where['type'];
        unset($where['type']);
        $info = $this->dao->getOne($where);
        if (!$info) return [];
        $info = $info->toArray();
        switch ($type) {
            case 'is_system':
                $info['content'] = $info['system_text'] ?? '';
                break;
            case 'is_sms':
                $info['content'] = $info['sms_text'];
                break;
            case 'is_wechat':
                $info['tempkey'] = $info['wechat_tempkey'] ?? '';
                $info['tempid'] = $info['wechat_tempid'] ?? '';
                $info['content'] = $info['wechat_content'] ?? '';
                $info['key_list'] = json_decode($info['wechat_data'], true) ?? [];
                break;
            case 'is_routine':
                $info['tempkey'] = $info['routine_tempkey'] ?? '';
                $info['tempid'] = $info['routine_tempid'] ?? '';
                $info['content'] = $info['routine_content'] ?? '';
                $info['key_list'] = json_decode($info['routine_data'], true) ?? [];
                break;
            case 'is_ent_wechat':
                $info['content'] = $info['ent_wechat_text'];
                break;
        }
        if ($info['type'] == 3) {
            $info['custom_variable'] = $this->messageData[$info['custom_trigger']];
            if (in_array($type, ['is_system', 'is_sms', 'is_ent_wechat'])) {
                foreach ($info['custom_variable'] as &$item) {
                    $item['value'] = '{' . $item['value'] . '}';
                }
            }
        }
        return $info;
    }

    /**
     * Lưu dữ liệu
     * @param array $data
     * @return bool|\crmeb\basic\BaseModel|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveData(array $data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $info = $this->dao->get($id);
        if (!$info) {
            throw new AdminException(100026);
        }
        $res = null;
        switch ($type) {
            case 'is_system':
                $update = [];
                $update['name'] = $data['name'];
                $update['title'] = $data['title'];
                $update['is_system'] = $data['is_system'];
                $update['is_app'] = $data['is_app'];
                $update['system_title'] = $data['system_title'];
                $update['system_text'] = $data['system_text'];
                $res = $this->dao->update((int)$id, $update);
                break;
            case 'is_sms':
                $update = [];
                $update['name'] = $data['name'];
                $update['title'] = $data['title'];
                $update['is_sms'] = $data['is_sms'];
                $update['sms_id'] = $data['sms_id'];
                $update['sms_text'] = $data['sms_text'];
                $res = $this->dao->update((int)$id, $update);
                break;
            case 'is_wechat':
                $update['is_wechat'] = $data['is_wechat'];
                $update['wechat_tempid'] = $data['tempid'];
                $update['wechat_tempkey'] = $data['tempkey'];
                $update['wechat_content'] = $data['content'];
                $update['wechat_link'] = $data['wechat_link'];
                $update['wechat_to_routine'] = $data['wechat_to_routine'];
                $update['wechat_data'] = json_encode($data['key_list']);
                $res = $this->dao->update((int)$id, $update);
                break;
            case 'is_routine':
                $update['is_routine'] = $data['is_routine'];
                $update['routine_tempid'] = $data['tempid'];
                $update['routine_tempkey'] = $data['tempkey'];
                $update['routine_content'] = $data['content'];
                $update['routine_data'] = json_encode($data['key_list']);
                $update['routine_link'] = $data['routine_link'];
                $res = $this->dao->update((int)$id, $update);
                break;
            case 'is_ent_wechat':
                $update['name'] = $data['name'];
                $update['title'] = $data['title'];
                $update['is_ent_wechat'] = $data['is_ent_wechat'];
                $update['ent_wechat_text'] = $data['ent_wechat_text'];
                $update['url'] = $data['url'];
                $res = $this->dao->update((int)$id, $update);
                break;
        }
        return $res;
    }

    /**
     * Lấy tempid
     * @param $type
     * @return array
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/16
     */
    public function getTempId($type)
    {
        return $this->dao->getTempId($type);
    }

    /**
     * Lấy tempkey
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/16
     */
    public function getTempKey($type)
    {
        return $this->dao->getTempKey($type);
    }
}
