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

namespace app\services\other;


use app\dao\other\AgreementDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Class AgreementServices
 * @package app\services\other
 */
class AgreementServices extends BaseServices
{

    public function __construct(AgreementDao $dao)
    {
        $this->dao = $dao;
    }

    /** Cập nhật nội dung thỏa thuận
     * @param array $where
     * @param $content
     * @return bool|\crmeb\basic\BaseModel
     */
    public function saveAgreement(array $data, $id = 0)
    {
        if (!$data) return false;
        if (!isset($data['type']) || !$data['type'] || $data['type'] == 0) throw new AdminException('Thiếu loại thỏa thuận');
        if (!isset($data['title']) || !$data['title']) throw new AdminException('Vui lòng điền tên thỏa thuận');
        if (!isset($data['content']) || !$data['content']) throw new AdminException('Vui lòng điền nội dung thỏa thuận');
        if (!$id) {
            $getOne = $this->getAgreementBytype($data['type']);
            if ($getOne) throw new AdminException('Thỏa thuận loại này đã tồn tại');
        }
        return $this->dao->saveAgreement($data, $id);
    }

    /**Lấy thỏa thuận thành viên
     * @param $type
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAgreementBytype($type)
    {
        if (!$type) return [];
        $data = $this->dao->getOne(['type' => $type]);
        return $data ? $data->toArray() : [];
    }
}
