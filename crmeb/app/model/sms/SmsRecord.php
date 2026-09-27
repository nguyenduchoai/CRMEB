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

namespace app\model\sms;

use crmeb\basic\BaseModel;
use crmeb\traits\ModelTrait;
use think\Model;

/**
 *  Model lịch sử SMS
 * Class SmsRecord
 * @package app\model\sms
 */
class SmsRecord extends BaseModel
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
    protected $name = 'sms_record';

    /**
     * Trạng thái SMS
     * @var array
     */
    protected $resultcode = ['100' => 'Thành công', '130' => 'Thất bại', '131' => 'Số không tồn tại', '132' => 'Thuê bao tạm khóa', '133' => 'Tắt máy', '134' => 'Không có trạng thái'];

    /**
     * Getter thời gian
     * @param $value
     * @return false|string
     */
    protected function getAddTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * Getter mã trạng thái
     * @param $value
     * @return mixed|string
     */
    protected function getResultcodeAttr($value)
    {
        return $this->resultcode[$value] ?? 'Không có trạng thái';
    }

    /**
     * Bộ lọc số điện thoại
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchPhoneAttr($query, $value)
    {
        $query->where('phone', $value);
    }

    /**
     * Bộ lọc trạng thái SMS
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchResultcodeAttr($query, $value)
    {
        $query->where('resultcode', $value);
    }

    /**
     * Bộ lọc uid
     * @param Model $query
     * @param $value
     */
    public function searchUidAttr($query, $value)
    {
        if ($value) {
            $query->where('uid', $value);
        }
    }

    /**
     * ip
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchAddIpAttr($query, $value)
    {
        $query->where('add_ip', $value);
    }

    /**
     * resultcode
     * @param Model $query
     * @param $value
     * @param $data
     */
    public function searchTypeAttr($query, $value)
    {
        if ($value !== '') {
            if (is_array($value)) {
                $query->whereIn('resultcode', $value)->when(in_array('134', $value), function ($query) {
                    $query->whereOr('resultcode', NULL);
                });
            } else {
                $query->where('resultcode', $value)->when($value == 134, function ($query) {
                    $query->whereOr('resultcode', NULL);
                });
            }
        }
    }
}
