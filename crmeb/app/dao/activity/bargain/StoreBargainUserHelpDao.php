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
use app\model\activity\bargain\StoreBargainUserHelp;

/**
 *
 * Class StoreBargainUserHelpDao
 * @package app\dao\activity
 */
class StoreBargainUserHelpDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return StoreBargainUserHelp::class;
    }

    /**
     * Lấy số người giúp giảm giá
     * @return array
     */
    public function getHelpAllCount(array $where = [])
    {
        return $this->getModel()->where($where)->group('bargain_id')->column('count(*)', 'bargain_id');
    }

    /**
     * Lấy danh sách giúp giảm giá
     * @param int $bid
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getHelpList(int $bid, int $page = 0, int $limit = 0)
    {
        return $this->getModel()
            ->where('bargain_user_id', $bid)
            ->order('add_time desc')
            ->when($page, function ($query) use ($page, $limit) {
                $query->page($page, $limit);
            })->column("uid,price,from_unixtime(add_time,'%Y-%m-%d %H:%i:%s') as add_time", 'id');
    }

    /**
     * Lấy số người đã giúp giảm giá cho sản phẩm săn giảm giá
     * @return array
     */
    public function getNums()
    {
        return $this->getModel()->field('count(id) as num,bargain_user_id')->group('bargain_user_id')->select()->toArray();
    }
}
