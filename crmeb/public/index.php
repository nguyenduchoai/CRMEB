<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ File khởi điểm ứng dụng ]
namespace think;

if ('7.1.0' > phpversion()) {
    exit('Phiên bản php của bạn quá thấp, không thể cài đặt phần mềm này, phiên bản php tương thích là 7.1~7.4, xin cảm ơn!');
}
if (phpversion() >= '8.0.0') {
    exit('Phiên bản php của bạn quá cao, không thể cài đặt phần mềm này, phiên bản php tương thích là 7.1~7.4, xin cảm ơn!');
}

define('DS', DIRECTORY_SEPARATOR);

//Kiểm tra đã cài đặt hệ thống CRMEB chưa
if(file_exists("./install/") && !file_exists("./install.lock")){
    if($_SERVER['PHP_SELF'] != '/index.php'){
        header("Content-type: text/html; charset=utf-8");
        exit("Vui lòng cài đặt tại thư mục gốc của tên miền, ví dụ:<br/> www.xxx.com/index.php là đúng <br/>  www.xxx.com/www/index.php là sai, sau tên miền không được lồng thêm thư mục, nhưng dự án không giới hạn vị trí thư mục gốc, có thể đặt ở bất kỳ thư mục nào, chỉ cần cấu hình virtual host của apache là được");
    }
    header('Location:/install/index.php');
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

// Thực thi ứng dụng HTTP và phản hồi
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
