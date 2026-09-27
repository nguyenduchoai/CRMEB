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

namespace app\services\product\product;


use app\dao\product\product\StoreProductReplyDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\FormBuilder as Form;
use think\facade\Route as Url;

/**
 * Class StoreProductReplyService
 * @package app\services\product\product
 * @method int count(array $where = []) Lấy số lượng bản ghi
 * @method save(array $data) Lưu dữ liệu
 */
class StoreProductReplyServices extends BaseServices
{
    /**
     * StoreProductReplyServices constructor.
     * @param StoreProductReplyDao $dao
     */
    public function __construct(StoreProductReplyDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách đánh giá
     * @param array $where
     * @return array
     */
    public function sysPage(array $where)
    {
        /** @var StoreProductReplyStoreProductServices $storeProductReplyStoreProductServices */
        $storeProductReplyStoreProductServices = app()->make(StoreProductReplyStoreProductServices::class);
        $data = $storeProductReplyStoreProductServices->getProductReplyList($where);
        foreach ($data['list'] as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);
            $item['time'] = time_tran(strtotime($item['add_time']));
            $item['create_time'] = $item['add_time'];
            $item['score'] = ($item['product_score'] + $item['service_score']) / 2;
        }
        return $data;
    }

    /**
     * Tạo form đánh giá ảo
     * @param int $product_id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createForm(int $product_id)
    {
        if ($product_id == 0) {
            $field[] = Form::frameImage('image', 'Sản phẩm', Url::buildUrl(config('app.admin_prefix', 'admin') . '/store.StoreProduct/index', array('fodder' => 'image')))->icon('el-icon-picture-outline')->width('950px')->height('560px')->Props(['srcKey' => 'image', 'footer' => false]);
        } else {
            $field[] = Form::hidden('product_id', $product_id);
        }
        $field[] = Form::frameImage('avatar', 'Ảnh đại diện người dùng', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'avatar')))->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['footer' => false]);
        $field[] = Form::input('nickname', 'Tên người dùng')->col(24);
        $field[] = Form::input('comment', 'Nội dung đánh giá')->type('textarea');
        $field[] = Form::rate('product_score', 'Điểm sản phẩm', 0)->allowHalf(false);
        $field[] = Form::rate('service_score', 'Điểm dịch vụ', 0)->allowHalf(false);
        $field[] = Form::frameImages('pics', 'Hình ảnh đánh giá', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'pics', 'type' => 'many', 'maxLength' => 8)))->maxLength(8)->icon('el-icon-picture-outline')->width('950px')->height('560px')->props(['closeBtn' => false, 'okBtn' => false, 'footer' => false]);
        $field[] = Form::dateTime('add_time', 'Thời gian đánh giá', '')->placeholder('Vui lòng chọn thời gian đánh giá (không chọn sẽ mặc định là thời gian thêm hiện tại)')->style(['width' => '300px']);
        return create_form('Thêm đánh giá ảo', $field, Url::buildUrl('/product/reply/save_fictitious_reply'), 'POST');
    }

    /**
     * Thêm đánh giá ảo
     * @param array $data
     */
    public function saveReply(array $data)
    {
        $time = time();
        $data['uid'] = 0;
        $data['oid'] = 0;
        $data['unique'] = uniqid();
        $data['reply_type'] = 'product';
        $data['add_time'] = empty($data['add_time']) ? $time : strtotime($data['add_time']);
        $data['pics'] = json_encode($data['pics']);
        $data['status'] = 1;
        unset($data['image']);
        if ($data['add_time'] > $time) {
            throw new AdminException('Thời gian đánh giá phải trước thời điểm hiện tại');
        }
        $res = $this->dao->save($data);
        if (!$res) throw new AdminException('Thêm đánh giá ảo thất bại');
    }

    /**
     * Trả lời đánh giá
     * @param int $id
     * @param string $content
     */
    public function setReply(int $id, string $content)
    {
        if ($content == '') throw new AdminException('Vui lòng nhập nội dung trả lời');
        $save['merchant_reply_content'] = $content;
        $save['merchant_reply_time'] = time();
        $save['is_reply'] = 1;
        $res = $this->dao->update($id, $save);
        if (!$res) throw new AdminException('Trả lời thất bại');
    }

    /**
     * Xóa
     * @param int $id
     */
    public function del(int $id)
    {
        $res = $this->dao->update($id, ['is_del' => 1]);
        if (!$res) throw new AdminException('Xóa thất bại');
    }

    /**
     * Lấy đánh giá tốt nhất gần đây
     * @param int $productId
     * @return array|\think\Model|null
     */
    public function getRecProductReply(int $productId)
    {
        $res = $this->dao->getProductReply($productId);

        if ($res) {
            $res = $res->toArray();
            if ($res['suk'] == '') {
                $res['suk'] = isset($res['cart_info']['productInfo']['attrInfo']) ? $res['cart_info']['productInfo']['attrInfo']['suk'] : '';
            }
            $res['nickname'] = anonymity($res['nickname']);
            $res['merchant_reply_time'] = date('Y-m-d H:i', $res['merchant_reply_time']);
            $res['add_time'] = time_tran($res['add_time']);
            $res['star'] = bcadd($res['product_score'], $res['service_score'], 2);
            $res['star'] = bcdiv($res['star'], '2', 0);
            $res['comment'] = $res['comment'] ?: 'Người dùng này chưa viết đánh giá';
            $res['pics'] = is_string($res['pics']) ? json_decode($res['pics'], true) : $res['pics'];
            unset($res['cartInfo']);
        }
        return $res;
    }

    /**
     * Lấy dữ liệu đánh giá: tổng số đánh giá, tổng số đánh giá tốt, tỷ lệ đánh giá tốt
     * @param int $id
     * @return array
     */
    public function getProductReplyData(int $id)
    {
        $goodReply = 0;
        $replyCount = $this->dao->replyCount($id);
        if ($replyCount) {
            $goodReply = $this->dao->replyCount($id, 1);
            if ($goodReply) {
                $replyChance = bcmul((string)bcdiv((string)$goodReply, (string)$replyCount, 2), '100', 0);
            } else {
                $replyChance = 0;
            }
        } else {
            $replyChance = 100;
        }
        return [$replyCount, $goodReply, $replyChance];
    }

    /**Số lượng đánh giá sản phẩm
     * @return int
     */
    public function replyCount()
    {
        return $this->dao->count(['is_reply' => 0, 'is_del' => 0]);
    }

    /**
     * Lấy số lượng đánh giá sản phẩm
     * @param int $id
     * @return mixed
     */
    public function productReplyCount(int $id)
    {
        $data['sum_count'] = $this->dao->replyCount($id);
        $data['good_count'] = $this->dao->replyCount($id, 1);
        $data['in_count'] = $this->dao->replyCount($id, 2);
        $data['poor_count'] = $this->dao->replyCount($id, 3);
        if ($data['sum_count'] != 0) {
            $data['reply_chance'] = bcdiv($data['good_count'], $data['sum_count'], 2);
            $num = ($this->dao->sum(['product_id' => $id, 'is_del' => 0], 'service_score') + $this->dao->sum(['product_id' => $id, 'is_del' => 0], 'product_score')) / 2;
            $data['reply_star'] = bcdiv($num, $data['sum_count'], 0);
        } else {
            $data['reply_chance'] = 100;
            $data['reply_star'] = 5;
        }
        $data['reply_chance'] = $data['sum_count'] == 0 ? 100 : bcmul($data['reply_chance'], 100, 0);
        return $data;
    }

    /**
     * Lấy danh sách đánh giá sản phẩm
     * @param int $id
     * @param int $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getProductReplyList(int $id, int $type)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->replyList($id, $type, $page, $limit);
        foreach ($list as &$item) {
            $item['suk'] = $item['suk'] != '' ? $item['suk'] : (isset($item['cart_info']['productInfo']['attrInfo']) ? $item['cart_info']['productInfo']['attrInfo']['suk'] : '');
            $item['nickname'] = anonymity($item['nickname']);
            $item['merchant_reply_time'] = date('Y-m-d H:i', $item['merchant_reply_time']);
            $item['add_time'] = time_tran($item['add_time']);
            $item['star'] = bcadd($item['product_score'], $item['service_score'], 2);
            $item['star'] = bcdiv($item['star'], 2, 0);
            $item['comment'] = $item['comment'] ?: 'Người dùng này chưa viết đánh giá';
            $item['pics'] = is_string($item['pics']) ? json_decode($item['pics'], true) : $item['pics'];
            unset($item['cart_info']);
        }
        return $list;
    }
}
