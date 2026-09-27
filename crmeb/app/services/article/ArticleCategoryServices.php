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

namespace app\services\article;

use app\dao\article\ArticleCategoryDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use crmeb\utils\Arr;
use think\facade\Route as Url;

/**
 * Class ArticleCategoryServices
 * @package app\services\article
 * @method getArticleCategory()
 * @method getArticleTwoCategory()
 */
class ArticleCategoryServices extends BaseServices
{
    /**
     * ArticleCategoryServices constructor.
     * @param ArticleCategoryDao $dao
     */
    public function __construct(ArticleCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách danh mục bài viết
     * @param array $where
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where)
    {
        $list = $this->dao->getList($where);
        $list = get_tree_children($list);
        return compact('list');
    }

    /**
     * Tạo form thêm sửa
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm(int $id)
    {
        $method = 'POST';
        $url = '/cms/category';
        if ($id) {
            $info = $this->dao->get($id);
            $method = 'PUT';
            $url = $url . '/' . $id;
            $pid = $info['pid'];
        } else {
            $pid = '';
        }
        $f = array();
        $f[] = Form::hidden('id', $info['id'] ?? 0);
        $f[] = Form::select('pid', 'Danh mục cha', (int)($info['pid'] ?? ''))->setOptions($this->menus($pid))->filterable(1);
        $f[] = Form::input('title', 'Tên danh mục', $info['title'] ?? '')->maxlength(20)->required();
        $f[] = Form::input('intr', 'Mô tả danh mục', $info['intr'] ?? '')->type('textarea')->required();
        $f[] = Form::frameImage('image', 'Ảnh danh mục', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'image')), $info['image'] ?? '')->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $f[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($info['sort'] ?? 0))->precision(0);
        $f[] = Form::radio('status', 'Trạng thái', $info['status'] ?? 1)->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return create_form($id ? 'Sửa danh mục' :'Thêm danh mục', $f, Url::buildUrl($url), $method);
    }

    /**
     * Lưu
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        return $this->dao->save($data);
    }

    /**
     * Sửa
     * @param array $data
     * @return mixed
     */
    public function update(array $data)
    {
        return $this->dao->update($data['id'], $data);
    }

    /**
     * Xóa
     * @param int $id
     * @return mixed
     */
    public function del(int $id)
    {
        /** @var ArticleServices $articleService */
        $articleService = app()->make(ArticleServices::class);
        $pidCount = $this->dao->count(['pid' => $id]);
        if ($pidCount > 0) throw new AdminException('Danh mục này có danh mục con, không thể xóa');
        $count = $articleService->count(['cid' => $id]);
        if ($count > 0) {
            throw new AdminException('Danh mục này có bài viết, không thể xóa');
        } else {
            return $this->dao->delete($id);
        }
    }

    /**
     * Sửa trạng thái
     * @param int $id
     * @param int $status
     * @return mixed
     */
    public function setStatus(int $id, int $status)
    {
        return $this->dao->update($id, ['status' => $status]);
    }

    /**
     * Lấy dữ liệu tổ hợp danh mục cấp 1
     * @param string $pid
     * @return array[]
     */
    public function menus($pid = '')
    {
        $list = $this->dao->getMenus(['pid' => 0]);
        $menus = [['value' => 0, 'label' => 'Danh mục cấp cao nhất']];
        if ($pid === 0) return $menus;
        if ($pid != '') $menus = [];
        foreach ($list as $menu) {
            $menus[] = ['value' => $menu['id'], 'label' => $menu['title']];
        }
        return $menus;
    }

    /**
     * Danh sách dạng cây
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/9/7
     */
    public function getTreeList()
    {
        return get_tree_children($this->dao->getTreeList(['is_del' => 0, 'status' => 1, 'hidden' => 0], ['id', 'id as value', 'title as label', 'title', 'pid']), 'children', 'id');
//        return sort_list_tier($this->dao->getMenus([]));
    }
}
