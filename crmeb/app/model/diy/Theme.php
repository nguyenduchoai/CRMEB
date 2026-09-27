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
namespace app\model\diy;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;

/**
 * Chủ đề tùy chỉnh
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2025/12/18
 */
class Theme extends BaseModel
{
    use ModelTrait;

    protected $pk = 'id';

    protected $name = 'theme';
}
