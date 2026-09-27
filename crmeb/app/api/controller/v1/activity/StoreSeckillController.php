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
namespace app\api\controller\v1\activity;

use app\Request;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\other\QrcodeServices;
use crmeb\services\GroupDataService;

/**
 * Lớp sản phẩm flash sale
 * Class StoreSeckillController
 * @package app\api\controller\activity
 */
class StoreSeckillController
{

    protected $services;

    public function __construct(StoreSeckillServices $services)
    {
        $this->services = $services;
    }

    /**
     * Khung thời gian sản phẩm flash sale
     * @return mixed
     */
    public function index()
    {
        //Khung giờ flash sale
        $seckillTime = GroupDataService::getData('routine_seckill_time') ?? [];
        $seckillTimeIndex = -1;
        $timeCount = count($seckillTime);//Tổng số
        $unTimeCunt = 0;//Sắp bắt đầu
        if ($timeCount) {
            $today = strtotime(date('Y-m-d'));
            $currentHour = date('H');
            foreach ($seckillTime as $key => &$value) {
                $activityEndHour = bcadd((int)$value['time'], (int)$value['continued'], 0);
                if ($activityEndHour > 24) {
                    $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'] . ':00' : '0' . (int)$value['time'] . ':00';
                    $value['state'] = 'Sắp bắt đầu';
                    $value['status'] = 2;
                    $value['stop'] = (int)bcadd($today, bcmul($activityEndHour, 3600, 0));
                } else {
                    if ($currentHour >= (int)$value['time'] && $currentHour < $activityEndHour) {
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'] . ':00' : '0' . (int)$value['time'] . ':00';
                        $value['state'] = 'Đang diễn ra';
                        $value['stop'] = (int)bcadd($today, bcmul($activityEndHour, 3600, 0));
                        $value['status'] = 1;
                        if ($seckillTimeIndex == -1) $seckillTimeIndex = $key;
                    } else if ($currentHour < (int)$value['time']) {
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'] . ':00' : '0' . (int)$value['time'] . ':00';
                        $value['state'] = 'Sắp bắt đầu';
                        $value['status'] = 2;
                        $value['stop'] = (int)bcadd($today, bcmul($activityEndHour, 3600, 0));
                        $unTimeCunt += 1;
                    } else if ($currentHour >= $activityEndHour) {
                        $value['time'] = strlen((int)$value['time']) == 2 ? (int)$value['time'] . ':00' : '0' . (int)$value['time'] . ':00';
                        $value['state'] = 'Đã kết thúc';
                        $value['status'] = 0;
                        $value['stop'] = (int)bcadd($today, bcmul($activityEndHour, 3600, 0));
                    }
                }
            }
            //Có khoảng thời gian nhưng không nằm trong thời gian đang săn mua
            if ($seckillTimeIndex == -1 && $currentHour <= (int)$seckillTime[$timeCount - 1]['time'] ?? 0) {
                if ($currentHour < (int)$seckillTime[0]['time'] ?? 0) {//Thời gian hiện tại
                    $seckillTimeIndex = 0;
                } elseif ($unTimeCunt) {//Có tồn tại khoảng chưa bắt đầu
                    foreach ($seckillTime as $key => $item) {
                        if ($item['status'] == 2) {
                            $seckillTimeIndex = $key;
                            break;
                        }
                    }
                } else {
                    $seckillTimeIndex = $timeCount - 1;
                }
            }
        }
        $data['lovely'] = sys_config('seckill_header_banner');
        if (strstr($data['lovely'], 'http') === false && strlen(trim($data['lovely']))) $data['lovely'] = sys_config('site_url') . $data['lovely'];
        $data['lovely'] = str_replace('\\', '/', $data['lovely']);
        $data['seckillTime'] = $seckillTime;
        $data['seckillTimeIndex'] = $seckillTimeIndex;
        return app('json')->success($data);
    }

    /**
     * Danh sách sản phẩm flash sale
     * @param $time
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst($time)
    {
        if (!$time) return app('json')->fail(100100);
        $seckillInfo = $this->services->getListByTime($time);
        return app('json')->success(get_thumb_water($seckillInfo));
    }

    /**
     * Chi tiết sản phẩm flash sale
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(Request $request, $id)
    {
        [$time_id] = $request->getMore([
            ['time_id', 0]
        ], true);
        $data = $this->services->seckillDetail($request, $id, $time_id);
        return app('json')->success($data);
    }

    /**
     * Lấy mã QR Mini Program flash sale
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function code(Request $request, $id)
    {
        [$time_id] = $request->getMore([
            ['time_id', 0]
        ], true);
        /** @var QrcodeServices $qrcodeService */
        $qrcodeService = app()->make(QrcodeServices::class);
        $url = $qrcodeService->getRoutineQrcodePath($id, $request->uid(), 2, ['time_id' => $time_id]);
        if ($url) {
            return app('json')->success(['code' => $url]);
        } else {
            return app('json')->success(['code' => '']);
        }
    }
}
