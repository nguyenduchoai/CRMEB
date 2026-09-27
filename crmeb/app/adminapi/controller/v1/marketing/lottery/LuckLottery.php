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

namespace app\adminapi\controller\v1\marketing\lottery;

use app\adminapi\controller\AuthController;
use app\services\activity\lottery\LuckLotteryServices;
use think\facade\App;

/**
 * Hoạt động quay thưởng
 * Class LuckLottery
 * @package app\controller\admin\v1\marketing\lottery
 */
class LuckLottery extends AuthController
{

    /**
     * LuckLottery constructor.
     * @param App $app
     * @param LuckLotteryServices $services
     */
    public function __construct(App $app, LuckLotteryServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách quay thưởng
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['factor', ''],
            ['start', ''],
            ['status', ''],
            ['time', ''],
            ['keyword', ''],
        ]);
        return app('json')->success($this->services->getList($where));
    }

    /**
     * Chi tiết hoạt động quay thưởng
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        return app('json')->success($this->services->getLotteryInfo((int)$id));
    }

    /**
     * Thêm quay thưởng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['desc', ''],
            ['image', ''],
            ['factor', 1],
            ['factor_num', 1],
            ['attends_user', 1],
            ['user_level', []],
            ['user_label', []],
            ['is_svip', 0],
            ['period', [0, 0]],
            ['lottery_num_term', 1],
            ['lottery_num', 1],
            ['spread_num', 1],
            ['is_all_record', 1],
            ['is_personal_record', 1],
            ['is_content', 1],
            ['content', ''],
            ['status', 1],
            ['prize', []]
        ]);
        if (!$data['name']) {
            return app('json')->fail('Vui lòng thêm tên chương trình quay thưởng');
        }
        if ($data['is_content'] && !$data['content']) {
            return app('json')->fail('Vui lòng thêm nội dung như mô tả quay thưởng');
        }
        [$start, $end] = $data['period'];
        unset($data['period']);
        $data['start_time'] = $start ? strtotime($start) : 0;
        $data['end_time'] = $end ? strtotime($end) + 86399 : 0;
        if ($data['start_time'] && $data['end_time'] && $data['end_time'] <= $data['start_time']) {
            return app('json')->fail('Thời gian kết thúc chương trình phải sau thời gian bắt đầu');
        }
        if (!$data['prize']) {
            return app('json')->fail('Vui lòng thêm giải thưởng');
        }
        if (in_array($data['factor'], [1, 2]) && !$data['factor_num']) {
            return app('json')->fail('Vui lòng điền số lượng tiêu hao');
        }
        return app('json')->success($this->services->add($data) ? 'Lưu thành công' : 'Lưu thất bại');
    }

    /**
     * Sửa vòng quay may mắn
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($id)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['desc', ''],
            ['image', ''],
            ['factor', 1],
            ['factor_num', 1],
            ['attends_user', 1],
            ['user_level', []],
            ['user_label', []],
            ['is_svip', 0],
            ['period', [0, 0]],
            ['lottery_num_term', 1],
            ['lottery_num', 1],
            ['spread_num', 1],
            ['is_all_record', 1],
            ['is_personal_record', 1],
            ['is_content', 1],
            ['content', ''],
            ['status', 1],
            ['prize', []]
        ]);
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        if (!$data['name']) {
            return app('json')->fail('Vui lòng thêm tên chương trình quay thưởng');
        }
        [$start, $end] = $data['period'];
        unset($data['period']);
        $data['start_time'] = $start ? strtotime($start) : 0;
        $data['end_time'] = $end ? strtotime($end) + 86399 : 0;
        if ($data['start_time'] && $data['end_time'] && $data['end_time'] <= $data['start_time']) {
            return app('json')->fail('Thời gian kết thúc chương trình phải sau thời gian bắt đầu');
        }
        if ($data['is_content'] && !$data['content']) {
            return app('json')->fail('Vui lòng thêm nội dung như mô tả quay thưởng');
        }
        if (!$data['prize']) {
            return app('json')->fail('Vui lòng thêm giải thưởng');
        }
        if (in_array($data['factor'], [1, 2]) && !$data['factor_num']) {
            return app('json')->fail('Vui lòng điền số lượng tiêu hao');
        }
        return app('json')->success($this->services->edit((int)$id, $data) ? 'Sửa thành công' : 'Sửa thất bại');
    }

    /**
     * Xóa quay thưởng
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        list($id) = $this->request->getMore([
            ['id', 0],
        ], true);
        if (!$id) return app('json')->fail('Dữ liệu không tồn tại');
        $this->services->delLottery((int)$id);
        return app('json')->success('Xóa thành công');
    }

    /**
     * Thiết lập trạng thái hoạt động
     * @param string $id
     * @param string $status
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setStatus($id = '', $status = '')
    {
        if ($status == '' || $id == '') return app('json')->fail('Tham số không hợp lệ');
        $this->services->setStatus((int)$id, (int)$status);
        return app('json')->success('Cài đặt thành công');
    }

    public function factorList()
    {
        return app('json')->success($this->services->factorList());
    }

    public function factorUse()
    {
        $data = $this->request->postMore([
            [['point', 'd'], 0],
            [['pay', 'd'], 0],
            [['evaluate', 'd'], 0],
        ]);
        $this->services->factorUse($data);
        return app('json')->success('Lưu thành công');
    }
}
