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
/**
 * @author: Wu Xi
 * @email: 442384644@qq.com
 * @date: 2023/8/3
 */

namespace app\adminapi\controller\v1\kefu;

use app\adminapi\controller\AuthController;
use app\services\wechat\WechatReplyServices;

class StoreServiceAutoReply extends AuthController
{
    /**
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplyList()
    {
        $where = $this->request->getMore([
            ['key', ''],
            ['type', ''],
        ]);
        $where['key_type'] = 1;
        $list = app()->make(WechatReplyServices::class)->getKeyAll($where);
        return app('json')->success($list);
    }

    /**
     * Lấy form trả lời tự động
     * @param int $id
     * @return \think\Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplyForm($id = 0)
    {
        return app('json')->success(app()->make(WechatReplyServices::class)->autoReplyForm($id));
    }

    /**
     * Lưu trả lời tự động
     * @param int $id
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplySave($id = 0)
    {
        $data = $this->request->postMore([
            ['keys', ''],
            ['type', ''],
            ['data', ''],
            ['status', 1],
        ]);
        app()->make(WechatReplyServices::class)->autoReplySave($id, $data);
        return app('json')->success('Lưu thành công');
    }

    /**
     * Xóa trả lời tự động
     * @param $id
     * @return \think\Response
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/3
     */
    public function autoReplyDel($id)
    {
        app()->make(WechatReplyServices::class)->autoReplyDel($id);
        return app('json')->success('Xóa thành công');
    }
}