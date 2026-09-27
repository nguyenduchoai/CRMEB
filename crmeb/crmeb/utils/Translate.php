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
namespace crmeb\utils;

use crmeb\exceptions\ApiException;
use Volc\Base\V4Curl;

/**
 * Dịch máy
 */
class Translate extends V4Curl
{
    protected $apiList = [
        "LangDetect" => [
            "url" => "/",
            "method" => "post",
            "config" => [
                "query" => [
                    "Action" => "LangDetect",
                    "Version" => "2020-06-01",
                ],
            ],
        ],
        "TranslateText" => [
            "url" => "/",
            "method" => "post",
            "config" => [
                "query" => [
                    "Action" => "TranslateText",
                    "Version" => "2020-06-01",
                ],
            ],
        ],
    ];

    protected function getConfig(string $region)
    {
        return [
            "host" => "https://open.volcengineapi.com",
            "config" => [
                "timeout" => 5.0,
                "headers" => [
                    "Accept" => "application/json"
                ],
                "v4_credentials" => [
                    "region" => "cn-north-1",
                    "service" => "translate",
                ],
            ],
        ];
    }

    public function langDetect(array $textList): array
    {
        $req = array('TextList' => $textList);
        try {
            $resp = $this->request('LangDetect', ['json' => $req]);
        } catch (\Throwable $e) {
            throw $e;
        }
        if ($resp->getStatusCode() != 200) {
            throw new ApiException("failed to detect language: status_code=%d, resp=%s", $resp->getStatusCode(), $resp->getBody());
        }
        return json_decode($resp->getBody()->getContents(), true)["DetectedLanguageList"];
    }

    public function translateText(string $sourceLanguage, string $targetLanguage, array $textList): array
    {
        $req = array('SourceLanguage' => $sourceLanguage, 'TargetLanguage' => $targetLanguage, 'TextList' => $textList);
        try {
            $resp = $this->request('TranslateText', ['json' => $req]);
        } catch (\Throwable $e) {
            throw $e;
        }
        if ($resp->getStatusCode() != 200) {
            throw new ApiException("failed to translate: status_code=%d, resp=%s", $resp->getStatusCode(), $resp->getBody());
        }
        $result = json_decode($resp->getBody()->getContents(), true);
        if (isset($result["TranslationList"])) {
            return $result["TranslationList"];
        } else {
            throw new ApiException("failed to translate: " . $result["ResponseMetadata"]['Error']['Message']);
        }
    }
}
