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

namespace app\services\kefu\service;


use app\dao\service\StoreServiceLogDao;
use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;

/**
 * Lịch sử trò chuyện CSKH
 * Class StoreServiceLogServices
 * @package app\services\kefu\service
 * @method whereByCount(array $where) Lấy số lượng theo điều kiện
 * @method getServiceList(array $where, int $page, int $limit, array $field = ['*']) Lấy lịch sử chat có phân trang
 * @method saveAll(array $data) Thêm dữ liệu
 * @method getMessageNum(array $where) Lấy số lượng bản ghi chat
 */
class StoreServiceLogServices extends BaseServices
{
    /**
     * Loại thông báo
     * @var array  1 = văn bản 2 = biểu tượng cảm xúc 3 = hình ảnh 4 = ghi âm 5 = liên kết sản phẩm 6 = loại đơn hàng
     */
    const MSN_TYPE = [1, 2, 3, 4, 5, 6];

    /**
     * Loại tin nhắn liên kết sản phẩm
     */
    const MSN_TYPE_GOODS = 5;

    /**
     * Loại tin nhắn thông tin đơn hàng
     */
    const MSN_TYPE_ORDER = 6;

    /**
     * Phương thức khởi tạo
     * StoreServiceLogServices constructor.
     * @param StoreServiceLogDao $dao
     */
    public function __construct(StoreServiceLogDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy uid và to_uid trong bản ghi chat
     * @param int $uid
     * @return array
     */
    public function getChatUserIds(int $uid)
    {
        $list = $this->dao->getServiceUserUids($uid);
        $arr_user = $arr_to_user = [];
        foreach ($list as $key => $value) {
            array_push($arr_user, $value["uid"]);
            array_push($arr_to_user, $value["to_uid"]);
        }
        $uids = array_merge($arr_user, $arr_to_user);
        $uids = array_flip(array_flip($uids));
        $uids = array_flip($uids);
        unset($uids[$uid]);
        return array_flip($uids);
    }

    /**
     * Lấy bản ghi chat CSKH của một người dùng
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatLogList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getServiceList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Lấy danh sách bản ghi chat
     * @param array $where
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChatList(array $where, int $uid)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getServiceList($where, $page, $limit);
        return $this->tidyChat($list);
    }

    /**
     * Định dạng danh sách chat
     * @param array $list
     * @param int $uid
     * @return array
     */
    public function tidyChat(array $list)
    {
        $productIds = $orderIds = $productList = $orderInfo = $toUser = $user = [];
        $toUid = $list[0]['to_uid'] ?? 0;
        $uid = $list[0]['uid'] ?? 0;
        foreach ($list as &$item) {
            $item['_add_time'] = $item['add_time'];
            $item['add_time'] = strtotime($item['_add_time']);
            $item['productInfo'] = $item['orderInfo'] = null;
            if ($item['msn_type'] == self::MSN_TYPE_GOODS && $item['msn']) {
                $productIds[] = $item['msn'];
            } elseif ($item['msn_type'] == self::MSN_TYPE_ORDER && $item['msn']) {
                $orderIds[] = $item['msn'];
            }
        }
        if ($productIds) {
            /** @var StoreProductServices $productServices */
            $productServices = app()->make(StoreProductServices::class);
            $where = [
                ['id', 'in', $productIds],
                ['is_del', '=', 0],
                ['is_show', '=', 1],
            ];
            $productList = get_thumb_water($productServices->getProductArray($where, '*', 'id'),'mid');
        }
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        if ($orderIds) {
            $orderWhere = [
                ['order_id|unique', 'in', $orderIds],
                ['is_del', '=', 0],
            ];
            $orderInfo = $orderServices->getColumn($orderWhere, '*', 'order_id');
        }
        if ($toUid && $uid) {
            /** @var StoreServiceRecordServices $recordServices */
            $recordServices = app()->make(StoreServiceRecordServices::class);
            $toUser = $recordServices->get(['user_id' => $uid, 'to_uid' => $toUid], ['nickname', 'avatar']);
            $user = $recordServices->get(['user_id' => $toUid, 'to_uid' => $uid], ['nickname', 'avatar']);
        }

        foreach ($list as &$item) {
            if ($item['msn_type'] == self::MSN_TYPE_GOODS && $item['msn']) {
                $item['productInfo'] = $productList[$item['msn']] ?? null;
            } elseif ($item['msn_type'] == self::MSN_TYPE_ORDER && $item['msn']) {
                $order = $orderInfo[$item['msn']] ?? null;
                if ($order) {
                    $order = $orderServices->tidyOrder($order, true, true);
                    $order['add_time_y'] = date('Y-m-d', $order['add_time']);
                    $order['add_time_h'] = date('H:i:s', $order['add_time']);
                    $item['orderInfo'] = $order;
                } else {
                    $item['orderInfo'] = null;
                }
            }
            $item['msn_type'] = (int)$item['msn_type'];
            if (!isset($item['nickname'])) {
                $item['nickname'] = '';
            }
            if (!isset($item['avatar'])) {
                $item['avatar'] = '';
            }

            if (!$item['avatar'] && !$item['nickname']) {
                if ($item['uid'] == $uid && $item['to_uid'] == $toUid) {
                    $item['nickname'] = $user['nickname'] ?? '';
                    $item['avatar'] = $user['avatar'] ?? '';
                }
                if ($item['uid'] == $toUid && $item['to_uid'] == $uid) {
                    $item['nickname'] = $toUser['nickname'] ?? '';
                    $item['avatar'] = $toUser['avatar'] ?? '';
                }
            }
        }
        return $list;
    }

    /**
     * Lấy lịch sử trò chuyện
     * @param array $where
     * @param int $page
     * @param int $limit
     * @param bool $isUp
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getServiceChatList(array $where, int $limit, int $upperId)
    {
        return $this->dao->getChatList($where, $limit, $upperId);
    }
}
