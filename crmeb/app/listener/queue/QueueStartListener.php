<?php
/**
 *  +----------------------------------------------------------------------
 *  | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
 *  +----------------------------------------------------------------------
 *  | Author: CRMEB Team <admin@crmeb.com>
 *  +----------------------------------------------------------------------
 */

namespace app\listener\queue;


use think\console\Output;

class QueueStartListener
{

    public function handle(Output $output)
    {
        $output->writeln('Hàng đợi tin nhắn đã khởi động và đang chạy, trên windows vui lòng không đóng cửa sổ dòng lệnh. Trên linux vui lòng dùng Supervisor để giám sát tiến trình');
    }
}
