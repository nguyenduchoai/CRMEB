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
namespace app\dao\system;

use app\dao\BaseDao;
use app\model\system\SystemPem;

class SystemPemDao extends BaseDao
{
    protected function setModel(): string
    {
        return SystemPem::class;
    }

    public function savePem($data)
    {
        $info = $this->getModel()->where('name', $data['name'])->find();
        if ($info) {
            $info = $info->toArray();
            $this->getModel()->where('id', $info['id'])->update($data);
        } else {
            $data['add_time'] = time();
            $this->getModel()->save($data);
        }
        return true;
    }
}