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

namespace app\services\kefu\service;


use app\dao\service\StoreServiceAuxiliaryDao;
use app\services\BaseServices;

/**
 * Class StoreServiceAuxiliaryServices
 * @package app\services\kefu\service
 */
class StoreServiceAuxiliaryServices extends BaseServices
{
    /**
     * StoreServiceAuxiliaryServices constructor.
     * @param StoreServiceAuxiliaryDao $dao
     */
    public function __construct(StoreServiceAuxiliaryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lưu thông tin chuyển tiếp
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveAuxliary(array $data)
    {
        $auxliaryInfo = $this->dao->get(['type' => 0, 'binding_id' => $data['binding_id'], 'relation_id' => $data['relation_id']]);
        if ($auxliaryInfo) {
            $auxliaryInfo->update_time = time();
            return $auxliaryInfo->save();
        } else {
            return $this->dao->save([
                'type' => 0,
                'binding_id' => $data['binding_id'],
                'relation_id' => $data['relation_id'],
                'update_time' => time(),
                'add_time' => time(),
            ]);
        }
    }

}
