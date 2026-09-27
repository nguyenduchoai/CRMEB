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

namespace app\services\shipping;


use app\dao\shipping\ShippingTemplatesNoDeliveryCityDao;
use app\services\BaseServices;

/**
 * Tầng xử lý nghiệp vụ liên kết bảng dữ liệu không giao tới và thành phố
 * Class ShippingTemplatesNoDeliveryCityServices
 * @package app\services\shipping
 * @method getUniqidList(array $where, bool $group) Lấy danh sách miễn phí vận chuyển theo điều kiện chỉ định
 */
class ShippingTemplatesNoDeliveryCityServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * ShippingTemplatesNoDeliveryCityServices constructor.
     * @param ShippingTemplatesNoDeliveryCityDao $dao
     */
    public function __construct(ShippingTemplatesNoDeliveryCityDao $dao)
    {
        $this->dao = $dao;
    }
}
