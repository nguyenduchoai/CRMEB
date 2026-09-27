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

use app\dao\product\product\StoreProductProtectionDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

class StoreProductProtectionServices extends BaseServices
{
    public function __construct(StoreProductProtectionDao $dao)
    {
        $this->dao = $dao;
    }

    public function protectionList(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->protectionList($where, 'id,title,image,num,sort,add_time,status', $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = $this->dao->protectionCount($where);
        return compact('list', 'count');
    }

    public function protectionInfo($id)
    {
        $info = $this->dao->get(['id' => $id]);
        if (!$info) throw new AdminException('Dữ liệu không tồn tại');
        return $info->toArray();
    }

    public function protectionForm($id)
    {
        $info = $id ? $this->dao->get($id) : [];
        $f[] = Form::input('title', 'Tên đảm bảo', $info['title'] ?? '')->maxlength(8)->required();
        $f[] = Form::textarea('content', 'Nội dung đảm bảo', $info['content'] ?? '')->required();
        $f[] = Form::frameImage('image', 'Biểu tượng', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'image')),$info['image'] ?? '')->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $f[] = Form::number('sort', 'Thứ tự sắp xếp', (int)($info['sort'] ?? 0))->min(0)->precision(0);
        $f[] = Form::radio('status', 'Hiển thị', (int)($info['status'] ?? 1))->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return create_form($id ? 'Sửa đảm bảo' : 'Thêm đảm bảo', $f, Url::buildUrl('/product/protection/save/' . $id), 'POST');
    }

    public function protectionSave($id, $data)
    {
        if ($id) {
            $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->dao->save($data);
        }
        return true;
    }

    public function protectionStatus($id, $status)
    {
        $this->dao->update($id, ['status' => $status]);
        return true;
    }

    public function protectionDel($id)
    {
        $this->dao->update($id, ['is_del' => 1]);
        return true;
    }
}
