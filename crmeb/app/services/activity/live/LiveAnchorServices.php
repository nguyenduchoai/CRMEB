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


use app\dao\activity\live\LiveAnchorDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use app\jobs\LiveJob;
use crmeb\services\FormBuilder as Form;
use crmeb\services\app\MiniProgramService;
use FormBuilder\components\Validate;
use think\facade\Route as Url;


/**
 * Class LiveGoodsServices
 * @package app\services\activity\live
 */
class LiveAnchorServices extends BaseServices
{
    /**
     * LiveAnchorServices constructor.
     * @param LiveAnchorDao $dao
     */
    public function __construct(LiveAnchorDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy thông tin một streamer
     * @param int $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLiveAnchor(int $id)
    {
        return $this->dao->get($id);
    }

    public function getList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $where['is_del'] = 0;
        $list = $this->dao->getList($where, '*', $page, $limit);
        $count = $this->dao->count($where);
        return compact('count', 'list');
    }

    /**
     * Form thêm/sửa nhãn
     * @param int $id
     * @return mixed
     */
    public function add(int $id)
    {
        $anchor = $this->getLiveAnchor($id);
        $field = array();
        if (!$anchor) {
            $title = 'Thêm streamer';
            $field[] = Form::input('name', 'Tên streamer', '')->maxlength(20)->required('Vui lòng nhập tên');
            $field[] = Form::input('wechat', 'ID WeChat của streamer', '')->maxlength(32)->required('Vui lòng nhập ID WeChat');
            $field[] = Form::input('phone', 'Số điện thoại của streamer', '')->maxlength(20)->required('Vui lòng điền số điện thoại');
            $field[] = Form::frameImage('cover_img', 'Ảnh streamer', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'cover_img')), '')->icon('el-icon-picture-outline')->width('950px')->height('560px')->Props(['footer' => false])->appendValidate(Validate::str()->required('Vui lòng chọn ảnh'));
        } else {
            $title = 'Sửa streamer';
            $field[] = Form::hidden('id', $anchor->getData('id'));
            $field[] = Form::input('name', 'Tên streamer', $anchor->getData('name'))->maxlength(20)->required('Vui lòng nhập tên');
            $field[] = Form::input('wechat', 'ID WeChat của streamer', $anchor->getData('wechat'))->maxlength(32)->required('Vui lòng nhập ID WeChat');
            $field[] = Form::input('phone', 'Số điện thoại của streamer', $anchor->getData('phone'))->maxlength(20)->required('Vui lòng điền số điện thoại');
            $field[] = Form::frameImage('cover_img', 'Ảnh streamer', Url::buildUrl(config('app.admin_prefix', 'admin') . '/widget.images/index', array('fodder' => 'cover_img')), $anchor->getData('cover_img'))->icon('el-icon-picture-outline')->width('950px')->height('560px')->Props(['footer' => false])->appendValidate(Validate::str()->required('Vui lòng chọn ảnh'));
        }
        return create_form($title, $field, $this->url('/live/anchor/save'), 'POST');
    }

    /**
     * Lưu dữ liệu form nhãn
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save(int $id, array $data)
    {
        $liveAnchor = $this->dao->get(['wechat' => $data['wechat'], 'is_del' => 0]);
        if (!MiniProgramService::getRoleList(2, 0, 30, $data['wechat'])) {
            throw new AdminException('Vui lòng xác thực streamer trên Mini Program trước');
        }
        if ($id) {
            if ($liveAnchor && $id != $liveAnchor['id']) {
                throw new AdminException('Streamer này đã tồn tại');
            }
            if ($this->dao->update($id, $data)) {
                return true;
            } else {
                throw new AdminException('Sửa thất bại');
            }
        } else {
            unset($data['id']);
            if ($liveAnchor) {
                throw new AdminException('Streamer này đã tồn tại');
            }
            if ($this->dao->save($data)) {
                return true;
            } else {
                throw new AdminException('Thêm thất bại');
            }
        }
    }

    /**
     * Xóa
     * @param $id
     * @throws \Exception
     */
    public function delAnchor(int $id)
    {
        if ($anchor = $this->getLiveAnchor($id)) {
            if (!$this->dao->update($id, ['is_del' => 1])) {
                throw new AdminException('Xóa thất bại');
            }
            /** @var LiveRoomServices $liveRoom */
            $liveRoom = app()->make(LiveRoomServices::class);
            $room = $liveRoom->get(['anchor_wechat' => $anchor['wechat'], 'is_del' => 0], ['id']);
            if ($room) {
                $liveRoom->delete((int)$room->id);
            }
        }
        return true;
    }

    /**
     * Thiết lập hiện/ẩn
     * @param int $id
     * @param $is_show
     * @return mixed
     */
    public function setShow(int $id, $is_show)
    {
        if (!$this->getLiveAnchor($id))
            throw new AdminException('Dữ liệu không tồn tại');
        if ($this->dao->update($id, ['is_show' => $is_show])) {
            return true;
        } else {
            throw new AdminException('Cài đặt thất bại');
        }
    }

    public function syncAnchor($is_job = false)
    {
        $key = md5('Live_sync_status');
        $res = CacheService::get($key);
        if (!$res || $is_job) {
            $start = 0;
            $limit = 30;
            $data = $dataAll = [];
            $anchors = $this->dao->getColumn([], 'id,wechat', 'wechat');
            do {
                $wxAnchor = MiniProgramService::getRoleList(2, $start, $limit);
                foreach ($wxAnchor as $anchor) {
                    if (isset($anchors[$anchor['username']])) {
                        $this->dao->update($anchors[$anchor['username']]['id'], ['cover_img' => $anchor['headingimg'], 'name' => $anchor['nickname']]);
                    } else {
                        $data['name'] = $anchor['nickname'];
                        $data['wechat'] = $anchor['username'];
                        $data['cover_img'] = $anchor['headingimg'];
                        $data['add_time'] = $anchor['updateTimestamp'] ?? time();
                        $dataAll[] = $data;
                    }
                }
                $start++;
            } while (count($wxAnchor) >= $limit);
            if ($dataAll) {
                $this->dao->saveAll($dataAll);
            }
            //Gửi tin nhắn sau khi thanh toán thành công
            if (!$is_job) LiveJob::dispatchSecs(120);
            CacheService::set($key, 1, 0);
        }
        return true;
    }
}
