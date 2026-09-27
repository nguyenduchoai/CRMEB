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

namespace crmeb\basic;


/**
 * Class BaseStorage
 * @package crmeb\basic
 */
abstract class BaseStorage
{

    /**
     * Tên driver
     * @var string
     */
    protected $name;

    /**
     * Tên file cấu hình driver
     * @var string
     */
    protected $configFile;

    /**
     * Thông tin lỗi
     * @var string
     */
    protected $error;

    /**
     * BaseStorage constructor.
     * @param string $name Tên driver
     * @param string $configFile Tên cấu hình driver
     * @param array $config Cấu hình khác
     */
    public function __construct(string $name, array $config = [], string $configFile = null)
    {
        $this->name = $name;
        $this->configFile = $configFile;
        $this->initialize($config);
    }


    /**
     * Đặt thông tin lỗi
     * @param string|null $error
     * @return bool
     */
    protected function setError(?string $error = null)
    {
        $this->error = $error ?: 'Lỗi không xác định';
        return false;
    }

    /**
     * Lấy thông tin lỗi
     * @return string
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = null;
        return $error;
    }

    /**
     * Khởi tạo
     * @param array $config
     * @return mixed
     */
    abstract protected function initialize(array $config);

}
