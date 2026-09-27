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
namespace app\adminapi\controller\v1\system;

use app\adminapi\controller\AuthController;
use app\services\system\config\SystemConfigServices;
use crmeb\services\UpgradeService as uService;
//use app\models\system\SystemConfig;
use think\facade\Db;

/**
 * Controller nâng cấp trực tuyến
 * Class SystemUpgradeclient
 * @package app\admin\controller\system
 *
 */
class SystemUpgradeClient extends AuthController
{

    protected $serverweb = array('version' => '1.0', 'version_code' => 0);//Thông tin site này

    public function initialize()
    {
        parent::initialize();
        self::snyweninfo();//Cập nhật thông tin site
    }

    //Đồng bộ cập nhật thông tin site
    public function snyweninfo()
    {
        /** @var SystemConfigServices $systemConfig */
        $systemConfig = app()->make(SystemConfigServices::class);
        $this->serverweb['ip'] = $this->request->ip();
        $this->serverweb['host'] = $this->request->host();
        $this->serverweb['https'] = !empty($this->request->domain()) ? $this->request->domain() : $systemConfig->getConfigValue('site_url');
        $this->serverweb['webname'] = $systemConfig->getConfigValue('site_name');
        $local = uService::getVersion();
        if ($local['code'] == 200 && isset($local['msg']['version']) && isset($local['msg']['version_code'])) {
            $this->serverweb['version'] = uService::replace($local['msg']['version']);
            $this->serverweb['version_code'] = (int)uService::replace($local['msg']['version_code']);
        }
        uService::snyweninfo($this->serverweb);
    }

    //Đã cấp phép
    public function isauth()
    {
        return uService::isauth();
    }

    /**
     * Danh sách nâng cấp
     */
    public function index()
    {
        $where = $this->request->getMore([
            ['page', 1],
            ['limit', 20]
        ]);
        $list = uService::request_post(uService::$isList, ['page' => $where['page'], 'limit' => $where['limit']]);
        if (is_array($list) && isset($list['code']) && isset($list['data']) && $list['code'] == 200) {
            $list = $list['data'];
        } else {
            $list = [];
        }
        return app('json')->success($list);
    }

    //Xóa file backup
    public function setcopydel()
    {
        $post = input('post.');
        if (!isset($post['id'])) app('json')->fail('Xóa tệp sao lưu thất bại, thiếu tham số ID');
        if (!isset($post['ids'])) app('json')->fail('Xóa tệp sao lưu thất bại, thiếu tham số IDS');
        $fileservice = new uService;
        if (is_array($post['ids'])) {
            foreach ($post['ids'] as $file) {
                $fileservice->del_dir(app()->getRootPath() . 'public' . DS . 'copyfile' . $file);
            }
        }
        if ($post['id']) {
            $copyFile = app()->getRootPath() . 'public' . DS . 'copyfile' . $post['id'];
            $fileservice->del_dir($copyFile);
        }
        return app('json')->success('Xóa thành công');
    }

    public function get_new_version_conte()
    {
        $post = $this->request->post();
        if (!isset($post['id'])) app('json')->fail('Thiếu tham số ID');
        $versionInfo = uService::request_post(uService::$NewVersionCount, ['id' => $post['id']]);
        if (isset($versionInfo['code']) && isset($versionInfo['data']['count']) && $versionInfo['code'] == 200) {
            return app('json')->success(['count' => $versionInfo['data']['count']]);
        } else {
            return app('json')->fail('Lỗi máy chủ');
        }
    }

    //Nâng cấp bằng một cú nhấp
    public function auto_upgrade()
    {
        $prefix = config('database.prefix');
        $fileservice = new uService;
        $post = $this->request->post();
        if (!isset($post['id'])) return app('json')->fail('Thiếu tham số ID');
        $versionInfo = $fileservice->request_post(uService::$isNowVersion, ['id' => $post['id']]);
        if ($versionInfo === null) return app('json')->fail('Lỗi máy chủ, vui lòng thử lại sau');
        if (isset($versionInfo['code']) && $versionInfo['code'] == 400) return app('json')->fail($versionInfo['msg'] ?? 'Bạn tạm thời không có quyền nâng cấp, vui lòng liên hệ quản trị viên!');
        if (is_array($versionInfo) && isset($versionInfo['data'])) {
            $list = $versionInfo['data'];
            $id = [];
            foreach ($list as $key => $val) {
                $savefile = app()->getRootPath() . 'public' . DS . 'upgrade_lv';
                //1, Kiểm tra file cần tải từ xa, và tải về
                if (($save_path = $fileservice->check_remote_file_exists($val['zip_name'], $savefile)) === false) app('json')->fail('Gói nâng cấp từ xa không tồn tại');
                //2, Trước tiên giải nén file
                $savename = app()->getRootPath() . 'public' . DS . 'upgrade_lv' . DS . time();
                $fileservice->zipOpen($save_path, $savename);
                //3, Thực thi file SQL
                Db::startTrans();
                try {
                    //Tham số 3 không phân biệt hoa thường
                    $sqlfile = $fileservice->listDirInfo($savename . DS, true, 'sql');
                    if (is_array($sqlfile) && !empty($sqlfile)) {
                        foreach ($sqlfile as $file) {
                            if (file_exists($file)) {
                                //Chuẩn bị cho cài đặt một cú nhấp, nhớ đổi tiền tố bảng thành [#DB_PREFIX#] nhé
                                $execute_sql = explode(";\r", str_replace(['[#DB_PREFIX#]', "\n"], [$prefix, "\r"], file_get_contents($file)));
                                foreach ($execute_sql as $_sql) {
                                    if ($query_string = trim(str_replace(array(
                                        "\r",
                                        "\n",
                                        "\t"
                                    ), '', $_sql))) Db::execute($query_string);
                                }
                                //Thực thi sql xong nhớ xóa nhé
                                $fileservice->unlinkFile($file);
                            }
                        }
                    }
                    Db::commit();
                } catch (\Exception $e) {
                    Db::rollback();
                    //Xóa file sau khi giải nén
                    $fileservice->del_dir(app()->getRootPath() . 'public' . DS . 'upgrade_lv');
                    //Xóa file nén
                    $fileservice->unlinkFile($save_path);
                    //Nâng cấp thất bại, gửi thông báo lỗi
                    $fileservice->request_post(uService::$isInsertLog, [
                        'content' => 'Nâng cấp thất bại, thông tin lỗi:' . $e->getMessage(),
                        'add_time' => time(),
                        'ip' => $this->request->ip(),
                        'http' => $this->request->domain(),
                        'type' => 'error',
                        'version' => $val['version']
                    ]);
                    return app('json')->fail('Nâng cấp thất bại, thực thi tệp SQL bị lỗi');
                }
                //4, Backup file
                $copyFile = app()->getRootPath() . 'public' . DS . 'copyfile' . $val['id'];
                $copyList = $fileservice->getDirs($savename . DS);
                if (isset($copyList['dir'])) {
                    if ($copyList['dir'][0] == '.' && $copyList['dir'][1] == '..') {
                        array_shift($copyList['dir']);
                        array_shift($copyList['dir']);
                    }
                    foreach ($copyList['dir'] as $dir) {
                        if (file_exists(app()->getRootPath() . $dir, $copyFile . DS . $dir)) {
                            $fileservice->copyDir(app()->getRootPath() . $dir, $copyFile . DS . $dir);
                        }
                    }
                }
                //5, Ghi đè file
                $fileservice->handleDir($savename, app()->getRootPath());
                //6, Xóa thư mục do nâng cấp tạo ra
                $fileservice->del_dir(app()->getRootPath() . 'public' . DS . 'upgrade_lv');
                //7, Xóa file nén
                $fileservice->unlinkFile($save_path);
                //8, Ghi lại file nâng cấp cục bộ
                $handle = fopen(app()->getRootPath() . '.version', 'w+');
                if ($handle === false) return app('json')->fail(app()->getRootPath() . '.version' . 'không thể mở để ghi');
                $content = <<<EOT
version={$val['version']}
version_code={$val['id']}
EOT;
                if (fwrite($handle, $content) === false) return app('json')->fail('Ghi gói nâng cấp thất bại');
                fclose($handle);
                //9, Gửi log nâng cấp lên server
                $posts = [
                    'ip' => $this->request->ip(),
                    'https' => $this->request->domain(),
                    'update_time' => time(),
                    'content' => 'Nâng cấp nhanh thành công, phiên bản nâng cấp:' . $val['version'] . '. Code phiên bản:' . $val['id'],
                    'type' => 'log',
                    'versionbefor' => $this->serverweb['version'],
                    'versionend' => $val['version']
                ];
                $inset = $fileservice->request_post(uService::$isInsertLog, $posts);
                $id[] = $val['id'];
            }
            //10, Hoàn tất nâng cấp
            return app('json')->success('Nâng cấp thành công', ['code' => end($id), 'version' => $val['version']]);
        } else {
            return app('json')->fail('Lỗi máy chủ, vui lòng thử lại sau');
        }
    }
}
