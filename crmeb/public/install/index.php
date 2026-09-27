<?php
//Chữ ký file
$fileValue = '';
//Yêu cầu phiên bản PHP tối thiểu
define('PHP_EDITION', '7.1.0');
//Kiểm tra môi trường server
if (function_exists('saeAutoLoader') || isset($_SERVER['HTTP_BAE_ENV_APPID'])) {
    showHtml('Xin lỗi, môi trường hiện tại không hỗ trợ hệ thống này, vui lòng sử dụng máy chủ riêng hoặc máy chủ đám mây!');
}

define('APP_DIR', _dir_path(substr(dirname(__FILE__), 0, -15)));//Thư mục project
define('SITE_DIR', _dir_path(substr(dirname(__FILE__), 0, -8)));//Thư mục file khởi điểm (entry)

if (file_exists('../install.lock')) {
    showHtml('Bạn đã cài đặt hệ thống này, nếu muốn cài đặt lại, vui lòng xóa tệp install.lock trong thư mục public trước, sau đó cài đặt lại.');
}

@set_time_limit(1000);

if ('7.1.0' > phpversion()) {
    exit('Phiên bản php của bạn quá thấp, không thể cài đặt phần mềm này, phiên bản php tương thích là 7.1~7.4, xin cảm ơn!');
}
if (phpversion() >= '8.0.0') {
    exit('Phiên bản php của bạn quá cao, không thể cài đặt phần mềm này, phiên bản php tương thích là 7.1~7.4, xin cảm ơn!');
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=UTF-8');

//Cơ sở dữ liệu
$sqlFile = 'crmeb.sql';
$configFile = '.env';
if (!file_exists(SITE_DIR . 'install/' . $sqlFile) || !file_exists(SITE_DIR . 'install/' . $configFile)) {
    echo 'Thiếu tệp cài đặt cần thiết!';
    exit;
}
$Title = "Trình hướng dẫn cài đặt CRMEB";
$Powered = "Powered by CRMEB";
$steps = array(
    '1' => 'Thỏa thuận cấp phép cài đặt',
    '2' => 'Kiểm tra môi trường chạy',
    '3' => 'Thiết lập tham số cài đặt',
    '4' => 'Chi tiết quá trình cài đặt',
    '5' => 'Cài đặt hoàn tất',
);
$step = $_GET['step'] ?? 1;

//Địa chỉ
$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
$rootPath = @preg_replace("/\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
[$request_scheme, $request_host] = getSchemeAndHost();

switch ($step) {
    case '1':
        include_once("./templates/step1.php");
        exit();

    case '2':
        if (phpversion() < '7.1.0' || phpversion() >= '8.0.0') {
            die('Hệ thống này yêu cầu PHP phiên bản 7.1~7.4, phiên bản PHP hiện tại là:' . phpversion());
        }

        $passOne = $passTwo = 'yes';
        $os = PHP_OS;
        $server = $_SERVER["SERVER_SOFTWARE"];
        $phpv = phpversion();
        if (ini_get('file_uploads')) {
            $uploadSize = '<img class="yes" src="images/install/yes.png" alt="Đạt">' . ini_get('upload_max_filesize');
        } else {
            $passOne = 'no';
            $uploadSize = '<img class="no" src="images/install/warring.png" alt="Lỗi">Không cho phép tải lên';
        }
        if (function_exists('session_start')) {
            $session = '<img class="yes" src="images/install/yes.png" alt="Đạt">Bật';
        } else {
            $passOne = 'no';
            $session = '<img class="no" src="images/install/warring.png" alt="Lỗi">Tắt';
        }
        if (!ini_get('safe_mode')) {
            $safe_mode = '<img class="yes" src="images/install/yes.png" alt="Đạt">Bật';
        } else {
            $passOne = 'no';
            $safe_mode = '<img class="no" src="images/install/warring.png" alt="Lỗi">Tắt';
        }
        $tmp = function_exists('gd_info') ? gd_info() : array();
        if (!empty($tmp['GD Version'])) {
            $gd = '<img class="yes" src="images/install/yes.png" alt="Đạt">' . $tmp['GD Version'];
        } else {
            $passOne = 'no';
            $gd = '<img class="no" src="images/install/warring.png" alt="Lỗi">Chưa cài đặt';
        }
        if (function_exists('mysqli_connect')) {
            $mysql = '<img class="yes" src="images/install/yes.png" alt="Đạt">Đã cài đặt';
        } else {
            $passOne = 'no';
            $mysql = '<img class="no" src="images/install/warring.png" alt="Lỗi">Vui lòng cài đặt extension mysqli';
        }
        if (function_exists('curl_init')) {
            $curl = '<img class="yes" src="images/install/yes.png" alt="Đạt">Bật';
        } else {
            $passOne = 'no';
            $curl = '<img class="no" src="images/install/warring.png" alt="Lỗi">Tắt';
        }
        if (function_exists('bcadd')) {
            $bcmath = '<img class="yes" src="images/install/yes.png" alt="Đạt">Bật';
        } else {
            $passOne = 'no';
            $bcmath = '<img class="no" src="images/install/warring.png" alt="Lỗi">Tắt';
        }
        if (function_exists('openssl_encrypt')) {
            $openssl = '<img class="yes" src="images/install/yes.png" alt="Đạt">Bật';
        } else {
            $passOne = 'no';
            $openssl = '<img class="no" src="images/install/warring.png" alt="Lỗi">Tắt';
        }

        $folder = array(
            'backup',
            'public',
            'runtime',
        );
        foreach ($folder as $dir) {
            if (!is_file(APP_DIR . $dir)) {
                if (!is_dir(APP_DIR . $dir)) {
                    dir_create(APP_DIR . $dir);
                }
            }
            if (!testwrite(APP_DIR . $dir) || !is_readable(APP_DIR . $dir)) {
                $passTwo = 'no';
            }
        }
        $file = array(
            '.env',
            '.version',
            '.constant',
        );
        foreach ($file as $filename) {
            if (!is_writeable(APP_DIR . $filename) || !is_readable(APP_DIR . $filename)) {
                $passTwo = 'no';
            }
        }

        include_once("./templates/step2.php");
        exit();

    case '3':
        $dbName = strtolower(trim($_POST['dbName']));
        $_POST['dbport'] = $_POST['dbport'] ?: '3306';
        if ($_GET['mysqldbpwd']) {
            $dbHost = $_POST['dbHost'];
            $conn = mysqli_init();
            mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);
            @mysqli_real_connect($conn, $dbHost, $_POST['dbUser'], $_POST['dbPwd'], NULL, $_POST['dbport']);
            if ($error = mysqli_connect_errno($conn)) {
                if ($error == 2002) {
                    die(json_encode(2002));//Địa chỉ hoặc cổng (port) không đúng
                } else if ($error == 1045) {
                    die(json_encode(1045));//Tên đăng nhập hoặc mật khẩu không đúng
                } else {
                    die(json_encode(-1));//Kết nối thất bại
                }
            } else {
                if (mysqli_get_server_info($conn) < 5.1) {
                    die(json_encode(-5));//Phiên bản quá thấp
                }
                $result = mysqli_query($conn, "SELECT @@global.sql_mode");
                $result = $result->fetch_array();
                $version = mysqli_get_server_info($conn);
                if ($version >= 5.7) {
                    if (strstr($result[0], 'STRICT_TRANS_TABLES') || strstr($result[0], 'STRICT_ALL_TABLES') || strstr($result[0], 'TRADITIONAL') || strstr($result[0], 'ANSI'))
                        exit(json_encode(-2));//Cần chỉnh sửa cấu hình cơ sở dữ liệu
                }
                $result = mysqli_query($conn, "select count(table_name) as c from information_schema.`TABLES` where table_schema='$dbName'");
                $result = $result->fetch_array();
                if ($result['c'] > 0) {
                    mysqli_close($conn);
                    exit(json_encode(-3));//Cơ sở dữ liệu đã tồn tại
                } else {
                    if (!mysqli_select_db($conn, $dbName)) {
                        //Thiết lập encoding cùng lúc khi tạo dữ liệu
                        if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;")) {
                            exit(json_encode(-4));//Không có quyền tạo cơ sở dữ liệu
                        } else {
                            mysqli_query($conn, "DROP DATABASE `" . $dbName . "` ;");
                            mysqli_close($conn);
                            exit(json_encode(1));//Cấu hình cơ sở dữ liệu thành công
                        }
                    } else {
                        mysqli_close($conn);
                        exit(json_encode(1));//Cấu hình cơ sở dữ liệu thành công
                    }
                }
            }
        }
        if ($_GET['redisdbpwd']) {

            //Thông tin cơ sở dữ liệu redis
            $rbhost = $_POST['rbhost'] ?? '127.0.0.1';
            $rbport = $_POST['rbport'] ?? 6379;
            $rbpw = $_POST['rbpw'] ?? '';
            $rbselect = $_POST['rbselect'] ?? 0;

            try {
                if (!class_exists('redis')) {
                    exit(json_encode(-1));
                }
                $redis = new Redis();
                if (!$redis) {
                    exit(json_encode(-1));
                }
                $redis->connect($rbhost, $rbport);
                if ($rbpw) {
                    $redis->auth($rbpw);
                }
                if ($rbselect) {
                    $redis->select($rbselect);
                }
                $res = $redis->set('install', 1, 10);
                if ($res) {
                    exit(json_encode(1));
                } else {
                    exit(json_encode(-3));
                }
            } catch (Throwable $e) {
                exit(json_encode(-3));
            }
        }
        include_once("./templates/step3.php");
        exit();

    case '4':
        if (intval($_GET['install'])) {
            $n = intval($_GET['n']);
            if ($n == 999999)
                exit;
            $arr = array();

            $dbHost = trim($_POST['dbhost']);
            $_POST['dbport'] = $_POST['dbport'] ?: '3306';
            $dbName = strtolower(trim($_POST['dbname']));
            $dbUser = trim($_POST['dbuser']);
            $dbPwd = trim($_POST['dbpw']);
            $dbPrefix = empty($_POST['dbprefix']) ? 'eb_' : trim($_POST['dbprefix']);

            $username = trim($_POST['manager']);
            $password = trim($_POST['manager_pwd']) ?: 'crmeb.com';

            if (!function_exists('mysqli_connect')) {
                $arr['msg'] = "Vui lòng cài đặt extension mysqli!";
                exit(json_encode($arr));
            }
            $conn = @mysqli_connect($dbHost, $dbUser, $dbPwd, NULL, $_POST['dbport']);
            if (mysqli_connect_errno($conn)) {
                $arr['msg'] = "Kết nối cơ sở dữ liệu thất bại!" . mysqli_connect_error($conn);
                exit(json_encode($arr));
            }
            mysqli_set_charset($conn, "utf8"); //,character_set_client=binary,sql_mode='';
            $version = mysqli_get_server_info($conn);
            if ($version < 5.1) {
                $arr['msg'] = 'Phiên bản cơ sở dữ liệu quá thấp! Phải từ 5.1 trở lên';
                exit(json_encode($arr));
            }

            if (!mysqli_select_db($conn, $dbName)) {
                //Thiết lập encoding cùng lúc khi tạo dữ liệu
                if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;")) {
                    $arr['msg'] = 'Cơ sở dữ liệu ' . $dbName . ' không tồn tại và cũng không có quyền tạo cơ sở dữ liệu mới!';
                    exit(json_encode($arr));
                }
                if ($n == -1) {
                    $arr['n'] = 0;
                    $arr['msg'] = "Tạo cơ sở dữ liệu thành công:{$dbName}";
                    exit(json_encode($arr));
                }
                mysqli_select_db($conn, $dbName);
            }

            //Đọc file dữ liệu
            $sqldata = file_get_contents(SITE_DIR . 'install/' . $sqlFile);
            $sqlFormat = sql_split($sqldata, $dbPrefix);
            //Tạo và ghi file cơ sở dữ liệu sql vào database - kết thúc

            /**
             * Thực thi câu lệnh SQL
             */
            $counts = count($sqlFormat);
            for ($i = $n; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE (IF NOT EXISTS)? `eb_([^ ]*)`/is', $sql, $matches);
                    mysqli_query($conn, "DROP TABLE IF EXISTS `$matches[2]`");
                    $sql = str_replace('`eb_', '`' . $dbPrefix, $sql);//Thay thế tiền tố bảng (table prefix)
                    $ret = mysqli_query($conn, $sql);
                    if ($ret) {
                        $message = 'Tạo bảng dữ liệu [' . $dbPrefix . $matches[2] . '] hoàn tất!';
                    } else {
                        $err = mysqli_error($conn);
                        $message = 'Tạo bảng dữ liệu [' . $dbPrefix . $matches[2] . '] thất bại! Lý do:' . $err;
                    }
                    $i++;
                    $arr = array('n' => $i, 'count' => $counts, 'msg' => $message, 'time' => date('Y-m-d H:i:s'));
                    exit(json_encode($arr));
                } else {
                    if (trim($sql) == '')
                        continue;
                    $sql = str_replace('`eb_', '`' . $dbPrefix, $sql);//Thay thế tiền tố bảng (table prefix)
                    $sql = str_replace('http://demo.crmeb.com', $request_scheme . '://' . $request_host, $sql);//Thay thế domain ảnh
                    $sql = str_replace('http:\\\\/\\\\/demo.crmeb.com', $request_scheme . ':\\\\/\\\\/' . $request_host, $sql);//Thay thế domain ảnh
                    $ret = mysqli_query($conn, $sql);
                    $message = '';
                    $arr = array('n' => $i, 'count' => $counts, 'msg' => $message, 'time' => date('Y-m-d H:i:s'));
                }
            }


            // Xóa sạch dữ liệu test
            if (!$_POST['demo']) {
                $result = mysqli_query($conn, "show tables");
                $tables = mysqli_fetch_all($result);//Tham số MYSQL_ASSOC, MYSQLI_NUM, MYSQLI_BOTH quy định loại mảng được tạo ra
                $bl_table = array('eb_system_admin'
                , 'eb_system_role'
                , 'eb_cache'
                , 'eb_agent_level'
                , 'eb_page_link'
                , 'eb_page_categroy'
                , 'eb_system_config'
                , 'eb_system_config_tab'
                , 'eb_system_menus'
                , 'eb_system_notification'
                , 'eb_express'
                , 'eb_system_group'
                , 'eb_system_group_data'
                , 'eb_lang_code'
                , 'eb_lang_country'
                , 'eb_lang_type'
                , 'eb_template_message'
                , 'eb_shipping_templates'
                , "eb_shipping_templates_region"
                , 'eb_system_city'
                , 'eb_diy'
                , 'eb_member_ship'
                , 'eb_system_timer'
                , 'eb_member_right'
                , 'eb_agreement'
                , 'eb_store_service_speechcraft'
                , 'eb_system_user_level'
                , 'eb_out_interface'
                , 'eb_cache');
                foreach ($bl_table as $k => $v) {
                    $bl_table[$k] = str_replace('eb_', $dbPrefix, $v);
                }

                foreach ($tables as $key => $val) {
                    if (!in_array($val[0], $bl_table)) {
                        mysqli_query($conn, "truncate table " . $val[0]);
                    }
                }
            }

            $unique = uniqid();

            //Đọc file cấu hình, và thay thế bằng dữ liệu cấu hình thực tế 1
            $strConfig = file_get_contents(SITE_DIR . 'install/' . $configFile);
            $strConfig = str_replace('#DB_HOST#', $dbHost, $strConfig);
            $strConfig = str_replace('#DB_NAME#', $dbName, $strConfig);
            $strConfig = str_replace('#DB_USER#', $dbUser, $strConfig);
            $strConfig = str_replace('#DB_PWD#', $dbPwd, $strConfig);
            $strConfig = str_replace('#DB_PORT#', $_POST['dbport'], $strConfig);
            $strConfig = str_replace('#DB_PREFIX#', $dbPrefix, $strConfig);
            $strConfig = str_replace('#DB_CHARSET#', 'utf8', $strConfig);

            //Cấu hình bộ nhớ đệm
            $cachetype = $_POST['cache_type'] == 0 ? 'file' : 'redis';
            $strConfig = str_replace('#CACHE_TYPE#', $cachetype, $strConfig);
            $strConfig = str_replace('#CACHE_PREFIX#', 'cache_' . $unique . ':', $strConfig);
            $strConfig = str_replace('#CACHE_TAG_PREFIX#', 'cache_tag_' . $unique . ':', $strConfig);

            //Thông tin cơ sở dữ liệu redis
            $rbhost = $_POST['rbhost'] ?? '127.0.0.1';
            $rbport = $_POST['rbport'] ?? '6379';
            $rbpw = $_POST['rbpw'] ?? '';
            $rbselect = $_POST['rbselect'] ?? 0;
            $strConfig = str_replace('#RB_HOST#', $rbhost, $strConfig);
            $strConfig = str_replace('#RB_PORT#', $rbport, $strConfig);
            $strConfig = str_replace('#RB_PWD#', $rbpw, $strConfig);
            $strConfig = str_replace('#RB_SELECT#', $rbselect, $strConfig);

            //Cần đổi tên hàng đợi
            $strConfig = str_replace('#QUEUE_NAME#', $unique, $strConfig);

            @chmod(APP_DIR . '/.env', 0777); //Đường dẫn file cấu hình cơ sở dữ liệu
            @file_put_contents(APP_DIR . '/.env', $strConfig); //Đường dẫn file cấu hình cơ sở dữ liệu

            //Chèn field vào bảng quản trị viên tp_admin
            $time = time();
            $ip = get_client_ip();
            $ip = empty($ip) ? "0.0.0.0" : $ip;
            $password = password_hash($_POST['manager_pwd'], PASSWORD_BCRYPT);
            mysqli_query($conn, "truncate table {$dbPrefix}system_admin");
            $addadminsql = "INSERT INTO `{$dbPrefix}system_admin` (`id`, `account`, `head_pic`, `pwd`, `real_name`, `roles`, `last_ip`, `last_time`, `add_time`, `login_count`, `level`, `status`, `is_del`) VALUES
(1, '" . $username . "', '/statics/system_images/admin_head_pic.png', '" . $password . "', 'admin', '1', '" . $ip . "',$time , $time, 0, 0, 1, 0)";
            $res = mysqli_query($conn, $addadminsql);
            $res2 = true;
            if ($request_host) {
                $site_url = '\'"' . $request_scheme . '://' . $request_host . '"\'';
                $res2 = mysqli_query($conn, 'UPDATE `' . $dbPrefix . 'system_config` SET `value`=' . $site_url . ' WHERE `menu_name`="site_url"');
            }
            $arr = array('n' => 999999, 'count' => $counts, 'msg' => 'Cài đặt hoàn tất', 'time' => date('Y-m-d H:i:s'));
            exit(json_encode($arr));

        }
        include_once("./templates/step4.php");
        exit();

    case '5':
        $ip = get_client_ip();
        $host = $_SERVER['HTTP_HOST'];
        $curent_version = getversion();
        $version = trim($curent_version['version']);
        $platform = trim($curent_version['platform']);
        installlog();
        include_once("./templates/step5.php");
        @touch('../install.lock');
        generateSignature();
        exit();
}
//Đọc số phiên bản
function getversion()
{
    $version_arr = [];
    $curent_version = @file(APP_DIR . '.version');
    foreach ($curent_version as $val) {
        list($k, $v) = explode('=', $val);
        $version_arr[$k] = $v;
    }
    return $version_arr;
}

//Ghi thông tin cài đặt
function installlog()
{
    $mt_rand_str = sp_random_string(6);
    $str_constant = "<?php" . PHP_EOL . "define('INSTALL_DATE'," . time() . ");" . PHP_EOL . "define('SERIALNUMBER','" . $mt_rand_str . "');";
    @file_put_contents(APP_DIR . '.constant', $str_constant);
}

//Kiểm tra quyền
function testwrite($d)
{
    if (is_file($d)) {
        if (is_writeable($d)) {
            return true;
        }
        return false;

    } else {
        $tfile = "_test.txt";
        $fp = @fopen($d . "/" . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($d . "/" . $tfile);
        if ($rs) {
            return true;
        }
        return false;
    }

}


function sql_split($sql, $tablepre)
{

    if ($tablepre != "tp_")
        $sql = str_replace("tp_", $tablepre, $sql);

    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesarray as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach ($queries as $query) {
            $str1 = substr($query, 0, 1);
            if ($str1 != '#' && $str1 != '-')
                $ret[$num] .= $query;
        }
        $num++;
    }
    return $ret;
}

function _dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

// Lấy địa chỉ IP client
function get_client_ip()
{
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // Kiểm tra tính hợp lệ của địa chỉ IP
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

function dir_create($path, $mode = 0777)
{
    if (is_dir($path))
        return TRUE;
    $ftp_enable = 0;
    $path = dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}

function dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}

function sp_password($pw, $pre)
{
    $decor = md5($pre);
    $mi = md5($pw);
    return substr($decor, 0, 12) . $mi . substr($decor, -4, 4);
}

function sp_random_string($len = 8)
{
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // Xáo trộn mảng (shuffle)
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

// Xóa thư mục theo kiểu đệ quy
function delFile($dir, $file_type = '')
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        //Mở thư mục // liệt kê toàn bộ file trong thư mục và bỏ qua . và ..
        foreach ($files as $filename) {
            if ($filename != '.' && $filename != '..') {
                if (!is_dir($dir . '/' . $filename)) {
                    if (empty($file_type)) {
                        unlink($dir . '/' . $filename);
                    } else {
                        if (is_array($file_type)) {
                            //Dùng regex khớp file chỉ định
                            if (preg_match($file_type[0], $filename)) {
                                unlink($dir . '/' . $filename);
                            }
                        } else {
                            //Chỉ định file có chứa một số chuỗi nào đó
                            if (false != stristr($filename, $file_type)) {
                                unlink($dir . '/' . $filename);
                            }
                        }
                    }
                } else {
                    delFile($dir . '/' . $filename);
                    rmdir($dir . '/' . $filename);
                }
            }
        }
    } else {
        if (file_exists($dir)) unlink($dir);
    }
}

//Phương thức thông báo lỗi
function showHtml($str)
{
    echo '
		<html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <body>
        ' . $str . '
        </body>
        </html>';
    exit;
}

/**
 * Tính chữ ký
 * @param string $path
 * @throws Exception
 */
function getFileSignature(string $path)
{
    global $fileValue;
    if (!is_dir($path)) {
        $fileValue .= @md5_file($path);
    } else {
        if (!$dh = opendir($path)) throw new Exception($path . " File open failed!");
        while (($file = readdir($dh)) != false) {
            if ($file == "." || $file == "..") {
                continue;
            } else {
                getFileSignature($path . DIRECTORY_SEPARATOR . $file);
            }
        }
        closedir($dh);
    }
}

/**
 * Ghi chữ ký
 * @return void
 * @throws Exception
 */
function generateSignature()
{
    $file = APP_DIR . '.version';
    if (!$data = @file($file)) {
        throw new Exception('Đọc tệp .version thất bại');
    }
    $list = [];
    if (!empty($data)) {
        foreach ($data as $datum) {
            list($name, $value) = explode('=', $datum);
            $list[$name] = rtrim($value);
        }
    }

    if (!isset($list['project_signature'])) {
        $list['project_signature'] = '';
    }

    global $fileValue;
    getFileSignature(APP_DIR . DIRECTORY_SEPARATOR . 'app');
    getFileSignature(APP_DIR . DIRECTORY_SEPARATOR . 'crmeb');

    $list['project_signature'] = md5($fileValue);

    $str = "";
    foreach ($list as $key => $item) {
        $str .= "{$key}={$item}\n";
    }

    file_put_contents($file, $str);
}

function getSchemeAndHost()
{
    // Kiểm tra header do reverse proxy thiết lập
    $request_scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
    $request_host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? '';

    // Nếu không có header reverse proxy thì dùng header HTTP chuẩn
    if (empty($request_host)) {
        $request_scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $request_host = $_SERVER['HTTP_HOST'] ?? '';
    }

    // Nếu vẫn không lấy được domain thì dùng biến server làm phương án dự phòng
    if (empty($request_host)) {
        $request_host = $_SERVER['SERVER_NAME'] ?? 'localhost';

        // Nếu có dùng số cổng (không phải cổng chuẩn) thì thêm số cổng vào
        $port = $_SERVER['SERVER_PORT'] ?? '';
        if (($request_scheme === 'https' && $port !== '443') || ($request_scheme === 'http' && $port !== '80')) {
            $request_host .= ':' . $port;
        }
    }

    // Xây dựng và trả về scheme và host
    return [$request_scheme, $request_host];
}

?>
