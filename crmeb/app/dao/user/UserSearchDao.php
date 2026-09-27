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

namespace app\dao\user;

use app\dao\BaseDao;
use app\model\user\UserSearch;

/**
 * Tìm kiếm của người dùng
 * Class UserSearchDao
 * @package app\dao\user
 */
class UserSearchDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return UserSearch::class;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param string $order
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where = [], string $order = 'id desc', int $page = 0, int $limit = 0): array
    {
        return $this->search($where)->order($order)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->select()->toArray();
    }

    /**
     * * Lấy kết quả tìm kiếm một từ khóa trên toàn hệ thống|của người dùng
     * @param int $uid
     * @param string $keyword Từ khóa
     * @param int $preTime Trong bao lâu thì coi kết quả còn hiệu lực
     * @return array|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getKeywordResult(int $uid, string $keyword, int $preTime = 7200)
    {
        if (!$keyword) return [];
        $where = ['keyword' => $keyword];
        if ($uid) $where['uid'] = $uid;
        return $this->search($where)->when($uid && $preTime == 0, function ($query) {
                $query->where('is_del', 0);
            })->when($preTime > 0, function ($query) use ($preTime) {
                $query->where('add_time', '>', time() - $preTime);
            })->order('add_time desc,id desc')->find() ?? [];
    }
}
