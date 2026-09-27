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
return [
    // Bộ nhớ đệm request toàn cục
    // \think\middleware\CheckRequestCache::class,
    // Nạp đa ngôn ngữ
    // \think\middleware\LoadLangPack::class,
    // Khởi tạo Session
    \think\middleware\SessionInit::class,
    //Khởi tạo đa ngôn ngữ
    \think\middleware\LoadLangPack::class,
    // Debug Trace trang
    // \think\middleware\TraceDebug::class,
    //Khởi tạo middleware cơ bản
    \app\http\middleware\BaseMiddleware::class,
    // Hỗ trợ đa ngôn ngữ
    \think\middleware\LoadLangPack::class,
];
