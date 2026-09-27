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

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserSignDao;
use app\services\system\SystemSignRewardServices;
use app\services\user\member\MemberCardServices;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;
use think\facade\Log;

/**
 *
 * Class UserSignServices
 * @package app\services\user
 */
class UserSignServices extends BaseServices
{

    /**
     * UserSignServices constructor.
     * @param UserSignDao $dao
     */
    public function __construct(UserSignDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy trạng thái người dùng đã điểm danh chưa
     * @param int $uid
     * @param string $type
     * @return bool
     * @throws \ReflectionException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function getIsSign(int $uid, string $type = 'today')
    {
        return (bool)$this->dao->count(['uid' => $uid, 'time' => $type]);
    }

    /**
     * Lấy số lần điểm danh tích lũy của người dùng
     * @param int $uid
     * @return int
     * @throws \ReflectionException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function getSignSumDay(int $uid)
    {
        return $this->dao->count(['uid' => $uid]);
    }

    /**
     * Đặt dữ liệu điểm danh
     * @param $uid
     * @param string $title
     * @param int $number
     * @param int $integral_balance
     * @param int $exp_banlance
     * @param int $exp_num
     * @return bool
     * @throws \think\Exception
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function setSignData($uid, $title = '', $number = 0, $integral_balance = 0, $exp_banlance = 0, $exp_num = 0)
    {
        $data = [];
        $data['uid'] = $uid;
        $data['title'] = $title;
        $data['number'] = $number;
        $data['balance'] = $integral_balance + $number;
        $data['add_time'] = time();
        if (!$this->dao->save($data)) {
            throw new ApiException(410290);
        }
        /** @var UserBillServices $userBill */
        $userBill = app()->make(UserBillServices::class);
        $data['mark'] = $title;
        $userBill->incomeIntegral($uid, 'sign', $data);

        if ($exp_num) {
            $data['number'] = $exp_num;
            $data['category'] = 'exp';
            $data['type'] = 'sign';
            $data['title'] = $data['mark'] = 'Phần thưởng điểm danh';
            $data['balance'] = $exp_banlance + $exp_num;
            $data['pm'] = 1;
            $data['status'] = 1;
            if (!$userBill->save($data)) {
                throw new ApiException(410291);
            }
            //Kiểm tra hạng thành viên
            try {
                //Event nâng cấp người dùng
                event('UserLevelListener', [$uid]);
            } catch (\Throwable $e) {
                Log::error('Nâng hạng thành viên thất bại, nguyên nhân:' . $e->getMessage());
            }
        }
        return true;
    }

    /**
     * Lấy danh sách điểm danh của người dùng
     * @param int $uid
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function getUserSignList(int $uid, string $field = '*')
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList(['uid' => $uid], $field, $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = $item['add_time'] ? date('Y-m-d', $item['add_time']) : '';
        }
        return $list;
    }

    /**
     * Người dùng điểm danh
     * @param $uid
     * @return bool|int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sign(int $uid)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);

        //Kiểm tra người dùng có tồn tại không
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException(410032);
        }

        $userServices->offMemberLevel($uid);

        //Kiểm tra hôm nay đã điểm danh chưa
        if ($this->getIsSign($uid, 'today')) {
            throw new ApiException(410293);
        }
        $title = 'Phần thưởng điểm danh';
        //Kiểm tra hôm qua có điểm danh không, nếu không thì số lần điểm danh liên tục về 0
        if (!$this->getIsSign($uid, 'yesterday')) $user->sign_num = 0;

        //Lấy cấu hình chu kỳ điểm danh: nếu điểm danh theo tuần, mỗi thứ Hai xóa bản ghi điểm danh liên tục; nếu điểm danh theo tháng, mỗi ngày 1 hàng tháng xóa bản ghi điểm danh liên tục
        $sign_mode = sys_config('sign_mode', -1);
        if ($sign_mode == 1 && date('w') == 1) $user->sign_num = 0;
        if ($sign_mode == 0 && date('d') == 1) $user->sign_num = 0;

        //Số ngày điểm danh liên tục
        $user->sign_num += 1;
        $continuousDays = $user->sign_num;
        //Số ngày điểm danh tích lũy
        $cumulativeDays = $this->dao->getCumulativeDays($sign_mode, $uid);

        //Thưởng điểm danh cơ bản
        $sign_point = sys_config('sign_give_point', 0);
        $sign_exp = sys_config('member_func_status', 1) ? sys_config('sign_give_exp', 0) : 0;

        //Thưởng điểm danh liên tục và điểm danh tích lũy
        $signRewardsServices = app()->make(SystemSignRewardServices::class);
        [$continuousStatus, $continuousRewardPoint, $continuousRewardExp] = $signRewardsServices->getSignRewards(0, $continuousDays);
        [$cumulativeStatus, $cumulativeRewardPoint, $cumulativeRewardExp] = $signRewardsServices->getSignRewards(1, $cumulativeDays);
        if ($continuousStatus && $cumulativeStatus) {
            $sign_point = $continuousRewardPoint + $cumulativeRewardPoint;
            $sign_exp = $continuousRewardExp + $cumulativeRewardExp;
        } elseif ($continuousStatus) {
            $sign_point = $continuousRewardPoint;
            $sign_exp = $continuousRewardExp;
        } elseif ($cumulativeStatus) {
            $sign_point = $cumulativeRewardPoint;
            $sign_exp = $cumulativeRewardExp;
        }

        //Thưởng điểm thưởng điểm danh cho thành viên
        if ($user->is_money_level > 0) {
            //Xem có mở thưởng gấp đôi điểm thưởng điểm danh không
            /** @var MemberCardServices $memberCardService */
            $memberCardService = app()->make(MemberCardServices::class);
            $sign_rule_number = $memberCardService->isOpenMemberCard('sign');
            if ($sign_rule_number) {
                $up_num = (int)$sign_rule_number * $sign_point - $sign_point;
                $sign_point = (int)$sign_rule_number * $sign_point;
                if (!$this->getIsSign($uid, 'yesterday')) $title = 'Thưởng điểm danh (SVIP+' . $up_num . ')';
            }
        }

        //Thêm dữ liệu điểm danh
        $this->transaction(function () use ($uid, $title, $sign_point, $user, $sign_exp) {
            $this->setSignData($uid, $title, $sign_point, $user['integral'], (int)$user['exp'], $sign_exp);
            $user->integral = (int)$user->integral + (int)$sign_point;
            if ($sign_exp) $user->exp = bcadd((string)$user->exp, (string)$sign_exp, 2);
            if (!$user->save()) {
                throw new ApiException(410287);
            }
        });

        //Sự kiện tùy chỉnh - Người dùng điểm danh
        event('CustomEventListener', ['user_sign', [
            'uid' => $uid,
            'sign_point' => $sign_point,
            'sign_exp' => $sign_exp,
            'sign_time' => date('Y-m-d H:i:s'),
        ]]);

        return $sign_point;
    }

    /**
     * Thông tin người dùng điểm danh
     * @param int $uid
     * @param $sign
     * @param $integral
     * @param $all
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function signUser(int $uid, $sign, $integral, $all)
    {
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $user = $userServices->getUserInfo($uid);
        if (!$user) {
            throw new ApiException(100026);
        }
        //Có thống kê điểm danh không
        if ($sign || $all) {
            $user['sum_sgin_day'] = $this->getSignSumDay($user['uid']);
            $user['is_day_sgin'] = false;
            $user['is_YesterDay_sgin'] = $this->getIsSign($user['uid'], 'yesterday');
            if (!$user['is_day_sgin'] && !$user['is_YesterDay_sgin']) {
                $user['sign_num'] = 0;
            }
        }
        //Có thống kê tình hình sử dụng điểm thưởng không
        if ($integral || $all) {
            /** @var UserBillServices $userBill */
            $userBill = app()->make(UserBillServices::class);
            $user['sum_integral'] = intval($userBill->getRecordCount($user['uid'], 'integral', 'sign,system_add,gain,lottery_add,product_gain,pay_product_integral_back'));
            $user['deduction_integral'] = intval($userBill->getRecordCount($user['uid'], 'integral', 'deduction,lottery_use,order_deduction,storeIntegral_use', '', true) ?? 0);
            $user['today_integral'] = intval($userBill->getRecordCount($user['uid'], 'integral', 'sign,system_add,gain,product_gain,lottery_add,pay_product_integral_back', 'today'));
            /** @var UserBillServices $userBillServices */
            $userBillServices = app()->make(UserBillServices::class);
            $user['frozen_integral'] = $userBillServices->getBillSum(['uid' => $user['uid'], 'is_frozen' => 1]);
        }
        unset($user['pwd']);
        if (!$user['is_promoter']) {
            $user['is_promoter'] = (int)sys_config('store_brokerage_statu') == 2;
        }
        return $user->hidden(['account', 'real_name', 'birthday', 'card_id', 'mark', 'partner_id', 'group_id', 'add_time', 'add_ip', 'phone', 'last_time', 'last_ip', 'spread_uid', 'spread_time', 'user_type', 'status', 'level', 'clean_time', 'addres'])->toArray();
    }

    /**
     * Lấy điểm danh
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function getSignMonthList($uid)
    {
        [$page, $limit] = $this->getPageValue();
        $data = $this->dao->getListGroup(['uid' => $uid], 'FROM_UNIXTIME(add_time,"%Y-%m") as time,group_concat(id SEPARATOR ",") ids', $page, $limit, 'time');
        $list = [];
        if ($data) {
            $ids = array_unique(array_column($data, 'ids'));
            $dataIdsList = $this->dao->getList(['id' => $ids], 'FROM_UNIXTIME(add_time,"%Y-%m-%d") as add_time,title,number,id,uid', 0, 0);
            foreach ($data as $item) {
                $value['month'] = $item['time'];
                $value['list'] = array_merge(array_filter($dataIdsList, function ($val) use ($item) {
                    if (in_array($val['id'], explode(',', $item['ids']))) {
                        return $val;
                    }
                }));
                array_push($list, $value);
            }
        }
        return $list;
    }

    /**
     * Trả về dữ liệu danh sách điểm danh
     * @param $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/8
     */
    public function signConfig($uid, $signMode = 0)
    {
        if (!$signMode) {
            //Lấy biết là điểm danh theo tuần hay theo tháng
            $signMode = (int)sys_config('sign_mode', 1);
        }
        //Lấy danh sách điểm danh
        $startDate = $signMode == 1 ? strtotime('this week Monday') : strtotime('first day of this month midnight');
        $endDate = $signMode == 1 ? strtotime('this week Sunday') : strtotime('last day of this month midnight');
        $dateList = range($startDate, $endDate, 86400);

        //Lấy danh sách đã điểm danh
        $list = $this->dao->getUserSignList($signMode, $uid);

        //Lấy điểm danh tích lũy và điểm danh liên tục
        $cumulativeSignDays = $this->dao->getCumulativeDays($signMode, $uid);
        $continuousSignDays = app()->make(UserServices::class)->value($uid, 'sign_num');

        //Lấy thưởng điểm danh tích lũy và điểm danh liên tục
        $nextCumulativeSignRewardList = app()->make(SystemSignRewardServices::class)->selectList(['type' => 1], '*', 1, 200, 'days asc')->toArray();
        $nextContinuousSignRewardList = app()->make(SystemSignRewardServices::class)->selectList(['type' => 0], '*', 1, 200, 'days asc')->toArray();

        //Cần điểm danh thêm mấy ngày nữa để nhận thưởng điểm danh liên tục lần sau
        $nextContinuousDays = 0;
        foreach ($nextContinuousSignRewardList as $continuousNext) {
            if ($continuousSignDays < $continuousNext['days']) {
                $nextContinuousDays = $continuousNext['days'] - $continuousSignDays > 0 ? $continuousNext['days'] - $continuousSignDays : 1;
                break;
            }
        }
        $nextCumulativeDays = 0;
        foreach ($nextCumulativeSignRewardList as $cumulativeNext) {
            if ($cumulativeSignDays < $cumulativeNext['days']) {
                $nextCumulativeDays = $cumulativeNext['days'] - $cumulativeSignDays > 0 ? $cumulativeNext['days'] - $cumulativeSignDays : 1;
                break;
            }
        }

        //Sắp xếp dữ liệu danh sách điểm danh
        $signList = [];
        $i = 0;
        $checkSign = $this->getIsSign($uid, 'today');
        foreach ($dateList as $key => $time) {
            $day = date('m.d', $time);
            if ($day[0] == '0') $day = substr($day, 1);
            $signList[$key]['day'] = $day;
            $signList[$key]['is_sign'] = false;
            $signList[$key]['type'] = 0;

            //Xác định ngày điểm danh hiện tại
            $signList[$key]['sign_day'] = date('Y-m-d', $time) == date('Y-m-d', time());

            //Xác định hôm nay đã điểm danh chưa
            foreach ($list as $value) {
                if (date('Y-m-d', $time) == date('Y-m-d', $value['add_time'])) {
                    $signList[$key]['is_sign'] = true;
                    break;
                }
            }

            //Xử lý hiển thị loại điểm danh: type 0 đã điểm danh, 1 điểm thưởng, 2 kinh nghiệm, 3 liên tục, 4 tích lũy
            $signList[$key]['type'] = sys_config('sign_give_point', 0) == 0 && sys_config('member_func_status', 1) == 1 && sys_config('sign_give_exp', 0) > 0 ? 2 : 1;
            $signList[$key]['point'] = (int)sys_config('sign_give_point');
            if (date('Y-m-d', $time) >= date('Y-m-d', time())) {
                foreach ($nextContinuousSignRewardList as $continuous) {
                    if (($continuous['days'] - $continuousSignDays) == $i) {
                        $signList[$key]['type'] = 3;
                        $signList[$key]['point'] = $continuous['point'];
                    }
                }
                foreach ($nextCumulativeSignRewardList as $cumulative) {
                    if (($cumulative['days'] - $cumulativeSignDays) == $i) {
                        $signList[$key]['type'] = 4;
                        $signList[$key]['point'] = $cumulative['point'];
                    }
                }
                $i++;
            }
        }

        //Định dạng dữ liệu điểm danh
        $signList = array_chunk($signList, 7);

        //Lấy trạng thái nhắc nhở điểm danh của người dùng
        $signRemindStatus = app()->make(UserServices::class)->value($uid, 'sign_remind');

        //Có hiển thị công tắc nhắc nhở điểm danh không
        $signRemindSwitch = (int)sys_config('sign_remind', 0);

        //Chức năng điểm danh có bị tắt không
        $signStatus = (int)sys_config('sign_status', 0);

        //Chức năng điểm danh có bị tắt không
        $signGivePoint = (int)sys_config('sign_give_point', 0);

        return compact('signList', 'continuousSignDays', 'cumulativeSignDays', 'nextContinuousDays', 'nextCumulativeDays', 'signMode', 'checkSign', 'signRemindStatus', 'signRemindSwitch', 'signStatus', 'signGivePoint');
    }

    /**
     * Cài đặt nhắc nhở điểm danh
     * @param $uid
     * @param $status
     * @return bool
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/9
     */
    public function setSignRemind($uid, $status)
    {
        app()->make(UserServices::class)->update($uid, ['sign_remind' => $status]);
        return true;
    }

    /**
     * Nhắc nhở điểm danh
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2023/9/30
     */
    public function sendSignRemind()
    {
        //Hôm nay đã gửi rồi, không thực hiện logic gửi nhắc nhở
        if (CacheService::get('sign_remind_expire')) return true;
        //Thời gian hiện tại nhỏ hơn thời gian gửi nhắc nhở hàng ngày, không thực hiện logic gửi nhắc nhở
        if (time() < strtotime('today ' . sys_config('sign_remind_time'))) return true;
        //Lấy người dùng cần nhắc nhở điểm danh
        $list = app()->make(UserServices::class)->getColumn(['sign_remind' => 1], 'phone', 'uid');
        if ($list) {
            //Lấy người dùng đã điểm danh hôm nay
            $signList = $this->dao->getColumn([['add_time', 'between', [strtotime('today'), strtotime('today 23:59:59')]]], 'uid');
            $noSignList = array_diff_key($list, array_flip($signList));
            foreach ($noSignList as $uid => $phone) {
                event('NoticeListener', [['uid' => $uid, 'phone' => $phone], 'sign_remind']);
            }
        }
        //Đã gửi nhắc nhở, ghi vào cache, cấm thực hiện nhiều lần trong ngày
        CacheService::set('sign_remind_expire', 1, strtotime('tomorrow') - time());
        return true;
    }
}
