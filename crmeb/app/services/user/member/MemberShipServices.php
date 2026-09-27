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

namespace app\services\user\member;

use app\dao\user\MemberShipDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Class MemberShipServices
 * @package app\services\user
 */
class MemberShipServices extends BaseServices
{

    public function __construct(MemberShipDao $memberShipDao)
    {
        $this->dao = $memberShipDao;
    }

    /**Admin lấy loại thành viên
     * @param array $where
     * @return array
     */
    public function getSearchList(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getSearchList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');

    }

    /**Lấy api interface thẻ thành viên
     * @return mixed
     */
    public function getApiList(array $where)
    {
        return $this->dao->getApiList($where);
    }

    /** Sửa và lưu loại thẻ
     * @param int $id
     * @param array $data
     */
    public function save(int $id, array $data)
    {
        if (!$data['title']) throw new AdminException('Vui lòng điền tên thẻ thành viên');
        if (!$data['type']) throw new AdminException('Thiếu loại thẻ thành viên');
        if ($data['type'] == "ever") {
            $data['vip_day'] = -1;
        } else {
            if (!$data['vip_day']) throw new AdminException('Vui lòng điền thời hạn (ngày)');
            if ($data['vip_day'] < 0) throw new AdminException('Thời hạn (ngày) không được là số âm');
        }
        if ($data['type'] == "free") {
            $data['pre_price'] = 0.00;
        } else {
            if ($data['pre_price'] == 0 || $data['price'] == 0) throw new AdminException('Vui lòng điền giá');
        }
        if ($data['pre_price'] < 0 || $data['price'] < 0) throw new AdminException('Giá không được là số âm');
        if ($data['pre_price'] > $data['price']) throw new AdminException('Giá ưu đãi không được lớn hơn giá gốc');
        if ($id){
            return $this->dao->update($id, $data);
        }else{
            return $this->dao->save($data);
        }

    }

    /**Lấy số ngày thành viên của thẻ
     * @param array $where
     * @return mixed
     */
    public function getVipDay(array $where)
    {
        return $this->dao->value($where, 'vip_day');
    }

    /**
     * Sửa trạng thái loại thành viên
     * @param $id
     * @param $is_del
     * @return bool
     */
    public function setStatus($id, $is_del)
    {
        $res = $this->dao->update($id, ['is_del' => $is_del]);
        if ($res) return true;
        return false;
    }
}
