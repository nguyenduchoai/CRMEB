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

namespace app\services\activity\bargain;

use app\dao\activity\bargain\StoreBargainDao;
use app\jobs\ProductLogJob;
use app\Request;
use app\services\BaseServices;
use app\services\order\StoreOrderServices;
use app\services\other\PosterServices;
use app\services\other\QrcodeServices;
use app\services\product\product\StoreCategoryServices;
use app\services\product\product\StoreDescriptionServices;
use app\services\product\product\StoreProductServices;
use app\services\product\sku\StoreProductAttrResultServices;
use app\services\product\sku\StoreProductAttrServices;
use app\services\product\sku\StoreProductAttrValueServices;
use app\services\system\attachment\SystemAttachmentServices;
use app\services\user\UserServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;
use crmeb\services\CacheService;
use crmeb\services\app\MiniProgramService;
use app\services\other\UploadService;
use Guzzle\Http\EntityBody;

/**
 *
 * Class StoreBargainServices
 * @package app\services\activity
 * @method get(int $id, ?array $field) Lấy một dòng dữ liệu
 * @method getBargainIdsArray(array $ids, array $field)
 * @method sum(array $where, string $field)
 * @method update(int $id, array $data)
 * @method addBargain(int $id, string $field)
 * @method value(array $where, string $field)
 * @method validWhere()
 * @method getList(array $where, int $page = 0, int $limit = 0) Lấy danh sách săn giảm giá
 */
class StoreBargainServices extends BaseServices
{

    /**
     * StoreCombinationServices constructor.
     * @param StoreBargainDao $dao
     */
    public function __construct(StoreBargainDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Kiểm tra sản phẩm săn giảm giá có mở hay không
     * @param int $bargainId
     * @return int
     */
    public function validBargain($bargainId = 0)
    {
        $where = [];
        $time = time();
        $where[] = ['is_del', '=', 0];
        $where[] = ['status', '=', 1];
        $where[] = ['start_time', '<', $time];
        $where[] = ['stop_time', '>', $time - 85400];
        if ($bargainId) $where[] = ['id', '=', $bargainId];
        return $this->dao->getCount($where);
    }

    /**
     * Lấy danh sách ở trang quản trị
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStoreBargainList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getList($where, $page, $limit);
        $count = $this->dao->count($where);
        /** @var StoreBargainUserServices $storeBargainUserServices */
        $storeBargainUserServices = app()->make(StoreBargainUserServices::class);
        $ids = array_column($list, 'id');
        $countAll = $storeBargainUserServices->getAllCount([['bargain_id', 'in', $ids]]);
        $countSuccess = $storeBargainUserServices->getAllCount([
            ['status', '=', 3],
            ['bargain_id', 'in', $ids]
        ]);
        /** @var StoreBargainUserHelpServices $storeBargainUserHelpServices */
        $storeBargainUserHelpServices = app()->make(StoreBargainUserHelpServices::class);
        $countHelpAll = $storeBargainUserHelpServices->getHelpAllCount([['bargain_id', 'in', $ids]]);
        $stopIds = [];
        foreach ($list as &$item) {
            $item['count_people_all'] = $countAll[$item['id']] ?? 0;//Số người tham gia
            $item['count_people_help'] = $countHelpAll[$item['id']] ?? 0;//Số người giúp giảm giá
            $item['count_people_success'] = $countSuccess[$item['id']] ?? 0;//Số người săn giảm giá thành công
            $item['stop_status'] = $item['stop_time'] < time() ? 1 : 0;
            if ($item['status']) {
                if ($item['start_time'] > time()) {
                    $item['start_name'] = 'Chưa bắt đầu';
                } else if ($item['stop_time'] < time()) {
                    $item['start_name'] = 'Đã kết thúc';
                    $item['status'] = 0;
                    $stopIds[] = $item['id'];
                } else if ($item['stop_time'] > time() && $item['start_time'] < time()) {
                    $item['start_name'] = 'Đang diễn ra';
                }
            } else {
                $item['start_name'] = 'Đã kết thúc';
            }
            $item['start_time'] = $item['start_time'] ? date('Y-m-d H:i:s', $item['start_time']) : '';
            $item['stop_time'] = $item['stop_time'] ? date('Y-m-d 23:59:59', $item['stop_time']) : '';
        }
        if ($stopIds) {
            $this->dao->batchUpdate($stopIds, ['status' => 0]);
        }
        return compact('list', 'count');
    }

    /**
     * Lưu dữ liệu
     * @param int $id
     * @param array $data
     */
    public function saveData(int $id, array $data)
    {
        $description = $data['description'];
        $detail = $data['attrs'];
        $items = $data['items'];
        $data['start_time'] = strtotime($data['section_time'][0]);
        $data['stop_time'] = strtotime($data['section_time'][1]);
        $data['image'] = $data['image'];
        $data['images'] = json_encode($data['images']);
        $data['stock'] = $detail[0]['stock'];
        $data['quota'] = $detail[0]['quota'];
        $data['quota_show'] = $detail[0]['quota'];
        $data['price'] = $detail[0]['price'];
        $data['min_price'] = $detail[0]['min_price'];
        $data['logistics'] = implode(',', $data['logistics']);
        if ($detail[0]['min_price'] < 0 || $detail[0]['price'] <= 0 || $detail[0]['min_price'] === '' || $detail[0]['price'] === '') throw new AdminException('Số tiền không được nhỏ hơn 0');
        if ($detail[0]['min_price'] >= $detail[0]['price']) throw new AdminException('Giá thấp nhất khi săn giảm giá không được lớn hơn hoặc bằng giá khởi điểm');
        if ($detail[0]['quota'] > $detail[0]['stock']) throw new AdminException('Số lượng giới hạn không được vượt quá tồn kho sản phẩm');

        //Tính số người tối đa được đặt theo số tiền có thể giảm, và kiểm tra số người săn giảm giá nhập vào có lớn hơn số người tối đa đã đặt không
        $bNum = bcmul(bcsub((string)$data['price'], (string)$data['min_price'], 2), '100');
        if ($data['people_num'] > $bNum) throw new AdminException('Số người săn giảm giá không được lớn hơn {:num} người', ['num' => $bNum]);

        unset($data['section_time'], $data['description'], $data['attrs'], $data['items'], $detail[0]['min_price'], $detail[0]['_index'], $detail[0]['_rowKey']);
        /** @var StoreDescriptionServices $storeDescriptionServices */
        $storeDescriptionServices = app()->make(StoreDescriptionServices::class);
        /** @var StoreProductAttrServices $storeProductAttrServices */
        $storeProductAttrServices = app()->make(StoreProductAttrServices::class);
        /** @var StoreProductServices $storeProductServices */
        $storeProductServices = app()->make(StoreProductServices::class);
        $this->transaction(function () use ($id, $data, $description, $detail, $items, $storeDescriptionServices, $storeProductAttrServices, $storeProductServices) {
            if ($id) {
                $res = $this->dao->update($id, $data);
                $storeDescriptionServices->saveDescription((int)$id, $description, 2);
                $skuList = $storeProductServices->validateProductAttr($items, $detail, (int)$id, 2);
                $valueGroup = $storeProductAttrServices->saveProductAttr($skuList, (int)$id, 2);
                if (!$res) throw new AdminException('Sửa thất bại');
            } else {
                if (!$storeProductServices->getOne(['is_del' => 0, 'id' => $data['product_id']])) {
                    throw new AdminException('Không thể thêm sản phẩm trong thùng rác');
                }
                $data['add_time'] = time();
                $res = $this->dao->save($data);
                $storeDescriptionServices->saveDescription((int)$res->id, $description, 2);
                $skuList = $storeProductServices->validateProductAttr($items, $detail, (int)$res->id, 2, 1, true);
                $valueGroup = $storeProductAttrServices->saveProductAttr($skuList, (int)$res->id, 2);
                if (!$res) throw new AdminException('Thêm thất bại');
            }
        });
    }

    /**
     * Lấy chi tiết săn giảm giá
     * @param int $id
     * @return array|\think\Model|null
     */
    public function getInfo(int $id)
    {
        $info = $this->dao->get($id);
        if ($info) {
            if ($info['start_time'])
                $start_time = date('Y-m-d H:i:s', $info['start_time']);

            if ($info['stop_time'])
                $stop_time = date('Y-m-d H:i:s', $info['stop_time']);
            if (isset($start_time) && isset($stop_time))
                $info['section_time'] = [$start_time, $stop_time];
            else
                $info['section_time'] = [];
            unset($info['start_time'], $info['stop_time']);
        }
        $info['give_integral'] = intval($info['give_integral']);
        $info['price'] = floatval($info['price']);
        $info['postage'] = floatval($info['postage']);
        $info['cost'] = floatval($info['cost']);
        $info['bargain_max_price'] = floatval($info['bargain_max_price']);
        $info['bargain_min_price'] = floatval($info['bargain_min_price']);
        $info['min_price'] = floatval($info['min_price']);
        $info['weight'] = floatval($info['weight']);
        $info['volume'] = floatval($info['volume']);
        $info['logistics'] = explode(',', $info['logistics']);
        /** @var StoreDescriptionServices $storeDescriptionServices */
        $storeDescriptionServices = app()->make(StoreDescriptionServices::class);
        $info['description'] = $storeDescriptionServices->getDescription(['product_id' => $id, 'type' => 2]);
        $info['attrs'] = $this->attrList($id, $info['product_id']);
        return $info;
    }

    /**
     * Lấy phân loại
     * @param int $id
     * @param int $pid
     * @return mixed
     */
    public function attrList(int $id, int $pid)
    {
        /** @var StoreProductAttrServices $storeProductAttrService */
        $storeProductAttrService = app()->make(StoreProductAttrServices::class);
        /** @var StoreProductAttrResultServices $storeProductAttrResultServices */
        $storeProductAttrResultServices = app()->make(StoreProductAttrResultServices::class);
        $bargainResult = $storeProductAttrResultServices->value(['product_id' => $id, 'type' => 2], 'result');
        $items = json_decode($bargainResult, true)['attr'];
        $productAttr = $storeProductAttrService->getProductAttr(['product_id' => $pid, 'type' => 0]);
        $pAttr = [];
        foreach ($productAttr as $key => $value) {
            $pAttr[$key]['value'] = $value['attr_name'];
            $pAttr[$key]['detailValue'] = '';
            $pAttr[$key]['attrHidden'] = true;
            $pAttr[$key]['detail'] = $value['attr_values'];
        }

        $bargainAttr = $storeProductAttrService->getProductAttr(['product_id' => $id, 'type' => 3]);
        $bAttr = [];
        foreach ($bargainAttr as $key => $value) {
            $bAttr[$key]['value'] = $value['attr_name'];
            $bAttr[$key]['detailValue'] = '';
            $bAttr[$key]['attrHidden'] = true;
            $bAttr[$key]['detail'] = $value['attr_values'];
        }
        $productAttr = $this->getattr($pAttr, $pid, 0);
        $bargainAttr = $this->getattr($bAttr, $id, 2);
        foreach ($productAttr as $pk => $pv) {
            foreach ($bargainAttr as &$sv) {
                if ($pv['detail'] == $sv['detail']) {
                    $productAttr[$pk] = $sv;
                    $productAttr[$pk]['r_price'] = $pv['price'];
                }
            }
            $productAttr[$pk]['detail'] = json_decode($productAttr[$pk]['detail']);
            $productAttr[$pk]['r_price'] = $productAttr[$pk]['r_price'] ?? $productAttr[$pk]['price'];
        }
        $attrs['items'] = $items;
        $attrs['value'] = $productAttr;
        foreach ($items as $key => $item) {
            $header[] = ['title' => $item['value'], 'key' => 'value' . ($key + 1), 'align' => 'center', 'minWidth' => 80];
        }
        $header[] = ['title' => 'Hình ảnh', 'slot' => 'pic', 'align' => 'center', 'minWidth' => 120];
        $header[] = ['title' => 'Giá khởi điểm săn giảm giá', 'slot' => 'price', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Giá thấp nhất săn giảm giá', 'slot' => 'min_price', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Giá vốn', 'key' => 'cost', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Giá bán thường ngày', 'key' => 'r_price', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Tồn kho', 'key' => 'stock', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Giới hạn số lượng', 'slot' => 'quota', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Trọng lượng (KG)', 'key' => 'weight', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Thể tích (m³)', 'key' => 'volume', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Mã sản phẩm', 'key' => 'bar_code', 'align' => 'center', 'minWidth' => 80];
        $header[] = ['title' => 'Mã vạch', 'key' => 'bar_code_number', 'align' => 'center', 'minWidth' => 80];
        $attrs['header'] = $header;
        return $attrs;
    }

    /**
     * Lấy phân loại
     * @param $attr
     * @param $id
     * @param $type
     * @return array
     */
    public function getattr($attr, $id, $type)
    {
        /** @var StoreProductAttrValueServices $storeProductAttrValueServices */
        $storeProductAttrValueServices = app()->make(StoreProductAttrValueServices::class);
        list($value, $head) = attr_format($attr);
        $valueNew = [];
        $count = 0;
        if ($type == 2) {
            $min_price = $this->dao->value(['id' => $id], 'min_price');
        } else {
            $min_price = 0;
        }
        foreach ($value as $suk) {
            $detail = explode(',', $suk);

            $sukValue = $storeProductAttrValueServices->getColumn(['product_id' => $id, 'type' => $type, 'suk' => $suk], 'bar_code,bar_code_number,cost,price,ot_price,stock,image as pic,weight,volume,brokerage,brokerage_two,quota', 'suk');
            if (count($sukValue)) {
                foreach ($detail as $k => $v) {
                    $valueNew[$count]['value' . ($k + 1)] = $v;
                }
                $valueNew[$count]['detail'] = json_encode(array_combine($head, $detail));
                $valueNew[$count]['pic'] = $sukValue[$suk]['pic'] ?? '';
                $valueNew[$count]['price'] = $sukValue[$suk]['price'] ? floatval($sukValue[$suk]['price']) : 0;
                $valueNew[$count]['min_price'] = $min_price ? floatval($min_price) : 0;
                $valueNew[$count]['cost'] = $sukValue[$suk]['cost'] ? floatval($sukValue[$suk]['cost']) : 0;
                $valueNew[$count]['ot_price'] = isset($sukValue[$suk]['ot_price']) ? floatval($sukValue[$suk]['ot_price']) : 0;
                $valueNew[$count]['stock'] = $sukValue[$suk]['stock'] ? intval($sukValue[$suk]['stock']) : 0;
                $valueNew[$count]['quota'] = $sukValue[$suk]['quota'] ? intval($sukValue[$suk]['quota']) : 0;
                $valueNew[$count]['bar_code'] = $sukValue[$suk]['bar_code'] ?? '';
                $valueNew[$count]['bar_code_number'] = $sukValue[$suk]['bar_code_number'] ?? '';
                $valueNew[$count]['weight'] = $sukValue[$suk]['weight'] ? floatval($sukValue[$suk]['weight']) : 0;
                $valueNew[$count]['volume'] = $sukValue[$suk]['volume'] ? floatval($sukValue[$suk]['volume']) : 0;
                $valueNew[$count]['brokerage'] = $sukValue[$suk]['brokerage'] ? floatval($sukValue[$suk]['brokerage']) : 0;
                $valueNew[$count]['brokerage_two'] = $sukValue[$suk]['brokerage_two'] ? floatval($sukValue[$suk]['brokerage_two']) : 0;
                $valueNew[$count]['opt'] = $type != 0;
                $count++;
            }
        }
        return $valueNew;
    }

//    /**
//     * TODO Lấy ID bảng săn giảm giá
//     * @param int $bargainId $bargainId sản phẩm săn giảm giá
//     * @param int $bargainUserUid $bargainUserUid  mã người dùng mở săn giảm giá
//     * @param int $status $status  trạng thái săn giảm giá: 1 đang tham gia, 2 hoạt động kết thúc tham gia thất bại, 3 hoạt động kết thúc tham gia thành công
//     * @return mixed
//     */
//    public function getBargainUserTableId($bargainId = 0, $bargainUserUid = 0)
//    {
//        return $this->dao->value(['bargain_id' => $bargainId, 'uid' => $bargainUserUid, 'is_del' => 0], 'id');
//    }

//    /**
//     * TODO Lấy số tiền người dùng có thể giảm được
//     * @param $id $id mã bản ghi người dùng tham gia săn giảm giá
//     * @return float
//     * @throws \think\db\exception\DataNotFoundException
//     * @throws \think\db\exception\ModelNotFoundException
//     * @throws \think\exception\DbException
//     */
//    public function getBargainUserDiffPriceFloat($id)
//    {
//        $price = $this->dao->get($id, ['bargain_price,bargain_price_min']);
//        return (float)bcsub($price['bargain_price'], $price['bargain_price_min'], 2);
//    }

//    /**
//     * TODO Lấy số tiền người dùng đã giảm được
//     * @param int $id $id mã bản ghi người dùng tham gia săn giảm giá
//     * @return float
//     */
//    public function getBargainUserPrice($id = 0)
//    {
//        return (float)$this->dao->value(['id' => $id], 'price');
//    }

//    /**
//     * Lấy một sản phẩm săn giảm giá
//     * @param int $bargainId
//     * @param string $field
//     * @return array
//     */
//    public function getBargainOne($bargainId = 0, $field = 'id,product_id,title,price,min_price,image')
//    {
//        if (!$bargainId) return [];
//        $bargain = $this->dao->getOne(['id' => $bargainId], $field);
//        if ($bargain) return $bargain->toArray();
//        else return [];
//    }

    /**
     * Danh sách săn giảm giá
     * @return array
     */
    public function getBargainList()
    {
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        [$page, $limit] = $this->getPageValue();
        $field = 'id,product_id,title,min_price,image,price';
        $list = $this->dao->BargainList($page, $limit, $field);
        foreach ($list as &$item) {
            $item['people'] = $bargainUserService->getUserIdList($item['id']);
            $item['price'] = floatval($item['price']);
            $item['product_price'] = floatval($item['product_price']);
        }
        return $list;
    }

    /**
     * Trang thiết kế giao diện ở trang quản trị lấy danh sách săn giảm giá
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDiyBargainList($where)
    {
        $where['status'] = 1;
        unset($where['is_show']);
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->DiyBargainList($where, $page, $limit);
        $count = $this->dao->getCount($where);
        $cateIds = implode(',', array_column($list, 'cate_id'));
        /** @var StoreCategoryServices $storeCategoryServices */
        $storeCategoryServices = app()->make(StoreCategoryServices::class);
        $cateList = $storeCategoryServices->getCateArray($cateIds);
        foreach ($list as &$item) {
            $cateName = array_filter($cateList, function ($val) use ($item) {
                if (in_array($val['id'], explode(',', $item['cate_id']))) {
                    return $val;
                }
            });
            $item['cate_name'] = implode(',', array_column($cateName, 'cate_name'));
            $item['store_name'] = $item['title'];
            $item['price'] = floatval($item['price']);
            $item['is_product_type'] = 1;
        }
        return compact('count', 'list');
    }

    /**
     * Sản phẩm săn giảm giá trang chủ
     * @param $where
     * @return array
     */
    public function getHomeList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $where['is_show'] = 1;
        $data = [];
        $list = $this->dao->getHomeList($where, $page, $limit);
        foreach ($list as &$item) {
            $item['price'] = floatval($item['price']);
        }
        $data['list'] = $list;
        return $data;
    }

    /**
     * Lấy chi tiết săn giảm giá ở phía người dùng
     * @param Request $request
     * @param int $id
     * @param int $bargainUid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBargain(Request $request, int $id, int $bargainUid)
    {
        /** @var StoreProductAttrServices $storeProductAttrServices */
        $storeProductAttrServices = app()->make(StoreProductAttrServices::class);
        /** @var StoreOrderServices $orderService */
        $orderService = app()->make(StoreOrderServices::class);
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);

        //Lấy thông tin sản phẩm săn giảm giá
        $bargain = $this->dao->getOne(['id' => $id], '*', ['description']);
        if (!$bargain) throw new ApiException('Sản phẩm săn giảm giá không tồn tại');
        if ($bargain['stop_time'] < time()) throw new ApiException('Săn giảm giá đã kết thúc');
        list($productAttr, $productValue) = $storeProductAttrServices->getProductAttrDetail($id, $request->uid(), 0, 2, $bargain['product_id']);
        foreach ($productValue as $v) {
            $bargain['attr'] = $v;
        }
        $bargain['time'] = time();
        $bargain = get_thumb_water($bargain);
        $bargain['small_image'] = $bargain['image'];
        $data['bargain'] = $bargain;

        //Ghi dữ liệu xem và chia sẻ
        $this->dao->addBargain($id, 'look');

        //Dữ liệu người dùng
        $user = $request->user();
        $data['userInfo']['uid'] = $user['uid'];
        $data['userInfo']['nickname'] = $user['nickname'];
        $data['userInfo']['avatar'] = $user['avatar'];

        //Dữ liệu săn giảm giá
        $userBargainInfo = $bargainUserService->helpCount($request, $id, $bargainUid);
        //Tổng số đơn hàng săn giảm giá người dùng đã tạo
        $userBargainInfo['bargainOrderCount'] = $orderService->count(['bargain_id' => $id, 'uid' => $user['uid']]);
        //Tổng số lần săn giảm giá của người dùng
        $userBargainInfo['bargainCount'] = $bargainUserService->count(['bargain_id' => $id, 'uid' => $user['uid'], 'is_del' => 0]);
        //Kiểm tra trạng thái săn giảm giá
        if (($userBargainInfo['bargainCount'] == 0 || $userBargainInfo['bargainCount'] == $userBargainInfo['bargainOrderCount']) //Chưa từng khởi tạo săn giảm giá, hoặc số lần khởi tạo bằng số đơn hàng của sản phẩm săn giảm giá tương ứng
            && $bargain['people_num'] > $userBargainInfo['bargainCount'] //Số lần được khởi tạo săn giảm giá của sản phẩm lớn hơn số lần đã khởi tạo
            && $userBargainInfo['price'] > 0 //Số tiền còn lại lớn hơn 0
            && $request->uid() == $bargainUid) { //Là săn giảm giá của chính mình
            $userBargainInfo['bargainType'] = 1; //Người dùng khởi tạo săn giảm giá
        } elseif ($userBargainInfo['bargainCount'] > $userBargainInfo['bargainOrderCount'] //Số lần khởi tạo săn giảm giá lớn hơn số đơn hàng đã tạo
            && $userBargainInfo['price'] > 0 //Số tiền còn lại lớn hơn 0
            && $request->uid() == $bargainUid) { //Là săn giảm giá của chính mình
            $userBargainInfo['bargainType'] = 2; //Gửi cho bạn bè để mời giúp giảm giá
        } elseif ($userBargainInfo['userBargainStatus'] //Người dùng có thể săn giảm giá
            && $userBargainInfo['price'] > 0 //Số tiền còn lại lớn hơn 0
            && $request->uid() != $bargainUid) { //Không phải săn giảm giá của chính mình
            $userBargainInfo['bargainType'] = 3; //Giúp bạn giảm giá
        } elseif ($userBargainInfo['userBargainStatus'] //Người dùng có thể săn giảm giá
            && $userBargainInfo['price'] == 0 //Số tiền còn lại lớn hơn 0
            && $request->uid() != $bargainUid) { //Không phải săn giảm giá của chính mình
            $userBargainInfo['bargainType'] = 4; //Bạn bè đã hoàn thành
        } elseif (!$userBargainInfo['userBargainStatus'] //Người dùng không thể săn giảm giá
            && $request->uid() != $bargainUid) { //Không phải săn giảm giá của chính mình
            $userBargainInfo['bargainType'] = 5; //Đã giúp bạn giảm giá
        } elseif ($userBargainInfo['price'] == 0 //Số tiền còn lại bằng 0
            && $request->uid() == $bargainUid //Là săn giảm giá của chính mình
            && $userBargainInfo['status'] != 3) { //Chưa tạo đơn hàng
            $userBargainInfo['bargainType'] = 6; //Thanh toán ngay
        } else {
            $userBargainInfo['bargainType'] = 1; //Thanh toán ngay
        }
        $data['userBargainInfo'] = $userBargainInfo;
        $data['bargain']['price'] = bcsub($data['bargain']['price'], (string)$userBargainInfo['alreadyPrice'], 2);
        $data['bargain']['product_is_show'] = app()->make(StoreProductServices::class)->value($data['bargain']['product_id'], 'is_show');

        //Sự kiện người dùng truy cập
        event('UserVisitListener', [$user['uid'], $id, 'bargain', $bargain['product_id'], 'view']);

        //Lịch sử xem
        ProductLogJob::dispatch(['visit', ['uid' => $user['uid'], 'product_id' => $bargain['product_id']]]);
        return $data;
    }

    /**
     * Kiểm tra săn giảm giá có thể thanh toán hay không
     * @param int $bargainId
     * @param int $uid
     */
    public function checkBargainUser(int $bargainId, int $uid)
    {
        /** @var StoreBargainUserServices $bargainUserServices */
        $bargainUserServices = app()->make(StoreBargainUserServices::class);
        $bargainUserInfo = $bargainUserServices->getOne(['uid' => $uid, 'bargain_id' => $bargainId, 'status' => 1, 'is_del' => 0]);
        if (!$bargainUserInfo)
            throw new ApiException('Săn giảm giá thất bại');
        $bargainUserTableId = $bargainUserInfo['id'];
        if ($bargainUserInfo['bargain_price_min'] < bcsub((string)$bargainUserInfo['bargain_price'], (string)$bargainUserInfo['price'], 2)) {
            throw new ApiException('Săn giảm giá chưa thành công');
        }
        if ($bargainUserInfo['status'] == 3)
            throw new ApiException('Lượt săn giảm giá đã được thanh toán');
        /** @var StoreProductAttrValueServices $attrValueServices */
        $attrValueServices = app()->make(StoreProductAttrValueServices::class);
        $res = $attrValueServices->getOne(['product_id' => $bargainId, 'type' => 2]);
        if (!$this->validBargain($bargainId) || !$res) {
            throw new ApiException('Sản phẩm này đã ngừng bán hoặc bị xóa');
        }
        $StoreBargainInfo = $this->dao->get($bargainId);
        if (1 > $res['quota']) {
            throw new ApiException('Sản phẩm này không đủ tồn kho');
        }
        $product_stock = $attrValueServices->value(['product_id' => $StoreBargainInfo['product_id'], 'suk' => $res['suk'], 'type' => 0], 'stock');
        if ($product_stock < 1) {
            throw new ApiException('Sản phẩm này không đủ tồn kho');
        }
        //Sửa trạng thái săn giảm giá
        $this->setBargainUserStatus($bargainId, $uid, $bargainUserTableId);
        return true;
    }

    /**
     * Sửa trạng thái săn giảm giá
     * @param int $bargainId
     * @param int $uid
     * @param int $bargainUserTableId
     * @return bool|\crmeb\basic\BaseModel
     */
    public function setBargainUserStatus(int $bargainId, int $uid, int $bargainUserTableId)
    {
        if (!$bargainId || !$uid) return false;
        if (!$bargainUserTableId) return false;
        /** @var StoreBargainUserServices $bargainUserServices */
        $bargainUserServices = app()->make(StoreBargainUserServices::class);
        $count = $bargainUserServices->count(['id' => $bargainUserTableId, 'uid' => $uid, 'bargain_id' => $bargainId, 'status' => 1]);
        if (!$count) return false;
        $userPrice = $bargainUserServices->value(['id' => $bargainUserTableId, 'uid' => $uid, 'bargain_id' => $bargainId, 'status' => 1], 'price');
        $price = $bargainUserServices->get($bargainUserTableId, ['bargain_price', 'bargain_price_min']);
        $price = bcsub($price['bargain_price'], $price['bargain_price_min'], 2);
        if (bcsub($price, $userPrice, 2) > 0) {
            return false;
        }
        return $bargainUserServices->updateBargainStatus($bargainUserTableId);
    }

    /**
     * Tạo lượt săn giảm giá
     * @param int $uid
     * @param int $bargainId
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setBargain(int $uid, int $bargainId)
    {
        if (!$bargainId) throw new ApiException('Thao tác không hợp lệ');
        $bargainInfo = $this->dao->getOne([
            ['is_del', '=', 0],
            ['status', '=', 1],
            ['start_time', '<', time()],
            ['stop_time', '>', time()],
            ['id', '=', $bargainId],
        ]);
        if (!$bargainInfo) throw new ApiException('Săn giảm giá đã kết thúc');
        $bargainInfo = $bargainInfo->toArray();
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        $count = $bargainUserService->count(['bargain_id' => $bargainId, 'uid' => $uid, 'is_del' => 0, 'status' => 1]);
        if ($count === false) {
            throw new ApiException('Thao tác không hợp lệ');
        } else {
            /** @var StoreBargainUserHelpServices $bargainUserHelpService */
            $bargainUserHelpService = app()->make(StoreBargainUserHelpServices::class);
            $count = $bargainUserService->count(['uid' => $uid, 'bargain_id' => $bargainId, 'is_del' => 0]);
            if ($count >= $bargainInfo['num']) throw new ApiException('Bạn không thể tạo thêm lượt săn giảm giá cho sản phẩm này');
            return $this->transaction(function () use ($bargainUserService, $bargainUserHelpService, $bargainId, $uid, $bargainInfo) {
                $bargainUserInfo = $bargainUserService->setBargain($bargainId, $uid, $bargainInfo);
                $price = $bargainUserHelpService->setBargainRecord($uid, $bargainUserInfo->toArray(), $bargainInfo);
                return ['bargainUserInfo' => $bargainUserInfo, 'price' => $price];
            });
        }
    }

    /**
     * Tham gia săn giảm giá
     * @param int $uid
     * @param int $bargainId
     * @param int $bargainUserUid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setHelpBargain(int $uid, int $bargainId, int $bargainUserUid)
    {
        if (!$bargainId || !$bargainUserUid) throw new ApiException('Tham số không hợp lệ');
        $bargainInfo = $this->dao->getOne([
            ['is_del', '=', 0],
            ['status', '=', 1],
            ['start_time', '<', time()],
            ['stop_time', '>', time()],
            ['id', '=', $bargainId],
        ]);
        if (!$bargainInfo) throw new ApiException('Săn giảm giá đã kết thúc');
        $bargainInfo = $bargainInfo->toArray();
        /** @var StoreBargainUserHelpServices $userHelpService */
        $userHelpService = app()->make(StoreBargainUserHelpServices::class);
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        $bargainUserTableId = $bargainUserService->getBargainUserTableId((int)$bargainId, (int)$bargainUserUid);
        if (!$bargainUserTableId) throw new ApiException('Lượt chia sẻ này chưa bắt đầu săn giảm giá');
        $bargainUserInfo = $bargainUserService->get($bargainUserTableId)->toArray();
        $count = $userHelpService->isBargainUserHelpCount($bargainId, $bargainUserTableId, $uid);
        if (!$count) throw new ApiException('Bạn đã giúp giảm giá cho lượt săn này rồi');
        $price = $userHelpService->setBargainRecord($uid, $bargainUserInfo, $bargainInfo);
        if ($price) {
            if (!$bargainUserService->getSurplusPrice($bargainUserTableId, 1)) {
                event('NoticeListener', [['uid' => $bargainUserUid, 'bargainInfo' => $bargainInfo, 'bargainUserInfo' => $bargainUserInfo,], 'bargain_success']);
            }
        }
        return ['bargainUserInfo' => $bargainUserInfo, 'price' => $price];
    }

    /**
     * Giảm tồn kho, tăng lượt bán
     * @param int $num
     * @param int $bargainId
     * @param string $unique
     * @return bool
     */
    public function decBargainStock(int $num, int $bargainId, string $unique)
    {
        $product_id = $this->dao->value(['id' => $bargainId], 'product_id');
        if ($unique) {
            /** @var StoreProductAttrValueServices $skuValueServices */
            $skuValueServices = app()->make(StoreProductAttrValueServices::class);
            //Giảm tồn kho sku sản phẩm săn giảm giá, tăng lượt bán
            $res = false !== $skuValueServices->decProductAttrStock($bargainId, $unique, $num, 2);
            //Giảm tồn kho và lượt bán sản phẩm săn giảm giá
            $res = $res && $this->dao->decStockIncSales(['id' => $bargainId, 'type' => 2], $num);
            //Giảm tồn kho sku sản phẩm thường, tăng lượt bán
            $suk = $skuValueServices->value(['unique' => $unique, 'product_id' => $bargainId], 'suk');
            $productUnique = $skuValueServices->value(['suk' => $suk, 'product_id' => $product_id, 'type' => 0], 'unique');
            if ($productUnique) {
                $res = $res && $skuValueServices->decProductAttrStock($product_id, $productUnique, $num);
            }
        } else {
            //Giảm tồn kho và lượt bán sản phẩm săn giảm giá
            $res = false !== $this->dao->decStockIncSales(['id' => $bargainId, 'type' => 2], $num);
        }
        /** @var StoreProductServices $services */
        $services = app()->make(StoreProductServices::class);
        //Giảm tồn kho sản phẩm thường, tăng lượt bán
        $res = $res && $services->decProductStock($num, $product_id);
        return $res;
    }

    /**
     * Giảm lượt bán, tăng tồn kho
     * @param int $num
     * @param int $bargainId
     * @param string $unique
     * @return bool
     */
    public function incBargainStock(int $num, int $bargainId, string $unique)
    {
        $product_id = $this->dao->value(['id' => $bargainId], 'product_id');
        if ($unique) {
            /** @var StoreProductAttrValueServices $skuValueServices */
            $skuValueServices = app()->make(StoreProductAttrValueServices::class);
            //Giảm lượt bán sku sản phẩm săn giảm giá, tăng tồn kho và số lượng mua tối đa
            $res = false !== $skuValueServices->incProductAttrStock($bargainId, $unique, $num, 2);
            //Giảm lượt bán sản phẩm săn giảm giá, tăng tồn kho
            $res = $res && $this->dao->incStockDecSales(['id' => $bargainId, 'type' => 2], $num);
            //Giảm lượt bán sku sản phẩm thường, tăng tồn kho
            $suk = $skuValueServices->value(['unique' => $unique, 'product_id' => $bargainId], 'suk');
            $productUnique = $skuValueServices->value(['suk' => $suk, 'product_id' => $product_id], 'unique');
            if ($productUnique) {
                $res = $res && $skuValueServices->incProductAttrStock($product_id, $productUnique, $num);
            }
        } else {
            //Giảm lượt bán sản phẩm săn giảm giá, tăng tồn kho
            $res = false !== $this->dao->incStockDecSales(['id' => $bargainId, 'type' => 2], $num);
        }
        /** @var StoreProductServices $services */
        $services = app()->make(StoreProductServices::class);
        //Giảm tồn kho sản phẩm thường, tăng lượt bán
        $res = $res && $services->incProductStock($num, $product_id);
        return $res;
    }

    /**
     * Chia sẻ săn giảm giá
     * @param $bargainId
     * @param $user
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function poster($bargainId, $user, $from)
    {
        $storeBargainInfo = $this->dao->get($bargainId, ['title', 'image', 'price']);
        if (!$storeBargainInfo) {
            throw new ApiException('Không tìm thấy thông tin săn giảm giá');
        }
        /** @var StoreBargainUserServices $services */
        $services = app()->make(StoreBargainUserServices::class);
        $bargainUser = $services->get(['bargain_id' => $bargainId, 'uid' => $user['uid']], ['price', 'bargain_price_min']);
        if (!$bargainUser) {
            throw new ApiException('Không tìm thấy thông tin săn giảm giá của người dùng');
        }
        try {
            $siteUrl = sys_config('site_url');
            $data['title'] = $storeBargainInfo['title'];
            $data['image'] = $storeBargainInfo['image'];
            $data['price'] = bcsub($storeBargainInfo['price'], $bargainUser['price'], 2);
            $data['label'] = 'Đã giảm còn';
            $price = bcsub($storeBargainInfo['price'], $bargainUser['price'], 2);
            $data['msg'] = 'Còn thiếu' . (bcsub($price, $bargainUser['bargain_price_min'], 2)) . 'đ nữa là săn giảm giá thành công';
            /** @var SystemAttachmentServices $systemAttachmentServices */
            $systemAttachmentServices = app()->make(SystemAttachmentServices::class);
            if ($from == 'wechat') {
                $name = $bargainId . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_bargain_share_wap.jpg';
                //OA WeChat
                $imageInfo = $systemAttachmentServices->getInfo(['name' => $name]);
                if (!$imageInfo) {
                    $codeUrl = set_http_type($siteUrl . '/pages/activity/goods_bargain_details/index?id=' . $bargainId . '&bargain=' . $user['uid'] . '&spread=' . $user['uid'], 1);//Liên kết mã QR
                    $imageInfo = PosterServices::getQRCodePath($codeUrl, $name);
                    if (is_string($imageInfo)) {
                        throw new ApiException('Tạo mã QR thất bại');
                    }
                    $systemAttachmentServices->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => $imageInfo['time'],
                        'pid' => 1,
                        'type' => 1
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                $data['url'] = $url;
                if ($imageInfo['image_type'] == 1) $data['url'] = $siteUrl . $url;
                $posterImage = PosterServices::setShareMarketingPoster($data, 'wap/activity/bargain/poster');
                if (!is_array($posterImage)) {
                    throw new ApiException('Tạo poster thất bại');
                }
                $systemAttachmentServices->save([
                    'name' => $posterImage['name'],
                    'att_dir' => $posterImage['dir'],
                    'satt_dir' => $posterImage['thumb_path'],
                    'att_size' => $posterImage['size'],
                    'att_type' => $posterImage['type'],
                    'image_type' => $posterImage['image_type'],
                    'module_type' => 2,
                    'time' => $posterImage['time'],
                    'pid' => 1,
                    'type' => 1
                ]);
                if ($posterImage['image_type'] == 1) $posterImage['dir'] = $siteUrl . $posterImage['dir'];
                $wapPosterImage = set_http_type($posterImage['dir'], 1);//Poster giới thiệu OA WeChat
                return $wapPosterImage;
            } else {
                //Mini Program
                $name = $bargainId . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_bargain_share_routine.jpg';
                $imageInfo = $systemAttachmentServices->getInfo(['name' => $name]);
                if (!$imageInfo) {
                    $valueData = 'id=' . $bargainId . '&bargain=' . $user['uid'];
                    /** @var UserServices $userServices */
                    $userServices = app()->make(UserServices::class);
                    if ($userServices->checkUserPromoter((int)$user['uid'], $user)) {
                        $valueData .= '&spread=' . $user['uid'];
                    }
                    $res = MiniProgramService::appCodeUnlimitService($valueData, 'pages/activity/goods_bargain_details/index', 280);
                    if (!$res) throw new ApiException('Tạo mã QR thất bại');
                    $uploadType = (int)sys_config('upload_type', 1);
                    $upload = UploadService::init();
                    $res = (string)EntityBody::factory($res);
                    $res = $upload->to('routine/activity/bargain/code')->validate()->setAuthThumb(false)->stream($res, $name);
                    if ($res === false) {
                        throw new ApiException($upload->getError());
                    }
                    $imageInfo = $upload->getUploadInfo();
                    $imageInfo['image_type'] = $uploadType;
                    if ($imageInfo['image_type'] == 1) $remoteImage = PosterServices::remoteImage($siteUrl . $imageInfo['dir']);
                    else $remoteImage = PosterServices::remoteImage($imageInfo['dir']);
                    if (!$remoteImage['status']) throw new ApiException('Tạo mã QR thất bại');
                    $systemAttachmentServices->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => time(),
                        'pid' => 1,
                        'type' => 1
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                $data['url'] = $url;
                if ($imageInfo['image_type'] == 1)
                    $data['url'] = $siteUrl . $url;
                $posterImage = PosterServices::setShareMarketingPoster($data, 'routine/activity/bargain/poster');
                if (!is_array($posterImage)) throw new ApiException('Tạo poster thất bại');
                $systemAttachmentServices->save([
                    'name' => $posterImage['name'],
                    'att_dir' => $posterImage['dir'],
                    'satt_dir' => $posterImage['thumb_path'],
                    'att_size' => $posterImage['size'],
                    'att_type' => $posterImage['type'],
                    'image_type' => $posterImage['image_type'],
                    'module_type' => 2,
                    'time' => $posterImage['time'],
                    'pid' => 1,
                    'type' => 1
                ]);
                if ($posterImage['image_type'] == 1) $posterImage['dir'] = $siteUrl . $posterImage['dir'];
                $routinePosterImage = set_http_type($posterImage['dir'], 0);//Poster giới thiệu Mini Program
                return $routinePosterImage;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Lấy thông tin poster săn giảm giá
     * @param int $bargainId
     * @param $user
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function posterInfo(int $bargainId, $user)
    {
        $storeBargainInfo = $this->dao->get($bargainId, ['title', 'image', 'price']);
        if (!$storeBargainInfo) {
            throw new ApiException('Không tìm thấy thông tin săn giảm giá');
        }
        /** @var StoreBargainUserServices $services */
        $services = app()->make(StoreBargainUserServices::class);
        $bargainUser = $services->get(['bargain_id' => $bargainId, 'uid' => $user['uid'], 'status' => 1], ['price', 'bargain_price_min']);
        if (!$bargainUser) {
            throw new ApiException('Không tìm thấy thông tin săn giảm giá của người dùng');
        }
        $data['url'] = '';
        $data['title'] = $storeBargainInfo['title'];
        $data['image'] = $storeBargainInfo['image'];
        $data['price'] = bcsub($storeBargainInfo['price'], $bargainUser['price'], 2);
        $data['label'] = 'Đã giảm còn';
        $price = bcsub($storeBargainInfo['price'], $bargainUser['price'], 2);
        $data['msg'] = 'Còn thiếu' . (bcsub($price, $bargainUser['bargain_price_min'], 2)) . 'đ nữa là săn giảm giá thành công';
        //Chỉ tạo mã QR ở phía Mini Program
        if (\request()->isRoutine()) {
            try {
                /** @var SystemAttachmentServices $systemAttachmentServices */
                $systemAttachmentServices = app()->make(SystemAttachmentServices::class);
                //Mini Program
                $name = $bargainId . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_bargain_share_routine.jpg';
                $siteUrl = sys_config('site_url');
                $imageInfo = $systemAttachmentServices->getInfo(['name' => $name]);
                if (!$imageInfo) {
                    $valueData = 'id=' . $bargainId . '&bargain=' . $user['uid'];
                    /** @var UserServices $userServices */
                    $userServices = app()->make(UserServices::class);
                    if ($userServices->checkUserPromoter((int)$user['uid'], $user)) {
                        $valueData .= '&spread=' . $user['uid'];
                    }
                    $res = MiniProgramService::appCodeUnlimitService($valueData, 'pages/activity/goods_bargain_details/index', 280);
                    if (!$res) throw new ApiException('Tạo mã QR thất bại');
                    $uploadType = (int)sys_config('upload_type', 1);
                    $upload = UploadService::init();
                    $res = (string)EntityBody::factory($res);
                    $res = $upload->to('routine/activity/bargain/code')->validate()->setAuthThumb(false)->stream($res, $name);
                    if ($res === false) {
                        throw new ApiException($upload->getError());
                    }
                    $imageInfo = $upload->getUploadInfo();
                    $imageInfo['image_type'] = $uploadType;
                    if ($imageInfo['image_type'] == 1) $remoteImage = PosterServices::remoteImage($siteUrl . $imageInfo['dir']);
                    else $remoteImage = PosterServices::remoteImage($imageInfo['dir']);
                    if (!$remoteImage['status']) throw new ApiException($remoteImage['msg']);
                    $systemAttachmentServices->save([
                        'name' => $imageInfo['name'],
                        'att_dir' => $imageInfo['dir'],
                        'satt_dir' => $imageInfo['thumb_path'],
                        'att_size' => $imageInfo['size'],
                        'att_type' => $imageInfo['type'],
                        'image_type' => $imageInfo['image_type'],
                        'module_type' => 2,
                        'time' => time(),
                        'pid' => 1,
                        'type' => 1
                    ]);
                    $url = $imageInfo['dir'];
                } else $url = $imageInfo['att_dir'];
                if ($imageInfo['image_type'] == 1) {
                    $data['url'] = $siteUrl . $url;
                } else {
                    $data['url'] = $url;
                }
            } catch (\Throwable $e) {
            }
        } else {
            if (sys_config('share_qrcode', 0) && request()->isWechat()) {
                /** @var QrcodeServices $qrcodeService */
                $qrcodeService = app()->make(QrcodeServices::class);
                $data['url'] = $qrcodeService->getTemporaryQrcode('bargain-' . $bargainId . '-' . $user['uid'], $user['uid'])->url;
            }
        }
        return $data;
    }

    /**
     * Kiểm tra giới hạn tồn kho khi đặt hàng săn giảm giá
     * @param int $uid
     * @param int $bargainId
     * @param int $cartNum
     * @param string $unique
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkBargainStock(int $uid, int $bargainId, int $cartNum = 1, string $unique = '')
    {
        if (!$this->validBargain($bargainId)) {
            throw new ApiException('Sản phẩm này đã ngừng bán hoặc bị xóa');
        }
        /** @var StoreProductAttrValueServices $attrValueServices */
        $attrValueServices = app()->make(StoreProductAttrValueServices::class);
        $attrInfo = $attrValueServices->getOne(['product_id' => $bargainId, 'type' => 2]);
        if (!$attrInfo || $attrInfo['product_id'] != $bargainId) {
            throw new ApiException('Vui lòng chọn thuộc tính sản phẩm hợp lệ');
        }
        $productInfo = $this->dao->get($bargainId, ['*', 'title as store_name']);
        /** @var StoreBargainUserServices $bargainUserService */
        $bargainUserService = app()->make(StoreBargainUserServices::class);
        $bargainUserInfo = $bargainUserService->getOne(['uid' => $uid, 'bargain_id' => $bargainId, 'status' => 1, 'is_del' => 0]);
        if ($bargainUserInfo['bargain_price_min'] < bcsub((string)$bargainUserInfo['bargain_price'], (string)$bargainUserInfo['price'], 2)) {
            throw new ApiException('Giá săn giảm giá không được thấp hơn giá thấp nhất');
        }
        $unique = $attrInfo['unique'];
        if ($cartNum > $attrInfo['quota']) {
            throw new ApiException('Sản phẩm này không đủ tồn kho');
        }
        return [$attrInfo, $unique, $productInfo, $bargainUserInfo];
    }

    /**
     * Thống kê săn giảm giá
     * @param $id
     * @return array
     */
    public function bargainStatistics($id)
    {
        /** @var StoreBargainUserServices $bargainUser */
        $bargainUser = app()->make(StoreBargainUserServices::class);
        /** @var StoreBargainUserHelpServices $bargainUserHelp */
        $bargainUserHelp = app()->make(StoreBargainUserHelpServices::class);
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        $people_count = $bargainUserHelp->count(['bargain_id' => $id]);
        $spread_count = $bargainUserHelp->count(['bargain_id' => $id, 'type' => 0]);
        $start_count = $bargainUser->count(['bargain_id' => $id]);
        $success_count = $bargainUser->count(['bargain_id' => $id, 'status' => 3]);
        $pay_price = $orderServices->sum([['bargain_id', '=', $id], ['paid', '=', 1], ['refund_type', 'in', [0, 3]], ['is_del', '=', 0]], 'pay_price', false);
        $pay_count = $orderServices->getDistinctCount([['bargain_id', '=', $id], ['paid', '=', 1], ['refund_type', 'in', [0, 3]], ['is_del', '=', 0]], 'uid', false);
        $pay_rate = $start_count > 0 ? bcmul(bcdiv((string)$pay_count, (string)$start_count, 2), '100', 2) : 0;
        return compact('people_count', 'spread_count', 'start_count', 'success_count', 'pay_price', 'pay_count', 'pay_rate');
    }

    /**
     * Danh sách săn giảm giá
     * @param $id
     * @param array $where
     * @return array
     */
    public function bargainStatisticsList($id, $where = [])
    {
        /** @var StoreBargainUserServices $bargainUser */
        $bargainUser = app()->make(StoreBargainUserServices::class);
        $where['bargain_id'] = $id;
        return $bargainUser->bargainUserList($where);
    }

    /**
     * Đơn săn giảm giá
     * @param $id
     * @param array $where
     * @return array
     */
    public function bargainStatisticsOrder($id, $where = [])
    {
        /** @var StoreOrderServices $orderServices */
        $orderServices = app()->make(StoreOrderServices::class);
        [$page, $limit] = $this->getPageValue();
        $where = $where + ['paid' => 1, 'refund_status' => 0, 'is_del' => 0];
        $list = $orderServices->bargainStatisticsOrder($id, $where, $page, $limit);
        $count = $orderServices->bargainStatisticsOrderCount($id, $where);
        foreach ($list as &$item) {
            if ($item['status'] == 0) {
                if ($item['paid'] == 0) {
                    $item['status'] = 'Chưa thanh toán';
                } else {
                    $item['status'] = 'Chưa giao hàng';
                }
            } elseif ($item['status'] == 1) {
                $item['status'] = 'Chờ nhận hàng';
            } elseif ($item['status'] == 2) {
                $item['status'] = 'Chờ đánh giá';
            } elseif ($item['status'] == 3) {
                $item['status'] = 'Đã hoàn thành';
            } elseif ($item['status'] == -2) {
                $item['status'] = 'Đã hoàn tiền';
            } else {
                $item['status'] = 'Không xác định';
            }
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '';
        }
        return compact('list', 'count');
    }
}
