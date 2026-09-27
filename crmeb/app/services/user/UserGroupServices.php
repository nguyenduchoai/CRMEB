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
use app\dao\user\UserGroupDao;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 *
 * Class UserGroupServices
 * @package app\services\user
 */
class UserGroupServices extends BaseServices
{

    /**
     * UserGroupServices constructor.
     * @param UserGroupDao $dao
     */
    public function __construct(UserGroupDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy một nhóm
     * @param int $id
     * @return array|\think\Model|null
     */
    public function getGroup(int $id)
    {
        return $this->dao->get($id);
    }

    /**
     * Lấy danh sách nhóm
     * @param string $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupList(string $field = 'id,group_name', bool $is_page = false): array
    {
        $page = $limit = 0;
        if ($is_page) {
            [$page, $limit] = $this->getPageValue();
            $count = $this->dao->count([]);
        }
        $list = $this->dao->getList([], $field, $page, $limit);

        return $is_page ? compact('list', 'count') : $list;
    }

    /**
     * Lấy tên nhóm của một số người dùng
     * @param array $ids
     * @return array
     */
    public function getUsersGroupName(array $ids)
    {
        return $this->dao->getColumn([['id', 'IN', $ids]], 'group_name', 'id');
    }

    /**
     * Trang thêm/sửa nhóm
     * @param int $id
     * @return string
     */
    public function add(int $id)
    {
        $group = $this->getGroup($id);
        $field = array();
        if (!$group) {
            $title = 'Thêm nhóm';
            $field[] = Form::input('group_name', 'Tên nhóm', '')->required();
        } else {
            $title = 'Sửa nhóm';
            $field[] = Form::hidden('id', $id);
            $field[] = Form::input('group_name', 'Tên nhóm', $group->getData('group_name'))->required();
        }
        return create_form($title, $field, Url::buildUrl('/user/user_group/save'), 'POST');
    }

    /**
     * Thêm|Sửa
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function save(int $id, array $data)
    {
        $groupName = $this->dao->getOne(['group_name' => $data['group_name']]);
        if ($id) {
            if (!$this->getGroup($id)) {
                throw new AdminException(100026);
            }
            if ($groupName && $id != $groupName['id']) {
                throw new AdminException(400666);
            }
            if ($this->dao->update($id, $data)) {
                return true;
            } else {
                throw new AdminException(100007);
            }
        } else {
            unset($data['id']);
            if ($groupName) {
                throw new AdminException(400666);
            }
            if ($this->dao->save($data)) {
                return true;
            } else {
                throw new AdminException(100022);
            }
        }
    }

    /**
     * Xóa
     * @param int $id
     * @return string
     */
    public function delGroup(int $id)
    {
        if ($this->getGroup($id)) {
            if (!$this->dao->delete($id)) {
                throw new AdminException(100008);
            }
        }
        return 'Xóa thành công!';
    }
}
