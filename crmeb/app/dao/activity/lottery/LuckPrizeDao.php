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

namespace app\dao\activity\lottery;

use app\dao\BaseDao;
use app\model\activity\lottery\LuckPrize;

/**
 *
 * Class LuckPrizeDao
 * @package app\dao\activity\lottery
 */
class LuckPrizeDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return LuckPrize::class;
    }

    /**
     * Lấy tất cả giải thưởng của một hoạt động
     * @param int $lottery_id
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPrizeList(int $lottery_id, string $field = '*')
    {
        $where = ['is_del' => 0, 'status' => 1];
        return $this->search($where + ['lottery_id' => $lottery_id])->field($field)->order('sort desc,id desc')->select()->toArray();
    }
}
