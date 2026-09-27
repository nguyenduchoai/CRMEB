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

namespace app\services\product\product;


use app\dao\product\product\StoreCategoryDao;
use app\services\BaseServices;
use app\services\diy\DiyServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\FormBuilder as Form;
use crmeb\utils\Arr;
use think\facade\Route as Url;

/**
 * Class StoreCategoryService
 * @package app\services\product\product
 * @method cateIdByPid(array $cateId) Lấy id cấp trên theo id danh mục
 * @method byIndexList(int $limit, ?string $field) Lấy id cấp trên theo id danh mục
 * @method getCateParentAndChildName(string $cateIds) Lấy tập hợp gồm danh mục cấp 1 và cấp 2
 * @method value(array $where, string $field) Lấy giá trị một trường
 * @method getColumn(array $where, string $field, string $key = '') Lấy mảng của một trường
 */
class StoreCategoryServices extends BaseServices
{
    public function __construct(StoreCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách danh mục
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($where)
    {
        $list = $this->dao->getTierList($where);
        if (!empty($list) && ($where['cate_name'] !== '' || $where['pid'] !== '')) {
            $pids = Arr::getUniqueKey($list, 'pid');
            $parentList = $this->dao->getTierList(['id' => $pids]);
            $list = array_merge($list, $parentList);
            foreach ($list as $key => $item) {
                $arr = $item;
                unset($list[$key]);
                if (!in_array($arr, $list)) {
                    $list[] = $arr;
                }
            }
        }
        foreach ($list as &$value) {
            if ($value['pid'] == 0) {
                $value['url'] = '/pages/goods/goods_list/index?cid=' . $value['id'] . '&title=' . $value['cate_name'];
            } else {
                $value['url'] = '/pages/goods/goods_list/index?sid=' . $value['id'] . '&title=' . $value['cate_name'];
            }
        }
        $list = get_tree_children($list);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Dropdown tìm kiếm danh mục sản phẩm
     * @param string $show
     * @param string $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTierList($show = '', $type = 0)
    {
        $where = [];
        if ($show !== '') $where['is_show'] = $show;
        if (!$type) $where['pid'] = 0;
        return sort_list_tier($this->dao->getTierList($where));
    }

    /**
     * Lấy cascader danh mục
     * @param string $show
     * @param int $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cascaderList($show = '', $type = 0)
    {
        $where = [];
        if ($show !== '') $where['is_show'] = $show;
        if (!$type) $where['pid'] = 0;
        $data = get_tree_children($this->dao->getTierList($where, ['id', 'id as value', 'cate_name as label', 'cate_name as title', 'pid']), 'children', 'id');
//        foreach ($data as &$item) {
//            if (!isset($item['children'])) {
//                $item['disabled'] = true;
//            }
//        }
        return $data;
    }

    /**
     * Đặt trạng thái danh mục
     * @param int $id
     * @param int $is_show
     */
    public function setShow(int $id, int $is_show)
    {
        $res = $this->dao->update($id, ['is_show' => $is_show]);
        $res = $res && $this->dao->update($id, ['is_show' => $is_show], 'pid');
        CacheService::clear();
        if (!$res) {
            throw new AdminException(100005);
        }
    }

    /**
     * Tạo form thêm mới
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm()
    {
        return create_form('Thêm danh mục', $this->form(), Url::buildUrl('/product/category'), 'POST');
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
        return create_form('Sửa danh mục', $this->form($info), $this->url('/product/category/' . $id), 'PUT');
    }

    /**
     * Tạo tham số form
     * @param array $info
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function form($info = [])
    {
        if (isset($info['pid'])) {
            $f[] = Form::select('pid', 'Danh mục cha', (int)($info['pid'] ?? ''))->setOptions($this->menus($info['pid']))->filterable(1);
        } else {
            $f[] = Form::select('pid', 'Danh mục cha', (int)($info['pid'] ?? ''))->setOptions($this->menus())->filterable(1);
        }
        $f[] = Form::input('cate_name', 'Tên danh mục', $info['cate_name'] ?? '')->maxlength(8)->required();
        $f[] = Form::frameImage('pic', 'Biểu tượng danh mục (180*180)', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'pic')), $info['pic'] ?? '')->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $f[] = Form::frameImage('big_pic', 'Ảnh lớn danh mục (468*340)', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'big_pic')), $info['big_pic'] ?? '')->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $f[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($info['sort'] ?? 0))->min(0)->precision(0);
        $f[] = Form::radio('is_show', 'Trạng thái', $info['is_show'] ?? 1)->options([['label' => 'Hiện', 'value' => 1], ['label' => 'Ẩn', 'value' => 0]]);
        return $f;
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
//        if ($pid != '') $menus = [];
        foreach ($list as $menu) {
            $menus[] = ['value' => $menu['id'], 'label' => $menu['cate_name']];
        }
        return $menus;
    }

    /**
     * Lưu dữ liệu thêm mới
     * @param $data
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createData($data)
    {
        if (!$data['cate_name']) {
            throw new AdminException(400100);
        }

        if ($this->dao->getOne(['cate_name' => $data['cate_name'], 'pid' => $data['pid']])) {
            throw new AdminException(400101);
        }

        $parent = $this->dao->getOne(['id' => $data['pid']]);
        if ($data['pid'] && (!$parent || $parent['pid'] > 0)) {
            throw new AdminException(400740);
        }

        $data['add_time'] = time();
        $res = $this->dao->save($data);
        if (!$res) throw new AdminException(100006);

        CacheService::clear();

        return (int)$res->id;
    }

    /**
     * Lưu dữ liệu sửa
     * @param $id
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/23
     */
    public function editData($id, $data)
    {
        if (!$data['cate_name']) {
            throw new AdminException(400100);
        }

        $parent = $this->dao->getOne(['id' => $data['pid']]);
        if ($parent && $parent['pid'] > 0) {
            throw new AdminException(400740);
        }

        $cate = $this->dao->getOne(['cate_name' => $data['cate_name'], 'pid' => $data['pid']]);
        if ($cate && $cate['id'] != $id) {
            throw new AdminException(400101);
        }
        $this->transaction(function () use ($id, $data) {
            $res = $this->dao->update($id, $data);
            /** @var StoreProductCateServices $productCate */
            $productCate = app()->make(StoreProductCateServices::class);
            $res = $res && $productCate->update(['cate_id' => $id], ['cate_pid' => $data['pid']]);
            if (!$res) throw new AdminException(100007);
        });

        CacheService::clear();
    }

    /**
     * Xóa dữ liệu
     * @param int $id
     */
    public function del(int $id)
    {
        if ($this->dao->count(['pid' => $id])) {
            throw new AdminException(400102);
        }
        $res = $this->dao->delete($id);
        if (!$res) throw new AdminException(100008);

        CacheService::clear();
    }

    /**
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/2/8
     */
    public function getCategoryVersion()
    {
        return CacheService::remember('category_version', function () {
            return [
                'is_diy' => app()->make(DiyServices::class)->value(['status' => 1], 'is_diy'),
                'version' => uniqid()
            ];
        });
    }

    /**
     * Lấy danh mục theo id chỉ định, trả về dạng mảng
     * @param string $cateIds
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/23
     */
    public function getCateArray(string $cateIds)
    {
        return $this->dao->getCateArray($cateIds);
    }

    /**
     * Danh sách danh mục ở phía người dùng
     * @param array $where
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/02/23
     */
    public function getCategory(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        if ($limit) {
            return $this->dao->getALlByIndex($where, 'id,cate_name,pid,pic', $limit);
        } else {
            return CacheService::remember('CATEGORY', function () {
                return $this->dao->getCategory();
            });
        }
    }

    /**
     * Chi tiết danh mục
     * @param int $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo(int $id)
    {
        $info = $this->dao->get($id, ['id', 'cate_name', 'pid', 'pic', 'big_pic', 'sort', 'is_show']);
        if ($info) {
            $info = $info->toArray();
        }
        return $info;
    }

    /**
     * Danh sách danh mục
     * @return mixed
     */
    public function getCategoryList(array $where)
    {
        return CacheService::remember('CATEGORY_LIST', function () use ($where) {
            return $this->dao->getALlByIndex($where, 'id, cate_name, pid, pic, big_pic, sort, is_show, add_time');
        }, 86400);
    }

    /**
     * @param string $cate_name_one
     * @param string $cate_name_two
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/7/16
     */
    public function getCateId(string $cate_name_one = '', string $cate_name_two = '')
    {
        if ($cate_name_one != '') {
            $cate_id_one = $this->dao->value(['cate_name' => $cate_name_one], 'id');
            if (!$cate_id_one) {
                $cate_id_one = $this->dao->save(['cate_name' => $cate_name_one, 'add_time' => time()])['id'];
            }
            if ($cate_name_two != '') {
                $cate_id_two = $this->dao->value(['cate_name' => $cate_name_two, 'pid' => $cate_id_one], 'id');
                if (!$cate_id_two) {
                    $cate_id_two = $this->dao->save(['cate_name' => $cate_name_two, 'pid' => $cate_id_one, 'add_time' => time()])['id'];
                }
                return [$cate_id_one, $cate_id_two];
            }
            return [$cate_id_one];
        }
        return [];
    }
}
