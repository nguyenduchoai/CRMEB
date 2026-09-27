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

use app\dao\product\product\StoreProductParamDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * Thông số sản phẩm
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2024/12/17
 */
class StoreProductParamServices extends BaseServices
{
    /**
     * Đặt tầng dao
     * @param StoreProductParamDao $dao
     */
    public function __construct(StoreProductParamDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Danh sách thông số sản phẩm
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function getParamList(array $where = [])
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $list = $this->dao->getParamList($where, 'id,name,sort,add_time,status', $page, $limit);
        foreach ($list as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
        }
        $count = $this->dao->getParamCount($where);
        return compact('list', 'count');
    }

    /**
     * Chi tiết thông số sản phẩm
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function getParamInfo($id)
    {
        $info = $this->dao->get(['id' => $id]);
        if (!$info) throw new AdminException('Dữ liệu không tồn tại');
        $info = $info->toArray();
        $info['value'] = json_decode($info['value'], true);
        return $info;
    }

    /**
     * Lấy giá trị tham số sản phẩm
     * @param $id
     * @return mixed
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function getParamValue($id)
    {
        $value = $this->dao->value(['id' => $id], 'value');
        return json_decode($value, true);
    }

    /**
     * Lưu tham số sản phẩm
     * @param $id
     * @param $data
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function saveParamData($id, $data)
    {
        $data['value'] = json_encode($data['value']);
        if ($id) {
            $this->dao->update($id, $data);
        } else {
            $data['add_time'] = time();
            $this->dao->save($data);
        }
        return true;
    }

    /**
     * Sửa trạng thái tham số sản phẩm
     * @param $id
     * @param $status
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function setParamStatus($id, $status)
    {
        $this->dao->update($id, ['status' => $status]);
        return true;
    }

    /**
     * Xóa tham số sản phẩm
     * @param $id
     * @return bool
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function delParamData($id)
    {
        $this->dao->update($id, ['is_del' => 1]);
        return true;
    }
}
