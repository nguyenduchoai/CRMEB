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

if (!function_exists('get_this_class_methods')) {
    /**Lấy phương thức của lớp hiện tại
     * @param $class
     * @return array
     */
    function get_this_class_methods($class, $unarray = [])
    {
        $arrayall = get_class_methods($class);
        if ($parent_class = get_parent_class($class)) {
            $arrayparent = get_class_methods($parent_class);
            $arraynow = array_diff($arrayall, $arrayparent);//Loại bỏ phần của lớp cha
        } else {
            $arraynow = $arrayall;
        }
        return array_diff($arraynow, $unarray);//Loại bỏ phần không dùng
    }
}


if (!function_exists('setconfig')) {
    /**
     * Hàm sửa config
     * @param $arr1 or $string Tiền tố cấu hình
     * @param $arr2 or $string Biến dữ liệu
     * @return bool Trạng thái trả về
     */
    function setconfig($name, $pat, $rep)
    {
        /**
         * Nguyên lý là mở file cấu hình config, dùng regex để tìm và thay thế, sau đó lưu file. Không thể sửa cấu hình có giá trị là mảng
         * Tham số truyền vào là 2 mảng, mảng trước là cấu hình, mảng sau là giá trị. Regex khớp theo dấu nháy đơn. Nếu của bạn là dấu chấm phẩy thì tự sửa thành dấu chấm phẩy
         * $pat[0] = tiền tố tham số; ví dụ: default_return_type
         * $rep[0] = nội dung cần thay thế; ví dụ: json
         */
        $pats = $reps = [];
        if (is_array($pat) && is_array($rep)) {
            for ($i = 0; $i < count($pat); $i++) {
                $pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
                $reps[$i] = "'" . $pat[$i] . "'" . "=>" . "'" . $rep[$i] . "',";
            }
            $fileurl = app()->getConfigPath() . $name . ".php";
            $string = file_get_contents($fileurl); //Tải file cấu hình
            $string = preg_replace($pats, $reps, $string); // Dùng regex tìm rồi thay thế
            @file_put_contents($fileurl, $string); // Ghi file cấu hình
            return true;
        } else if (is_string($pat) && is_string($rep)) {
            $pats = '/\'' . $pat . '\'(.*?),/';
            if (substr_count($rep, '[')) {
                $reps = "'" . $pat . "'" . "=>" . $rep . ",";
            } else {
                $rep = str_replace('\'', "", $rep);
                $reps = "'" . $pat . "'" . "=>" . "'" . $rep . "',";
            }
            $fileurl = app()->getConfigPath() . $name . ".php";
            $string = file_get_contents($fileurl); //Tải file cấu hình
            $string = preg_replace($pats, $reps, $string); // Dùng regex tìm rồi thay thế
            @file_put_contents($fileurl, $string); // Ghi file cấu hình
            return true;
        } else {
            return false;

        }
    }
}
if (!function_exists('arrayToText')) {
    /**
     * Hàm sửa config
     * @param $array
     * @return string
     */
    function arrayToText($array)
    {
        $config = print_r($array, true);
        $config = str_replace('[', "\"", $config);
        $config = str_replace(']', "\"", $config);
        $input = explode("\n", $config);
        foreach ($input as $k => $v) {
            if (empty($v) || strpos($v, 'Array') !== false || strpos($v, '(') !== false || strpos($v, ')') !== false) {
                continue;
            }
            $tmpValArr = explode('=>', $v);
            if (count($tmpValArr) == 2) {
                $input[$k] = $tmpValArr[0] . '=> \'' . trim($tmpValArr[1]) . '\',';
            }
        }
        $config = implode("\n", $input);
        $config = str_replace('Array', "", $config);
        $config = str_replace('(', "[", $config);
        $config = str_replace(')', "],", $config);
        $config = rtrim($config, "\n");
        $config = rtrim($config, ",");
        $config = "<?php \n return " . $config . ';';
//        $fileurl = app()->getConfigPath() ."templates.php";
//        @file_put_contents($fileurl, $config); // Ghi file cấu hình
        return $config;
    }
}
if (!function_exists('attr_format')) {
    /**
     * Định dạng thuộc tính
     * @param $arr
     * @return array
     */
    function attr_format($arr): array
    {
        $len = count($arr);
        $title = array_column($arr, 'value');
        $result = [];

        // Khi mảng thuộc tính không rỗng, tiến hành định dạng và tổ hợp
        if ($len > 0) {
            // Khi số loại thuộc tính lớn hơn 1, cần tổ hợp theo tích Descartes
            if ($len > 1) {
                // Lấy chi tiết thuộc tính của nhóm đầu tiên làm kết quả ban đầu
                $result = $arr[0]['detail'];
                // Lần lượt tổ hợp từng cặp với chi tiết thuộc tính của mỗi nhóm tiếp theo
                for ($i = 0; $i < $len - 1; $i++) {
                    // Lưu tập kết quả hiện tại để dùng cho vòng lặp tiếp theo
                    $temp = $result;
                    // Xóa kết quả, chuẩn bị thu thập lại các tổ hợp mới
                    $result = [];
                    // Duyệt tất cả các tổ hợp thu được ở vòng trước
                    foreach ($temp as $item) {
                        // Ghép lần lượt tổ hợp hiện tại với từng chi tiết thuộc tính của nhóm tiếp theo
                        foreach ($arr[$i + 1]['detail'] as $datum) {
                            // Nếu phần tử là mảng thì lấy giá trị value để ghép; nếu không thì ghép trực tiếp
                            if (is_array($item)) {
                                $result[] = trim($item['value']) . ',' . trim($datum['value']);
                            } else {
                                $result[] = trim($item) . ',' . trim($datum);
                            }
                        }
                    }
                }
            } else {
                // Khi chỉ có một loại thuộc tính, lấy trực tiếp tất cả giá trị thuộc tính của nhóm đó
                foreach ($arr[0]['detail'] as $item) {
                    // Tương tự, phân biệt mảng và không phải mảng, thống nhất lấy value hoặc lấy giá trị trực tiếp
                    if (is_array($item)) {
                        $result[] = trim($item['value']);
                    } else {
                        $result[] = trim($item);
                    }
                }
            }
        }
        // Trả về danh sách giá trị thuộc tính sau khi tổ hợp và danh sách tên thuộc tính
        return [$result, $title];
    }
}

if (!function_exists('verify_domain')) {

    /**
     * Xác thực tên miền có hợp lệ không
     * @param string $domain
     * @return bool
     */
    function verify_domain(string $domain): bool
    {
        $res = "/^(?=^.{3,255}$)(http(s)?:\/\/)(www\.)?[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+(:\d+)*(\/\w+\.\w+)*$/";
        if (preg_match($res, $domain))
            return true;
        else
            return false;
    }
}
