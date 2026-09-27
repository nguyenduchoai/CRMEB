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

namespace app\dao\wechat;

use app\dao\BaseDao;
use app\model\wechat\WechatNewsCategory;

/**
 *
 * Class UserWechatUserDao
 * @package app\dao\user
 */
class WechatNewsCategoryDao extends BaseDao
{
    /**
     * @return string
     */
    protected function setModel(): string
    {
        return WechatNewsCategory::class;
    }

    /**Danh mục tin tức $model
     * @param array $where
     * @return \crmeb\basic\BaseModel
     */
    public function getNewCtae(array $where)
    {
        return parent::getModel()->when(isset($where['cate_name']), function ($query) use ($where) {
            $query->where('cate_name', 'LIKE', "%$where[cate_name]%");
        })->where('status', 1)->order('add_time desc');
    }


}
