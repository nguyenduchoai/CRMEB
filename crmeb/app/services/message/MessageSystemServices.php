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
declare (strict_types=1);

namespace app\services\message;

use app\dao\system\MessageSystemDao;
use app\services\BaseServices;
use crmeb\exceptions\ApiException;

/**
 * Class services thông báo nội bộ
 * Class MessageSystemServices
 * @package app\services\system
 * @method save(array $data) Lưu dữ liệu
 * @method mixed saveAll(array $data) Lưu dữ liệu theo lô
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 *
 */
class MessageSystemServices extends BaseServices
{

    /**
     * SystemNotificationServices constructor.
     * @param MessageSystemDao $dao
     */
    public function __construct(MessageSystemDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách thông báo nội bộ
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMessageSystemList($uid)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $where['uid'] = $uid;
        $list = $this->dao->getMessageList($where, '*', $page, $limit);
        $count = $this->dao->getCount($where);
        if (!$list) return ['list' => [], 'count' => 0];
        foreach ($list as &$item) {
            $item['add_time'] = time_tran($item['add_time']);
            if ($item['data'] != '' && $this->getMsg($item['mark']) != 000000) {
                $item['content'] = getLang($this->getMsg($item['mark']), json_decode($item['data'], true));
            }
        }
        return ['list' => $list, 'count' => $count];
    }

    /**
     * Chi tiết thông báo nội bộ
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo($where)
    {
        $info = $this->dao->getOne($where);
        if (!$info || $info['is_del'] == 1) {
            throw new ApiException('Dữ liệu không tồn tại');
        }
        $info = $info->toArray();
        if ($info['look'] == 0) {
            $this->update($info['id'], ['look' => 1]);
        }
        if ($info['data'] != '' && $this->getMsg($info['mark']) != 000000) {
            $info['content'] = getLang($this->getMsg($info['mark']), json_decode($info['data'], true));
        }
        $info['add_time'] = time_tran($info['add_time']);
        return $info;
    }

    public function getMsg($mark)
    {
        switch ($mark) {
            case 'admin_pay_success_code':
                $code = 'Bạn có một đơn hàng đã thanh toán thành công đang chờ xử lý, mã đơn hàng {:order_id}!';
                break;
            case 'bind_spread_uid':
                $code = 'Chúc mừng, lại thêm một chiến tướng được liên kết vĩnh viễn vào đội của bạn, người dùng {:nickname} đã gia nhập đội ngũ của bạn!';
                break;
            case 'order_pay_success':
                $code = 'Sản phẩm bạn mua đã được thanh toán thành công, số tiền thanh toán {:pay_price}đ, mã đơn hàng {:order_id}, cảm ơn bạn đã mua hàng!';
                break;
            case 'order_take':
                $code = 'Bạn ơi, đơn hàng {:order_id}, sản phẩm {:store_name} đã được xác nhận nhận hàng, cảm ơn bạn đã mua hàng!';
                break;
            case 'price_revision':
                $code = 'Đơn hàng {:order_id} của bạn đã được thay đổi số tiền thanh toán thực tế thành {:pay_price}';
                break;
            case 'order_refund':
                $code = 'Đơn hàng {:order_id} của bạn đã được đồng ý hoàn tiền, số tiền hoàn {:refund_price}đ.';
                break;
            case 'recharge_success':
                $code = 'Bạn đã nạp thành công {:price}đ, số dư hiện tại là {:now_money}đ';
                break;
            case 'integral_accout':
                $code = 'Bạn ơi, bạn đã nhận được {:gain_integral} điểm thưởng, điểm thưởng hiện có {:integral}';
                break;
            case 'order_brokerage':
                $code = 'Bạn ơi, chúc mừng bạn đã nhận được {:brokerage_price}đ hoa hồng';
                break;
            case 'bargain_success':
                $code = 'Bạn ơi, tuyệt quá! Bạn bè đã giúp bạn săn được giá thấp nhất rồi, tên sản phẩm {:title}, giá thấp nhất {:min_price}';
                break;
            case 'order_user_groups_success':
                $code = 'Bạn ơi, nhóm mua chung của bạn đã hoàn tất, tên chương trình mua chung {:title}, trưởng nhóm {:nickname}';
                break;
            case 'send_order_pink_fial':
                $code = 'Bạn ơi, nhóm mua chung của bạn đã thất bại, tên chương trình {:title}';
                break;
            case 'can_pink_success':
            case 'open_pink_success':
                $code = 'Bạn ơi, bạn đã tham gia mua chung thành công, tên chương trình {:title}';
                break;
            case 'user_extract':
                $code = 'Bạn ơi, bạn đã rút thành công {:extract_number}đ hoa hồng';
                break;
            case 'user_balance_change':
                $code = 'Bạn ơi, yêu cầu rút tiền của bạn đã bị từ chối, hoàn lại {:extract_number}đ hoa hồng';
                break;
            case 'recharge_order_refund_status':
                $code = 'Bạn ơi, khoản tiền nạp của bạn đã được hoàn lại, lần này hoàn {:refund_price}đ';
                break;
            case 'send_order_refund_no_status':
                $code = 'Xin chào! Đơn hàng {:order_id} của bạn đã bị từ chối hoàn tiền.';
                break;
            case 'send_order_apply_refund':
                $code = 'Bạn có một đơn hoàn tiền cần xử lý, mã đơn hàng {:order_id}!';
                break;
            case 'order_deliver_success':
            case 'order_postage_success':
                $code = 'Chào bạn {:nickname}, sản phẩm {:store_name} của bạn thuộc đơn hàng {:order_id} đã được gửi đi, vui lòng chú ý nhận hàng';
                break;
            case 'send_order_pink_clone':
                $code = 'Bạn ơi, nhóm mua chung của bạn đã bị hủy, tên chương trình {:title}';
                break;
            case 'kefu_send_extract_application':
                $code = 'Bạn có một yêu cầu rút tiền cần xử lý, số tiền rút {:money}!';
                break;
            case 'send_admin_confirm_take_over':
                $code = 'Bạn có một đơn hàng đã được xác nhận nhận hàng, mã đơn hàng {:order_id}!';
                break;
            case 'order_pay_false':
                $code = 'Bạn có đơn hàng chưa thanh toán, mã đơn hàng: {:order_id}, số lượng sản phẩm có hạn, vui lòng thanh toán sớm.';
                break;
            default:
                $code = 000000;
                break;
        }
        return $code;
    }
}
