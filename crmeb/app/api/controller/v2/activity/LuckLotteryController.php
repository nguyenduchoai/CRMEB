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
namespace app\api\controller\v2\activity;


use app\Request;
use app\services\activity\lottery\LuckLotteryRecordServices;
use app\services\activity\lottery\LuckLotteryServices;
use app\services\other\QrcodeServices;
use app\services\wechat\WechatServices;
use crmeb\services\CacheService;

class LuckLotteryController
{
    protected $services;

    public function __construct(LuckLotteryServices $services)
    {
        $this->services = $services;
    }

    /**
     * Thông tin hoạt động quay thưởng
     * @param Request $request
     * @param $factor
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function LotteryInfo(Request $request, $factor, $lottery_id = 0)
    {
        if (!$factor) return app('json')->fail('Tham số không hợp lệ');
        if ($lottery_id) {
            $lottery = $this->services->getLottery($lottery_id, '*', ['prize'], true);
        } else {
            $lottery = $this->services->getFactorLottery((int)$factor, '*', ['prize'], true);
        }
        if (!$lottery) {
            return app('json')->fail('Chương trình quay thưởng không tồn tại');
        }
        $uid = (int)$request->uid();
        $lottery = $lottery->toArray();
        $lotteryData = ['lottery' => $lottery];
        $this->services->checkoutUserAuth($uid, (int)$lottery['id'], [], $lottery);
        $lotteryData['lottery_num'] = $this->services->getLotteryNum($uid, (int)$lottery['id'], [], $lottery);
        $all_record = $user_record = [];
        if ($lottery['is_all_record'] || $lottery['is_personal_record']) {
            /** @var LuckLotteryRecordServices $lotteryRecordServices */
            $lotteryRecordServices = app()->make(LuckLotteryRecordServices::class);
            if ($lottery['is_all_record']) {
                $all_record = $lotteryRecordServices->getWinList(['lottery_id' => $lottery['id']]);
            }
            if ($lottery['is_personal_record']) {
                $user_record = $lotteryRecordServices->getWinList(['lottery_id' => $lottery['id'], 'uid' => $uid]);
            }
        }
        $lotteryData['all_record'] = $all_record;
        $lotteryData['user_record'] = $user_record;
        return app('json')->success($lotteryData);
    }

    /**
     * Tham gia quay thưởng
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function luckLottery(Request $request)
    {
        [$id, $type] = $request->postMore([
            ['id', 0],
            ['type', 0]
        ], true);

        $uid = (int)$request->uid();
        $channel_type = $request->getFromType();
        $key = 'lucklotter_limit_' . $uid;
        if (CacheService::get($key)) {
            return app('json')->fail('Bạn gửi yêu cầu quá thường xuyên, vui lòng thử lại sau!');
        }
        CacheService::set('lucklotter_limit_' . $uid, $uid, 1);

        if ($type == 5 && request()->isWechat()) {
            /** @var WechatServices $wechat */
            $wechat = app()->make(WechatServices::class);
            $subscribe = $wechat->get(['user_type' => 'wechat', 'uid' => $request->uid(), 'subscribe' => 1]);
            if (!$subscribe) {
                $url = '';
                /** @var QrcodeServices $qrcodeService */
                $qrcodeService = app()->make(QrcodeServices::class);
                $url = $qrcodeService->getTemporaryQrcode('luckLottery-5', $request->uid())->url;
                return app('json')->success('Vui lòng theo dõi OA WeChat trước', ['code' => 'subscribe', 'url' => $url]);
            }
        }
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }

        return app('json')->success($this->services->luckLottery($uid, $id, $channel_type));
    }

    /**
     * Nhận phần thưởng
     * @param Request $request
     * @param LuckLotteryRecordServices $lotteryRecordServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lotteryReceive(Request $request, LuckLotteryRecordServices $lotteryRecordServices)
    {
        [$id, $name, $phone, $address, $detail, $mark] = $request->postMore([
            ['id', 0],
            ['name', ''],
            ['phone', ''],
            ['address', ''],
            ['detail', ''],
            ['mark', '']
        ], true);
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $uid = (int)$request->uid();
        return app('json')->success($lotteryRecordServices->receivePrize($uid, $id, compact('name', 'phone', 'address', 'detail', 'mark')) ? 'Nhận thành công' : 'Nhận thất bại');
    }

    /**
     * Lấy lịch sử trúng thưởng
     * @param Request $request
     * @param LuckLotteryRecordServices $lotteryRecordServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lotteryRecord(Request $request, LuckLotteryRecordServices $lotteryRecordServices)
    {
        $uid = (int)$request->uid();
        return app('json')->success($lotteryRecordServices->getRecord($uid));
    }
}
