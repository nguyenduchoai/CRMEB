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

namespace crmeb\services\sms;

use crmeb\basic\BaseStorage;
use crmeb\services\AccessTokenServeService;

/**
 * Class BaseSmss
 * @package crmeb\basic
 */
abstract class BaseSms extends BaseStorage
{

    /**
     * access_token
     * @var null
     */
    protected $accessToken = NULL;

    /**
     * BaseSmss constructor.
     * @param string $name
     * @param AccessTokenServeService $accessTokenServeService
     * @param string $configFile
     */
    public function __construct(string $name, AccessTokenServeService $accessTokenServeService, string $configFile, array $config = [])
    {
        $this->accessToken = $accessTokenServeService;
        $this->name = $name;
        $this->configFile = $configFile;
        $this->initialize($config);
    }

    /**
     * Khởi tạo
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config = [])
    {

    }


    /**
     * Mở dịch vụ
     * @return mixed
     */
    abstract public function open();

    /**Sửa chữ ký
     * @return mixed
     */
    abstract public function modify(string $sign = null, string $phone, string $code);

    /**Thông tin người dùng
     * @return mixed
     */
    abstract public function info();

    /**Gửi SMS
     * @return mixed
     */
    abstract public function send(string $phone, string $templateId, array $data);

    /**
     * Mẫu SMS
     * @param int $page
     * @param int $limit
     * @param int $type
     * @return mixed
     */
    abstract public function temps(int $page, int $limit, int $type);


    /**
     * Đăng ký mẫu
     * @param string $title
     * @param string $content
     * @param int $type
     * @return mixed
     */
    abstract public function apply(string $title, string $content, int $type);

    /**
     * Bản ghi mẫu
     * @param int $tempType
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    abstract public function applys(int $tempType, int $page, int $limit);

    /**Bản ghi gửi
     * @return mixed
     */
    abstract public function record($record_id);
}
