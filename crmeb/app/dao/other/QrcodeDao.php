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

namespace app\dao\other;

use app\dao\BaseDao;
use app\model\other\Qrcode;

/**
 *
 * Class QrcodeDao
 * @package app\dao\other
 */
class QrcodeDao extends BaseDao
{

    /**
     * Thiết lập model
     * @return string
     */
    protected function setModel(): string
    {
        return Qrcode::class;
    }

    /**
     * Lấy một mã QR
     * @param $id
     * @param string $type
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getQrcode($id, $type = 'id')
    {
        return $this->getModel()->where($type, $id)->find();
    }

    /**
     * Cập nhật trạng thái sử dụng mã QR
     * @param $id
     * @param string $type
     * @return mixed
     */
    public function scanQrcode($id, $type = 'id')
    {
        return $this->getModel()->where($type, $id)->inc('scan')->update();
    }
}
