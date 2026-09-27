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
namespace app\api\controller\v1\publics;

use app\services\article\ArticleServices;

/**
 * Lớp bài viết
 * Class ArticleController
 * @package app\api\controller\publics
 */
class ArticleController
{
    protected $services;

    public function __construct(ArticleServices $services)
    {
        $this->services = $services;
    }

    /**
     * Danh sách bài viết
     * @param $cid
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst($cid)
    {
        if ($cid == 0) {
            $where = ['is_hot' => 1];
        } else {
            $where = ['cid' => $cid];
        }
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->getList($where, $page, $limit)['list'];
        foreach ($list as &$item){
            $item['add_time'] = date('Y-m-d H:i', $item['add_time']);
        }
        return app('json')->success($list);
    }

    /**
     * Chi tiết bài viết
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function details($id)
    {
        $info = $this->services->getInfo($id);
        return app('json')->success($info);
    }

    /**
     * Lấy bài viết phổ biến
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hot()
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->getList(['is_hot' => 1], $page, $limit)['list'];
        foreach ($list as &$item){
            $item['add_time'] = date('Y-m-d H:i', $item['add_time']);
        }
        return app('json')->success($list);
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function new()
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->getList([], $page, $limit)['list'];
        foreach ($list as &$item){
            $item['add_time'] = date('Y-m-d H:i', $item['add_time']);
        }
        return app('json')->success($list);
    }

    /**
     * Lấy bài viết banner đầu trang
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function banner()
    {
        [$page, $limit] = $this->services->getPageValue();
        $list = $this->services->getList(['is_banner' => 1], $page, $limit)['list'];
        foreach ($list as &$item){
            $item['add_time'] = date('Y-m-d H:i', $item['add_time']);
        }
        return app('json')->success($list);
    }
}
