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
namespace app\adminapi\controller\v1\diy;

use app\adminapi\controller\AuthController;
use app\services\activity\bargain\StoreBargainServices;
use app\services\activity\combination\StoreCombinationServices;
use app\services\activity\seckill\StoreSeckillServices;
use app\services\article\ArticleServices;
use app\services\diy\DiyServices;
use app\services\other\CacheServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreProductServices;
use crmeb\exceptions\AdminException;
use think\facade\App;

/**
 *
 * Class Diy
 * @package app\controller\admin\v1\diy
 */
class Diy extends AuthController
{
    /**
     * @param App $app
     * @param DiyServices $services
     */
    public function __construct(App $app, DiyServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Danh sách DIY
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList()
    {
        $where = $this->request->getMore([
            ['status', ''],
            ['type', 0],
            ['name', ''],
            ['version', ''],
        ]);
        $where['type'] = -1;
        $data = $this->services->getDiyList($where);
        return app('json')->success($data);
    }

    /**
     * Lưu resource chỉnh sửa trực quan
     * @param int $id
     * @return mixed
     */
    public function saveData(int $id = 0)
    {
        $data = $this->request->postMore([
            ['value', ''],
        ]);
        $value_config = ['seckill', 'bargain', 'combination', 'goodList'];
        $value = is_string($data['value']) ? json_decode($data['value'], true) : $data['value'];
        foreach ($value as $key => &$val) {
            if (in_array($key, $value_config) && is_array($val)) {
                foreach ($val as $k => &$v) {
                    if (isset($v['selectConfig']['list']) && $v['selectConfig']['list']) {
                        $v['selectConfig']['list'] = [];
                    }
                    if (isset($v['goodsList']['list']) && $v['goodsList']['list'] && $v['tabConfig']['tabVal'] == 1) {
                        $limitMax = config('database.page.limitMax', 50);
                        if (count($v['goodsList']['list']) > $limitMax) {
                            return app('json')->fail(400350);
                        }
                        $v['ids'] = array_column($v['goodsList']['list'], 'id');
                        $v['goodsList']['list'] = [];
                    }
                }
            }
        }
        $data['value'] = json_encode($value);
        $data['version'] = uniqid();
        $this->services->saveData($id, $data);
        return app('json')->success(100000);
    }

    /**
     * Lưu resource Diy
     * @param int $id
     * @return mixed
     */
    public function saveDiyData(int $id = 0)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['title', ''],
            ['value', ''],
            ['type', ''],
            ['cover_image', ''],
            ['is_show', 0],
            ['is_bg_color', 0],
            ['is_bg_pic', 0],
            ['bg_tab_val', 0],
            ['color_picker', ''],
            ['bg_pic', ''],
        ]);
        $value = is_string($data['value']) ? json_decode($data['value'], true) : $data['value'];
        $infoDiy = $id ? $this->services->get($id, ['is_diy']) : [];
        if ($infoDiy && $infoDiy['is_diy']) {
            foreach ($value as $key => &$item) {
                if ($item['name'] === 'goodList') {
                    if (isset($item['selectConfig']['list'])) {
                        unset($item['selectConfig']['list']);
                    }
                    if (isset($item['goodsList']['list']) && is_array($item['goodsList']['list'])) {
                        $limitMax = config('database.page.limitMax', 50);
                        if (isset($item['numConfig']['val']) && isset($item['tabConfig']['tabVal']) && $item['tabConfig']['tabVal'] == 0 && $item['numConfig']['val'] > $limitMax) {
                            return app('json')->fail(400350);
                        }
                        $item['goodsList']['ids'] = array_column($item['goodsList']['list'], 'id');
                        unset($item['goodsList']['list']);
                    }
                } elseif ($item['name'] === 'articleList') {
                    if (isset($item['selectList']['list']) && is_array($item['selectList']['list'])) {
                        unset($item['selectList']['list']);
                    }
                } elseif ($item['name'] === 'promotionList') {
                    unset($item['productList']['list']);
                }
            }
            $data['value'] = json_encode($value);
        } else {
            if (isset($value['d_goodList']['selectConfig']['list'])) {
                unset($value['d_goodList']['selectConfig']['list']);
            } elseif (isset($value['d_goodList']['goodsList']['list'])) {
                $limitMax = config('database.page.limitMax', 50);
                if (isset($value['d_goodList']['numConfig']['val']) && isset($value['d_goodList']['tabConfig']['tabVal']) && $value['d_goodList']['tabConfig']['tabVal'] == 0 && $value['d_goodList']['numConfig']['val'] > $limitMax) {
                    return app('json')->fail(400350);
                }
                $value['d_goodList']['goodsList']['ids'] = array_column($value['d_goodList']['goodsList']['list'], 'id');
                unset($value['d_goodList']['goodsList']['list']);
            } elseif (isset($value['k_newProduct']['goodsList']['list'])) {
                $list = [];
                foreach ($value['k_newProduct']['goodsList']['list'] as $item) {
                    $list[] = [
                        'image' => $item['image'],
                        'store_info' => $item['store_info'],
                        'store_name' => $item['store_name'],
                        'id' => $item['id'],
                        'price' => $item['price'],
                        'ot_price' => $item['ot_price'],
                    ];
                }
                $value['k_newProduct']['goodsList']['list'] = $list;
            } elseif (isset($value['selectList']['list']) && is_array($value['selectList']['list'])) {
                unset($value['goodsList']['list']);
            }
            $data['value'] = json_encode($value);
        }
        $data['version'] = '1.0';
        $data['type'] = 2;
        $data['is_diy'] = 1;
        $data['version'] = uniqid();
        return app('json')->success($id ? 100001 : 100000, ['id' => $this->services->saveData($id, $data)]);
    }

    /**
     * Xóa mẫu
     * @param $id
     * @return mixed
     */
    public function del($id)
    {
        $this->services->del($id);
        return app('json')->success(100002);
    }

    /**
     * Dùng mẫu
     * @param $id
     * @return mixed
     */
    public function setStatus($id)
    {
        $this->services->setStatus($id);
        return app('json')->success(100014);
    }

    /**
     * Lấy một dòng dữ liệu
     * @param int $id
     * @param StoreProductServices $services
     * @param StoreSeckillServices $seckillServices
     * @param StoreCombinationServices $combinationServices
     * @param StoreBargainServices $bargainServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfo(int $id, StoreProductServices $services, StoreSeckillServices $seckillServices, StoreCombinationServices $combinationServices, StoreBargainServices $bargainServices)
    {
        if (!$id) throw new AdminException(100100);
        $info = $this->services->get($id);
        if ($info) {
            $info = $info->toArray();
        } else {
            throw new AdminException(400351);
        }
        if (!$info['value']) return app('json')->success(compact('info'));
        $info['value'] = json_decode($info['value'], true);
        $value_config = ['seckill', 'bargain', 'combination', 'goodList'];
        foreach ($info['value'] as $key => &$val) {
            if (in_array($key, $value_config) && is_array($val)) {
                if ($key == 'goodList') {
                    foreach ($val as $k => &$v) {
                        if (isset($v['ids']) && $v['ids'] && $v['tabConfig']['tabVal'] == 1) {
                            $v['goodsList']['list'] = $services->getSearchList(['ids' => $v['ids']]);
                        }
                    }
                }
                if ($key == "seckill") {
                    foreach ($val as $k => &$v) {
                        if (isset($v['ids']) && $v['ids'] && $v['tabConfig']['tabVal'] == 1) {
                            $v['goodsList']['list'] = $seckillServices->getDiySeckillList(['ids' => $v['ids']])['list'];
                        }
                    }
                }
                if ($key == "bargain") {
                    foreach ($val as $k => &$v) {
                        if (isset($v['ids']) && $v['ids'] && $v['tabConfig']['tabVal'] == 1) {
                            $v['goodsList']['list'] = $bargainServices->getHomeList(['ids' => $v['ids']])['list'];
                        }
                    }
                }
                if ($key == "combination") {
                    foreach ($val as $k => &$v) {
                        if (isset($v['ids']) && $v['ids'] && $v['tabConfig']['tabVal'] == 1) {
                            $v['goodsList']['list'] = $combinationServices->getHomeList(['ids' => $v['ids']])['list'];
                        }
                    }
                }

            }
        }
        return app('json')->success(compact('info'));
    }

    /**
     * Lấy dữ liệu diy
     * @param $id
     * @param StoreProductServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDiyInfo($id, StoreProductServices $services)
    {
        if (!$id) throw new AdminException(100100);
        $info = $this->services->get($id);
        if ($info) {
            $info = $info->toArray();
        } else {
            throw new AdminException(400351);
        }
        $info['value'] = json_decode($info['value'], true);
        if ($info['value']) {
            /** @var ArticleServices $articleServices */
            $articleServices = app()->make(ArticleServices::class);
            if ($info['is_diy']) {
                foreach ($info['value'] as &$item) {
                    if ($item['name'] === 'goodList') {
                        if (isset($item['goodsList']['ids']) && count($item['goodsList']['ids'])) {
                            $item['goodsList']['list'] = $services->getSearchList(['ids' => $item['goodsList']['ids']]);
                        } else {
                            $item['goodsList']['list'] = [];
                        }
                    } elseif ($item['name'] === 'articleList') {//Bài viết
                        $data = [];
                        if ($item['selectConfig']['activeValue'] ?? 0) {
                            $data = $articleServices->getList(['cid' => $item['selectConfig']['activeValue'] ?? 0], 0, $item['numConfig']['val'] ?? 0);
                        }
                        $item['selectList']['list'] = $data['list'] ?? [];
                    } elseif ($item['name'] === 'promotionList') {//Sao chép hoạt động
                        $data = [];
                        if (isset($item['tabConfig']['tabCur']) && $typeArr = $item['tabConfig']['list'][$item['tabConfig']['tabCur']] ?? []) {
                            $val = $typeArr['link']['activeVal'] ?? 0;
                            if ($val) {
                                $data = $this->get_groom_list($val, (int)($item['numConfig']['val'] ?? 0));
                            }
                        }
                        $item['productList']['list'] = $data;
                    }
                }
            } else {
                if (isset($info['value']['d_goodList']['goodsList'])) {
                    $info['value']['d_goodList']['goodsList']['list'] = [];
                }
                if (isset($info['value']['d_goodList']['goodsList']['ids']) && count($info['value']['d_goodList']['goodsList']['ids'])) {
                    $info['value']['d_goodList']['goodsList']['list'] = $services->getSearchList(['ids' => $info['value']['d_goodList']['goodsList']['ids']]);
                }
            }
        }
        return app('json')->success(compact('info'));
    }

    /**
     * Lấy sản phẩm đề xuất
     * @param $type
     * @param int $num
     * @return array|array[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function get_groom_list($type, int $num = 0)
    {
        /** @var StoreProductServices $services */
        $services = app()->make(StoreProductServices::class);
        $info = [];
        if ($type == 1) {// Đề xuất nổi bật
            $info = $services->getRecommendProduct(0, 'is_best', $num);// Số lượng đề xuất nổi bật
        } else if ($type == 2) {//  Top bán chạy
            $info = $services->getRecommendProduct(0, 'is_hot', $num);// Bảng xếp hạng phổ biến - Có thể bạn thích
        } else if ($type == 3) {// Hàng mới ra mắt
            $info = $services->getRecommendProduct(0, 'is_new', $num);// Hàng mới ra mắt
        } else if ($type == 4) {// Sản phẩm khuyến mãi
            $info = $services->getRecommendProduct(0, 'is_benefit', $num);// Sản phẩm khuyến mãi
        } else if ($type == 5) {// Sản phẩm thành viên
            $whereVip = [
                ['vip_price', '>', 0],
                ['is_vip', '=', 1],
            ];
            $info = $services->getRecommendProduct(0, $whereVip, $num);// Sản phẩm thành viên
        }
        return $info;
    }

    /**
     * Hiển thị sản phẩm đề xuất
     * @param $type
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/3/11
     */
    public function getGroomList($type)
    {
        [$page, $limit] = $this->request->getMore([
            ['page', 1],
            ['limit', 6],
        ], true);
        $list = $this->get_groom_list($type, $limit);
        return app('json')->success($list);
    }

    /**
     * Lấy đường dẫn uni-app
     * @return mixed
     */
    public function getUrl()
    {
        $url = sys_data('uni_app_link');
        if ($url) {
            $model_checkbox = sys_config('model_checkbox', ['seckill', 'bargain', 'combination']);
            foreach ($url as $key => &$link) {
                $link['url'] = $link['link'];
                $link['parameter'] = trim($link['param']);
                if (!in_array('seckill', $model_checkbox) && strpos($link['name'], 'Flash sale') !== false) unset($url[$key]);
                if (!in_array('bargain', $model_checkbox) && strpos($link['name'], 'Săn giảm giá') !== false) unset($url[$key]);
                if (!in_array('combination', $model_checkbox) && strpos($link['name'], 'Mua chung') !== false) unset($url[$key]);
            }
        } else {
            /** @var CacheServices $cache */
            $cache = app()->make(CacheServices::class);
            $url = $cache->getDbCache('uni_app_url', null);
        }
        return app('json')->success(compact('url'));
    }

    /**
     * Lấy danh mục sản phẩm
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCategory()
    {
        /** @var StoreCategoryServices $categoryService */
        $categoryService = app()->make(StoreCategoryServices::class);
        $list = $categoryService->cascaderList(1, 1);
        return app('json')->success($list);
    }

    /**
     * Lấy sản phẩm
     * @return mixed
     */
    public function getProduct()
    {
        $where = $this->request->getMore([
            ['id', 0],
            ['salesOrder', ''],
            ['priceOrder', ''],
        ]);
        $id = $where['id'];
        unset($where['id']);
        $where['is_show'] = 1;
        /** @var StoreCategoryServices $storeCategoryServices */
        $storeCategoryServices = app()->make(StoreCategoryServices::class);
        if ($storeCategoryServices->value(['id' => $id], 'pid')) {
            $where['sid'] = $id;
        } else {
            $where['cid'] = $id;
        }
        [$page, $limit] = $this->services->getPageValue();
        /** @var StoreProductServices $productService */
        $productService = app()->make(StoreProductServices::class);
        $list = $productService->getSearchList($where, $page, $limit);
        return app('json')->success($list);
    }

    /**
     * Lấy trạng thái mở nhận tại điểm nhận hàng
     * @return mixed
     */
    public function getStoreStatus()
    {
        $data['store_status'] = sys_config('store_self_mention', 0);
        return app('json')->success($data);
    }

    /**
     * Khôi phục dữ liệu mẫu
     * @param $id
     * @return mixed
     */
    public function Recovery($id)
    {
        if (!$id) throw new AdminException(100100);
        $info = $this->services->get($id);
        if ($info) {
            $info->value = $info->default_value;
            $info->update_time = time();
            $info->save();
            return app('json')->success(100014);
        } else {
            throw new AdminException(400351);
        }
    }

    /**
     * Lấy danh mục cấp 2
     * @return mixed
     */
    public function getByCategory()
    {
        $where = $this->request->getMore([
            ['pid', -1],
            ['name', '']
        ]);
        /** @var StoreCategoryServices $categoryServices */
        $categoryServices = app()->make(StoreCategoryServices::class);
        return app('json')->success($categoryServices->getALlByIndex($where));
    }

    /**
     * Thêm trang
     * @return mixed
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function create()
    {
        return app('json')->success($this->services->createForm());
    }

    /**
     * Lưu trang
     * @return mixed
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['name', ''],
        ]);
        if (!$data['name']) app('json')->fail(400352);
        $data['version'] = '1.0';
        $data['add_time'] = time();
        $data['type'] = 0;
        $data['is_diy'] = 1;
        $this->services->save($data);
        return app('json')->success(100000);
    }

    /**
     * Đặt dữ liệu mặc định
     * @param $id
     * @return mixed
     */
    public function setRecovery($id)
    {
        if (!$id) throw new AdminException(100100);
        $info = $this->services->get($id);
        if ($info) {
            $info->default_value = $info->value;
            $info->update_time = time();
            $info->save();
            return app('json')->success(100014);
        } else {
            throw new AdminException(100026);
        }
    }

    /**
     * Lấy danh sách sản phẩm
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductList()
    {
        $where = $this->request->getMore([
            ['cate_id', ''],
            ['store_name', ''],
            ['type', 0],
        ]);
        $where['is_show'] = 1;
        $where['is_del'] = 0;
        /** @var StoreCategoryServices $storeCategoryServices */
        $storeCategoryServices = app()->make(StoreCategoryServices::class);
        if ($where['cate_id'] !== '') {
            if ($storeCategoryServices->value(['id' => $where['cate_id']], 'pid')) {
                $where['sid'] = $where['cate_id'];
            } else {
                $where['cid'] = $where['cate_id'];
            }
        }
        unset($where['cate_id']);
        $list = $this->services->ProductList($where);
        return app('json')->success($list);
    }

    /**
     * Danh mục, trang cá nhân, đổi màu nhanh
     * @param $type
     * @return mixed
     */
    public function getColorChange($type)
    {
        $status = (int)$this->services->getColorChange((string)$type);
        return app('json')->success(compact('status'));
    }

    /**
     * Lưu danh mục, trang cá nhân, đổi màu nhanh
     * @param $status
     * @param $type
     * @return mixed
     */
    public function colorChange($status, $type)
    {
        if (!$status) throw new AdminException(100100);
        $info = $this->services->get(['template_name' => $type, 'type' => 1]);
        if ($info) {
            $info->value = $status;
            $info->update_time = time();
            $info->save();
            return app('json')->success(100014);
        } else {
            throw new AdminException(100026);
        }
    }

    /**
     * Lấy dữ liệu trang cá nhân
     * @return mixed
     */
    public function getMember()
    {
        $data = $this->services->getMemberData();
        return app('json')->success($data);
    }

    /**
     * Lưu dữ liệu trang cá nhân
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function memberSaveData()
    {
        $data = $this->request->postMore([
            ['status', 0],
            ['order_status', 0],
            ['my_banner_status', 0],
            ['my_menus_status', 0],
            ['business_status', 0],
            ['routine_my_banner', []],
            ['routine_my_menus', []]
        ]);
        $this->services->memberSaveData($data);
        return app('json')->success(100000);
    }

    /**
     * Lấy quảng cáo màn hình khởi động
     * @return mixed
     */
    public function getOpenAdv()
    {
        /** @var CacheServices $cacheServices */
        $cacheServices = app()->make(CacheServices::class);
        $data = $cacheServices->getDbCache('open_adv', '');
        if ($data == '') {
            $data = [
                'status' => 0,
                'time' => '',
                'type' => 'pic',
                'value' => [],
                'video_link' => '',
            ];
        }
        return app('json')->success($data);
    }

    /**
     * Lưu quảng cáo màn hình khởi động
     * @return mixed
     */
    public function openAdvAdd()
    {
        $data = $this->request->postMore([
            ['status', 0],
            ['time', 0],
            ['type', ''],
            ['value', []],
            ['video_link', '']
        ]);
        if ($data['type'] == '') $data['type'] = 'pic';
        /** @var CacheServices $cacheServices */
        $cacheServices = app()->make(CacheServices::class);
        $cacheServices->setDbCache('open_adv', $data);
        return app('json')->success(100000);
    }

    /**
     * Lấy mã QR xem trước Mini Program diy của một trang
     * @param $id
     * @return mixed
     */
    public function getRoutineCode($id)
    {
        $image = $this->services->getRoutineCode((int)$id);
        return app('json')->success(compact('image'));
    }
}
