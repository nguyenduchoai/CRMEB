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

namespace app\services\system\attachment;

use app\services\BaseServices;
use app\dao\system\attachment\SystemAttachmentCategoryDao;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 *
 * Class SystemAttachmentCategoryServices
 * @package app\services\attachment
 * @method get($id) Lấy một dòng dữ liệu
 * @method count($where) Lấy tổng số dữ liệu theo điều kiện
 */
class SystemAttachmentCategoryServices extends BaseServices
{

    /**
     * SystemAttachmentCategoryServices constructor.
     * @param SystemAttachmentCategoryDao $dao
     */
    public function __construct(SystemAttachmentCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách danh mục
     * @param array $where
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAll(array $where)
    {
        $list = $this->dao->getList($where);
        if ($where['all'] == 1) {
            $list = $this->tidyMenuTier($list);
        } else {
            foreach ($list as &$item) {
                $item['title'] = $item['name'];
                if ($where['name'] == '' && $this->dao->count(['pid' => $item['id']])) {
                    $item['loading'] = false;
                    $item['children'] = [];
                }
            }
        }
        return compact('list');
    }

    /**
     * Định dạng danh sách
     * @param $menusList
     * @param int $pid
     * @param array $navList
     * @return array
     */
    public function tidyMenuTier($menusList, $pid = 0, $navList = [])
    {
        foreach ($menusList as $k => $menu) {
            $menu['title'] = $menu['name'];
            if ($menu['pid'] == $pid) {
                unset($menusList[$k]);
                $menu['children'] = $this->tidyMenuTier($menusList, $menu['id']);
                if (count($menu['children'])) {
                    $menu['expand'] = true;
                } else {
                    unset($menu['children']);
                }
                $navList[] = $menu;
            }
        }
        return $navList;
    }

    /**
     * Tạo form thêm mới
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm($pid, $type)
    {
        return create_form('Thêm danh mục', $this->form(['pid' => $pid, 'type' => $type]), Url::buildUrl('/file/category'), 'POST');
    }

    /**
     * Tạo form sửa
     * @param $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function editForm(int $id)
    {
        $info = $this->dao->get($id);
        return create_form('Sửa danh mục', $this->form($info), Url::buildUrl('/file/category/' . $id), 'PUT');
    }

    /**
     * Tạo tham số form
     * @param array $info
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function form($info = [])
    {
        [$pidList, $data] = $this->getPidList((int)($info['pid'] ?? 0));
        return [
            Form::cascader('pid', 'Danh mục cha', $data)->options($pidList)->filterable(true)->props(['props' => ['multiple' => false, 'checkStrictly' => true, 'emitPath' => false]])->style(['width' => '100%']),
            Form::input('name', 'Tên danh mục', $info['name'] ?? '')->maxlength(30),
            Form::hidden('type', $info['type'] ?? 0),
        ];
    }

    /**
     * Lấy danh mục
     * @param $value
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/9/12
     */
    public function getPidList($value)
    {
        $pidList = $this->dao->selectList([], 'id as value, pid, name as label')->toArray();
        if ($value) {
            $data = get_tree_value($pidList, $value);
        } else {
            $data = [0];
        }
        $pidList = get_tree_children($pidList, 'children', 'value');
        array_unshift($pidList, ['value' => 0, 'pid' => 0, 'label' => 'Danh mục cấp cao nhất']);
        return [$pidList, array_reverse($data)];
    }

    /**
     * Lấy danh sách danh mục (thêm/sửa)
     * @param array $where
     * @return mixed
     */
    public function getCateList(array $where)
    {
        $list = $this->dao->getList($where);
        $options = [['value' => 0, 'label' => 'Tất cả danh mục']];
        foreach ($list as $id => $cateName) {
            $options[] = ['label' => $cateName['name'], 'value' => $cateName['id']];
        }
        return $options;
    }

    /**
     * Lưu resource mới tạo
     * @param array $data
     */
    public function save(array $data)
    {
        if ($this->dao->getOne(['name' => $data['name']])) {
            throw new AdminException(400101);
        }
        $res = $this->dao->save($data);
        if (!$res) throw new AdminException(100022);
        return $res;
    }

    /**
     * Lưu tài nguyên đã sửa
     * @param int $id
     * @param array $data
     */
    public function update(int $id, array $data)
    {
        $attachment = $this->dao->getOne(['name' => $data['name']]);
        if ($attachment && $attachment['id'] != $id) {
            throw new AdminException(400101);
        }
        $res = $this->dao->update($id, $data);
        if (!$res) throw new AdminException(100007);
    }

    /**
     * Xóa danh mục
     * @param int $id
     */
    public function del(int $id)
    {
        $count = $this->dao->getCount(['pid' => $id]);
        if ($count) {
            throw new AdminException(400102);
        } else {
            $res = $this->dao->delete($id);
            if (!$res) throw new AdminException(400102);
        }
    }


    /**
     * Lấy một dòng dữ liệu
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOne($where)
    {
        return $this->dao->getOne($where);
    }
}
