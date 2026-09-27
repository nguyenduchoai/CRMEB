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

namespace app\services\user;


use app\dao\other\CategoryDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder;
use think\Model;

/**
 * Class UserLabelCateServices
 * @package app\services\user
 * @method delete($id, ?string $key = null) Xóa
 * @method update($id, array $data, ?string $key = null) Cập nhật dữ liệu
 * @method save(array $data) Lưu dữ liệu
 * @method array|Model|null get($id, ?array $field = [], ?array $with = []) Lấy một dòng dữ liệu
 * @method getAll(array $with = []) Lấy tất cả danh mục nhãn
 */
class UserLabelCateServices extends BaseServices
{
    /**
     * Cache danh mục nhãn
     * @var string
     */
    protected $cacheName = 'label_list_all';

    /**
     * UserLabelCateServices constructor.
     * @param CategoryDao $dao
     */
    public function __construct(CategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh mục nhãn
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLabelList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getCateList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Xóa cache danh mục
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteCateCache()
    {
        return CacheService::delete($this->cacheName);
    }

    /**
     * Lấy tất cả danh mục nhãn
     * @return bool|mixed|null
     */
    public function getLabelCateAll()
    {
        return CacheService::remember($this->cacheName, function () {
            return $this->dao->getCateList(['type' => 0]);
        });
    }

    /**
     * Biểu mẫu danh mục nhãn
     * @param array $cataData
     * @return mixed
     */
    public function labelCateForm(array $cataData = [])
    {
        $f[] = FormBuilder::input('name', 'Tên danh mục', $cataData['name'] ?? '')->required();
        $f[] = FormBuilder::number('sort', 'Thứ tự sắp xếp', (int)($cataData['sort'] ?? 0));
        return $f;
    }

    /**
     * Tạo form
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm()
    {
        return create_form('Thêm danh mục nhãn', $this->labelCateForm(), $this->url('/user/user_label_cate'), 'POST');
    }

    /**
     * Biểu mẫu sửa danh mục nhãn
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateForm(int $id)
    {
        $labelCate = $this->dao->get($id);
        if (!$labelCate) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        return create_form('Sửa danh mục nhãn', $this->labelCateForm($labelCate->toArray()), $this->url('user/user_label_cate/' . $id), 'PUT');
    }

    /**
     * Danh sách nhãn người dùng
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserLabel(int $uid)
    {
        $list = $this->dao->getAll(['type' => 0], ['label']);
        /** @var UserLabelRelationServices $services */
        $services = app()->make(UserLabelRelationServices::class);
        $labelIds = $services->getUserLabels($uid) ?? [];
        foreach ($list as $key => &$item) {
            if (is_array($item['label'])) {
                if (!$item['label']) {
                    unset($list[$key]);
                    continue;
                }
                foreach ($item['label'] as &$value) {
                    if (in_array($value['id'], $labelIds)) {
                        $value['disabled'] = true;
                    } else {
                        $value['disabled'] = false;
                    }
                }
            }

        }
        return array_merge($list);
    }
}
