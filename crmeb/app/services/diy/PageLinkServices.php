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

namespace app\services\diy;

use app\services\BaseServices;
use app\dao\diy\PageLinkDao;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;


/**
 *
 * Class DiyServices
 * @package app\services\diy
 */
class PageLinkServices extends BaseServices
{

    /**
     * PageLinkServices constructor.
     * @param PageLinkDao $dao
     */
    public function __construct(PageLinkDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy liên kết trang
     * @param array $where
     * @return array
     */
    public function getLinkList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, '*', $page, $limit);
        $count = $this->dao->count($where);
        foreach ($list as &$item) {
            $item['h5_url'] = sys_config('site_url') . $item['url'];
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        return compact('list', 'count');
    }

    /**
     * Xóa
     * @param int $id
     */
    public function del(int $id)
    {
        $res = $this->dao->delete($id);
        if (!$res) throw new AdminException('Xóa thất bại');
    }

    public function getLinkSave($id, $data)
    {
        unset($data['id']);
        if ($id) {
            $res = $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $data['status'] = 1;
            $res = $this->dao->save($data);
        }
        if (!$res) {
            throw new AdminException('Lưu thất bại');
        } else {
            return true;
        }
    }
}
