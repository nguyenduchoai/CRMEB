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

namespace app\services\user;

use app\services\BaseServices;
use app\dao\user\UserLabelDao;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 *
 * Class UserLabelServices
 * @package app\services\user
 *  * @method getColumn(array $where, string $field, string $key = '') Lấy mảng của một trường (field)
 */
class UserLabelServices extends BaseServices
{

    /**
     * UserLabelServices constructor.
     * @param UserLabelDao $dao
     */
    public function __construct(UserLabelDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy một nhãn
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLable($id)
    {
        return $this->dao->get($id);
    }

    /**
     * Lấy tất cả nhãn người dùng
     * @param array $where
     * @param array|string[] $field
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelList(array $where = [], array $field = ['*'])
    {
        return $this->dao->getList(0, 0, $where, $field);
    }

    /**
     * Lấy danh sách
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($page, $limit, $where);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Form thêm/sửa nhãn
     * @param int $id
     * @param int $cateId
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function add(int $id, int $cateId)
    {
        $label = $this->getLable($id);
        $field = array();
        /** @var UserLabelCateServices $service */
        $service = app()->make(UserLabelCateServices::class);
        $options[] = ['value' => 0, 'label' => 'Tất cả'];
        foreach ($service->getLabelCateAll() as $item) {
            $options[] = ['value' => $item['id'], 'label' => $item['name']];
        }
        if (!$label) {
            $title = 'Thêm nhãn';
            $field[] = Form::select('label_cate', 'Danh mục nhãn', $cateId)->setOptions($options);
            $field[] = Form::input('label_name', 'Tên nhãn', '')->required();
        } else {
            $title = 'Sửa nhãn';
            $field[] = Form::select('label_cate', 'Danh mục', (int)$label->getData('label_cate'))->setOptions($options);
            $field[] = Form::hidden('id', $label->getData('id'));
            $field[] = Form::input('label_name', 'Tên nhãn', $label->getData('label_name'))->required('Vui lòng nhập tên nhãn');
        }
        return create_form($title, $field, Url::buildUrl('/user/user_label/save'), 'POST');
    }

    /**
     * Lưu dữ liệu form nhãn
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save(int $id, array $data)
    {
        if (!$data['label_cate']) {
            throw new AdminException('Vui lòng chọn danh mục nhãn');
        }
        $levelName = $this->dao->getOne(['label_name' => $data['label_name'], 'label_cate' => $data['label_cate']]);
        if ($id) {
            if (!$this->getLable($id)) {
                throw new AdminException('Dữ liệu không tồn tại');
            }
            if ($levelName && $id != $levelName['id']) {
                throw new AdminException('Nhãn này đã tồn tại');
            }
            if ($this->dao->update($id, $data)) {
                return true;
            } else {
                throw new AdminException('Sửa thất bại');
            }
        } else {
            unset($data['id']);
            if ($levelName) {
                throw new AdminException('Nhãn này đã tồn tại');
            }
            if ($this->dao->save($data)) {
                return true;
            } else {
                throw new AdminException('Thêm thất bại');
            }
        }
    }

    /**
     * Xóa
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delLabel(int $id)
    {
        if ($this->getLable($id)) {
            if (!$this->dao->delete($id)) {
                throw new AdminException('Xóa thất bại');
            }
        }
        return true;
    }

    /**
     * Xử lý tree - dữ liệu danh mục, nhãn
     * @param array $cate
     * @param array $label
     * @return array
     */
    public function get_tree_children(array $cate, array $label)
    {
        if ($cate) {
            foreach ($cate as $key => $value) {
                if ($label) {
                    foreach ($label as $k => $item) {
                        if ($value['id'] == $item['label_cate']) {
                            $cate[$key]['children'][] = $item;
                            unset($label[$k]);
                        }
                    }
                } else {
                    $cate[$key]['children'] = [];
                }
            }
        }
        return $cate;
    }
}
