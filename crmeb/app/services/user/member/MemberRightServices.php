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

use app\dao\user\MemberRightDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Class MemberRightServices
 * @package app\services\user
 */
class MemberRightServices extends BaseServices
{
    /**
     * MemberCardServices constructor.
     * @param MemberRightDao $memberCardDao
     */
    public function __construct(MemberRightDao $memberRightDao)
    {
        $this->dao = $memberRightDao;
    }

    /**
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSearchList(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getSearchList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['image'] = set_file_url($item['image']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');

    }

    /**
     * Sửa và lưu
     * @param int $id
     * @param array $data
     */
    public function save(int $id, array $data)
    {
        if (!$data['right_type']) throw new AdminException('Thiếu loại quyền lợi thành viên');
        if (!$id) throw new AdminException('Tham số không hợp lệ');
        if (!$data['title'] || !$data['show_title']) throw new AdminException('Vui lòng thiết lập tên quyền lợi');
        if (!$data['image']) throw new AdminException('Vui lòng tải lên biểu tượng quyền lợi thành viên');
        if (mb_strlen($data['show_title']) > 6) throw new AdminException('Tên hiển thị không được quá 6 ký tự');
        if (mb_strlen($data['explain']) > 8) throw new AdminException('Mô tả ngắn quyền lợi không được quá 8 ký tự');
        switch ($data['right_type']) {
            case "integral":
                if (!$data['number']) throw new AdminException('Vui lòng thiết lập hệ số nhân điểm thưởng hoàn lại');
                if ($data['number'] < 0) throw new AdminException('Hệ số nhân điểm thưởng hoàn lại không được là số âm');
                $save['number'] = abs($data['number']);
                break;
            case "express" :
                if (!$data['number']) throw new AdminException('Vui lòng thiết lập chiết khấu phí vận chuyển');
                if ($data['number'] < 0) throw new AdminException('Chiết khấu phí vận chuyển không được là số âm');
                $save['number'] = abs($data['number']);
                break;
            case "sign" :
                if (!$data['number']) throw new AdminException('Vui lòng thiết lập hệ số nhân điểm thưởng điểm danh');
                if ($data['number'] < 0) throw new AdminException('Hệ số nhân điểm thưởng điểm danh không được là số âm');
                $save['number'] = abs($data['number']);
                break;
            case "offline" :
                if (!$data['number']) throw new AdminException('Vui lòng thiết lập chiết khấu thanh toán ngoại tuyến');
                if ($data['number'] < 0) throw new AdminException('Chiết khấu thanh toán ngoại tuyến không được là số âm');
                $save['number'] = abs($data['number']);
        }
        $save['show_title'] = $data['show_title'];
        $save['image'] = $data['image'];
        $save['status'] = $data['status'];
        $save['sort'] = $data['sort'];
        //TODO $save chưa được sử dụng
        return $this->dao->update($id, $data);
    }

    /**
     * Lấy một thông tin
     * @param array $where
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOne(array $where)
    {
        if (!$where) return false;
        return $this->dao->getOne($where);
    }

    /**
     * Kiểm tra một quyền lợi có được mở không
     * @param $rightType
     * @return bool
     */
    public function getMemberRightStatus($rightType)
    {
        if (!$rightType) return false;
        $status = $this->dao->value(['right_type' => $rightType], 'status');
        if ($status) return true;
        return false;
    }

}
