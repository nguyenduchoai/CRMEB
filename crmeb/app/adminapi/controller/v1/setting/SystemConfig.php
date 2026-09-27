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
namespace app\adminapi\controller\v1\setting;

use app\adminapi\controller\AuthController;
use app\Request;
use app\services\system\config\SystemConfigServices;
use app\services\system\config\SystemConfigTabServices;
use app\services\system\SystemPemServices;
use crmeb\services\CacheService;
use crmeb\services\easywechat\orderShipping\MiniOrderService;
use think\facade\App;

/**
 * Cấu hình hệ thống
 * Class SystemConfig
 * @package app\adminapi\controller\v1\setting
 */
class SystemConfig extends AuthController
{

    /**
     * SystemConfig constructor.
     * @param App $app
     * @param SystemConfigServices $services
     */
    public function __construct(App $app, SystemConfigServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * Hiển thị danh sách resource
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['tab_id', 0],
            ['config_name', ''],
            ['status', -1]
        ]);
        if (!$where['tab_id'] && $where['config_name'] == '') {
            return app('json')->fail('Tham số không hợp lệ');
        }
        if ($where['status'] == -1) {
            unset($where['status']);
        }
        return app('json')->success($this->services->getConfigList($where));
    }

    /**
     * Hiển thị trang form tạo resource.
     * @return \think\Response
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create()
    {
        [$type, $tabId] = $this->request->getMore([
            [['type', 'd'], ''],
            [['tab_id', 'd'], 1]
        ], true);
        return app('json')->success($this->services->createFormRule($type, $tabId));
    }

    /**
     * Lưu resource mới tạo
     * @return \think\Response
     */
    public function save()
    {
        $data = $this->request->postMore([
            ['menu_name', ''],
            ['type', ''],
            ['input_type', 'input'],
            ['config_tab_id', 0],
            ['parameter', ''],
            ['upload_type', 1],
            ['required', ''],
            ['width', 0],
            ['high', 0],
            ['value', ''],
            ['info', ''],
            ['desc', ''],
            ['sort', 0],
            ['level', 0],
            ['link_data', []],
            ['status', 0]
        ]);
        if (is_array($data['config_tab_id'])) $data['config_tab_id'] = end($data['config_tab_id']);
        if (!$data['info']) return app('json')->fail('Vui lòng nhập tên cấu hình');
        if (!$data['menu_name']) return app('json')->fail('Vui lòng nhập tên trường');
        if (!$data['desc']) return app('json')->fail('Vui lòng nhập mô tả cấu hình');
        if ($data['sort'] < 0) {
            $data['sort'] = 0;
        }
        if ($data['type'] == 'text') {
            if (!$data['width']) return app('json')->fail('Vui lòng nhập chiều rộng ô văn bản');
            if ($data['width'] <= 0) return app('json')->fail('Vui lòng nhập đúng chiều rộng ô văn bản');
        }
        if ($data['type'] == 'textarea') {
            if (!$data['width']) return app('json')->fail('Vui lòng nhập chiều rộng ô văn bản nhiều dòng');
            if (!$data['high']) return app('json')->fail('Vui lòng nhập chiều cao ô văn bản nhiều dòng');
            if ($data['width'] < 0) return app('json')->fail('Vui lòng nhập đúng chiều rộng ô văn bản nhiều dòng');
            if ($data['high'] < 0) return app('json')->fail('Vui lòng nhập đúng chiều rộng ô văn bản nhiều dòng');
        }
        if ($data['type'] == 'radio' || $data['type'] == 'checkbox') {
            if (!$data['parameter']) return app('json')->fail('Vui lòng nhập tham số cấu hình');
            $this->services->valiDateRadioAndCheckbox($data);
        }
        if ($data['level'] == 1) {
            if (!$data['link_data']) return app('json')->fail('Vui lòng chọn tùy chọn cấp cao nhất cần liên kết');
            $data['link_id'] = $data['link_data'][0];
            $data['link_value'] = $data['link_data'][1];
        }
        $data['value'] = json_encode($data['value']);
        $config = $this->services->getOne(['menu_name' => $data['menu_name']]);
        if ($config) {
            $this->services->update($config['id'], $data, 'id');
        } else {
            $this->services->save($data);
        }
        CacheService::clear();
        return app('json')->success('Thêm cấu hình thành công');
    }

    /**
     * Hiển thị resource được chỉ định
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        if (!$id) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $info = $this->services->getReadList((int)$id);
        return app('json')->success(compact('info'));
    }

    /**
     * Hiển thị trang form sửa resource.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        return app('json')->success($this->services->editConfigForm((int)$id));
    }

    /**
     * Lưu resource đã cập nhật
     *
     * @param int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $type = request()->post('type');
        if ($type == 'text' || $type == 'textarea' || $type == 'radio' || ($type == 'upload' && (request()->post('upload_type') == 1 || request()->post('upload_type') == 3))) {
            $value = request()->post('value');
        } else {
            $value = request()->post('value/a');
        }
        if (!$value) $value = request()->post(request()->post('menu_name'));
        $data = $this->request->postMore([
            ['menu_name', ''],
            ['type', ''],
            ['input_type', 'input'],
            ['config_tab_id', 0],
            ['parameter', ''],
            ['upload_type', 1],
            ['required', ''],
            ['width', 0],
            ['high', 0],
            ['value', $value],
            ['info', ''],
            ['desc', ''],
            ['sort', 0],
            ['level', 0],
            ['link_data', []],
            ['status', 0]
        ]);
        if (is_array($data['config_tab_id'])) $data['config_tab_id'] = end($data['config_tab_id']);
        if (!$this->services->get($id)) {
            return app('json')->fail('Dữ liệu không tồn tại');
        }
        if ($data['level'] == 1) {
            if (!$data['link_data']) return app('json')->fail('Vui lòng chọn tùy chọn cấp cao nhất cần liên kết');
            $data['link_id'] = $data['link_data'][0];
            $data['link_value'] = $data['link_data'][1];
        }
        $data['value'] = json_encode($data['value']);
        $this->services->update($id, $data);
        CacheService::clear();
        return app('json')->success('Sửa thành công');
    }

    /**
     * Xóa resource được chỉ định
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (!$this->services->delete($id))
            return app('json')->fail('Xóa thất bại');
        else {
            CacheService::clear();
            return app('json')->success('Xóa thành công');
        }
    }

    /**
     * Sửa trạng thái
     * @param $id
     * @param $status
     * @return mixed
     */
    public function set_status($id, $status)
    {
        if ($status == '' || $id == 0) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $this->services->update($id, ['status' => $status]);
        CacheService::clear();
        return app('json')->success('Cài đặt thành công');
    }

    /**
     * Cấu hình cơ bản
     * */
    public function edit_basics(Request $request)
    {
        $tabId = $this->request->param('tab_id', 1);
        if (!$tabId) {
            return app('json')->fail('Tham số không hợp lệ');
        }
        $url = $request->baseUrl();
        return app('json')->success($this->services->getConfigForm($url, $tabId));
    }

    /**
     * Lưu dữ liệu    true
     * */
    public function save_basics(Request $request)
    {
        $post = $this->request->post();
        foreach ($post as $k => $v) {
            if (is_array($v)) {
                $res = $this->services->getUploadTypeList($k);
                foreach ($res as $kk => $vv) {
                    if ($kk == 'upload') {
                        if ($vv == 1 || $vv == 3) {
                            $post[$k] = $v[0];
                        }
                    }
                }
            }
        }
        $this->validate($post, \app\adminapi\validate\setting\SystemConfigValidata::class);
        if (isset($post['upload_type'])) {
            $this->services->checkThumbParam($post);
        }
        if (isset($post['extract_type']) && !count($post['extract_type'])) {
            return app('json')->fail('Chọn ít nhất một phương thức rút tiền');
        }
        if (isset($post['store_brokerage_binding_status'])) {
            $this->services->checkBrokerageBinding($post);
        }
        if (isset($post['store_brokerage_ratio']) && isset($post['store_brokerage_two'])) {
            $num = $post['store_brokerage_ratio'] + $post['store_brokerage_two'];
            if ($num > 100) {
                return app('json')->fail('Tổng tỷ lệ trả hoa hồng cấp 1 và cấp 2 không được lớn hơn 100%');
            }
        }
        if (isset($post['spread_banner'])) {
            $num = count($post['spread_banner']);
            if ($num > 5) {
                return app('json')->fail('Poster tiếp thị liên kết không được quá 5 ảnh');
            }
        }
        if (isset($post['user_extract_min_price'])) {
            if (!preg_match('/[0-9]$/', $post['user_extract_min_price'])) {
                return app('json')->fail('Số tiền rút tối thiểu chỉ được là số');
            }
        }
        if (isset($post['wss_open'])) {
            $this->services->saveSslFilePath((int)$post['wss_open'], $post['wss_local_pk'] ?? '', $post['wss_local_cert'] ?? '');
        }
        if (isset($post['store_brokerage_price']) && $post['store_brokerage_statu'] == 3) {
            if ($post['store_brokerage_price'] === '') {
                return app('json')->fail('Mức chi tiêu tối thiểu để trở thành CTV không được để trống');
            }
            if ($post['store_brokerage_price'] < 0) {
                return app('json')->fail('Mức chi tiêu tối thiểu để trở thành CTV không được nhỏ hơn 0');
            }
        }
        if (isset($post['store_brokerage_binding_time']) && $post['store_brokerage_binding_status'] == 2) {
            if (!preg_match("/^[0-9][0-9]*$/", $post['store_brokerage_binding_time'])) {
                return app('json')->fail('Vui lòng điền số nguyên dương cho thời hạn liên kết');
            }
        }
        if (isset($post['uni_brokerage_price']) && $post['uni_brokerage_price'] < 0) {
            return app('json')->fail('Đơn giá hoa hồng giới thiệu không được nhỏ hơn 0');
        }
        if (isset($post['day_brokerage_price_upper']) && $post['day_brokerage_price_upper'] < -1) {
            return app('json')->fail('Giới hạn hoa hồng giới thiệu mỗi ngày không được nhỏ hơn -1');
        }
        if (isset($post['pay_new_weixin_open']) && (bool)$post['pay_new_weixin_open']) {
            if (empty($post['pay_new_weixin_mchid'])) {
                return app('json')->fail('Mã merchant không được để trống');
            }
        }
        if (isset($post['uni_brokerage_price']) && preg_match('/\.[0-9]{2,}[1-9][0-9]*$/', (string)$post['uni_brokerage_price']) > 0) {
            return app('json')->fail('Số tiền chỉ được có tối đa hai chữ số thập phân');
        }

        if (isset($post['weixin_ckeck_file'])) {
            $from = public_path() . $post['weixin_ckeck_file'];
            $to = public_path() . array_reverse(explode('/', $post['weixin_ckeck_file']))[0];
            @copy($from, $to);
        }
        if (isset($post['ico_path'])) {
            $from = public_path() . $post['ico_path'];
            $toAdmin = public_path('admin') . 'favicon.ico';
            $toHome = public_path('home') . 'favicon.ico';
            $toPublic = public_path() . 'favicon.ico';
            @copy($from, $toAdmin);
            @copy($from, $toHome);
            @copy($from, $toPublic);
        }
        if (isset($post['reward_integral']) || isset($post['reward_money'])) {
            if ($post['reward_money'] < 0) return app('json')->fail('Số dư tặng không được nhỏ hơn 0đ');
            if ($post['reward_integral'] < 0) return app('json')->fail('Điểm thưởng tặng không được nhỏ hơn 0');
        }

        if (isset($post['sign_give_point'])) {
            if (!is_int($post['sign_give_point']) || $post['sign_give_point'] < 0) {
                return app('json')->fail('Vui lòng nhập số nguyên lớn hơn hoặc bằng 0 cho điểm thưởng tặng khi điểm danh');
            }
        }
        if (isset($post['sign_give_exp'])) {
            if ((int)$post['sign_give_exp'] < 0) {
                return app('json')->fail('Vui lòng nhập số nguyên lớn hơn hoặc bằng 0 cho điểm kinh nghiệm tặng khi điểm danh');
            }
        }
        if (isset($post['integral_frozen'])) {
            if (!ctype_digit($post['integral_frozen']) || $post['integral_frozen'] < 0) {
                return app('json')->fail('Vui lòng nhập số nguyên lớn hơn hoặc bằng 0 cho số ngày đóng băng điểm thưởng');
            }
        }
        if (isset($post['store_free_postage'])) {
            if (!is_int($post['store_free_postage']) || $post['store_free_postage'] < 0) {
                return app('json')->fail('Vui lòng nhập số nguyên lớn hơn hoặc bằng 0 cho mức đơn hàng được miễn phí vận chuyển');
            }
        }
        if (isset($post['withdrawal_fee'])) {
            if ($post['withdrawal_fee'] < 0 || $post['withdrawal_fee'] > 100) {
                return app('json')->fail('Phí rút tiền phải nằm trong khoảng 0-100');
            }
        }
        if (isset($post['routine_auth_type']) && count($post['routine_auth_type']) == 0) {
            return app('json')->fail('Phải bật ít nhất một trong hai hình thức đăng nhập bằng WeChat và số điện thoại');
        }
        if (isset($post['integral_max_num'])) {
            if (!ctype_digit($post['integral_max_num']) || $post['integral_max_num'] < 0) {
                return app('json')->fail('Vui lòng nhập số nguyên lớn hơn hoặc bằng 0 cho giới hạn khấu trừ bằng điểm thưởng');
            }
        }
        if (isset($post['customer_phone'])) {
            if (!ctype_digit($post['customer_phone']) || strlen($post['customer_phone']) > 11) {
                return app('json')->fail('Số điện thoại CSKH phải gồm 11 chữ số');
            }
        }
        if (isset($post['refund_time_available'])) {
            if (!ctype_digit($post['refund_time_available'])) {
                return app('json')->fail('Thời hạn hậu mãi phải là số nguyên lớn hơn 0');
            }
        }
        if (isset($post['sms_save_type']) && sys_config('sms_account', '') != '') {
            return app('json')->success('Sửa thành công');
        }
        if (isset($post['param_filter_data'])) {
            $post['param_filter_data'] = base64_encode($post['param_filter_data']);
        }
        if (isset($post['product_type_config'])) {
            if (count($post['product_type_config']) == 0) {
                return app('json')->fail('Vui lòng chọn ít nhất một loại sản phẩm');
            }
        }
        if (isset($post['yue_pay_status']) && $post['yue_pay_status'] == 1) {
            $post['balance_func_status'] = 1;
        }
        if (isset($post['pay_weixin_client_cert'])) {
            $certData = [
                'type' => 'wechat',
                'name' => 'pay_weixin_client_cert',
                'path' => 'cert' . time() . rand(1000, 9999),
                'content' => $post['pay_weixin_client_cert'] != '' ? file_get_contents($this->getPemPath($post['pay_weixin_client_cert'])) : '',
            ];
            $keyData = [
                'type' => 'wechat',
                'name' => 'pay_weixin_client_key',
                'path' => 'key' . time() . rand(1000, 9999),
                'content' => $post['pay_weixin_client_key'] != '' ? file_get_contents($this->getPemPath($post['pay_weixin_client_key'])) : '',
            ];
            $systemPemServices = app()->make(SystemPemServices::class);
            $systemPemServices->savePem($certData);
            $systemPemServices->savePem($keyData);
        }

        if (isset($post['merchant_cert_path'])) {
            $merchantCertData = [
                'type' => 'alipay',
                'name' => 'merchant_cert_path',
                'path' => 'merchant_cert' . time() . rand(1000, 9999),
                'content' => $post['merchant_cert_path'] != '' ? file_get_contents($this->getPemPath($post['merchant_cert_path'])) : '',
            ];
            $alipayCertData = [
                'type' => 'alipay',
                'name' => 'alipay_cert_path',
                'path' => 'alipay_cert' . time() . rand(1000, 9999),
                'content' => $post['alipay_cert_path'] != '' ? file_get_contents($this->getPemPath($post['alipay_cert_path'])) : '',
            ];
            $alipayRootCertData = [
                'type' => 'alipay',
                'name' => 'alipay_root_cert_path',
                'path' => 'alipay_root_cert' . time() . rand(1000, 9999),
                'content' => $post['alipay_root_cert_path'] != '' ? file_get_contents($this->getPemPath($post['alipay_root_cert_path'])) : '',
            ];
            $systemPemServices = app()->make(SystemPemServices::class);
            $systemPemServices->savePem($merchantCertData);
            $systemPemServices->savePem($alipayCertData);
            $systemPemServices->savePem($alipayRootCertData);
        }


        foreach ($post as $k => $v) {
            $config_one = $this->services->getOne(['menu_name' => $k]);
            if ($config_one) {
                $config_one['value'] = $v;
                $this->services->valiDateValue($config_one);
                $this->services->update($k, ['value' => json_encode($v)], 'menu_name');
            }
        }
        CacheService::clear();
        return app('json')->success('Sửa thành công');
    }

    /**
     * Lấy đường dẫn file chứng chỉ
     * @param string $path
     * @return string
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/10/21
     */
    public function getPemPath(string $path)
    {
        if (strstr($path, 'http://') || strstr($path, 'https://')) {
            $path = parse_url($path)['path'] ?? '';
        }
        $path = root_path('runtime/pem') . ltrim($path, '/');
        if (!file_exists($path)) {
            $path = public_path('uploads') . ltrim($path, '/');
        }
        return $path;
    }

    /**
     * Lấy danh mục phần đầu cài đặt hệ thống
     * @param SystemConfigTabServices $services
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function header_basics(SystemConfigTabServices $services)
    {
        [$type, $pid] = $this->request->getMore([
            [['type', 'd'], 0],
            [['pid', 'd'], 0]
        ], true);
        if ($type == 3) {//Danh mục khác
            $config_tab = [];
        } else {
            $config_tab = $services->getConfigTab($pid);
            if (empty($config_tab)) $config_tab[] = $services->get($pid, ['id', 'id as value', 'title as label', 'pid', 'icon', 'type']);
        }
        return app('json')->success(compact('config_tab'));
    }

    /**
     * Lấy giá trị của một cấu hình
     * @param $name
     * @return mixed
     */
    public function get_system($name)
    {
        $value = sys_config($name);
        return app('json')->success(compact('value'));
    }

    /**
     * Lấy tất cả cấu hình thuộc một danh mục
     * @param $tabId
     * @return mixed
     */
    public function get_config_list($tabId)
    {
        $list = $this->services->getConfigTabAllList($tabId);
        $data = [];
        foreach ($list as $item) {
            $data[$item['menu_name']] = json_decode($item['value']);
        }
        return app('json')->success($data);
    }

    /**
     * Lấy thông tin số phiên bản
     * @return mixed
     */
    public function getVersion()
    {
        $version = get_crmeb_version();
        return app('json')->success([
            'version' => $version,
            'label' => 19,
            'spread_uid' => (int)(parse_ini_file(app()->getRootPath() . '.version')['spread_uid'] ?? 0)
        ]);
    }
}
