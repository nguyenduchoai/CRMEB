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

namespace app\services\article;

use app\dao\article\ArticleDao;
use app\services\BaseServices;
use app\services\wechat\WechatNewsCategoryServices;
use crmeb\exceptions\AdminException;

/**
 * Class ArticleServices
 * @package app\services\article
 */
class ArticleServices extends BaseServices
{
    /**
     * ArticleServices constructor.
     * @param ArticleDao $dao
     */
    public function __construct(ArticleDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList(array $where, int $page = 0, int $limit = 0)
    {
        if (!$page && !$limit) {
            [$page, $limit] = $this->getPageValue();
        }
        $where['ids'] = app()->make(WechatNewsCategoryServices::class)->getNewIds();
        $list = $this->dao->getList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['store_name'] = $item['storeInfo']['store_name'] ?? '';
            $item['copy_url'] = sys_config('site_url') . '/pages/extension/news_details/index?id=' . $item['id'];
            $item['copy_url_pc'] = sys_config('site_url') . '/news_detail?id=' . $item['id'];
            unset($item['content']);
        }
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Thêm sửa bài viết
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        /** @var ArticleContentServices $articleContentService */
        $articleContentService = app()->make(ArticleContentServices::class);
        $content['content'] = $data['content'];
        $id = $data['id'];
        unset($data['content'], $data['id']);
        $info = $this->transaction(function () use ($id, $data, $articleContentService, $content) {
            if ($id) {
                $info = $this->dao->update($id, $data);
                $content['nid'] = $id;
                $res = $info && $articleContentService->update($id, $content, 'nid');
            } else {
                unset($data['id']);
                $data['add_time'] = time();
                $info = $this->dao->save($data);
                $content['nid'] = $info->id;
                $res = $info && $articleContentService->save($content);
            }
            if (!$res) {
                throw new AdminException(100006);
            } else {
                return $info;
            }
        });
        return $info;
    }

    /**
     * Lấy chi tiết bài viết
     * @param int $id
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read(int $id)
    {
        $info = $this->dao->read($id);
        $info['cid'] = (int)$info['cid'];
        return compact('info');
    }

    /**
     * Xóa bài viết
     * @param int $id
     */
    public function del(int $id)
    {
        /** @var ArticleContentServices $articleContentService */
        $articleContentService = app()->make(ArticleContentServices::class);
        $this->transaction(function () use ($id, $articleContentService) {
            $res = $this->dao->delete($id);
            $res = $res && $articleContentService->del($id);
            if (!$res) {
                throw new AdminException(100008);
            }
        });
    }

    /**
     * Liên kết sản phẩm với bài viết
     * @param int $id
     * @param int $product_id
     * @return mixed
     */
    public function bindProduct(int $id, int $product_id = 0)
    {
        return $this->dao->update($id, ['product_id' => $product_id]);
    }

    /**
     * Lấy số lượng
     * @param array $where
     * @param bool $search
     * @return int
     */
    public function count(array $where = [], bool $search = true): int
    {
        return $this->search($where, $search)->count();
    }

    /**
     * Lấy một dòng dữ liệu
     * @param int $id
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo(int $id)
    {
        $info = $this->dao->read($id);
        $info->visit = intval($info['visit']) + 1;
        if (!$info->save())
            throw new AdminException(400456);
        if ($info) {
            $info = $info->toArray();
            $info['visit'] = (int)$info['visit'];
            $info['add_time'] = date('Y-m-d', $info['add_time']);
        }
        return $info;
    }

    /**
     * Lấy danh sách bài viết
     * @param $new_id
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function articleList($new_id)
    {
        return $this->dao->articleLists($new_id);
    }

    /**
     * Chi tiết tin bài
     * @param $new_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function articlesList($new_id)
    {
        return $this->dao->articleContentList($new_id);
    }
}
