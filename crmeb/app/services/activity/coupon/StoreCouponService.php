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

namespace app\services\activity\coupon;


use app\dao\activity\coupon\StoreCouponDao;
use app\services\BaseServices;
use app\services\order\StoreCartServices;
use app\services\product\product\StoreCategoryServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 * Class StoreCouponService
 * @package app\services\coupon
 * @method save(array $data)
 */
class StoreCouponService extends BaseServices
{
    public function __construct(StoreCouponDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách
     * @param array $where
     * @return array
     */
    public function getList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $list = $this->dao->getList($where, $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * Form thêm phiếu giảm giá
     * @param int $type
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm(int $type)
    {
        $f[] = Form::input('title', 'Tên phiếu giảm giá');
        switch ($type) {
            case 1://Phiếu theo danh mục
                $options = function () {
                    /** @var StoreCategoryServices $storeCategoryService */
                    $storeCategoryService = app()->make(StoreCategoryServices::class);
                    $list = $storeCategoryService->getTierList(1, 1);
                    $menus = [];
                    foreach (sort_list_tier($list) as $menu) {
                        $menus[] = ['value' => $menu['id'], 'label' => $menu['html'] . $menu['cate_name'], 'disabled' => false];
                    }

                    return $menus;
                };
                $f[] = Form::select('category_id', 'Chọn danh mục')->setOptions(Form::setOptions($options))->filterable(1)->col(12);
                break;
            case 2://Phiếu theo sản phẩm
                $f[] = Form::frameImages('image', 'Sản phẩm', Url::buildUrl(config('app.admin_prefix', 'admin') . '/store.StoreProduct/index', array('fodder' => 'image', 'type' => 'many')))->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['srcKey' => 'image', 'footer' => false]);
                $f[] = Form::hidden('product_id', '');
                break;
        }
        $f[] = Form::number('coupon_price', 'Mệnh giá phiếu giảm giá', 0)->min(0);
        $f[] = Form::number('use_min_price', 'Giá trị đơn tối thiểu để dùng phiếu', 0)->min(0);
        $f[] = Form::number('coupon_time', 'Thời hạn hiệu lực phiếu giảm giá', 0)->min(0);
        $f[] = Form::number('sort', 'Thứ tự sắp xếp')->value(0)->precision(0);
        $f[] = Form::radio('status', 'Trạng thái', 1)->options([['label' => 'Bật', 'value' => 1], ['label' => 'Tắt', 'value' => 0]]);
        $f[] = Form::hidden('type', $type);
        return create_form('Thêm phiếu giảm giá', $f, Url::buildUrl('/marketing/coupon/save'), 'POST');
    }

    /**
     * Form sửa mẫu phiếu giảm giá
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createIssue(int $id)
    {
        $res = $this->dao->getOne(['id' => $id, 'status' => 1, 'is_del' => 0]);
        if (!$res) throw new AdminException('Phiếu giảm giá đã phát hành đã hết hiệu lực hoặc không tồn tại!');
        $f = [];
        $f[] = Form::input('id', 'ID phiếu giảm giá', $id)->disabled(1);
        $f[] = Form::input('coupon_title', 'Tên phiếu giảm giá', $res['title'])->disabled(1);
        $f[] = Form::dateTimeRange('range_date', 'Thời gian nhận')->placeholder('Để trống nếu có hiệu lực vĩnh viễn');
        $f[] = Form::radio('is_permanent', 'Giới hạn số lượng', 1)->options([['label' => 'Không giới hạn', 'value' => 1], ['label' => 'Giới hạn số lượng', 'value' => 0]]);
        $f[] = Form::number('count', 'Số lượng phát hành', 0)->min(0)->placeholder('Để trống hoặc nhập 0 là không giới hạn');
        $f[] = Form::radio('is_type', 'Loại phiếu giảm giá', 0)->options([['label' => 'Phiếu thường', 'value' => 0], ['label' => 'Phiếu tặng kèm', 'value' => 1], ['label' => 'Phiếu người mới', 'value' => 2]]);
        $f[] = Form::number('full_reduction', 'Giá trị đơn để được tặng phiếu', 0)->min(0)->placeholder('Giá trị đơn tối thiểu để được tặng phiếu giảm giá');
        $f[] = Form::radio('status', 'Trạng thái', 1)->options([['label' => 'Bật', 'value' => 1], ['label' => 'Tắt', 'value' => 0]]);
        return create_form('Phát hành phiếu giảm giá', $f, $this->url('/marketing/coupon/issue/' . $id), 'POST');
    }

    /**
     * Phát hành phiếu giảm giá
     * @param int $id
     * @param int $_id
     * @param string $coupon_title
     * @param array $rangeTime
     * @param int $count
     * @param int $status
     * @param int $is_permanent
     * @param float $full_reduction
     * @param int $is_give_subscribe
     * @param int $is_full_give
     * @param int $is_type
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upIssue(int $id, int $_id, string $coupon_title, array $rangeTime, int $count, int $status, int $is_permanent, float $full_reduction, int $is_give_subscribe, int $is_full_give, int $is_type)
    {
        if ($is_type == 1) {
            $is_full_give = 1;
        } elseif ($is_type == 2) {
            $is_give_subscribe = 1;
        }
        if ($_id != $id) throw new AdminException('Thao tác thất bại, thông tin không khớp');
        if (!$count) $count = 0;
        $couponInfo = $this->dao->getOne(['id' => $id, 'status' => 1, 'is_del' => 0]);
        if (!$couponInfo) throw new AdminException('Phiếu giảm giá đã phát hành đã hết hiệu lực hoặc không tồn tại!');
        if (count($rangeTime) != 2) throw new AdminException('Vui lòng chọn khoảng thời gian hợp lệ');
        list($startTime, $endTime) = $rangeTime;
        if (!$startTime) $startTime = 0;
        if (!$endTime) $endTime = 0;
        if (!$startTime && $endTime) throw new AdminException('Vui lòng chọn thời gian bắt đầu hợp lệ');
        if ($startTime && !$endTime) throw new AdminException('Vui lòng chọn thời gian kết thúc hợp lệ');
        $data['cid'] = $id;
        $data['coupon_title'] = $coupon_title;
        $data['start_time'] = strtotime($startTime);
        $data['end_time'] = strtotime($endTime);
        $data['total_count'] = $count;
        $data['remain_count'] = $count;
        $data['is_permanent'] = $is_permanent;
        $data['status'] = $status;
        $data['is_give_subscribe'] = $is_give_subscribe;
        $data['is_full_give'] = $is_full_give;
        $data['full_reduction'] = $full_reduction;
        $data['is_del'] = 0;
        $data['add_time'] = time();
        $data['title'] = $couponInfo['title'];
        $data['integral'] = $couponInfo['integral'];
        $data['coupon_price'] = $couponInfo['coupon_price'];
        $data['use_min_price'] = $couponInfo['use_min_price'];
        $data['coupon_time'] = $couponInfo['coupon_time'];
        $data['product_id'] = $couponInfo['product_id'];
        $data['category_id'] = $couponInfo['category_id'];
        $data['type'] = $couponInfo->getData('type');
        /** @var StoreCouponIssueServices $storeCouponIssueService */
        $storeCouponIssueService = app()->make(StoreCouponIssueServices::class);
        $res = $storeCouponIssueService->save($data);
        $productIds = explode(',', $data['product_id']);
        if (count($productIds)) {
            $couponData = [];
            foreach ($productIds as $product_id) {
                $couponData[] = ['product_id' => $product_id, 'coupon_id' => $res->id];
            }
            /** @var StoreCouponProductServices $storeCouponProductService */
            $storeCouponProductService = app()->make(StoreCouponProductServices::class);
            $storeCouponProductService->saveAll($couponData);
        }
        if (!$res) throw new AdminException('Phát hành phiếu giảm giá thất bại!');
    }

    /**
     * Phiếu giảm giá hết hiệu lực
     * @param int $id
     */
    public function invalid(int $id)
    {
        $res = $this->dao->update($id, ['status' => 0]);
        if (!$res) throw new AdminException('Thao tác thất bại');
        /** @var StoreCouponIssueServices $storeCouponIssueService */
        $storeCouponIssueService = app()->make(StoreCouponIssueServices::class);
        $storeCouponIssueService->update($id, ['status' => -1], 'cid');
    }

    /**
     * Lấy danh sách phiếu giảm giá có thể dùng khi đặt hàng
     * @param int $uid
     * @param $cartId
     * @param string $price
     * @param bool $new
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function beUsableCouponList(int $uid, $cartId, bool $new)
    {
        /** @var StoreCartServices $services */
        $services = app()->make(StoreCartServices::class);
        $cartGroup = $services->getUserProductCartListV1($uid, $cartId, $new);
        /** @var StoreCouponUserServices $coupServices */
        $coupServices = app()->make(StoreCouponUserServices::class);
        return $coupServices->getUsableCouponList($uid, $cartGroup);
    }


}
