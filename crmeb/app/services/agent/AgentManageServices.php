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

namespace app\services\agent;

use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\order\StoreOrderStatusServices;
use app\services\other\QrcodeServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\user\UserBrokerageFrozenServices;
use app\services\user\UserBrokerageServices;
use app\services\user\UserExtractServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\services\app\MiniProgramService;
use app\services\other\UploadService;

/**
 *
 * Class AgentManageServices
 * @package app\services\agent
 */
class AgentManageServices extends BaseServices
{

    /**
     * @param array $where
     * @param bool $is_page
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function agentSystemPage(array $where, $is_page = true)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data = $userServices->getAgentUserList($where, '*', $is_page);
        /** @var UserBrokerageServices $frozenPrices */
        $frozenPrices = app()->make(UserBrokerageServices::class);
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        foreach ($data['list'] as &$item) {
            $item['headimgurl'] = $item['avatar'];
            $item['extract_count_price'] = $item['extract'][0]['extract_count_price'] ?? 0;
            $item['extract_count_num'] = $item['extract'][0]['extract_count_num'] ?? 0;
            $item['spread_name'] = $item['spreadUser']['nickname'] ?? '';
            if ($item['spread_name']) {
                $item['spread_name'] .= '/' . $item['spread_uid'];
            }
            $item['spread_count'] = $item['spreadCount'][0]['spread_count'] ?? 0;
            $item['order_price'] = $item['order'][0]['order_price'] ?? 0;
            $item['order_count'] = $item['order'][0]['order_count'] ?? 0;
            $item['broken_commission'] = $frozenPrices->getUserFrozenPrice($item['uid']);
            if ($item['broken_commission'] < 0)
                $item['broken_commission'] = 0;
            $item['new_money'] = $item['bill'][0]['brokerage_money'] ?? 0;
            if ($item['brokerage_price'] > $item['broken_commission'])
                $item['new_money'] = bcsub((string)$item['brokerage_price'], (string)$item['broken_commission'], 2);
            else
                $item['new_money'] = 0;
            $item['brokerage_money'] = bcadd((string)$item['brokerage_price'], (string)$item['extract_count_price'], 2);
            unset($item['extract'], $item['order'], $item['bill'], $item['spreadUser'], $item['spreadCount']);
            if (strpos($item['headimgurl'], '/statics/system_images/') !== false) {
                $item['headimgurl'] = set_file_url($item['headimgurl']);
            }
            $item['spread_order'] = $orderServices->get(['spread_uid' => $item['uid'], 'paid' => 1, 'refund_status' => 0, 'pid' => 0], ['sum(pay_price) as order_price', 'count(id) as order_count']);
        }
        return $data;
    }

    /**
     * Thông tin phần đầu trang phân phối
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSpreadBadge($where)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $uids = $userServices->getAgentUserIds($where);

        //Số lượng cộng tác viên
        $data['uids'] = $uids;
        $data['sum_count'] = count($uids);

        //Số lượng thành viên phát triển và số tiền người dùng có thể rút
        $data['spread_sum'] = 0;
        $data['extract_price'] = 0;
        if ($data['sum_count']) {
            //Số lượng thành viên phát triển
            $data['spread_sum'] = $userServices->getCount([['spread_uid', 'in', $uids]]);
            //Lấy số tiền có thể rút của một người dùng
            /** @var UserBrokerageFrozenServices $frozenPrices */
            $frozenPrices = app()->make(UserBrokerageFrozenServices::class);
            $data['extract_price'] = bcsub((string)$userServices->getSumBrokerage(['uid' => $uids]), $frozenPrices->getSumFrozenBrokerage($uids), 2);
        }

        //Tổng số đơn hàng, số tiền đơn hàng, số lần rút tiền
        $data['order_count'] = 0;
        $data['pay_price'] = 0;
        $data['extract_count'] = 0;
        if ($data['sum_count']) {
            /** @var StoreOrderServices $storeOrder */
            $storeOrder = app()->make(StoreOrderServices::class);
            //Tổng số đơn hàng
            $data['order_count'] = $storeOrder->getCount([['uid', 'in', $uids], ['paid', '=', 1], ['refund_status', '=', 0], ['pid', '<=', 0]]);
            //Số tiền đơn hàng
            $data['pay_price'] = $storeOrder->sum([['uid', 'in', $uids], ['paid', '=', 1], ['refund_status', '=', 0], ['pid', '<=', 0]], 'pay_price');
            //Số lần rút tiền
            $data['extract_count'] = app()->make(UserExtractServices::class)->getCount([['uid', 'in', $uids], ['status', '=', 1]]);
        }

        return [
            [
                'name' => 'Số CTV (người)',
                'count' => $data['sum_count'],
                'className' => 'iconfaqirenshu',
                'col' => 4,
            ],
            [
                'name' => 'Số người dùng được giới thiệu (người)',
                'count' => $data['spread_sum'],
                'className' => 'icontuiguangrenshu',
                'col' => 4,
            ],
            [
                'name' => 'Số đơn hàng (đơn)',
                'count' => $data['order_count'],
                'className' => 'icondingdanliang',
                'col' => 4,
            ],
            [
                'name' => 'Số tiền đơn hàng (đ)',
                'count' => $data['pay_price'],
                'className' => 'icondingdanjine',
                'col' => 4,
            ],
            [
                'name' => 'Số lần rút tiền (lần)',
                'count' => $data['extract_count'],
                'className' => 'iconzhichujine',
                'col' => 4,
            ],
            [
                'name' => 'Số tiền chưa rút (đ)',
                'count' => $data['extract_price'],
                'className' => 'iconjiaoyijine',
                'col' => 4,
            ],
        ];
    }

    /**
     * Danh sách người được giới thiệu
     * @param array $where
     * @return mixed
     */
    public function getStairList(array $where)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data = $userServices->getSairList($where);
        foreach ($data['list'] as &$item) {
            $item['spread_count'] = $item['spreadCount'][0]['spread_count'] ?? 0;
            $item['order_count'] = $item['order'][0]['order_count'] ?? 0;
            $item['promoter_name'] = $item['is_promoter'] ? 'Có' : 'Không';
            $item['add_time'] = $item['spread_time'] ? date("Y-m-d H:i:s", $item['spread_time']) : '';
        }
        return $data;
    }

    //TODO đã loại bỏ

    /**
     * Thông tin phần đầu trang người giới thiệu
     * @param array $where
     * @return array[]
     */
    public function getSairBadge(array $where)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data['number'] = $userServices->getSairCount($where);
        $where['type'] = 1;
        $data['one_number'] = $userServices->getSairCount($where);
        $where['type'] = 2;
        $data['two_number'] = $userServices->getSairCount($where);

        $col = $data['two_number'] > 0 ? 4 : 6;
        return [
            [
                'name' => 'Tổng số người (người)',
                'count' => $data['number'],
                'col' => $col,
            ],
            [
                'name' => 'Số người cấp 1 (người)',
                'count' => $data['one_number'],
                'col' => $col,
            ],
            [
                'name' => 'Số người cấp 2 (người)',
                'count' => $data['two_number'],
                'col' => $col,
            ],
        ];
    }

    /**
     * Đơn hàng giới thiệu
     * @param int $uid
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStairOrderList(int $uid, array $where)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        if (!$userInfo) {
            return ['count' => 0, 'list' => []];
        }
        /** @var StoreOrderServices $storeOrder */
        $storeOrder = app()->make(StoreOrderServices::class);
        $data = $storeOrder->getUserStairOrderList($uid, $where);
        if ($data['list']) {
            $uids = array_unique(array_column($data['list'], 'uid'));
            $userList = [];
            if ($uids) {
                $userList = $userServices->getColumn([['uid', 'IN', $uids]], 'nickname,phone,avatar,real_name', 'uid');
            }
            $orderIds = array_column($data['list'], 'id');
            $orderChangTimes = [];
            if ($orderIds) {
                /** @var StoreOrderStatusServices $storeOrderStatus */
                $storeOrderStatus = app()->make(StoreOrderStatusServices::class);
                $orderChangTimes = $storeOrderStatus->getColumn([['oid', 'IN', $orderIds], ['change_type', '=', 'user_take_delivery']], 'change_time', 'oid');
            }
            foreach ($data['list'] as &$item) {
                $user = $userList[$item['uid']] ?? [];
                $item['user_info'] = '';
                $item['avatar'] = '';
                if (count($user)) {
                    $item['user_info'] = $user['nickname'] . '|' . ($user['phone'] ? $user['phone'] . '|' : '') . $user['real_name'];
                    $item['avatar'] = $user['avatar'];
                }
                $item['brokerage_price'] = $item['spread_uid'] == $uid ? $item['one_brokerage'] : $item['two_brokerage'];
                $item['_pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '';
                $item['_add_time'] = $item['add_time'] ? date('Y-m-d H:i:s', $item['add_time']) : '';
                $item['take_time'] = ($change_time = $orderChangTimes[$item['id']] ?? '') ? date('Y-m-d H:i:s', $change_time) : 'Chưa có';
            }
        }
        return $data;
    }


    /**
     * Lấy mã QR vĩnh viễn
     * @param $type
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function wechatCode(int $uid)
    {
        /** @var QrcodeServices $qrcode */
        $qrcode = app()->make(QrcodeServices::class);
        $code = $qrcode->getForeverQrcode('spread', $uid);
        if (!$code['ticket']) throw new AdminException(410072);
        return $code;
    }

    /**
     * TODO xem mã QR giới thiệu Mini Program
     * @param string $uid
     */
    public function lookXcxCode(int $uid)
    {
        if (!sys_config('routine_appId') || !sys_config('routine_appsecret')) {
            throw new AdminException(400236);
        }
        $userInfo = app()->make(UserServices::class)->getUserInfo($uid);
        if (!$userInfo) {
            throw new AdminException(100026);
        }
        $name = $userInfo['uid'] . '_' . $userInfo['is_promoter'] . '_user.jpg';
        /** @var SystemAttachmentServices $systemAttachmentModel */
        $systemAttachmentModel = app()->make(SystemAttachmentServices::class);
        $imageInfo = $systemAttachmentModel->getInfo(['name' => $name]);
        if (!$imageInfo) {
            /** @var QrcodeServices $qrcode */
            $qrcode = app()->make(QrcodeServices::class);
            $resForever = $qrcode->qrCodeForever($uid, 'spread_routine');
            if ($resForever) {
                $resCode = MiniProgramService::appCodeUnlimitService($resForever->id, '', 280);
                $res = ['res' => $resCode, 'id' => $resForever->id];
            } else {
                $res = false;
            }
            if (!$res) throw new AdminException(400237);
            $upload = UploadService::init();
            if ($upload->to('routine/spread/code')->setAuthThumb(false)->stream((string)$res['res'], $name) === false) {
                return $upload->getError();
            }
            $imageInfo = $upload->getUploadInfo();
            $imageInfo['image_type'] = sys_config('upload_type', 1);
            $systemAttachmentModel->attachmentAdd($imageInfo['name'], $imageInfo['size'], $imageInfo['type'], $imageInfo['dir'], $imageInfo['thumb_path'], 1, $imageInfo['image_type'], $imageInfo['time'], 2);
            $qrcode->update($res['id'], ['status' => 1, 'time' => time(), 'qrcode_url' => $imageInfo['dir']]);
            $urlCode = $imageInfo['dir'];
        } else $urlCode = $imageInfo['att_dir'];
        return ['code_src' => $urlCode];
    }

    /**
     * Xem mã QR giới thiệu H5
     * @param string $uid
     * @return mixed|string
     */
    public function lookH5Code(int $uid)
    {
        $userInfo = app()->make(UserServices::class)->getUserInfo($uid);
        if (!$userInfo) {
            throw new AdminException(100026);
        }
        $name = $userInfo['uid'] . '_h5_' . $userInfo['is_promoter'] . '_user.jpg';
        /** @var SystemAttachmentServices $systemAttachmentModel */
        $systemAttachmentModel = app()->make(SystemAttachmentServices::class);
        $imageInfo = $systemAttachmentModel->getInfo(['name' => $name]);
        if (!$imageInfo) {
            /** @var QrcodeServices $qrcodeService */
            $qrcodeService = app()->make(QrcodeServices::class);
            $urlCode = $qrcodeService->getWechatQrcodePathAgent($uid . '_h5_' . $userInfo['is_promoter'] . '_user.jpg', '?spread=' . $uid);
        } else $urlCode = $imageInfo['att_dir'];
        return ['code_src' => $urlCode];
    }

    /**
     * Xóa quan hệ giới thiệu
     * @param int $uid
     * @return mixed
     */
    public function delSpread(int $uid)
    {
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->getUserInfo($uid);
        if (!$userInfo) {
            throw new AdminException(100026);
        }
        $spreadInfo = $userServices->get($userInfo['spread_uid']);
        $spreadInfo->spread_count = $spreadInfo->spread_count - 1;
        $spreadInfo->save();
        if ($userServices->update($uid, ['spread_uid' => 0, 'spread_time' => 0]) !== false) {
            return true;
        } else {
            throw new AdminException(400447);
        }
    }

    /**
     * Hủy tư cách cộng tác viên
     * @param int $uid
     * @return mixed
     */
    public function delSystemSpread(int $uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        if (!$userServices->getUserInfo($uid, 'uid')) {
            throw new AdminException(100026);
        }
        if ($userServices->update($uid, ['spread_open' => 0]) !== false)
            return true;
        else
            throw new AdminException(100020);
    }

    /**
     * Hủy liên kết người giới thiệu
     * @return bool
     */
    public function removeSpread()
    {
        //Chức năng phân phối của cửa hàng có mở không, 0 là tắt, 1 là mở
        if (!sys_config('brokerage_func_status')) return true;

        //Loại liên kết
        $store_brokergae_binding_status = sys_config('store_brokerage_binding_status', 1);
        if ($store_brokergae_binding_status == 1 || $store_brokergae_binding_status == 3) {
            return true;
        } else {
            //Loại liên kết phân phối là theo khoảng thời gian và chưa hết hạn
            $store_brokerage_binding_time = (int)sys_config('store_brokerage_binding_time', 30) * 24 * 3600;
            $spread_time = bcsub((string)time(), (string)$store_brokerage_binding_time, 0);
            /** @var UserServices $userServices */
            $userServices = app()->make(UserServices::class);
            $list = $userServices->getList(['not_spread_uid' => 0, 'status' => 1, 'spread_time' => ['<', $spread_time]], 'uid,spread_uid,spread_time');
            foreach ($list as $userInfo) {
                $userServices->update($userInfo['uid'], ['spread_uid' => 0, 'spread_time' => 0], 'uid');
            }
        }
        return true;
    }

    /**
     * Khi đổi loại liên kết trong cấu hình thì đặt lại thời gian liên kết
     * @return bool
     */
    public function resetSpreadTime()
    {
        //Chức năng phân phối của cửa hàng có mở không, 0 là tắt, 1 là mở
        if (!sys_config('brokerage_func_status')) return true;
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $list = $userServices->getList(['not_spread_uid' => 0, 'status' => 1], 'uid');
        if ($list) {
            $uids = array_column($list, 'uid');
            $userServices->update([['uid', 'IN', $uids]], ['spread_time' => time()]);
        }
        return true;
    }
}
