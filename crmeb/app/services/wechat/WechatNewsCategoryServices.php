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
declare (strict_types=1);

namespace app\services\wechat;

use app\services\BaseServices;
use app\dao\wechat\WechatNewsCategoryDao;
use app\services\article\ArticleServices;

/**
 *
 * Class UserWechatuserServices
 * @package app\services\user
 * @method delete($id, ?string $key = null)  Xóa
 * @method update($id, array $data, ?string $key = null) Cập nhật dữ liệu
 * @method save(array $data) Thêm dữ liệu
 * @method get(int $id, ?array $field = []) Lấy một dòng dữ liệu
 */
class WechatNewsCategoryServices extends BaseServices
{

    /**
     * UserWechatuserServices constructor.
     * @param WechatNewsCategoryDao $dao
     */
    public function __construct(WechatNewsCategoryDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh mục cấu hình
     * @param array $where
     * @return array
     */
    public function getAll($where = array())
    {
        [$page, $limit] = $this->getPageValue();
        $model = $this->dao->getNewCtae($where);
        $count = $model->count();
        $list = $model->page($page, $limit)
            ->select()
            ->each(function ($item) {
                /** @var ArticleServices $services */
                $services = app()->make(ArticleServices::class);
                $new = $services->articleList($item['new_id']);
                if ($new) $new = $new->toArray();
                $item['new'] = $new;
            });
        return compact('count', 'list');
    }

    /**
     * Lấy một bài viết ảnh-văn
     * @param int $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getWechatNewsItem($id = 0)
    {
        if (!$id) return [];
        $list = $this->dao->getOne(['id' => $id, 'status' => 1], 'cate_name as title,new_id');
        if ($list) {
            $list = $list->toArray();
            /** @var ArticleServices $services */
            $services = app()->make(ArticleServices::class);
            $new = $services->articleList($list['new_id']);
            if ($new) $new = $new->toArray();
            $list['new'] = $new;
        }
        return $list;

    }

    /**
     * Gửi tin nhắn CSKH, danh sách bài viết để chọn
     * @param $where
     * @return array
     */
    public function list($where)
    {
        $list = $this->dao->getNewCtae($where)
            ->page((int)$where['page'], (int)$where['limit'])
            ->select()
            ->each(function ($item) {
                /** @var ArticleServices $services */
                $services = app()->make(ArticleServices::class);
                $item['new'] = $services->articleList($item['new_id']);
            });
        return ['list' => $list];
    }

    /**Xử lý tài nguyên bài viết ảnh-văn
     * @param $wechatNews
     * @return bool
     */
    public function wechatPush($wechatNews)
    {
        /** @var WechatReplyServices $services */
        $services = app()->make(WechatReplyServices::class);
        return $services->tidyNews($wechatNews);
    }

    /**Người dùng được gửi
     * @param $user_ids
     * @param $column
     * @param $key
     * @return array
     */
    public function getWechatUser($user_ids, $column, $key)
    {
        /** @var WechatUserServices $services */
        $services = app()->make(WechatUserServices::class);
        return $services->getColumnUser($user_ids, $column, $key);
    }

    /**
     * Lấy ID bài viết
     * @return array
     */
    public function getNewIds()
    {
        return $this->dao->getColumn([], 'new_id');
    }
}
