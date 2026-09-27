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

use app\dao\product\product\StoreProductLabelDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

class StoreProductLabelServices extends BaseServices
{
    public function __construct(StoreProductLabelDao $dao)
    {
        $this->dao = $dao;
    }

    public function LabelList(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getLabelList($where, '*', $page, $limit);
        $labelCate = app()->make(StoreProductLabelCateServices::class)->labelCateArr();
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['cate_name'] = $labelCate[$item['cate_id']] ?? '';
        }
        $count = $this->dao->getLabelCount($where);
        return compact('list', 'count');
    }

    public function labelInfo($id)
    {
        $info = $this->dao->get($id);
        if (!$info) throw new AdminException('Dữ liệu không tồn tại');
        return $info->toArray();
    }

    public function labelSave($data)
    {
        $id = $data['id'];
        unset($data['id']);
        if ($id) {
            $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->dao->save($data);
        }
        return true;
    }

    public function labelDel($id)
    {
        $this->dao->update($id, ['is_del' => 1]);
        return true;
    }

    public function labelIsShow($id, $is_show)
    {
        $this->dao->update($id, ['is_show' => $is_show]);
        return true;
    }

    public function labelStatus($id, $status)
    {
        $this->dao->update($id, ['status' => $status]);
        return true;
    }

    public function labelUseList()
    {
        $labelUserList = $this->dao->labelUseList();
        $labelCateList = app()->make(StoreProductLabelCateServices::class)->getLabelCateList(['is_del' => 0, 'status' => 1]);
        $data = [];
        foreach ($labelUserList as $key => $item) {
            $cate_name = '';
            foreach ($labelCateList as $items) {
                if ($key == $items['id']) {
                    $cate_name = $items['name'];
                }
            }
            $data[] = [
                'cate_name' => $cate_name,
                'list' => $item,
            ];
        }
        return $data;
    }
}
