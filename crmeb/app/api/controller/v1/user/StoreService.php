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
namespace app\api\controller\v1\user;

use app\api\validate\user\StoreServiceFeedbackValidate;
use app\Request;
use app\services\kefu\service\StoreServiceFeedbackServices;
use app\services\kefu\service\StoreServiceLogServices;
use app\services\kefu\service\StoreServiceRecordServices;
use app\services\kefu\service\StoreServiceServices;
use app\services\other\CacheServices;
use crmeb\services\CacheService;

/**
 * Lớp chăm sóc khách hàng
 * Class StoreService
 * @package app\api\controller\user
 */
class StoreService
{
    /**
     * @var StoreServiceLogServices
     */
    protected $services;

    /**
     * StoreService constructor.
     * @param StoreServiceLogServices $services
     */
    public function __construct(StoreServiceLogServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách nhân viên CSKH
     * @param StoreServiceServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(StoreServiceServices $services)
    {
        $serviceInfoList = $services->getServiceList(['status' => 1]);
        if (!count($serviceInfoList)) return app('json')->success([]);
        return app('json')->success($serviceInfoList['list']);
    }

    /**
     * Lịch sử trò chuyện CSKH
     * @param Request $request
     * @param StoreServiceServices $services
     * @param StoreServiceRecordServices $recordServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function record(Request $request, StoreServiceServices $services, StoreServiceRecordServices $recordServices)
    {
        list($uidTo) = $request->getMore([
            ['uidTo', 0]
        ], true);
        $serviceInfoList = $services->getServiceList(['status' => 1]);
        if (!count($serviceInfoList)) return app('json')->fail('Hiện không có nhân viên CSKH trực tuyến, vui lòng liên hệ lại sau');
        $uid = $request->uid();
        $uids = array_column($serviceInfoList['list'], 'uid');
        if (!$uidTo) {
            //Chính là nhân viên chăm sóc khách hàng
            if (in_array($uid, $uids)) {
                $uids = array_merge(array_diff($uids, [$uid]));
                if (!$uids) return app('json')->fail('Không thể trò chuyện với chính mình');
            }
        } else {
            if (in_array($uid, $uids)) {
                $uid = $uidTo;
            }
        }
        if (!$uids) {
            return app('json')->fail('Hiện không có nhân viên CSKH trực tuyến, vui lòng liên hệ lại sau');
        }
        //Ưu tiên trò chuyện với nhân viên CSKH đã chat lần trước
        $toUid = $recordServices->value(['user_id' => $uid], 'to_uid');
        if (!in_array($toUid, $uids)) {
            $toUid = 0;
        }
        if (!$toUid) {
            $toUid = $uids[array_rand($uids)] ?? 0;
        }

        if (!$toUid) return app('json')->fail('Hiện không có nhân viên CSKH trực tuyến, vui lòng liên hệ lại sau');
        $result = ['serviceList' => [], 'uid' => $toUid];
        $serviceLogList = $this->services->getChatList(['uid' => $uid], $uid);
        if (!$serviceLogList) return app('json')->success($result);
        $idArr = array_column($serviceLogList, 'id');
        array_multisort($idArr, SORT_ASC, $serviceLogList);
        $result['serviceList'] = $serviceLogList;
        return app('json')->success($result);
    }

    /**
     * Lấy nội dung quảng cáo trang chăm sóc khách hàng
     * @return mixed
     */
    public function getKfAdv()
    {
        /** @var CacheServices $cache */
        $cache = app()->make(CacheServices::class);
        $content = $cache->getDbCache('kf_adv', '');
        return app('json')->success(compact('content'));
    }

    /**
     * Lưu thông tin phản hồi
     * @param Request $request
     * @param StoreServiceFeedbackServices $services
     * @return mixed
     */
    public function saveFeedback(Request $request, StoreServiceFeedbackServices $services)
    {
        $data = $request->postMore([
            ['rela_name', ''],
            ['phone', ''],
            ['content', ''],
        ]);

        validate(StoreServiceFeedbackValidate::class)->check($data);

        $data['content'] = htmlspecialchars($data['content']);
        $data['add_time'] = time();
        $data['uid'] = $request->uid();
        $services->save($data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Chữ ở phần đầu trang phản hồi CSKH
     * @return mixed
     */
    public function getFeedbackInfo()
    {
        return app('json')->success(['feedback' => sys_config('service_feedback')]);
    }

    /**
     * Xác nhận đăng nhập
     * @param Request $request
     * @param StoreServiceServices $services
     * @param string $code
     * @return mixed
     */
    public function setLoginCode(Request $request, StoreServiceServices $services, string $code)
    {
        if (!$code) {
            return app('json')->fail('Quét mã thất bại, vui lòng quét lại');
        }
        $cacheCode = CacheService::get($code);
        if ($cacheCode === false || $cacheCode === null) {
            return app('json')->fail('Mã QR đã hết hạn, vui lòng quét lại');
        }
        $userInfo = $services->get(['uid' => $request->uid()]);
        if (!$userInfo) {
            return app('json')->fail('Bạn không phải nhân viên CSKH, không thể đăng nhập');
        }
        $userInfo->uniqid = $code;
        $userInfo->save();
        CacheService::set($code, '0', 600);
        return app('json')->success('Đăng nhập thành công');
    }

    /**
     * Lấy lịch sử chat giữa nhân viên CSKH hiện tại và người dùng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function recordList(StoreServiceRecordServices $services, Request $request, string $nickname = '', $is_tourist = 0)
    {
        return app('json')->success($services->getServiceList((int)$request->uid(), $nickname, (int)$is_tourist));
    }
}
