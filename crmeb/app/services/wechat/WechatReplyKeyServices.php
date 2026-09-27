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

namespace app\services\wechat;

use app\services\BaseServices;
use app\dao\wechat\WechatReplyKeyDao;

/**
 *
 * Class UserWechatuserServices
 * @package app\services\user
 */
class WechatReplyKeyServices extends BaseServices
{

    /**
     * Phương thức khởi tạo
     * WechatReplyKeyServices constructor.
     * @param WechatReplyKeyDao $dao
     */
    public function __construct(WechatReplyKeyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function getReplyKeyAll(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getReplyKeyList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }
}
