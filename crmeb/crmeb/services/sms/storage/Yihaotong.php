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

namespace crmeb\services\sms\storage;

use crmeb\services\sms\BaseSms;
use crmeb\exceptions\AdminException;
use think\facade\Config;


/**
 * Class yihaotong
 * @package crmeb\services\sms\storage
 */
class Yihaotong extends BaseSms
{

    /**
     * Kích hoạt
     */
    const SMS_OPEN = 'v2/sms_v2/open';

    /**
     * Sửa chữ ký
     */
    const SMS_MODIFY = 'v2/sms_v2/modify';

    /**
     * Thông tin người dùng
     */
    const SMS_INFO = 'v2/sms_v2/info';

    /**
     * Gửi SMS
     */
    const SMS_SEND = 'v2/sms_v2/send';

    /**
     * Mẫu SMS
     */
    const SMS_TEMPS = 'v2/sms_v2/temps';

    /**
     * Đăng ký mẫu
     */
    const SMS_APPLY = 'v2/sms_v2/apply';

    /**
     * Bản ghi mẫu
     */
    const SMS_APPLYS = 'v2/sms_v2/applys';

    /**
     * Bản ghi gửi
     */
    const SMS_RECORD = 'v2/sms_v2/record';

    /**
     * Lấy trạng thái gửi SMS
     */
    const SMS_STSTUS = 'v2/sms/status';

    /**
     * Chữ ký SMS
     * @var string
     */
    protected $sign = '';

    /** Khởi tạo
     * @param array $config
     */
    protected function initialize(array $config = [])
    {
        parent::initialize($config);
    }


    /**
     * Đặt chữ ký
     * @param $sign
     * @return $this
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * Lấy mã xác thực
     * @param string $phone
     * @return array|mixed
     */
    public function captcha(string $phone)
    {
        $params = [
            'phone' => $phone
        ];
        return $this->accessToken->httpRequest('sms/captcha', $params, 'GET', false);
    }

    /**
     * Mở dịch vụ
     * @return array|bool|mixed
     */
    public function open()
    {
        $param = [
            'sign' => $this->sign
        ];
        return $this->accessToken->httpRequest(self::SMS_OPEN, $param);
    }

    /**
     * Sửa chữ ký
     * @param string $sign
     * @return array|bool|mixed
     */
    public function modify(string $sign = null, string $phone, string $code)
    {
        $param = [
            'sign' => $sign ?: $this->sign,
            'verify_code' => $code,
            'phone' => $phone
        ];
        return $this->accessToken->httpRequest(self::SMS_MODIFY, $param);
    }

    /**
     * Lấy thông tin người dùng
     * @return array|bool|mixed
     */
    public function info()
    {
        return $this->accessToken->httpRequest(self::SMS_INFO, []);
    }

    /**
     * Lấy mẫu SMS
     * @param int $page
     * @param int $limit
     * @param int $type
     * @return array|mixed
     */
    public function temps(int $page = 0, int $limit = 10, int $type = 1)
    {
        $param = [
            'page' => $page,
            'limit' => $limit,
            'temp_type' => $type
        ];
        return $this->accessToken->httpRequest(self::SMS_TEMPS, $param);
    }

    /**
     * Đăng ký mẫu
     * @param $title
     * @param $content
     * @param $type
     * @return array|bool|mixed
     */
    public function apply(string $title, string $content, int $type)
    {
        $param = [
            'title' => $title,
            'content' => $content,
            'type' => $type
        ];
        return $this->accessToken->httpRequest(self::SMS_APPLY, $param);
    }

    /**
     * Bản ghi đăng ký
     * @param $temp_type
     * @param int $page
     * @param int $limit
     * @return array|bool|mixed
     */
    public function applys(int $tempType, int $page, int $limit)
    {
        $param = [
            'temp_type' => $tempType,
            'page' => $page,
            'limit' => $limit
        ];
        return $this->accessToken->httpRequest(self::SMS_APPLYS, $param);
    }

    /**
     * Gửi SMS
     * @param string $phone
     * @param string $templateId
     * @param array $data
     * @return bool|string
     */
    public function send(string $phone, string $templateId, array $data = [])
    {
        if (!$phone) {
            throw new AdminException('Số điện thoại không được để trống');
        }
        $param = [
            'phone' => $phone,
            'host' => request()->host()
        ];
        $param['temp_id'] = $templateId;
        if (is_null($param['temp_id'])) {
            throw new AdminException('ID mẫu không tồn tại');
        }
        $param['param'] = json_encode($data);
        return $this->accessToken->httpRequest(self::SMS_SEND, $param, 'post');
    }

    /**
     * Bản ghi gửi
     * @param $record_id
     * @return array|bool|mixed
     */
    public function record($record_id)
    {
        $param = [
            'record_id' => $record_id
        ];
        return $this->accessToken->httpRequest(self::SMS_RECORD, $param);
    }

    /**
     * Lấy trạng thái gửi
     * @param array $recordIds
     * @return array|mixed
     */
    public function getStatus(array $recordIds)
    {
        $data['record_id'] = json_encode($recordIds);
        return $this->accessToken->httpRequest(self::SMS_STSTUS, $data, 'POST', false);
    }
}
