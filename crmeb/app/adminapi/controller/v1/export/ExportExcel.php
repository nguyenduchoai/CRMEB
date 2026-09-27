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
namespace app\adminapi\controller\v1\export;

use app\adminapi\controller\AuthController;
use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\combination\StorePinkServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\agent\AgentManageServices;
use app\services\other\export\ExportServices;
use app\services\order\StoreOrderServices;
use app\services\product\product\StoreProductServices;
use app\services\system\store\SystemStoreServices;
use app\services\user\member\MemberCardServices;
use app\services\user\UserBillServices;
use app\services\user\UserRechargeServices;
use app\services\wechat\WechatUserServices;
use think\facade\App;

/**
 * Lớp xuất Excel
 * Class ExportExcel
 * @package app\adminapi\controller\v1\export
 */
class ExportExcel extends AuthController
{
    /**
     * @var ExportServices
     */
    protected $service;

    /**
     * ExportExcel constructor.
     * @param App $app
     * @param ExportServices $services
     */
    public function __construct(App $app, ExportServices $services)
    {
        parent::__construct($app);
        $this->service = $services;
    }

    public function userList()
    {
        $where = $this->request->getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['status', ''],
            ['pay_count', ''],
            ['is_promoter', ''],
            ['order', ''],
            ['data', ''],
            ['user_type', ''],
            ['country', ''],
            ['province', ''],
            ['city', ''],
            ['user_time_type', ''],
            ['user_time', ''],
            ['sex', ''],
            [['level', 0], 0],
            [['group_id', 'd'], 0],
            ['label_id', ''],
            ['now_money', 'normal'],
            ['field_key', ''],
            ['isMember', ''],
            ['ids', []]
        ]);
        return app('json')->success($this->service->exportUserList($where));
    }

    /**
     * Xuất đơn hàng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderList()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['real_name', ''],
            ['is_del', ''],
            ['data', '', '', 'time'],
            ['type', ''],
            ['pay_type', ''],
            ['order', ''],
            ['field_key', ''],
            ['ids', []],
        ]);
        $where['is_system_del'] = 0;
        $where['pid'] = 0;
        return app('json')->success($this->service->exportOrderList($where));
    }

    /**
     * Xuất danh sách đơn hàng cần giao
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function orderDeliveryList()
    {
        return app('json')->success($this->service->exportOrderDeliveryList());
    }

    /**
     * Xuất danh sách sản phẩm
     * @return mixed
     */
    public function productList()
    {
        $where = $this->request->getMore([
            ['store_name', ''],
            ['cate_id', ''],
            ['type', 1],
            ['ids', []]
        ]);
        return app('json')->success($this->service->exportProductList($where));
    }

    /**
     * Xuất danh sách sản phẩm săn giảm giá
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bargainList()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['store_name', ''],
        ]);
        $where['is_del'] = 0;
        return app('json')->success($this->service->exportBargainList($where));
    }

    /**
     * Xuất sản phẩm mua chung
     * @return mixed
     */
    public function combinationList()
    {
        $where = $this->request->getMore([
            ['is_show', ''],
            ['store_name', '']
        ]);
        $where['is_del'] = 0;
        return app('json')->success($this->service->exportCombinationList($where));
    }

    /**
     * Xuất sản phẩm flash sale
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function seckillList()
    {
        $where = $this->request->getMore([
            [['status', 's'], ''],
            [['store_name', 's'], '']
        ]);
        return app('json')->success($this->service->exportSeckillList($where));
    }

    /**
     * Xuất thẻ thành viên
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function memberCardList($id)
    {
        return app('json')->success($this->service->exportMemberCard($id));
    }

    /**
     * Lưu file excel theo dõi tài chính người dùng
     * @param UserBillServices $services
     * @return mixed
     */
    public function userFinance(UserBillServices $services)
    {
        $where = $this->request->getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['type', ''],
        ]);
        $data = $services->getBillList($where, '*', false);
        return app('json')->success($this->service->userFinance($data['data'] ?? []));
    }

    /**
     * Hoa hồng người dùng
     * @param UserBillServices $services
     * @return mixed
     */
    public function userCommission(UserBillServices $services)
    {
        $where = $this->request->getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['price_max', ''],
            ['price_min', ''],
            ['excel', '1'],
            ['time', ''],
        ]);
        $data = $services->getCommissionList($where, false);
        return app('json')->success($this->service->userCommission($data['list'] ?? []));
    }

    /**
     * Điểm thưởng người dùng
     * @param UserBillServices $services
     * @return mixed
     */
    public function userPoint(UserBillServices $services)
    {
        $where = $this->request->getMore([
            ['start_time', ''],
            ['end_time', ''],
            ['nickname', ''],
            ['excel', '1'],
        ]);
        $data = $services->getPointList($where, '*', false);
        return app('json')->success($this->service->userPoint($data['list'] ?? []));
    }

    /**
     * Nạp tiền người dùng
     * @param UserRechargeServices $services
     * @return mixed
     */
    public function userRecharge(UserRechargeServices $services)
    {
        $where = $this->request->getMore([
            ['data', ''],
            ['paid', ''],
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['excel', '1'],
        ]);
        $data = $services->getRechargeList($where, '*', false);
        return app('json')->success($this->service->userRecharge($data['list'] ?? []));
    }

    /**
     * Quản lý CTV - Người dùng giới thiệu
     * @param AgentManageServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userAgent(AgentManageServices $services)
    {
        $where = $this->request->getMore([
            ['nickname', ''],
            ['data', ''],
            ['excel', '1'],
        ]);
        $data = $services->agentSystemPage($where, false);
        return app('json')->success($this->service->userAgent($data['list']));
    }

    /**
     * Xuất người dùng WeChat (đã ngừng dùng)
     * @param WechatUserServices $services
     * @return mixed
     */
    public function wechatUser(WechatUserServices $services)
    {
        $where = $this->request->getMore([
            ['page', 1],
            ['limit', 20],
            ['nickname', ''],
            ['data', ''],
            ['tagid_list', ''],
            ['groupid', '-1'],
            ['sex', ''],
            ['export', '1'],
            ['subscribe', '']
        ]);
        $tagidList = explode(',', $where['tagid_list']);
        foreach ($tagidList as $k => $v) {
            if (!$v) {
                unset($tagidList[$k]);
            }
        }
        $tagidList = array_unique($tagidList);
        $where['tagid_list'] = implode(',', $tagidList);
        $data = $services->exportData($where);
        return app('json')->success($this->service->wechatUser($data));
    }

    /**
     * Xuất hoạt động săn giảm giá của shop
     * @param StoreBargainServices $services
     * @return mixed
     */
    public function storeBargain(StoreBargainServices $services)
    {
        $where = $this->request->getMore([
            ['start_status', ''],
            ['status', ''],
            ['store_name', ''],
        ]);
        $data = $services->getList($where);
        return app('json')->success($this->service->storeBargain($data));
    }

    /**
     * Xuất mua chung
     * @param StoreCombinationServices $services
     * @return mixed
     */
    public function storeCombination(StoreCombinationServices $services)
    {
        $where = $this->request->getMore([
            ['start_status', ''],
            ['is_show', ''],
            ['store_name', ''],
        ]);
        $data = $services->getList($where);
        /** @var StorePinkServices $storePinkServices */
        $storePinkServices = app()->make(StorePinkServices::class);
        $countAll = $storePinkServices->getPinkCount([]);
        $countTeam = $storePinkServices->getPinkCount(['k_id' => 0, 'status' => 2]);
        $countPeople = $storePinkServices->getPinkCount(['k_id' => 0]);
        foreach ($data as &$item) {
            $item['count_people'] = $countPeople[$item['id']] ?? 0;//Số lượng mua chung
            $item['count_people_all'] = $countAll[$item['id']] ?? 0;//Số người tham gia
            $item['count_people_pink'] = $countTeam[$item['id']] ?? 0;//Số nhóm thành công
            $item['stop_status'] = $item['stop_time'] < time() ? 1 : 0;
            if ($item['is_show']) {
                if ($item['start_time'] > time())
                    $item['start_name'] = 'Chưa bắt đầu';
                else if ($item['stop_time'] < time())
                    $item['start_name'] = 'Đã kết thúc';
                else if ($item['stop_time'] > time() && $item['start_time'] < time()) {
                    $item['start_name'] = 'Đang diễn ra';
                }
            } else $item['start_name'] = 'Đã kết thúc';
        }
        return app('json')->success($this->service->storeCombination($data));
    }

    /**
     * Xuất flash sale
     * @param StoreSeckillServices $services
     * @return mixed
     */
    public function storeSeckill(StoreSeckillServices $services)
    {
        $where = $this->request->getMore([
            ['start_status', ''],
            ['status', ''],
            ['store_name', '']
        ]);
        $data = $services->getList($where);
        return app('json')->success($this->service->storeSeckill($data));
    }

    /**
     * Xuất sản phẩm
     * @param StoreProductServices $services
     * @return mixed
     */
    public function storeProduct(StoreProductServices $services)
    {
        $where = $this->request->getMore([
            ['store_name', ''],
            ['cate_id', ''],
            ['type', 1]
        ]);
        $data = $services->searchList($where, true, false);
        return app('json')->success($this->service->storeProduct($data['list'] ?? []));
    }

    /**
     * Xuất danh sách đơn hàng
     * @param StoreOrderServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function storeOrder(StoreOrderServices $services)
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['real_name', ''],
            ['data', '', '', 'time']
        ]);
        $where['pid'] = 0;
        $ids = $this->request->get('ids');
        if ($ids) {
            $idsArr = array_filter(explode(',', $ids));
            if ($idsArr) {
                $where['id'] = $idsArr;
            }
        }
        $data = $services->getExportList($where);
        return app('json')->success($this->service->storeOrder($data));
    }

    /**
     * Lấy điểm nhận hàng
     * @param SystemStoreServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function storeMerchant(SystemStoreServices $services)
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['type', 'd'], 0],
        ]);
        $data = $services->getExportData($where);
        return app('json')->success($this->service->storeMerchant($data));
    }

    /**
     * Xuất thẻ thành viên
     * @param int $id
     * @param MemberCardServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function memberCard(int $id, MemberCardServices $services)
    {
        $data = $services->getExportData(['batch_card_id' => $id]);
        return app('json')->success($this->service->memberCard($data));
    }
}
