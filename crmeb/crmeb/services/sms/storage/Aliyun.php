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

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use crmeb\exceptions\ApiException;
use crmeb\services\sms\BaseSms;
use Darabonba\OpenApi\Models\Config as AliConfig;
use think\exception\ValidateException;
use think\facade\Config;


/**
 * Class Aliyun
 * @package crmeb\services\sms\storage
 */
class Aliyun extends BaseSms
{
    protected $AccessKeyId = '';
    protected $AccessKeySecret = '';
    protected $SignName = '';

    /**
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config = [])
    {
        parent::initialize($config);
        $this->SignName = $config['aliyun_SignName'] ?? '';
        $this->AccessKeyId = $config['aliyun_AccessKeyId'] ?? '';
        $this->AccessKeySecret = $config['aliyun_AccessKeySecret'] ?? '';
    }

    /**
     * Gửi SMS
     * @param string $phone
     * @param string $templateId
     * @param array $data
     * @return array|bool|mixed
     */
    public function send(string $phone, string $templateId, array $data = [])
    {
        if (empty($phone)) {
            return $this->setError('Số điện thoại không được để trống');
        }

        $config = new AliConfig([
            "accessKeyId" => $this->AccessKeyId,
            "accessKeySecret" => $this->AccessKeySecret
        ]);
        // Domain truy cập
        $config->endpoint = "dysmsapi.aliyuncs.com";
        $client = new Dysmsapi($config);

        if (!$templateId) throw new ApiException('Mẫu không tồn tại:' . $templateId);

        $sendSmsRequest = new SendSmsRequest([
            "phoneNumbers" => $phone,
            "signName" => $this->SignName,
            "templateCode" => $templateId,
            "templateParam" => json_encode($data),
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            // Sao chép code để chạy, vui lòng tự in giá trị trả về của API
            $resp = $client->sendSmsWithOptions($sendSmsRequest, $runtime);
            if (isset($resp) && $resp->body->code !== 'OK') {
                throw new ApiException('[Thông báo lỗi từ Alibaba Cloud]:' . $resp->body->message);
            }
            return [
                'id' => $resp->body->requestId,
                'content' => json_encode($data),
                'template' => $templateId,
            ];
        } catch (\Exception $e) {
            throw new ApiException('[Thông báo lỗi từ Alibaba Cloud]:' . $e->getMessage());
        }
    }

    public function open()
    {
    }

    public function modify(string $sign = null, string $phone, string $code)
    {
    }

    public function info()
    {
    }

    public function temps(int $page = 0, int $limit = 10, int $type = 1)
    {
    }

    public function apply(string $title, string $content, int $type)
    {
    }

    public function applys(int $tempType, int $page, int $limit)
    {
    }

    public function record($record_id)
    {
    }
}
