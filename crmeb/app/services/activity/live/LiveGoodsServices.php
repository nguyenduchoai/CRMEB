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
declare (strict_types=1);

namespace app\services\activity\live;


use app\dao\activity\live\LiveGoodsDao;
use app\services\BaseServices;
use app\services\product\product\StoreProductServices;
use crmeb\exceptions\AdminException;
use crmeb\services\app\MiniProgramService;
use crmeb\utils\DownloadImage;
use crmeb\utils\Str;
use think\facade\Log;

/**
 * Class LiveGoodsServices
 * @package app\services\activity\live
 */
class LiveGoodsServices extends BaseServices
{
    /**
     * LiveGoodsServices constructor.
     * @param LiveGoodsDao $dao
     */
    public function __construct(LiveGoodsDao $dao)
    {
        $this->dao = $dao;
    }

    public function getList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $list = $this->dao->getList($where, '*', ['product'], $page, $limit);
        $count = $this->dao->count($where);
        return compact('count', 'list');
    }

    public function create(array $product_ids)
    {
        if (!$product_ids) throw new AdminException('Tham số không hợp lệ');
        /** @var StoreProductServices $product */
        $productServices = app()->make(StoreProductServices::class);
        $products = $productServices->getColumn([['id', 'IN', $product_ids], ['is_del', '=', 0], ['is_show', '=', 1]], 'id,image,store_name,price,price as cost_price,stock', 'id');
        if (count($product_ids) != count($products)) {
            throw new AdminException('Sản phẩm đã ngừng bán hoặc đã chuyển vào thùng rác');
        }
        $checkGoods = $this->dao->getCount([['product_id', 'IN', $product_ids], ['is_del', '=', 0], ['audit_status', '<>', 3]]);
        if ($checkGoods > 0) {
            throw new AdminException('Thêm thất bại');
        }
        return array_merge($products);
    }

    /**
     * Thêm sản phẩm livestream
     * @param array $goods_info
     * @return bool
     * @throws \Exception
     */
    public function add(array $goods_info)
    {
        if (!$goods_info) throw new AdminException('Tham số không hợp lệ');
        $product_ids = array_column($goods_info, 'id');
        $this->create($product_ids);
        $miniUpload = MiniProgramService::materialTemporaryService();
        /** @var DownloadImage $download */
        $download = app()->make(DownloadImage::class);
        $dataAll = $data = [];
        $time = time();
        foreach ($goods_info as $product) {
            $data = [
                'product_id' => $product['id'],
                'name' => Str::substrUTf8($product['store_name'], 12, 'UTF-8', ''),
                'cover_img' => $product['image'] ?? '',
                'price_type' => 1,
                'cost_price' => $product['cost_price'] ?? 0.00,
                'price' => $product['price'] ?? 0.00,
                'url' => 'pages/goods_details/index?id=' . $product['id'],
                'sort' => $product['sort'] ?? 0,
                'add_time' => $time
            ];
            try {
                $path = root_path() . 'public' . $download->thumb(true)->downloadImage($data['cover_img'])['path'];
                $coverImgUrl = $miniUpload->uploadImage($path)->media_id;
                @unlink($path);
            } catch (\Throwable $e) {
                Log::error('Lỗi khi thêm ảnh sản phẩm livestream, nguyên nhân:' . $e->getMessage());
                @unlink($path);
                $coverImgUrl = $data['cover_img'];
            }
            $res = MiniProgramService::addGoods($coverImgUrl, $data['name'], $data['price_type'], $data['url'], floatval($data['price']));
            $data['goods_id'] = $res['goodsId'];
            $data['audit_id'] = $res['auditId'];
            $data['audit_status'] = 1;
            $dataAll[] = $data;
        }
        if (!$goods = $this->dao->saveAll($dataAll)) {
            throw new AdminException('Thêm thất bại');
        }
        return true;
    }

    /**
     * Đồng bộ sản phẩm
     * @return bool
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function syncGoods()
    {
        $liveGoods = $this->dao->getColumn([['goods_id', '>', 0]], '*', 'id');
        if ($liveGoods) {
            /** @var DownloadImage $downloadImage */
            $downloadImage = app()->make(DownloadImage::class);
            foreach ($liveGoods as $good) {
                $path = root_path() . 'public' . $downloadImage->thumb(true)->downloadImage($good['cover_img'])['path'];
                $coverImgUrl = MiniProgramService::materialTemporaryService()->uploadImage($path)->media_id;
                @unlink($path);
                $res = MiniProgramService::addGoods($coverImgUrl, $good['name'], $good['price_type'], $good['url'], floatval($good['price']));
                $data['goods_id'] = $res['goodsId'];
                $data['audit_id'] = $res['auditId'];
                $data['audit_status'] = 1;
                if (!$this->dao->update($good['id'], $data, 'id')) {
                    throw new AdminException('Đồng bộ thất bại');
                }
            }
        }
        return true;
    }

    public function wxCreate($goods)
    {
        if ($goods['goods_id'])
            throw new AdminException('Sản phẩm đã được tạo');

        $goods = $goods->toArray();
        /** @var DownloadImage $downloadImage */
        $downloadImage = app()->make(DownloadImage::class);
        $path = root_path() . 'public' . $downloadImage->thumb(true)->downloadImage($goods['cover_img'])['path'];

        $url = 'pages/goods_details/index?id=' . $goods['product_id'];
        $coverImgUrl = MiniProgramService::materialTemporaryService()->uploadImage($path)->media_id;
        @unlink($path);
        return MiniProgramService::addGoods($coverImgUrl, $goods['name'], 1, $url, floatval($goods['price']));
    }

    public function isShow(int $id, $is_show)
    {
        $goods = $this->dao->get(['id' => $id, 'audit_status' => 2]);
        if (!$goods) {
            throw new AdminException('Không cho phép thao tác này khi đang chờ duyệt hoặc duyệt thất bại');
        }
        $this->dao->update($id, ['is_show' => $is_show]);
        return true;
    }

    /**
     * Gửi lại để duyệt
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function audit(int $id)
    {
        $goods = $this->dao->get($id);
        if (!$goods) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        if ($goods['audit_status'] != 0) {
            throw new AdminException('Đang chờ duyệt hoặc đã được duyệt');
        }
        if (!$this->dao->update($id, ['audit_status' => 1])) {
            throw new AdminException('Sửa thất bại');
        }
        return MiniProgramService::auditGoods((int)$goods['good_id']);
    }

    /**
     * Rút lại yêu cầu duyệt
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function resetAudit(int $id)
    {
        $goods = $this->dao->get($id);
        if (!$goods) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        if ($goods['audit_status'] == 0) {
            return true;
        }
        if ($goods['audit_status'] != 1) {
            throw new AdminException('Đã duyệt hoặc duyệt thất bại');
        }
        if (!$this->dao->update($id, ['audit_status' => 0])) {
            throw new AdminException('Sửa thất bại');
        }
        return MiniProgramService::resetauditGoods((int)$goods['good_id'], $goods['audit_id']);
    }

    /**
     * Xóa sản phẩm
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete(int $id)
    {
        $goods = $this->dao->get(['id' => $id, 'is_del' => 0]);
        if ($goods) {
            if (in_array($goods['audit_status'], [0, 1])) {
                throw new AdminException('Sản phẩm đang chờ duyệt, không thể xóa');
            }
            if (!$this->dao->update($id, ['is_del' => 1])) {
                throw new AdminException('Xóa thất bại');
            }
            if (MiniProgramService::deleteGoods((int)$goods->goods_id)) {
                /** @var LiveRoomGoodsServices $liveRoomGoods */
                $liveRoomGoods = app()->make(LiveRoomGoodsServices::class);
                $liveRoomGoods->delete(['live_goods_id' => $id]);
            }
        }
        return true;
    }

    /**
     * Đồng bộ trạng thái duyệt sản phẩm livestream
     * @return bool
     */
    public function syncGoodStatus()
    {
        $goodsIds = $this->dao->goodsStatusAll();
        if (!count($goodsIds)) return true;
        $res = MiniProgramService::getGooodsInfo(array_keys($goodsIds));
        foreach ($res as $item) {
            if (isset($goodsIds[$item['goods_id']]) && $item['audit_status'] != $goodsIds[$item['goods_id']]) {
                $data = ['audit_status' => $item['audit_status']];
                //TODO đồng bộ trạng thái duyệt sản phẩm
                $this->dao->update((int)$goodsIds[$item['goods_id']]['id'], $data);
            }
        }
        return true;
    }

}
