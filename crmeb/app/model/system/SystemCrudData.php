<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2022 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace app\model\system;


use crmeb\basic\BaseModel;

/**
 * Class SystemCrudData
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/7/28
 * @package app\model\system
 */
class SystemCrudData extends BaseModel
{
    /**
     * @var string
     */
    protected $name = 'system_crud_data';

    /**
     * @var string
     */
    protected $pk = 'id';

//    public function getValueAttr($value)
//    {
//        return json_decode($value, true);
//    }

    /**
     * @param $query
     * @param $value
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/10
     */
    public function searchNameAttr($query, $value)
    {
        if ($value != '') {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }

    /**
     * @param $query
     * @param $value
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/20
     */
    public function searchPidAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('pid', $value);
        }
    }
}
