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

namespace app\services\activity\bargain;

use app\Request;
use app\services\BaseServices;
use app\dao\activity\bargain\StoreBargainUserDao;

/**
 *
 * Class StoreBargainUserServices
 * @package app\services\activity
 * @method getAllCount(array $where)
 * @method count(array $where)
 * @method value(array $where, ?string $field)
 * @method getBargainUserTableId(int $bargainId, int $bargainUserUid)
 * @method update(int $bargainId, array $data)
 * @method getOne(array $where, ?string $field = '*', array $with = [])
 * @method updateBargainStatus(int $id, ?int $status = 3)
 */
class StoreBargainUserServices extends BaseServices
{

    /**
     * StoreBargainUserServices constructor.
     * @param StoreBargainUserDao $dao
     */
    public function __construct(StoreBargainUserDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * TODO Lấy mã người đang tham gia theo mã sản phẩm săn giảm giá
     * @param int $bargainId $bargainId  ID sản phẩm săn giảm giá
     * @param int $status $status  Trạng thái  1 đang diễn ra  2 kết thúc thất bại  3 kết thúc thành công
     * @return array
     */
    public function getUserIdList($bargainId = 0, $status = 1)
    {
        $ids = $this->dao->getColumn(['bargain_id' => $bargainId], 'id');
        /** @var StoreBargainUserHelpServices $bargainHelp */
        $bargainHelp = app()->make(StoreBargainUserHelpServices::class);
        return $bargainHelp->getCount([['bargain_user_id', 'in', $ids], ['bargain_id', '=', $bargainId]]);
    }

    /**
     * Lấy săn giảm giá
     * @param Request $request
     * @param int $bargainId
     * @param int $bargainUserUid
     * @return mixed
     */
    public function helpCount(Request $request, int $bargainId, int $bargainUserUid)
    {
        $bargainUserTableId = $this->dao->value(['bargain_id' => $bargainId, 'uid' => $bargainUserUid, 'is_del' => 0, 'status' => 1]);//TODO Lấy mã bản ghi người dùng tham gia săn giảm giá
        $data['userBargainStatus'] = $this->isBargainUserHelpCount($bargainId, $request->uid(), $bargainUserTableId);
        /** @var StoreBargainUserHelpServices $helpService */
        $helpService = app()->make(StoreBargainUserHelpServices::class);
        if ($bargainUserTableId) {
            $count = $helpService->count(['bargain_user_id' => $bargainUserTableId, 'bargain_id' => $bargainId]);//TODO Lấy tổng số người giúp giảm giá
            $price = $this->getSurplusPrice($bargainUserTableId, 1);//TODO Lấy số tiền săn giảm giá còn lại
            $alreadyPrice = $this->dao->value(['id' => $bargainUserTableId], 'price');//TODO Số tiền người dùng đã giảm được, lấy số tiền người dùng đã giảm được sau khi bạn bè giúp giảm giá
            $pricePercent = $this->getSurplusPrice($bargainUserTableId, 2);//TODO Lấy thanh tiến trình săn giảm giá
            $data['count'] = $count;
            $data['price'] = $price;
            $data['status'] = $this->dao->value(['id' => $bargainUserTableId], 'status') ?? 0;
            $data['alreadyPrice'] = $alreadyPrice;
            $data['pricePercent'] = $pricePercent > 10 ? $pricePercent : 10;
        } else {
            /** @var StoreBargainServices $bargainService */
            $bargainService = app()->make(StoreBargainServices::class);
            $data['count'] = 0;
            $data['price'] = $bargainService->value(['id' => $bargainId], 'price - min_price');
            $data['status'] = $this->dao->value(['id' => $bargainUserTableId], 'status') ?? 0;
            $data['alreadyPrice'] = 0;
            $data['pricePercent'] = 0;
        }
        return $data;
    }

    /**
     * Lấy trạng thái săn giảm giá
     * @param int $bargainId
     * @param int $bargainUserUid
     * @param int $bargainUserHelpUid
     * @param $bargainUserTableId
     * @return bool
     */
    public function isBargainUserHelpCount($bargainId, $bargainUserHelpUid, $bargainUserTableId)
    {
        /** @var StoreBargainUserHelpServices $userHelp */
        $userHelp = app()->make(StoreBargainUserHelpServices::class);
        $count = $userHelp->count(['bargain_id' => $bargainId, 'bargain_user_id' => $bargainUserTableId, 'uid' => $bargainUserHelpUid]);
        if (!$count) return true;
        else return false;
    }

    /**
     * Lấy số tiền săn giảm giá còn lại hoặc tỷ lệ phần trăm săn giảm giá
     * @param $bargainUserTableId
     * @param $type
     * @return float
     */
    public function getSurplusPrice($bargainUserTableId, $type)
    {
        $coverPrice = $this->getBargainUserDiffPriceFloat($bargainUserTableId);//TODO Lấy số tiền người dùng có thể giảm được  lấy số tiền săn giảm giá sau khi bạn bè giúp giảm giá
        $alreadyPrice = $this->dao->value(['id' => $bargainUserTableId], 'price');//TODO Số tiền người dùng đã giảm được, lấy số tiền người dùng đã giảm được sau khi bạn bè giúp giảm giá
        if ($type == 1) {
            return (float)bcsub((string)$coverPrice, (string)$alreadyPrice, 2);//TODO Số tiền còn lại người dùng cần giảm
        } else {
            if ($alreadyPrice) return (int)bcmul((string)bcdiv((string)$alreadyPrice, (string)$coverPrice, 2), '100', 0);
            else return 100;
        }
    }

    /**
     * Lấy số tiền người dùng có thể giảm được  lấy số tiền săn giảm giá sau khi bạn bè giúp giảm giá
     * @param $id
     * @return float
     */
    public function getBargainUserDiffPriceFloat($id)
    {
        $price = $this->dao->get($id);
        return (float)bcsub((string)$price['bargain_price'], (string)$price['bargain_price_min'], 2);
    }

    /**
     * Thêm thông tin săn giảm giá
     * @param int $bargainId
     * @param int $bargainUserUid
     * @param array $bargainInfo
     * @return mixed
     */
    public function setBargain(int $bargainId, int $bargainUserUid, array $bargainInfo)
    {
        $data['bargain_id'] = $bargainId;
        $data['uid'] = $bargainUserUid;
        $data['bargain_price_min'] = $bargainInfo['min_price'];
        $data['bargain_price'] = $bargainInfo['price'];
        $data['price'] = 0;
        $data['status'] = 1;
        $data['is_del'] = 0;
        $data['add_time'] = time();
        return $this->dao->save($data);
    }


    /**
     * Sửa trạng thái săn giảm giá
     * @param $uid
     * @return bool
     */
    public function editBargainUserStatus($uid)
    {
        $currentBargain = $this->dao->getColumn(['uid' => $uid, 'is_del' => 0, 'status' => 1], 'bargain_id');
        /** @var StoreBargainServices $bargainService */
        $bargainService = app()->make(StoreBargainServices::class);
        $bargainProduct = $bargainService->validWhere()->column('id');
        $closeBargain = [];
        foreach ($currentBargain as $key => &$item) {
            if (!in_array($item, $bargainProduct)) {
                $closeBargain[] = $item;
            }
        }// TODO Lấy sản phẩm săn giảm giá đã kết thúc
        if (count($closeBargain)) $this->dao->update([['uid', '=', $uid], ['status', '=', 1], ['bargain_id', 'in', implode(',', $closeBargain)]], ['status' => 2]);
    }


    /**
     * TODO Lấy sản phẩm săn giảm giá của người dùng
     * @param int $bargainUserUid $bargainUserUid  Mã người dùng mở săn giảm giá
     * @return array
     */
    public function getBargainUserAll(int $bargainUserUid)
    {
        if (!$bargainUserUid) return [];
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->userAll($bargainUserUid, $page, $limit);
        $bargainHelpServices = app()->make(StoreBargainUserHelpServices::class);
        foreach ($list as &$item) {
            $item['residue_price'] = bcsub((string)$item['bargain_price'], (string)$item['price'], 2);
            if ($item['status'] == 3) {
                $item['success_time'] = date('Y-m-d H:i:s', (int)$bargainHelpServices->getMax(['bargain_user_id' => $item['id']], 'add_time'));
            } else {
                $item['success_time'] = '';
            }
        }
        return $list;
    }

    /**
     * Hủy săn giảm giá
     * @param $bargainId
     * @param $uid
     * @return mixed
     */
    public function cancelBargain($bargainId, $uid)
    {
        $status = $this->dao->getBargainUserStatus($bargainId, $uid);
        if ($status != 1) return app('json')->fail(100020);
        $id = $this->dao->value(['bargain_id' => $bargainId, 'uid' => $uid, 'is_del' => 0], 'id');
        return $this->dao->update($id, ['is_del' => 1, 'status' => 2]);
    }

    /**
     * Khi gỡ bán hoặc xóa săn giảm giá, cập nhật trạng thái săn giảm giá thành thất bại
     * @param $bargain_id
     */
    public function userBargainStatusFail($bargain_id, $is_true)
    {
        if ($is_true) {
            $this->dao->delete(['bargain_id' => $bargain_id]);
            /** @var StoreBargainUserHelpServices $service */
            $service = app()->make(StoreBargainUserHelpServices::class);
            $service->delete(['bargain_id' => $bargain_id]);
        } else {
            $this->dao->update(['bargain_id' => $bargain_id, 'status' => 1], ['status' => 2]);
        }

    }

    /**
     * Danh sách săn giảm giá
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bargainUserList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->bargainUserList($where, $page, $limit);
        $count = $this->dao->count($where);
        /** @var StoreBargainUserHelpServices $bargainUserHelpService */
        $bargainUserHelpService = app()->make(StoreBargainUserHelpServices::class);
        $nums = $bargainUserHelpService->getNums();
        foreach ($list as &$item) {
            $item['num'] = $item['people_num'] - $nums[$item['id']];
            $item['already_num'] = $nums[$item['id']] ?? 0;
            $item['now_price'] = bcsub((string)$item['bargain_price'], (string)$item['price'], 2);
            $item['add_time'] = $item['add_time'] ? date('Y-m-d H:i:s', (int)$item['add_time']) : '';
            $item['datatime'] = $item['datatime'] ? date('Y-m-d H:i:s', (int)$item['datatime']) : '';
        }
        return compact('list', 'count');
    }
}
