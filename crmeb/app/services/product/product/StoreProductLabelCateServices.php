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
namespace app\services\product\product;

use app\dao\product\product\StoreProductLabelCateDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

class StoreProductLabelCateServices extends BaseServices
{
    public function __construct(StoreProductLabelCateDao $dao)
    {
        $this->dao = $dao;
    }

    public function getLabelCateList(array $where = [])
    {
        $list = $this->dao->getLabelCateList($where, 'id,name,sort,add_time');
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        return $list;
    }

    public function labelCateForm($id = 0)
    {
        $info = $id ? $this->dao->get($id) : [];
        $f[] = Form::input('name', 'Tên danh mục', $info['name'] ?? '')->maxlength(8)->required();
        $f[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($info['sort'] ?? 0))->min(0)->precision(0);
        return create_form($id ? 'Sửa danh mục' : 'Thêm danh mục', $f, Url::buildUrl('/product/label_cate/save/' . $id), 'POST');
    }

    public function labelCateSave($id, $data)
    {
        if ($id) {
            $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->dao->save($data);
        }
        return true;
    }

    public function labelCateDel($id)
    {
        $count = app()->make(StoreProductLabelServices::class)->getCount(['cate_id' => $id, 'is_del' => 0]);
        if($count) throw new AdminException('Danh mục này đang có nhãn, không thể xóa');
        $this->dao->update($id, ['is_del' => 1]);
        return true;
    }
}
