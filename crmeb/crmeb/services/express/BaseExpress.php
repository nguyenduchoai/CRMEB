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

namespace crmeb\services\express;

use crmeb\basic\BaseStorage;
use crmeb\services\AccessTokenServeService;

/**
 * Tra cứu vận chuyển
 * Class BaseExpress
 * @package crmeb\basic
 */
abstract class BaseExpress extends BaseStorage
{

    /**
     * access_token
     * @var null
     */
    protected $accessToken = NULL;


    public function __construct(string $name, AccessTokenServeService $accessTokenServeService, string $configFile)
    {
        parent::__construct($name, [], $configFile);
        $this->accessToken = $accessTokenServeService;
    }

    /**
     * Khởi tạo
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config = [])
    {
//        parent::initialize($config);
    }


    /**
     * Mở dịch vụ
     * @return mixed
     */
    abstract public function open();

    /**Theo dõi vận chuyển
     * @return mixed
     */
    abstract public function query(string $num, string $com = '');

    /**Vận đơn điện tử
     * @return mixed
     */
    abstract public function dump($data);

    /**Đơn vị vận chuyển
     * @return mixed
     */
    //abstract public function express($type, $page, $limit);

    /**Mẫu vận đơn
     * @return mixed
     */
    abstract public function temp(string $com);
}
