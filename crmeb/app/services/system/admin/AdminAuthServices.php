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

namespace app\services\system\admin;


use app\dao\system\admin\AdminAuthDao;
use app\services\BaseServices;
use app\services\other\CacheServices;
use crmeb\exceptions\AuthException;
use crmeb\services\CacheService;
use crmeb\utils\JwtAuth;
use Firebase\JWT\ExpiredException;

/**
 * Service ủy quyền admin
 * Class AdminAuthServices
 * @package app\services\system\admin
 */
class AdminAuthServices extends BaseServices
{
    /**
     * Phương thức khởi tạo
     * AdminAuthServices constructor.
     * @param AdminAuthDao $dao
     */
    public function __construct(AdminAuthDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy thông tin ủy quyền Admin
     * @param string $token
     * @param string $msg
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function parseToken(string $token, string $msg = 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại'): array
    {
        /** @var CacheService $cacheService */
        $cacheService = app()->make(CacheService::class);

        if (!$token || $token === 'undefined') {
            throw new AuthException($msg, [], 401);
        }
        /** @var JwtAuth $jwtAuth */
        $jwtAuth = app()->make(JwtAuth::class);
        //Đặt phân tích token
        [$id, $type, $pwd] = $jwtAuth->parseToken($token);

        //Kiểm tra token có hết hạn không
        $md5Token = md5($token);
        if (!$cacheService->has($md5Token) || !$cacheService->get($md5Token, '', NULL, 'admin')) {
            $this->authFailAfter($id, $type);
            throw new AuthException($msg, [], 401);
        }

        //Xác thực token
        try {
            $jwtAuth->verifyToken();
        } catch (\Throwable $e) {
            if (!request()->isCli()) {
                $cacheService->delete($md5Token);
            }
            $this->authFailAfter($id, $type);
            throw new AuthException($msg, [], 401);
        }

        //Lấy thông tin quản trị viên
        $adminInfo = $this->dao->get($id);
        if (!$adminInfo || !$adminInfo->id) {
            if (!request()->isCli()) {
                $cacheService->delete($md5Token);
            }
            $this->authFailAfter($id, $type);
            throw new AuthException($msg, [], 401);
        }
        if ($pwd !== '' && $pwd !== md5($adminInfo->pwd)) {
            throw new AuthException($msg, [], 401);
        }

        $adminInfo->type = $type;
        return $adminInfo->hidden(['pwd', 'is_del', 'status'])->toArray();
    }

    /**
     * Sự kiện sau khi xác thực token thất bại
     */
    protected function authFailAfter($id, $type)
    {
        try {
            $postData = request()->post();
            $rule = trim(strtolower(request()->rule()->getRule()));
            $method = trim(strtolower(request()->method()));
            //Sự kiện sau khi thoát thêm sản phẩm
            if ($rule === 'product/product/<id>' && $method === 'post') {
                $this->saveProduct($id, $postData);
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Lưu dữ liệu gửi lên
     * @param $adminId
     * @param $postData
     */
    protected function saveProduct($adminId, $postData)
    {
        /** @var CacheServices $cacheService */
        $cacheService = app()->make(CacheServices::class);
        $cacheService->setDbCache($adminId . '_product_data', $postData, 68400);
    }
}
