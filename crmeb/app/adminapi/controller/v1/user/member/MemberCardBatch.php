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

namespace app\adminapi\controller\v1\user\member;


use app\adminapi\controller\AuthController;
use app\services\other\AgreementServices;
use app\services\other\QrcodeServices;
use app\services\user\member\MemberCardBatchServices;
use think\facade\App;

/**
 * Class MemberCardBatch
 * @package app\adminapi\controller\v1\user\member
 */
class MemberCardBatch extends AuthController
{
    /**
     * MemberCardBatch constructor.
     * @param App $app
     * @param MemberCardBatchServices $memberCardBatchServices
     */
    public function __construct(App $app, MemberCardBatchServices $memberCardBatchServices)
    {
        parent::__construct($app);
        $this->services = $memberCardBatchServices;
    }

    /**
     * Danh sách resource lô thẻ thành viên
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['title', ''],
        ]);
        $data = $this->services->getList($where);
        return app('json')->success($data);
    }

    /**
     * Lưu resource thẻ
     * @param $id
     * @return mixed
     */
    public function save($id)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['use_day', 1],
            ['total_num', 1],
            ['status', 0],
            ['remark', '']
        ]);
        $this->services->save((int)$id, $data);
        return app('json')->success(400313);
    }

    /**
     * Thao tác danh sách
     * @param $id
     * @return mixed
     */
    public function set_value($id)
    {

        $data = $this->request->getMore([
            ['value', ''],
            ['field', ''],
        ]);
        $this->services->setValue($id, $data);
        return app('json')->success(100001);
    }

    /**Mã QR thành viên, thẻ đổi
     * @return mixed
     */
    public function member_scan()
    {
        //Tạo địa chỉ h5
        $weixinPage = "/pages/annex/vip_active/index";
        $weixinFileName = "wechat_member_card.png";
        /** @var QrcodeServices $QrcodeService */
        $QrcodeService = app()->make(QrcodeServices::class);
        $wechatQrcode = $QrcodeService->getWechatQrcodePath($weixinFileName,$weixinPage, false, false);
        //Tạo địa chỉ Mini Program
        $routineQrcode = $QrcodeService->getRoutineQrcodePath(4,6,4, [], false);
        return app('json')->success(['wechat_img' => $wechatQrcode, 'routine' => $routineQrcode ?: ""]);
    }

    /** Thêm thỏa thuận thành viên
     * @param int $id
     * @param AgreementServices $agreementServices
     * @return mixed
     */
    public function save_member_agreement($id = 0, AgreementServices $agreementServices)
    {
        $data = $this->request->postMore([
            ['type', 1],
            ['title', ""],
            ['content', ''],
            ['status', ''],
        ]);

        return app('json')->success($agreementServices->saveAgreement($data, $id));
    }

    /**Lấy thỏa thuận thành viên
     * @param AgreementServices $agreementServices
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAgreement(AgreementServices $agreementServices)
    {
        $list = $agreementServices->getAgreementBytype(1);
        return app('json')->success($list);
    }

}
