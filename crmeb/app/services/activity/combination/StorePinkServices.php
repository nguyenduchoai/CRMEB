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

namespace app\services\activity\combination;

use app\dao\activity\combination\StorePinkDao;
use app\jobs\PinkJob;
use app\services\BaseServices;
use app\services\order\StoreOrderDeliveryServices;
use app\services\order\StoreOrderRefundServices;
use app\services\order\StoreOrderServices;
use app\services\other\PosterServices;
use app\services\other\QrcodeServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\user\UserServices;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;
use crmeb\services\app\MiniProgramService;
use app\services\other\UploadService;
use Guzzle\Http\EntityBody;

/**
 *
 * Class StorePinkServices
 * @package app\services\activity
 * @method getPinkCount(array $where)
 * @method int count(array $where = []) Lấy số lượng bản ghi theo điều kiện chỉ định
 * @method getPinkOkSumTotalNum()
 * @method isPink(int $id, int $uid) Có thể tiếp tục mua chung hay không
 * @method getPinkUserOne(int $id) Mua chung
 * @method getCount(array $where) Lấy tổng số theo một số điều kiện
 * @method value(array $where, string $field)
 * @method getColumn(array $where, string $field, ?string $key)
 * @method update(array $where, array $data)
 */
class StorePinkServices extends BaseServices
{

    /**
     * StorePinkServices constructor.
     * @param StorePinkDao $dao
     */
    public function __construct(StorePinkDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function systemPage(array $where)
    {
        $where['k_id'] = 0;
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['count_people'] = $this->dao->count(['k_id' => $item['id']]) + 1;
            $item['_add_time'] = $item['add_time'] ? date('Y-m-d H:i:s', (int)$item['add_time']) : '';
            $item['_stop_time'] = $item['stop_time'] ? date('Y-m-d H:i:s', (int)$item['stop_time']) : '';
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Đầu trang danh sách mua chung
     * @return array
     */
    public function getStatistics()
    {
        $res = [
            ['col' => 6, 'count' => $this->dao->count(), 'name' => 'Số người tham gia (người)', 'className' => 'iconfaqirenshu'],
            ['col' => 6, 'count' => $this->dao->count(['k_id' => 0, 'status' => 2]), 'name' => 'Số nhóm thành công (nhóm)', 'className' => 'iconshengyukucun'],
        ];
        return compact('res');
    }

    /**
     * Người tham gia nhóm
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkMember(int $id)
    {
        return $this->dao->getList(['k_id' => $id]);
    }

    /**
     * Hoàn tiền mua chung
     * @param $order
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setRefundPink($order)
    {
        $res = true;
        if ($order['pink_id']) {
            $id = $order['pink_id'];
        } else {
            return true;
        }
        //Đang mua chung: trưởng nhóm
        $count = $this->dao->getOne(['id' => $id, 'uid' => $order['uid']]);
        //Đang mua chung: thành viên nhóm
        $countY = $this->dao->getOne(['k_id' => $id, 'uid' => $order['uid']]);
        if (!$count && !$countY) {
            return $res;
        }
        if ($count) {//Trưởng nhóm
            //Kiểm tra trong nhóm còn người khác không, nếu có thì trưởng nhóm là người vào nhóm thứ hai
            $kCount = $this->dao->getPinking(['k_id' => $id]);
            if ($kCount) {
                $res11 = $this->dao->update($id, ['k_id' => $kCount['id']], 'k_id');
                $res12 = $this->dao->update($kCount['id'], ['stop_time' => $count['add_time'] + 86400, 'k_id' => 0]);
                $res1 = $res11 && $res12;
                $res2 = $this->dao->update($id, ['stop_time' => time() - 1, 'k_id' => $kCount['id'], 'is_refund' => $kCount['id'], 'status' => 3]);
                $res3 = app()->make(StoreOrderServices::class)->update(['pink_id' => $id], ['pink_id' => $kCount['id']]);
            } else {
                $res1 = $res3 = true;
                $res2 = $this->dao->update($id, ['stop_time' => time() - 1, 'is_refund' => $id, 'status' => 3]);
            }
            //Cập nhật thời gian kết thúc thành giây trước đó, ID trưởng nhóm là 0
            $res = $res1 && $res2 && $res3;
        } else if ($countY) {//Thành viên nhóm
            $res = $this->dao->update($countY['id'], ['stop_time' => time() - 1, 'is_refund' => $id, 'status' => 3]);
        }
        return $res;
    }

    /**
     * Xem danh sách mua chung ở chi tiết mua chung
     * @param int $id
     * @param bool $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkList(int $id, bool $type)
    {
        $where['cid'] = $id;
        $where['k_id'] = 0;
        $where['is_refund'] = 0;
        $where['status'] = 1;
        $pinkList = $this->dao->pinkList($where);
        $ids = array_column($pinkList, 'id');
        $orderIdKey = array_column($pinkList, 'order_id_key');
        $refunList = [];
        if ($orderIdKey) {
            $refunList = app()->make(StoreOrderRefundServices::class)->getColumn([['store_order_id', 'in', $orderIdKey]], 'id', 'store_order_id');
        }
        if ($refunList) {
            $list = [];
            foreach ($pinkList as $item) {
                if (!isset($refunList[$item['order_id_key']])) {
                    $list[] = $item;
                }
            }
        } else {
            $list = $pinkList;
        }
        $counts = $this->dao->getPinkPeopleCount($ids);
        if ($type) {
            $pinkAll = [];
            foreach ($list as &$v) {
                $v['count'] = $v['people'] - $counts[$v['id']];
                $v['h'] = date('H', (int)$v['stop_time']);
                $v['i'] = date('i', (int)$v['stop_time']);
                $v['s'] = date('s', (int)$v['stop_time']);
                $pinkAll[] = $v['id'];//ID trưởng nhóm khi mở nhóm
                $v['stop_time'] = (int)$v['stop_time'];
                $v['avatar'] = set_file_url($v['avatar']);
            }
            return [$list, $pinkAll];
        }
        return $list;
    }

    /**
     * Lấy thông tin danh sách nhóm đã thành công
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkOkList(int $uid)
    {
        $list = $this->dao->successList($uid);
        $msg = [];
        foreach ($list as &$item) {
            if (isset($item['nickname'])) $msg[] = $item['nickname'] .= 'Mua chung thành công';
        }
        return $msg;
    }

    /**
     * Tìm thông tin mua chung
     * @param $pink
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPinkMemberAndPinkK($pink)
    {
        //Tìm thành viên và trưởng nhóm mua chung
        if ($pink['k_id']) {
            $pinkAll = $this->dao->getPinkUserList(['k_id' => $pink['k_id']]);
            $pinkT = $this->dao->getPinkUserOne($pink['k_id']);
        } else {
            $pinkAll = $this->dao->getPinkUserList(['k_id' => $pink['id']]);
            $pinkT = $pink;
        }
        $count = count($pinkAll) + 1;
        $count = $pinkT['people'] - $count;
        $idAll = [];
        $uidAll = [];
        //Thu thập id người dùng và id mua chung
        foreach ($pinkAll as $k => $v) {
            $idAll[$k] = $v['id'];
            $uidAll[$k] = $v['uid'];
        }
        $idAll[] = $pinkT['id'];
        $uidAll[] = $pinkT['uid'];
        return [$pinkAll, $pinkT, $count, $idAll, $uidAll];
    }

    /**
     * Mua chung thất bại
     * @param $pinkAll
     * @param $pinkT
     * @param $pinkBool
     * @param bool $isRunErr
     * @param bool $isIds
     * @return array|int
     */
    public function pinkFail($pinkAll, $pinkT, $pinkBool, $isRunErr = true, $isIds = false)
    {
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        /** @var StoreOrderRefundServices $orderRefundService */
        $orderRefundService = app()->make(StoreOrderRefundServices::class);
        $pinkIds = [];
        try {
            if ($pinkT['stop_time'] < time()) {//Mua chung quá thời gian, hoàn tiền
                $virtual = $this->virtualCombination($pinkT['id']);
                if ($virtual) return 1;
                $pinkBool = -1;
                array_push($pinkAll, $pinkT);
                $oids = array_column($pinkAll, 'order_id_key');
                $orders = $orderService->getColumn([['id', 'in', $oids]], '*', 'id');
                $refundData = [
                    'refund_reason' => 'Hết thời gian mua chung',
                    'refund_explain' => 'Hết thời gian mua chung',
                    'refund_img' => json_encode([]),
                ];
                foreach ($pinkAll as $v) {
                    if (isset($orders[$v['order_id_key']]) && $order = $orders[$v['order_id_key']]) {
                        $res1 = $res2 = true;
                        if (!in_array($order['refund_status'], [1, 2])) {
                            $res1 = $orderRefundService->applyRefund((int)$order['id'], (int)$order['uid'], $order, [], 1, (float)$order['pay_price'], $refundData, 1);
                        }
                        $res2 = $this->dao->getCount([['uid', '=', $v['uid']], ['is_tpl', '=', 0], ['k_id|id', '=', $pinkT['id']]]);
                        if ($res1 && $res2) {
                            if ($isIds) array_push($pinkIds, $v['id']);
                            $this->orderPinkAfterNo($pinkT['uid'], $pinkT['id'], false, $orders[$v['order_id_key']]['is_channel']);
                        } else {
                            if ($isRunErr) return $pinkBool;
                        }
                    }
                }
            }
            if ($isIds) return $pinkIds;
            return $pinkBool;
        } catch (\Exception $e) {
            return $pinkBool;
        }
    }

    /**
     * Gửi tin nhắn và cập nhật trạng thái khi thất bại
     * @param $uid
     * @param $pid
     * @param bool $isRemove
     * @param $channel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderPinkAfterNo($uid, $pid, $isRemove = false, $channel)
    {
        $pink = $this->dao->getOne([['id|k_id', '=', $pid], ['uid', '=', $uid]], '*', ['getProduct']);
        if ($isRemove) {
            event('NoticeListener', [['uid' => $uid, 'pink' => $pink, 'user_type' => $channel], 'send_order_pink_clone']);
        } else {
            event('NoticeListener', [['uid' => $uid, 'pink' => $pink, 'user_type' => $channel], 'send_order_pink_fial']);
        }
        $this->dao->update([['id|k_id', '=', $pid]], ['status' => 3, 'stop_time' => time()]);
    }


    /**
     * Kiểm tra trạng thái mua chung
     * @param $pinkId
     * @return bool
     */
    public function isPinkStatus($pinkId)
    {
        if (!$pinkId) return false;
        $stopTime = $this->dao->value(['id' => $pinkId], 'stop_time');
        if ($stopTime < time()) return true; //Mua chung kết thúc
        else return false;//Mua chung chưa kết thúc
    }

    /**
     * Lấy order_id mua chung
     * @param int $id
     * @param int $uid
     * @return mixed
     */
    public function getCurrentPink(int $id, int $uid)
    {
        $oid = $this->dao->value(['id' => $id, 'uid' => $uid], 'order_id_key');
        if (!$oid) $oid = $this->dao->value(['k_id' => $id, 'uid' => $uid], 'order_id_key');
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        return $orderService->value(['id' => $oid], 'order_id');
    }

    /**
     * Mua chung thành công
     * @param $uidAll
     * @param $idAll
     * @param $uid
     * @param $pinkT
     * @return int
     */
    public function pinkComplete($uidAll, $idAll, $uid, $pinkT)
    {
        $pinkBool = 6;
        try {
            if (!$this->dao->getCount([['id', 'in', $idAll], ['is_refund', '=', 1]])) {
                $this->dao->update([['id', 'in', $idAll]], ['stop_time' => time(), 'status' => 2]);
                if (in_array($uid, $uidAll)) {
                    if ($this->dao->getCount([['uid', 'in', $uidAll], ['is_tpl', '=', 0], ['k_id|id', '=', $pinkT['id']]]))
                        $this->orderPinkAfter($uidAll, $pinkT['id']);
                    $pinkBool = 1;
                } else  $pinkBool = 3;
            }
            return $pinkBool;
        } catch (\Exception $e) {
            return $pinkBool;
        }
    }

    /**
     * Cập nhật khi mua chung thành công
     * @param $uidAll
     * @param $pid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderPinkAfter($uidAll, $pid)
    {
        //Loại bỏ người dùng ảo trước khi gửi tin nhắn
        foreach ($uidAll as $key => $uid) {
            if ($uid == 0) unset($uidAll[$key]);
        }
        /** @var StoreCombinationServices $storeCombinationServices */
        $storeCombinationServices = app()->make(StoreCombinationServices::class);
        $title = $storeCombinationServices->value(['id' => $this->dao->value(['id' => $pid], 'cid')], 'title');
        $pinkList = $this->dao->getColumn([['id|k_id', '=', $pid], ['uid', '<>', 0]], '*', 'uid');
        $pinkT_name = $this->dao->value(['id' => $pid], 'nickname');
        $order_ids = array_column($pinkList, 'order_id');
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        $order_channels = $orderService->getColumn([['order_id', 'in', $order_ids]], 'is_channel', 'order_id');
        if (!$pinkList) return false;
        foreach ($pinkList as $item) {
            $item['nickname'] = $pinkT_name;
            //Gửi tin nhắn cho người dùng
            event('NoticeListener', [
                [
                    'list' => $item,
                    'title' => $title,
                    'user_type' => $order_channels[$item['order_id']],
                    'url' => '/pages/users/order_details/index?order_id=' . $item['order_id']
                ], 'order_user_groups_success']);
        }
        $this->dao->update([['uid', 'in', $uidAll], ['id|k_id', '=', $pid]], ['is_tpl' => 1]);

        //Sản phẩm mã thẻ và phiếu giảm giá của mua chung, phát sau khi tạo nhóm thành công
        $orderInfos = $orderService->getColumn([['order_id', 'in', $order_ids]], '*', 'order_id');
        foreach ($orderInfos as $orderInfo) {
            if (in_array($orderInfo['virtual_type'], [1, 2])) {
                $orderInfo['cart_id'] = json_decode($orderInfo['cart_id'], true);
                /** @var StoreOrderDeliveryServices $orderDeliveryServices */
                $orderDeliveryServices = app()->make(StoreOrderDeliveryServices::class);
                $orderDeliveryServices->virtualSend($orderInfo);
            }
        }
        return true;
    }

    /**
     * Tạo mua chung
     * @param $order
     * @return mixed
     */
    public function createPink(array $orderInfo)
    {
        /** @var StoreCombinationServices $services */
        $services = app()->make(StoreCombinationServices::class);
        $product = $services->getOne(['id' => $orderInfo['combination_id']], 'effective_time,title,people');
        if (!$product) {
            return false;
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $userInfo = $userServices->get($orderInfo['uid']);
        if ($orderInfo['pink_id']) {
            //Mua chung đã tồn tại
            $res = false;
            $pink['uid'] = $orderInfo['uid'];//id người dùng
            $pink['nickname'] = $userInfo['nickname'];
            $pink['avatar'] = $userInfo['avatar'];
            if ($this->isPinkBe($pink, $orderInfo['pink_id'])) return false;
            $pink['order_id'] = $orderInfo['order_id'];//ID đơn hàng  được tạo
            $pink['order_id_key'] = $orderInfo['id'];//ID đơn hàng  ID database
            $pink['total_num'] = $orderInfo['total_num'];//Số lượng mua
            $pink['total_price'] = $orderInfo['pay_price'];//Tổng số tiền
            $pink['k_id'] = $orderInfo['pink_id'];//ID mua chung
            foreach ($orderInfo['cartInfo'] as $v) {
                $pink['cid'] = $v['combination_id'];//ID sản phẩm mua chung
                $pink['pid'] = $v['product_id'];//id sản phẩm
                $pink['people'] = $product['people'];//Số người mua chung
                $pink['price'] = $v['productInfo']['price'];//Đơn giá
                $pink['stop_time'] = 0;//Thời gian kết thúc
                $pink['add_time'] = time();//Thời gian mở nhóm
                $res = $this->save($pink);
            }
            // Gửi tin nhắn mẫu khi nhóm mua chung thành công
            event('NoticeListener', [['orderInfo' => $orderInfo, 'title' => $product['title'], 'pink' => $pink], 'can_pink_success']);

            //Xử lý khi mua chung hoàn thành
            list($pinkAll, $pinkT, $count, $idAll, $uidAll) = $this->getPinkMemberAndPinkK($pink);
            if ($pinkT['status'] == 1) {
                if (!$count)//Tạo nhóm hoàn thành
                    $this->pinkComplete($uidAll, $idAll, $pink['uid'], $pinkT);
                else
                    $this->pinkFail($pinkAll, $pinkT, 0);
            }

            if ($res) return true;
            else return false;
        } else {
            //Tạo mua chung
            $res = false;
            $pink['uid'] = $orderInfo['uid'];//id người dùng
            $pink['nickname'] = $userInfo['nickname'];
            $pink['avatar'] = $userInfo['avatar'];
            $pink['order_id'] = $orderInfo['order_id'];//ID đơn hàng  được tạo
            $pink['order_id_key'] = $orderInfo['id'];//ID đơn hàng  ID database
            $pink['total_num'] = $orderInfo['total_num'];//Số lượng mua
            $pink['total_price'] = $orderInfo['pay_price'];//Tổng số tiền
            $pink['k_id'] = 0;//ID mua chung
            /** @var StoreOrderServices $orderServices */
            $orderServices = app()->make(StoreOrderServices::class);
            foreach ($orderInfo['cartInfo'] as $v) {
                $pink['cid'] = $v['combination_id'];//ID sản phẩm mua chung
                $pink['pid'] = $v['product_id'];//id sản phẩm
                $pink['people'] = $product['people'];//Số người mua chung
                $pink['price'] = $v['productInfo']['price'];//Đơn giá
                $pink['stop_time'] = time() + $product->effective_time * 3600;//Thời gian kết thúc
                $pink['add_time'] = time();//Thời gian mở nhóm
                $res1 = $this->dao->save($pink);
                $res2 = $orderServices->update($orderInfo['id'], ['pink_id' => $res1['id']]);
                $res = $res1 && $res2;
                $pink['id'] = $res1['id'];
            }

            PinkJob::dispatchSecs((int)(($product->effective_time * 3600) + 60), [$pink['id']]);
            // Gửi tin nhắn mẫu khi mở nhóm thành công
            event('NoticeListener', [['orderInfo' => $orderInfo, 'title' => $product['title'], 'pink' => $pink], 'open_pink_success']);

            if ($res) return true;
            else return false;
        }
    }

    /**
     * Có phải mua chung hay không
     * @param array $data
     * @param int $id
     * @return int
     */
    public function isPinkBe(array $data, int $id)
    {
        $data['id'] = $id;
        $count = $this->dao->getCount($data);
        if ($count) return $count;
        $data['k_id'] = $id;
        $count = $this->dao->getCount($data);
        if ($count) return $count;
        else return 0;
    }

    /**
     * Hủy mua chung
     * @param int $uid
     * @param int $cid
     * @param int $pink_id
     * @param null $nextPinkT
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function removePink(int $uid, int $cid, int $pink_id, $nextPinkT = null)
    {
        $pinkT = $this->dao->getOne([
            ['uid', '=', $uid],
            ['id', '=', $pink_id],
            ['cid', '=', $cid],
            ['k_id', '=', 0],
            ['is_refund', '=', 0],
            ['status', '=', 1],
            ['stop_time', '>', time()],
        ]);
        if (!$pinkT) throw new ApiException(410314);
        list($pinkAll, $pinkT, $count, $idAll, $uidAll) = $this->getPinkMemberAndPinkK($pinkT);
        if (count($pinkAll)) {
            $count = $pinkT['people'] - ($this->dao->count(['k_id' => $pink_id, 'is_refund' => 0]) + 1);
            if ($count) {
                //Mua chung chưa hoàn thành, nếu có thành viên hủy thì lấy người mua chung ngay sau trưởng nhóm làm người mở nhóm
                if (isset($pinkAll[0])) $nextPinkT = $pinkAll[0];
            } else {
                //Mua chung hoàn thành
                $this->PinkComplete($uidAll, $idAll, $uid, $pinkT);
                throw new ApiException(410316);
            }
        }
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        /** @var StoreOrderRefundServices $orderRefundService */
        $orderRefundService = app()->make(StoreOrderRefundServices::class);
        //Hủy mở nhóm
        $order = $orderService->get($pinkT['order_id_key']);
        $refundData = [
            'refund_reason' => 'Người dùng tự hủy mua chung',
            'refund_explain' => 'Người dùng tự hủy mua chung',
            'refund_img' => json_encode([]),
        ];
        $res1 = $orderRefundService->applyRefund((int)$order['id'], (int)$order['uid'], $order, [], 1, (float)$order['pay_price'], $refundData, 1);
        $res2 = $this->dao->getCount([['uid', '=', $pinkT['uid']], ['k_id|id', '=', $pinkT['id']]]);
        if ($res1 && $res2) {
            $this->orderPinkAfterNo($pinkT['uid'], $pinkT['id'], true, $order->is_channel);
        }
        //Khi nhóm hiện tại còn có người
        if (is_array($nextPinkT)) {
            $this->dao->update($nextPinkT['id'], ['k_id' => 0, 'status' => 1, 'stop_time' => $pinkT['stop_time']]);
            $this->dao->update($pinkT['id'], ['k_id' => $nextPinkT['id']], 'k_id');
            $orderService->update($nextPinkT['order_id'], ['pink_id' => $nextPinkT['id']], 'order_id');
        }
        return true;
    }

    /**
     * Lấy poster mua chung
     * @param $pinkId
     * @param $from
     * @param $user
     * @return string
     */
    public function getPinkPoster($pinkId, $from, $user)
    {
        $pinkInfo = $this->dao->get((int)$pinkId);
        /** @var StoreCombinationServices $combinationService */
        $combinationService = app()->make(StoreCombinationServices::class);
        $storeCombinationInfo = $combinationService->getOne(['id' => $pinkInfo['cid']], '*', ['getPrice']);
        $data['title'] = $storeCombinationInfo['title'];
        $data['image'] = $storeCombinationInfo['image'];
        $data['price'] = $pinkInfo['price'];
        $data['label'] = $pinkInfo['people'] . 'người/nhóm';
        if ($pinkInfo['k_id']) $pinkAll = $this->getPinkMember($pinkInfo['k_id']);
        else $pinkAll = $this->getPinkMember($pinkInfo['id']);
        $count = count($pinkAll);
        $data['msg'] = 'Giá gốc ₫' . $storeCombinationInfo['product_price'] . ' còn thiếu' . ($pinkInfo['people'] - $count) . 'người nữa là mua chung thành công';

        /** @var SystemAttachmentServices $systemAttachmentServices */
        $systemAttachmentServices = app()->make(SystemAttachmentServices::class);

        try {
            $siteUrl = sys_config('site_url');
            if ($from == 'routine') {
                //Mini Program
                $name = $pinkId . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_pink_share_routine.jpg';
                $imageInfo = $systemAttachmentServices->getInfo(['name' => $name]);
                if (!$imageInfo) {
                    $valueData = 'id=' . $pinkId;
                    /** @var UserServices $userServices */
                    $userServices = app()->make(UserServices::class);
                    if ($userServices->checkUserPromoter((int)$user['uid'], $user)) {
                        $valueData .= '&pid=' . $user['uid'];
                    }
                    $res = MiniProgramService::appCodeUnlimitService($valueData, 'pages/activity/goods_combination_status/index', 280);
                    if (!$res) throw new ApiException(410167);
                    $uploadType = (int)sys_config('upload_type', 1);
                    $upload = UploadService::init();
                    $res = (string)EntityBody::factory($res);
                    $res = $upload->to('routine/activity/pink/code')->validate()->setAuthThumb(false)->stream($res, $name);
                    if ($res === false) {
                        throw new ApiException($upload->getError());
                    }
                    $imageInfo = $upload->getUploadInfo();
                    $imageInfo['image_type'] = $uploadType;
                    if ($imageInfo['image_type'] == 1) $remoteImage = PosterServices::remoteImage($siteUrl . $imageInfo['dir']);
                    else $remoteImage = PosterServices::remoteImage($imageInfo['dir']);
                    if (!$remoteImage['status']) throw new ApiException($remoteImage['msg']);
                    $systemAttachmentServices->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => time(),
                        'pid' => 1,
                        'type' => 1
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                $data['url'] = $url;
                if ($imageInfo['image_type'] == 1)
                    $data['url'] = $siteUrl . $url;
                $posterImage = PosterServices::setShareMarketingPoster($data, 'routine/activity/pink/poster');
                if (!is_array($posterImage)) throw new ApiException(410172);
                $systemAttachmentServices->save([
                    'name' => $posterImage['name'],
                    'att_dir' => $posterImage['dir'],
                    'satt_dir' => $posterImage['thumb_path'],
                    'att_size' => $posterImage['size'],
                    'att_type' => $posterImage['type'],
                    'image_type' => $posterImage['image_type'],
                    'module_type' => 2,
                    'time' => $posterImage['time'],
                    'pid' => 1,
                    'type' => 1
                ]);
                if ($posterImage['image_type'] == 1) $posterImage['dir'] = $siteUrl . $posterImage['dir'];
                $routinePosterImage = set_http_type($posterImage['dir'], 0);//Poster giới thiệu Mini Program
                return $routinePosterImage;
            } else if ($from == 'wechat') {
                //OA WeChat
                $name = $pinkId . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_pink_share_wap.jpg';
                $imageInfo = $systemAttachmentServices->getInfo(['name' => $name]);
                if (!$imageInfo) {
                    $codeUrl = set_http_type($siteUrl . '/pages/activity/goods_combination_status/index?id=' . $pinkId . '&spread=' . $user['uid'], 1);//Liên kết mã QR
                    $imageInfo = PosterServices::getQRCodePath($codeUrl, $name);
                    if (is_string($imageInfo)) {
                        throw new ApiException(410167);
                    }
                    $systemAttachmentServices->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => $imageInfo['time'],
                        'pid' => 1,
                        'type' => 1
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                $data['url'] = $url;
                if ($imageInfo['image_type'] == 1) $data['url'] = $siteUrl . $url;
                $posterImage = PosterServices::setShareMarketingPoster($data, 'wap/activity/pink/poster');
                if (!is_array($posterImage)) throw new ApiException(410172);
                $systemAttachmentServices->save([
                    'name' => $posterImage['name'],
                    'att_dir' => $posterImage['dir'],
                    'satt_dir' => $posterImage['thumb_path'],
                    'att_size' => $posterImage['size'],
                    'att_type' => $posterImage['type'],
                    'image_type' => $posterImage['image_type'],
                    'module_type' => 2,
                    'time' => $posterImage['time'],
                    'pid' => 1,
                    'type' => 1
                ]);
                if ($posterImage['image_type'] == 1) $posterImage['dir'] = $siteUrl . $posterImage['dir'];
                $wapPosterImage = set_http_type($posterImage['dir'], 1);//Poster giới thiệu OA WeChat
                return $wapPosterImage;
            }
            throw new ApiException(100100);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Cập nhật trạng thái mua chung đã hết hạn
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function statusPink()
    {
        $pinkListEnd = $this->dao->pinkListEnd();
        foreach ($pinkListEnd as $key => $pink) {
            [$pinkAll, $pinkT, $count, $idAll, $uidAll] = $this->getPinkMemberAndPinkK($pink);
            $this->pinkFail($pinkAll, $pinkT, 0);
        }
        return true;
    }

    /**
     * Mua chung thành công
     * @param array $pinkRegimental Mã trưởng nhóm thành công
     * @return bool
     * @throws \Exception
     */
    public function successPinkEdit(array $pinkRegimental)
    {
        if (!count($pinkRegimental)) return true;
        foreach ($pinkRegimental as $key => &$item) {
            $pinkList = $this->dao->getColumn(['k_id' => $item], 'id', 'id');
            $pinkList[] = $item;
            $pinkList = implode(',', $pinkList);
            $this->dao->update([['id', 'in', $pinkList]], ['stop_time' => time(), 'status' => 2]);
            $pinkUidList = $this->dao->getColumn([['id', 'in', $pinkList], ['is_tpl', '=', 0]], 'uid', 'uid');
            if (count($pinkUidList)) $this->orderPinkAfter($pinkUidList, $item);//Gửi tin nhắn mẫu
        }
        return true;
    }

    /**
     * Mua chung thất bại
     * @param array $pinkRegimental Mã trưởng nhóm thất bại
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function failPinkEdit(array $pinkRegimental)
    {
        if (!count($pinkRegimental)) return true;
        foreach ($pinkRegimental as $key => &$item) {
            $pinkList = $this->dao->getColumn(['k_id' => $item], 'id', 'id');
            $pinkList[] = $item;
            $pinkList = implode(',', $pinkList);
            $refundPinkList = $this->dao->getColumn([['id', 'in', $pinkList]], 'order_id,uid', 'id');
            if ($refundPinkList) {
                /** @var StoreOrderRefundServices $orderRefundService */
                $orderRefundService = app()->make(StoreOrderRefundServices::class);
                $refundData = [
                    'refund_reason' => 'Hết thời gian mua chung',
                    'refund_explain' => 'Hết thời gian mua chung',
                    'refund_img' => json_encode([]),
                ];
                foreach ($refundPinkList as &$items) {
                    $orderRefundService->applyRefund((int)$items['id'], (int)$items['uid'], $items, [], 1, (float)$items['pay_price'], $refundData, 1);//Yêu cầu hoàn tiền
                }
            }
            $this->dao->update([['id', 'in', $pinkList]], ['status' => 3]);
        }
        return true;
    }

    /**
     * Mua chung ảo
     * @param $pinkId
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function virtualCombination($pinkId, $operator = 'auto')
    {
        $pinkInfo = $this->dao->get($pinkId);
        $people = $pinkInfo['people'];
        $count = $this->dao->count(['k_id' => $pinkId]) + 1;
        $percent1 = bcdiv((string)$count, (string)$people, 2) * 100;
        /** @var StoreCombinationServices $services */
        $services = app()->make(StoreCombinationServices::class);
        $percent2 = $services->value(['id' => $pinkInfo['cid']], 'virtual');
        if ($percent1 >= $percent2 || $operator == 'admin') {
            $time = time();
            $num = $people - $count;
            $data = [];
            for ($i = 0; $i < $num; $i++) {
                $data[$i]['uid'] = 0;
                $data[$i]['nickname'] = substr(md5(time() . rand(1000, 9999)), 0, 12);
                $data[$i]['avatar'] = sys_config('h5_avatar');
                $data[$i]['order_id'] = 0;
                $data[$i]['order_id_key'] = 0;
                $data[$i]['total_num'] = 0;
                $data[$i]['total_price'] = 0;
                $data[$i]['cid'] = $pinkInfo['cid'];
                $data[$i]['pid'] = $pinkInfo['pid'];
                $data[$i]['people'] = $people;
                $data[$i]['price'] = 0;
                $data[$i]['add_time'] = $time;
                $data[$i]['stop_time'] = $time;
                $data[$i]['k_id'] = $pinkInfo['id'];
                $data[$i]['is_tpl'] = 1;
                $data[$i]['is_refund'] = 0;
                $data[$i]['status'] = 2;
                $data[$i]['is_virtual'] = 1;
            }
            //Thêm thành viên ảo
            $this->dao->saveAll($data);
            //Đổi trạng thái thành viên thành mua chung thành công
            $this->dao->update($pinkId, ['stop_time' => $time, 'status' => 2], 'k_id');
            //Đổi trạng thái trưởng nhóm thành mua chung thành công
            $this->dao->update($pinkId, ['stop_time' => $time, 'status' => 2]);
            $uidAll = $this->dao->getColumn([['id|k_id', '=', $pinkId]], 'uid');
            $this->orderPinkAfter($uidAll, $pinkId);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lấy thông tin chi tiết poster mua chung
     * @param int $id
     * @param $user
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function posterInfo(int $id, $user)
    {
        $pinkInfo = $this->dao->get($id);
        /** @var StoreCombinationServices $combinationService */
        $combinationService = app()->make(StoreCombinationServices::class);
        $storeCombinationInfo = $combinationService->getOne(['id' => $pinkInfo['cid']], '*', ['getPrice']);
        $data['title'] = $storeCombinationInfo['title'];
        $data['url'] = '';
        $data['image'] = $storeCombinationInfo['image'];
        $data['price'] = $pinkInfo['price'];
        $data['label'] = $pinkInfo['people'] . 'người/nhóm';
        if ($pinkInfo['k_id']) $pinkAll = $this->getPinkMember($pinkInfo['k_id']);
        else $pinkAll = $this->getPinkMember($pinkInfo['id']);
        $count = count($pinkAll);
        $data['msg'] = 'Giá gốc ₫' . $storeCombinationInfo['product_price'] . ' còn thiếu' . ($pinkInfo['people'] - $count) . 'người nữa là mua chung thành công';

        /** @var SystemAttachmentServices $systemAttachmentServices */
        $systemAttachmentServices = app()->make(SystemAttachmentServices::class);

        try {
            $siteUrl = sys_config('site_url');
            if (request()->isRoutine()) {
                //Mini Program
                $name = $id . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_pink_share_routine.jpg';
                $imageInfo = $systemAttachmentServices->getInfo(['name' => $name]);
                if (!$imageInfo) {
                    $valueData = 'id=' . $id;
                    /** @var UserServices $userServices */
                    $userServices = app()->make(UserServices::class);
                    if ($userServices->checkUserPromoter((int)$user['uid'], $user)) {
                        $valueData .= '&pid=' . $user['uid'];
                    }
                    $res = MiniProgramService::appCodeUnlimitService($valueData, 'pages/activity/goods_combination_status/index', 280);
                    if (!$res) throw new ApiException(410167);
                    $uploadType = (int)sys_config('upload_type', 1);
                    $upload = UploadService::init();
                    $res = $upload->to('routine/activity/pink/code')->validate()->setAuthThumb(false)->stream($res, $name);
                    if ($res === false) {
                        throw new ApiException($upload->getError());
                    }
                    $imageInfo = $upload->getUploadInfo();
                    $imageInfo['image_type'] = $uploadType;
                    if ($imageInfo['image_type'] == 1) $remoteImage = PosterServices::remoteImage($siteUrl . $imageInfo['dir']);
                    else $remoteImage = PosterServices::remoteImage($imageInfo['dir']);
                    if (!$remoteImage['status']) throw new ApiException($remoteImage['msg']);
                    $systemAttachmentServices->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => time(),
                        'pid' => 1,
                        'type' => 1
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                $data['url'] = $url;
                if ($imageInfo['image_type'] == 1)
                    $data['url'] = $siteUrl . $url;
            } else {
                if (sys_config('share_qrcode', 0) && request()->isWechat()) {
                    /** @var QrcodeServices $qrcodeService */
                    $qrcodeService = app()->make(QrcodeServices::class);
                    $data['url'] = $qrcodeService->getTemporaryQrcode('pink-' . $id, $user['uid'])->url;
                }
            }
        } catch (\Throwable $e) {
        }
        return $data;
    }
}
