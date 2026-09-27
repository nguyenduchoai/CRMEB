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

namespace app\model\user;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\model;

/**
 * Class UserExtract
 * @package app\model\user
 */
class UserExtract extends BaseModel
{
    use ModelTrait;

    /**
     * Khóa chính bảng dữ liệu
     * @var string
     */
    protected $pk = 'id';

    /**
     * Tên model
     * @var string
     */
    protected $name = 'user_extract';

    //Đang duyệt
    const AUDIT_STATUS = 0;
    //Không được duyệt
    const FAIL_STATUS = -1;
    //Đã rút tiền
    const SUCCESS_STATUS = 1;

    /**
     * Trạng thái
     * @var string[]
     */
    protected static $status = [
        -1 => 'Không được duyệt',
        0 => 'Đang duyệt',
        1 => 'Đã rút tiền'
    ];

    /**
     * Liên kết user
     * @return model\relation\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    /**
     * uid người dùng
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if (is_array($value))
            $query->whereIn('uid', $value);
        else
            $query->where('uid', $value);
    }

    /**
     * Phương thức rút tiền
     * @param Model $query
     * @param $value
     */
    public function searchExtractTypeAttr($query, $value)
    {
        if ($value != '') $query->where('extract_type', $value);
    }

    /**
     * Trạng thái duyệt
     * @param Model $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value !== '') {
            $query->where('status', $value);
        }
    }

    /**
     * Tìm kiếm gần đúng
     * @param Model $query
     * @param $value
     */
    public function searchLikeAttr($query, $value)
    {
        if ($value) {
            $query->where(function ($query) use ($value) {
                $query->where('real_name|id|bank_code|alipay_code', 'LIKE', "%$value%")->whereOr('uid', 'in', function ($query) use ($value) {
                    $query->name('user')->whereLike('nickname', '%' . $value . '%')->field('uid')->select();
                });
            });
        }
    }

}
