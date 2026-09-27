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
namespace app\adminapi\controller\v1\product;

use app\adminapi\controller\AuthController;
use app\services\product\product\StoreProductParamServices;
use think\facade\App;

/**
 * Thông số sản phẩm
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2024/12/17
 */
class StoreProductParam extends AuthController
{
    /**
     * @param App $app
     * @param StoreProductParamServices $services
     */
    public function __construct(App $app, StoreProductParamServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Lấy danh sách tham số
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function getParamList()
    {
        $where = $this->request->getMore([
            ['name', '']
        ]);
        return app('json')->success($this->services->getParamList($where));
    }

    /**
     * Lấy chi tiết tham số
     * @param $id
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function getParamInfo($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $info = $this->services->getParamInfo($id);
        return app('json')->success($info);
    }

    /**
     * Lấy giá trị tham số
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function getParamValue($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $info = $this->services->getParamValue($id);
        return app('json')->success($info);
    }

    /**
     * Lưu tham số
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function saveParamData($id)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['value', []],
            ['sort', 0],
            ['status', 1]
        ]);
        if (!$data['name']) return app('json')->fail('Vui lòng nhập tên thông số');
        if (!count($data['value'])) return app('json')->fail('Vui lòng nhập giá trị thông số');
        $this->services->saveParamData($id, $data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Sửa trạng thái tham số
     * @param $id
     * @param $status
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function setParamStatus($id, $status)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->setParamStatus($id, $status);
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa thông số
     * @param $id
     * @return \think\Response
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/12/17
     */
    public function delParamData($id)
    {
        if (!$id) return app('json')->fail('Tham số không hợp lệ');
        $this->services->delParamData($id);
        return app('json')->success('Xóa thành công');
    }
}
