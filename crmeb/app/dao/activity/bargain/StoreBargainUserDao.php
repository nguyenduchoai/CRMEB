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

namespace app\dao\activity\bargain;

use app\dao\BaseDao;
use app\model\activity\bargain\StoreBargainUser;

/**
 *
 * Class StoreBargainUserDao
 * @package app\dao\activity
 */
class StoreBargainUserDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreBargainUser::class;
    }

    /**
     * Lấy số lượt giúp giảm giá
     * @param array $where
     * @return array
     */
    public function getAllCount(array $where = [])
    {
        return $this->getModel()->where($where)->group('bargain_id')->column('count(*)', 'bargain_id');
    }

    /**
     * Lấy ID bảng săn giảm giá
     * @param int $bargainId $bargainId Sản phẩm săn giảm giá
     * @param int $bargainUserUid $bargainUserUid  Mã người dùng mở săn giảm giá
     * @param int $status $status  Trạng thái săn giảm giá: 1 đang tham gia, 2 hoạt động kết thúc tham gia thất bại, 3 hoạt động kết thúc tham gia thành công
     * @return mixed
     */
    public function getBargainUserTableId(int $bargainId = 0, int $bargainUserUid = 0)
    {
        return $this->value(['bargain_id' => $bargainId, 'uid' => $bargainUserUid, 'is_del' => 0, 'status' => 1], 'id') ?? 0;
    }

    /**
     * Lấy danh sách săn giảm giá của người dùng
     * @param int $bargainUserUid
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userAll(int $bargainUserUid, int $page, int $limit)
    {
        return $this->search(['uid' => $bargainUserUid, 'is_del' => 0])->with('getBargain')->order('add_time DESC,id DESC')->page($page, $limit)->select()->toArray();
    }

    /**
     * Lấy trạng thái săn giảm giá
     * @param $bargainId
     * @param $uid
     * @return mixed
     */
    public function getBargainUserStatus($bargainId, $uid)
    {
        return $this->search(['bargain_id' => $bargainId, 'uid' => $uid])->order('add_time DESC')->value('status');
    }


    /**
     * Sửa trạng thái săn giảm giá
     * @param int $id
     * @param int $status
     * @return \crmeb\basic\BaseModel
     */
    public function updateBargainStatus(int $id, int $status = 3)
    {
        return $this->getModel()->where('id', $id)->where('status', 1)->update(['status' => $status]);
    }

    /**
     * Danh sách săn giảm giá
     * @param $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bargainUserList($where, $page = 0, $limit = 0)
    {
        return $this->search($where)->with(['getBargain', 'getUser'])
            ->when(isset($where['real_name']) && $where['real_name'] != '', function ($query) use ($where) {
                $query->where('uid', $where['real_name']);
            })->when($page != 0, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->order('add_time desc')->select()->toArray();
    }
}
