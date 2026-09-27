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

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserLabelRelationDao;
use crmeb\exceptions\AdminException;

/**
 *
 * Class UserLabelRelationServices
 * @package app\services\user
 * @method getColumn(array $where, string $field, string $key = '') Lấy mảng của một trường
 * @method saveAll(array $data) Lưu dữ liệu theo lô
 */
class UserLabelRelationServices extends BaseServices
{

    /**
     * UserLabelRelationServices constructor.
     * @param UserLabelRelationDao $dao
     */
    public function __construct(UserLabelRelationDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy các ID nhãn của một người dùng
     * @param int $uid
     * @return array
     */
    public function getUserLabels(int $uid)
    {
        return $this->dao->getColumn(['uid' => $uid], 'label_id', '');
    }

    /**
     * Người dùng đặt nhãn
     * @param $uids
     * @param array $labels
     * @return bool
     * @throws \Exception
     */
    public function setUserLable($uids, array $labels)
    {
        if (!is_array($uids)) $uids = [$uids];
        $re = $this->dao->delete([['uid', 'in', $uids]]);
        if (!count($labels)) return true;
        if ($re === false) {
            throw new AdminException(400667);
        }
        /** @var UserServices $userServices */
        $userServices = app()->make(UserServices::class);
        $data = [];
        foreach ($uids as $uid) {
            foreach ($labels as $label) {
                $data[] = ['uid' => $uid, 'label_id' => $label];
            }
            $userServices->update(['uid' => $uid], ['label_ids' => implode(',', $labels)]);
        }
        if ($data) {
            if (!$this->dao->saveAll($data))
                throw new AdminException(400668);
        }
        return true;
    }

    /**
     * Bỏ nhãn người dùng
     * @param int $uid
     * @param array $labels
     * @return mixed
     */
    public function unUserLabel(int $uid, array $labels)
    {
        if (!count($labels)) {
            return true;
        }
        $this->dao->delete([
            ['uid', '=', $uid],
            ['label_id', 'in', $labels],
        ]);
        return true;
    }

    /**
     * Lấy nhãn người dùng
     * @param array $uids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserLabelList(array $uids)
    {
        return $this->dao->getLabelList($uids);
    }
}
