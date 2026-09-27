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
namespace crmeb\services\easywechat\open3rd;


/**
 * Class ProgramWechatLive
 * @package crmeb\services\wechatlive
 */
class ProgramOpen3rd
{
    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * Mã ủy quyền trước (pre-auth code)
     */
    const PRE_AUTH_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';

    /**
     * Lấy thông tin cơ bản tài khoản của bên ủy quyền
     */
    const GET_AUTHORIZER_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info';
    /**
     * Lấy danh sách người trải nghiệm
     */
    const MEMBER_AUTH_LIST = 'https://api.weixin.qq.com/wxa/memberauth';
    /**
     * Gắn người trải nghiệm (tester)
     */
    const BIND_MEMBER_AUTH = 'https://api.weixin.qq.com/wxa/bind_tester';
    /**
     * Bỏ gắn người trải nghiệm (tester)
     */
    const UNBIND_MEMBER_AUTH = 'https://api.weixin.qq.com/wxa/unbind_tester';
    /**
     * Lấy danh sách bản nháp code
     */
    const DRAFT_LIST = 'https://api.weixin.qq.com/wxa/gettemplatedraftlist';
    /**
     * Thêm bản nháp vào thư viện mẫu code
     */
    const ADD_TO_TEMPLATE = 'https://api.weixin.qq.com/wxa/addtotemplate';
    /**
     * Lấy danh sách mẫu code
     */
    const TEMPLATE_LIST = 'https://api.weixin.qq.com/wxa/gettemplatelist';
    /**
     * Xóa mẫu code chỉ định
     */
    const DEL_TEMPLATE = 'https://api.weixin.qq.com/wxa/deletetemplate';
    /**
     * Tải lên code
     */
    const COMMIT = 'https://api.weixin.qq.com/wxa/commit';
    /**
     * Lấy danh sách trang code đã tải lên
     */
    const GET_PAGE = 'https://api.weixin.qq.com/wxa/get_page';
    /**
     * Lấy mã QR trải nghiệm
     */
    const GET_QRCODE = 'https://api.weixin.qq.com/wxa/get_qrcode';
    /**
     * Gửi code để duyệt
     */
    const SUBMIT_AUDIT = 'https://api.weixin.qq.com/wxa/submit_audit';
    /**
     * Truy vấn trạng thái duyệt của phiên bản chỉ định
     */
    const GET_AUDIT_STATUS = 'https://api.weixin.qq.com/wxa/get_auditstatus';
    /**
     * Truy vấn trạng thái duyệt của lần gửi gần nhất
     */
    const GET_LATEST_AUDIT_STATUS = 'https://api.weixin.qq.com/wxa/get_latest_auditstatus';
    /**
     * Rút lại yêu cầu duyệt
     */
    const UNDO_CODE_AUDIT = 'https://api.weixin.qq.com/wxa/undocodeaudit';
    /**
     * Phát hành Mini Program đã được duyệt
     */
    const RELEASE = 'https://api.weixin.qq.com/wxa/release';
    /**
     * Phát hành theo giai đoạn
     */
    const GRAY_RELEASE = 'https://api.weixin.qq.com/wxa/grayrelease';
    /**
     * Quay lại phiên bản cũ (rollback)
     */
    const REVERT_CODE_RELEASE = 'https://api.weixin.qq.com/wxa/revertcoderelease';

    /**
     * ProgramOpen3rd constructor.
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->config = $accessToken->getConfig();
    }


    /**
     * Lấy mã ủy quyền trước (pre-auth code)
     * @return array|bool|mixed
     */
    public function getPreAuthCode()
    {
        return $this->accessToken->httpRequest(self::PRE_AUTH_CODE, [], false);
    }

    /**
     * Yêu cầu ủy quyền
     * @param $authorization_code
     * @return authorizer_appid
     */
    public function getAuth($authorization_code)
    {
        return $this->accessToken->getAuthorizationInfo($authorization_code);
    }

    /**
     * Lấy thông tin cơ bản tài khoản của bên ủy quyền
     * @param $authorizer_appid
     * @return array|bool|mixed
     */
    public function getAuthorizerinfo(string $authorizer_appid)
    {
        return $this->accessToken->httpRequest(self::GET_AUTHORIZER_INFO, ['authorizer_appid' => $authorizer_appid], false);
    }

    /**
     * Lấy danh sách người trải nghiệm được ủy quyền
     * @return array|bool|mixed
     */
    public function getMemberAuthList()
    {
        return $this->accessToken->httpRequest(self::MEMBER_AUTH_LIST, ['action' => 'get_experiencer']);
    }

    /**
     * Gắn người trải nghiệm (tester)
     * @param string $wechatid
     * @return array|bool|mixed
     */
    public function bindMemberAuth(string $wechatid)
    {
        return $this->accessToken->httpRequest(self::BIND_MEMBER_AUTH, ['wechatid' => $wechatid]);
    }

    /**
     * Bỏ gắn người trải nghiệm (tester)
     * @param string $wechatid
     * @param string $userstr
     * @return array|bool|mixed
     */
    public function unBindMemberAuth(string $wechatid, string $userstr = '')
    {
        $data = ['wechatid' => $wechatid];
        if ($userstr) $data['userstr'] = $userstr;
        return $this->accessToken->httpRequest(self::UNBIND_MEMBER_AUTH, $data);
    }

    /**
     * Lấy danh sách bản nháp
     * @return array|bool|mixed
     */
    public function getDraftList()
    {
        return $this->accessToken->httpRequest(self::DRAFT_LIST);
    }

    /**
     * Thêm bản nháp vào mẫu code
     * @param $draft_id
     * @return array|bool|mixed
     */
    public function addToTemplate($draft_id)
    {
        return $this->accessToken->httpRequest(self::ADD_TO_TEMPLATE, ['draft_id' => $draft_id]);
    }

    /**
     * Lấy danh sách mẫu code
     * @return array|bool|mixed
     */
    public function getTemplateList()
    {
        return $this->accessToken->httpRequest(self::TEMPLATE_LIST);
    }

    /**
     * Xóa mẫu chỉ định
     * @param $template_id
     * @return array|bool|mixed
     */
    public function delTemplate($template_id)
    {
        return $this->accessToken->httpRequest(self::DEL_TEMPLATE, ['template_id' => $template_id]);
    }

    /**
     * Tải lên code
     * @param $template_id
     * @param string $ext_json
     * @param string $user_version
     * @param string $user_desc
     * @return array|bool|mixed
     */
    public function commit($template_id, string $ext_json, string $user_version, string $user_desc = '')
    {
        return $this->accessToken->httpRequest(self::COMMIT, ['template_id' => $template_id, 'ext_json' => $ext_json, 'user_version' => $user_version, 'user_desc' => $user_desc]);
    }

    /**
     * Lấy danh sách code đã tải lên
     * @return array|bool|mixed
     */
    public function getPage()
    {
        return $this->accessToken->httpRequest(self::GET_PAGE, []);
    }

    /**
     * Lấy mã QR trải nghiệm
     * @return array|bool|mixed
     */
    public function getQrcode($path = '')
    {
        return $this->accessToken->httpRequest(self::GET_QRCODE, ['path' => $path], true, 'GET');
    }

    /**
     * Gửi duyệt
     * @param array $data
     * @param data = [
     * 'item_list' => [],//danh sách mục duyệt (không bắt buộc, điền tối đa 5 mục)
     * 'preview_info' => (object)[],//thông tin xem trước (ảnh chụp trang Mini Program và video quay lại thao tác)
     * 'version_desc' => '',//mô tả phiên bản và giải thích tính năng Mini Program
     * 'feedback_info' => '',//nội dung phản hồi, tối đa 200 ký tự
     * 'feedback_stuff' => '',//danh sách media_id phân tách bằng |, tối đa 5 ảnh, có thể tải lên qua API thêm tư liệu tạm thời để lấy được
     * 'ugc_declare' => (object)[],//tuyên bố an toàn thông tin cho tình huống nội dung do người dùng tạo (UGC)
     * ];
     * @return array|bool|mixed
     */
    public function submitAudit($data = [])
    {
        $base = [
            'item_list' => [],
            'preview_info' => (object)[],
            'version_desc' => '',
            'feedback_info' => '',
            'feedback_stuff' => '',
            'ugc_declare' => (object)[],
        ];
        $data = array_merge($base, $data);
        return $this->accessToken->httpRequest(self::SUBMIT_AUDIT, $data);
    }

    /**
     * Truy vấn trạng thái duyệt của phiên bản chỉ định
     * @param string $auditid
     * @return array|bool|mixed
     */
    public function getAuditStatus(string $auditid)
    {
        return $this->accessToken->httpRequest(self::GET_AUDIT_STATUS, ['auditid' => $auditid]);
    }

    /**
     * Lấy trạng thái duyệt của lần gửi cuối cùng
     * @return array|bool|mixed
     */
    public function getLatestAuditStatus()
    {
        return $this->accessToken->httpRequest(self::GET_LATEST_AUDIT_STATUS, [], true, 'GET');
    }

    /**
     * Rút lại yêu cầu duyệt
     * @return array|bool|mixed
     */
    public function undoAudit()
    {
        return $this->accessToken->httpRequest(self::UNDO_CODE_AUDIT, [], true, 'GET');
    }

    /**
     * Phát hành Mini Program đã được duyệt
     * @return array|bool|mixed
     */
    public function release()
    {
        return $this->accessToken->httpRequest(self::RELEASE);
    }

    /**
     * Phát hành theo giai đoạn
     * @param int $gray_percentage Số nguyên từ 1-100
     * @return mixed
     */
    public function grayRelease(int $gray_percentage)
    {
        return $this->accessToken->httpRequest(self::GRAY_RELEASE, ['gray_percentage' => $gray_percentage]);
    }

    /**
     * Quay lại phiên bản cũ (rollback)
     * @return array|bool|mixed
     */
    public function revertCodeRelease()
    {
        return $this->accessToken->httpRequest(self::REVERT_CODE_RELEASE, [], true, 'GET');
    }
}