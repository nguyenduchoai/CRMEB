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

namespace crmeb\services\copyproduct\storage;

use crmeb\services\copyproduct\BaseCopyProduct;


/**
 * Class Copy
 * @package crmeb\services\product\storage
 */
class Copy extends BaseCopyProduct
{

    /**
     * Có kích hoạt không
     */
    const PRODUCT_OPEN = 'v2/copy/open';
    /**
     * Lấy chi tiết
     */
    const PRODUCT_GOODS = 'v2/copy/goods';

    /** Khởi tạo
     * @param array $config
     */
    protected function initialize(array $config = [])
    {
        parent::initialize($config);
    }

    /** Có kích hoạt sao chép không
     * @return mixed
     */
    public function open()
    {
        return $this->accessToken->httpRequest(self::PRODUCT_OPEN, []);
    }

    /** Sao chép sản phẩm
     * @param string $url
     * @param array $options
     * @param string $yihaotongCopyAppid
     * @return mixed
     */
    public function goods(string $url, array $options = [])
    {
        $param['url'] = $url;
        return $this->accessToken->httpRequest(self::PRODUCT_GOODS, $param, 'post');
    }


}
