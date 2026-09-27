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

namespace app\services\diy;

use app\services\BaseServices;
use app\dao\diy\PageCategoryDao;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;


/**
 * Class PageCategoryServices
 * @package app\services\diy
 */
class PageCategoryServices extends BaseServices
{

    protected $tree_page_category_key = 'tree_page_categroy';

    /**
     * PageCategoryServices constructor.
     * @param PageCategoryDao $dao
     */
    public function __construct(PageCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách danh mục
     * @return bool|mixed|null
     */
    public function getCategroyList()
    {
//        return CacheService::remember($this->tree_page_category_key, function () {
        return $this->getSonCategoryList();
//        }, 86400);
    }

    /**
     * Danh sách danh mục dạng cây (tree)
     * @param int $pid
     * @param string $parent_name
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSonCategoryList($pid = 0)
    {
        $list = $this->dao->getList(['pid' => $pid], 'id,pid,type,name');
        $arr = [];
        if ($list) {
            foreach ($list as $item) {
                $item['title'] = $item['name'];
                $item['expand'] = true;
                $item['children'] = $this->getSonCategoryList($item['id']);
                $arr[] = $item;
            }
        }
        return $arr;
    }

    public function getLinkCategoryForm($cate_id = 0, $pid = 1)
    {
        $info = $this->dao->get($cate_id);
        $list = $this->dao->getList(['pid' => 1]);
        $data = [['value' => 1, 'label' => 'Danh mục cấp cao nhất']];
        foreach ($list as $menu) {
            $data[] = ['value' => $menu['id'], 'label' => $menu['name']];
        }
        $pid = isset($info['pid']) ? $info['pid'] : $pid;
        $f[] = Form::hidden('id', $cate_id);
        $f[] = Form::select('pid', 'Danh mục cha', (int)$pid)->setOptions($data)->filterable(true);
        $f[] = Form::input('name', 'Tên danh mục', $info['name'] ?? '')->required();
        $f[] = Form::input('type', 'Loại danh mục', $info['type'] ?? '')->required();
        $f[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($info['sort'] ?? 0))->min(0)->precision(0);
        $f[] = Form::radio('status', 'Trạng thái', $info['status'] ?? 1)->options([['label' => 'Hiện', 'value' => 1], ['label' => 'Ẩn', 'value' => 0]]);
        return create_form($cate_id ? 'Sửa danh mục' : 'Thêm danh mục', $f, Url::buildUrl('/diy/link/category/save/' . $cate_id), 'POST');
    }

    public function getLinkCategorySave($cate_id, $data)
    {
        if ($cate_id) {
            $res = $this->dao->update($cate_id, $data);
        } else {
            $data['add_time'] = time();
            $res = $this->dao->save($data);
        }
        if (!$res) {
            throw new AdminException('Lưu thất bại');
        } else {
            return true;
        }
    }

    public function getLinkCategoryDel($cate_id)
    {
        $res = $this->dao->delete($cate_id);
        if (!$res) {
            throw new AdminException('Xóa thất bại');
        } else {
            return true;
        }
    }
}
