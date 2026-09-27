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

namespace crmeb\services\printer;

use crmeb\basic\BaseStorage;

/**
 * Class BasePrinter
 * @package crmeb\basic
 */
abstract class BasePrinter extends BaseStorage
{

    /**
     * Handle của token
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * Nội dung in
     * @var string
     */
    protected $printerContent;

    /**
     * Số lần in
     * @var string
     */
    protected $times;

    /**
     * BasePrinter constructor.
     * @param string $name
     * @param AccessToken $accessToken
     * @param string $configFile
     */
    public function __construct(string $name, AccessToken $accessToken, string $configFile)
    {
        parent::__construct($name, [], $configFile);
        $this->accessToken = $accessToken;
    }

    /**
     * Bắt đầu in
     * @return mixed
     */
    abstract public function startPrinter();

    /**
     * Đặt nội dung in
     * @param $content
     * @return mixed
     */
    abstract public function setPrinterContent($content);

}
