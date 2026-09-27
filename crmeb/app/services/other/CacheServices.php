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

namespace app\services\other;


use app\dao\other\CacheDao;
use app\services\BaseServices;

/**
 * Bộ nhớ đệm bảng cơ sở dữ liệu
 * Class CacheServices
 * @package app\services\other
 * @method delectDeOverdueDbCache() Xóa bộ nhớ đệm đã hết hạn
 */
class CacheServices extends BaseServices
{

    public function __construct(CacheDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy dữ liệu bộ nhớ đệm
     * @param string $key
     * @param $default Nếu giá trị mặc định không tồn tại thì ghi vào
     * @param int $expire
     * @return mixed|null
     */
    public function getDbCache(string $key, $default, int $expire = 0)
    {
        $this->delectDeOverdueDbCache();
        $result = $this->dao->value(['key' => $key], 'result');
        if ($result) {
            return json_decode($result, true);
        } else {
            if ($default instanceof \Closure) {
                // Lấy dữ liệu bộ nhớ đệm
                $value = $default();
                if ($value) {
                    $this->setDbCache($key, $value, $expire);
                    return $value;
                }
            } else {
                $this->setDbCache($key, $default, $expire);
                return $default;
            }
            return null;
        }
    }

    /**
     * Đặt bộ nhớ đệm dữ liệu, nếu tồn tại thì cập nhật, không có thì ghi vào
     * @param string $key
     * @param string | array $result
     * @param int $expire
     * @return void
     */
    public function setDbCache(string $key, $result, $expire = 0)
    {
        $this->delectDeOverdueDbCache();
        $addTime = $expire ? time() + $expire : 0;
        if ($this->dao->count(['key' => $key])) {
            return $this->dao->update($key, [
                'result' => json_encode($result),
                'expire_time' => $addTime,
                'add_time' => time()
            ], 'key');
        } else {
            return $this->dao->save([
                'key' => $key,
                'result' => json_encode($result),
                'expire_time' => $addTime,
                'add_time' => time()
            ]);
        }
    }


    /**
     * Xóa một bộ nhớ đệm
     * @param string $key
     * @return false|mixed
     */
    public function delectDbCache(string $key = '')
    {
        if ($key)
            return $this->dao->delete($key, 'key');
        else
            return false;
    }

    /**
     * Kiểm tra cache có tồn tại không
     * @param string $key
     * @param $result
     * @return bool
     * @throws \ReflectionException
     */
    public function checkDbCache(string $key = '', $result = ''): bool
    {
        // Kiểm tra cache có tồn tại không, nếu $value tồn tại thì kiểm tra giá trị cache có khớp không
        if ($key) {
            if ($result) {
                return $this->dao->count(['key' => $key, 'result' => json_encode($result)]) > 0;
            } else {
                return $this->dao->count(['key' => $key]) > 0;
            }
        } else {
            return false;
        }
    }
}
