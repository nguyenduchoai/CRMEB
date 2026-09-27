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

namespace app\services\wechat;


use app\dao\wechat\WechatKeyDao;
use app\services\BaseServices;

/**
 * Menu WeChat
 * Class WechatMenuServices
 * @package app\services\wechat
 * @method delete($id, ?string $key = null)  Xóa
 * @method getOne(array $where)  Lấy một dòng dữ liệu
 * @method count(array $where)  Đọc số lượng dữ liệu
 * @method saveAll(array $where)  Thêm dữ liệu
 * @method getColumn($where,$key)  Lấy mảng của một trường
 */
class WechatKeyServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * WechatMenuServices constructor.
     * @param WechatKeyDao $dao
     */
    public function __construct(WechatKeyDao $dao)
    {
        $this->dao = $dao;
    }

}
