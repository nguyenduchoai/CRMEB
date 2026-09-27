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
namespace crmeb\services;

use crmeb\exceptions\AdminException;
use crmeb\services\crud\Make;

/**
 * Class xử lý file
 * Class FileService
 * @package crmeb\services
 */
class FileService
{

    /**
     * Tạo thư mục
     * @param string $dir
     * @return bool
     */
    public static function mkDir(string $dir)
    {
        $dir = rtrim($dir, '/') . '/';
        if (!is_dir($dir)) {
            if (mkdir($dir, 0700) == false) {
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * @param string $filename Ghi tên file
     * @param string $writetext Lưu nội dung
     * @param string $openmod Cách mở
     * @return bool
     */
    public static function writeFile(string $filename, string $writetext, string $openmod = 'w')
    {
        if (@$fp = fopen($filename, $openmod)) {
            flock($fp, 2);
            fwrite($fp, $writetext);
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Xóa tất cả file thỏa điều kiện trong thư mục
     * @param $path Thư mục file
     * @param $start Thời gian bắt đầu
     * @param $end Thời gian kết thúc
     *  return bool
     */
    public static function del_where_dir($path, $start = '', $end = '')
    {
        if (!file_exists($path)) {
            return false;
        }
        $dh = @opendir($path);
        if ($dh) {
            while (($d = readdir($dh)) !== false) {
                if ($d == '.' || $d == '..') {//Nếu là . hoặc ..
                    continue;
                }
                $tmp = $path . '/' . $d;
                if (!is_dir($tmp)) {//Nếu là file
                    $file_time = filemtime($tmp);
                    if ($file_time) {
                        if ($start != '' && $end != '') {
                            if ($file_time >= $start && $file_time <= $end) {
                                @unlink($tmp);
                            }
                        } elseif ($start != '' && $end == '') {
                            if ($file_time >= $start) {
                                @unlink($tmp);
                            }
                        } elseif ($start == '' && $end != '') {
                            if ($file_time <= $end) {
                                @unlink($tmp);
                            }
                        } else {
                            @unlink($tmp);
                        }
                    }
                } else {//Nếu là thư mục
                    self::delDir($tmp, $start, $end);
                }
            }
            //Kiểm tra trong thư mục còn file không
            $count = count(scandir($path));
            closedir($dh);
            if ($count <= 2) @rmdir($path);
        }
        return true;
    }

    /**
     * Xóa thư mục
     * @param $dirName
     * @return bool
     */
    public static function delDir($dirName)
    {
        if (!file_exists($dirName)) {
            return false;
        }

        $dir = opendir($dirName);
        while ($fileName = readdir($dir)) {
            $file = $dirName . '/' . $fileName;
            if ($fileName != '.' && $fileName != '..') {
                if (is_dir($file)) {
                    self::delDir($file);
                } else {
                    unlink($file);
                }
            }
        }
        closedir($dir);
        return rmdir($dirName);
    }


    /**
     * Sao chép thư mục
     * @param string $surDir
     * @param string $toDir
     * @return bool
     */
    public function copyDir(string $surDir, string $toDir)
    {
        $surDir = rtrim($surDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (!file_exists($surDir)) {
            return false;
        }

        if (!file_exists($toDir)) {
            $this->createDir($toDir);
        }
        $file = opendir($surDir);
        while ($fileName = readdir($file)) {
            $file1 = $surDir . '/' . $fileName;
            $file2 = $toDir . '/' . $fileName;
            if ($fileName != '.' && $fileName != '..') {
                if (is_dir($file1)) {
                    $this->copyDir($file1, $file2);
                } else {
                    copy($file1, $file2);
                }
            }
        }
        closedir($file);
        return true;
    }


    /**
     * Liệt kê thư mục
     * @param $dir Tên thư mục
     * @return array Liệt kê nội dung trong thư mục, trả về mảng $dirArray['dir']: chứa thư mục; $dirArray['file']: chứa file
     */
    static function getDirs($dir)
    {
        $dir = rtrim($dir, '/') . '/';
        $dirArray [][] = NULL;
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            $j = 0;
            while (false !== ($file = readdir($handle))) {
                if (is_dir($dir . $file)) { //Kiểm tra có phải thư mục không
                    $dirArray ['dir'] [$i] = $file;
                    $i++;
                } else {
                    $dirArray ['file'] [$j] = $file;
                    $j++;
                }
            }
            closedir($handle);
        }
        return $dirArray;
    }

    /**
     * Tính kích thước thư mục
     * @param $dir
     * @return int Kích thước thư mục (đơn vị B)
     */
    public static function getSize($dir)
    {
        $dirlist = opendir($dir);
        $dirsize = 0;
        while (false !== ($folderorfile = readdir($dirlist))) {
            if ($folderorfile != "." && $folderorfile != "..") {
                if (is_dir("$dir/$folderorfile")) {
                    $dirsize += self::getSize("$dir/$folderorfile");
                } else {
                    $dirsize += filesize("$dir/$folderorfile");
                }
            }
        }
        closedir($dirlist);
        return $dirsize;
    }

    /**
     * Kiểm tra có phải thư mục trống không
     * @param $dir
     * @return bool
     */
    static function emptyDir($dir)
    {
        return (($files = @scandir($dir)) && count($files) <= 2);
    }

    /**
     * Tạo thư mục nhiều cấp
     * @param string $dir
     * @param int $mode
     * @return boolean
     */
    public function createDir(string $dir, int $mode = 0777)
    {
        return is_dir($dir) or ($this->createDir(dirname($dir)) and mkdir($dir, $mode));
    }

    /**
     * Tạo file chỉ định tại đường dẫn chỉ định
     * @param string $path (cần bao gồm tên file và phần mở rộng)
     * @param boolean $over_write Có ghi đè file không
     * @param int $time Đặt thời gian. Mặc định là thời gian hệ thống hiện tại
     * @param int $atime Đặt thời gian truy cập. Mặc định là thời gian hệ thống hiện tại
     * @return boolean
     */
    public function createFile(string $path, bool $over_write = FALSE, int $time = NULL, int $atime = NULL)
    {
        $path = $this->dirReplace($path);
        $time = empty($time) ? time() : $time;
        $atime = empty($atime) ? time() : $atime;
        if (file_exists($path) && $over_write) {
            $this->unlinkFile($path);
        }
        $aimDir = dirname($path);
        $this->createDir($aimDir);
        return touch($path, $time, $atime);
    }

    /**
     * Đóng file
     * @param string $path
     * @return boolean
     */
    public function close(string $path)
    {
        return fclose($path);
    }

    /**
     * Đọc file
     * @param string $file
     * @return boolean
     */
    public static function readFile(string $file)
    {
        return @file_get_contents($file);
    }

    /**
     * Xác định giới hạn tải lên tối đa của server (số byte)
     * @return int Số byte tải lên tối đa mà server cho phép
     */
    public function allowUploadSize()
    {
        $val = trim(ini_get('upload_max_filesize'));
        return $val;
    }

    /**
     * Định dạng byte, chuyển số byte thành kích thước mô tả bằng B K M G T P E Z Y
     * @param int $size Kích thước
     * @param int $dec Kiểu hiển thị
     * @return int
     */
    public static function byteFormat($size, $dec = 2)
    {
        $a = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size, $dec) . " " . $a[$pos];
    }

    /**
     * Xóa thư mục không trống
     * Ghi chú: chỉ có thể xóa file không thuộc hệ thống và có quyền phù hợp, nếu không sẽ xảy ra lỗi
     * @param string $dirName Đường dẫn thư mục
     * @param boolean $is_all Có xóa tất cả không
     * @param boolean $delDir Có xóa thư mục không
     * @return boolean
     */
    public function removeDir(str $dir_path, bool $is_all = FALSE)
    {
        $dirName = $this->dirReplace($dir_path);
        $handle = @opendir($dirName);
        while (($file = @readdir($handle)) !== FALSE) {
            if ($file != '.' && $file != '..') {
                $dir = $dirName . '/' . $file;
                if ($is_all) {
                    is_dir($dir) ? $this->removeDir($dir) : $this->unlinkFile($dir);
                } else {
                    if (is_file($dir)) {
                        $this->unlinkFile($dir);
                    }
                }
            }
        }
        closedir($handle);
        return @rmdir($dirName);
    }

    /**
     * Lấy tên file đầy đủ
     * @param string $fn Đường dẫn
     * @return string
     */
    public function getBasename(string $file_path)
    {
        $file_path = $this->dirReplace($file_path);
        return basename(str_replace('\\', '/', $file_path));
        //return pathinfo($file_path,PATHINFO_BASENAME);
    }

    /**
     * Lấy phần mở rộng của file
     * @param string $file_name Đường dẫn tệp
     * @return string
     */
    public static function getExt(string $file)
    {
        $file = self::dirReplace($file);
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Lấy tên thư mục chỉ định
     * @param string $path Đường dẫn tệp
     * @param int $num Số cấp thư mục cha cần trả về
     * @return string
     */
    public function fatherDir(string $path, $num = 1)
    {
        $path = $this->dirReplace($path);
        $arr = explode('/', $path);
        if ($num == 0 || count($arr) < $num) return pathinfo($path, PATHINFO_BASENAME);
        return substr(strrev($path), 0, 1) == '/' ? $arr[(count($arr) - (1 + $num))] : $arr[(count($arr) - $num)];
    }

    /**
     * Xóa file
     * @param string $path
     * @return boolean
     */
    public function unlinkFile(string $path)
    {
        $path = $this->dirReplace($path);
        if (file_exists($path)) {
            return unlink($path);
        }
    }

    /**
     * Xử lý file (sao chép/di chuyển)
     * @param string $old_path Đường dẫn file cần xử lý (cần có tên file và phần mở rộng)
     * @param string $new_path Đường dẫn file mới (cần tên file mới và phần mở rộng)
     * @param string $type Loại xử lý file
     * @param boolean $overWrite Có ghi đè file đã tồn tại không
     * @param array $ignore Lọc theo phần mở rộng
     * @return boolean
     */
    public function handleFile(string $old_path, string $new_path, string $type = 'copy', bool $overWrite = FALSE, array $ignore = [])
    {
        $old_path = $this->dirReplace($old_path);
        $new_path = $this->dirReplace($new_path);
        if (file_exists($new_path) && $overWrite = FALSE) {
            return FALSE;
        } else if (file_exists($new_path) && $overWrite = TRUE) {
            $this->unlinkFile($new_path);
        }

        $extension = pathinfo($old_path, PATHINFO_EXTENSION);
        if ($ignore && $extension && in_array($extension, $ignore)) {
            return true;
        }

        $aimDir = dirname($new_path);
        $this->createDir($aimDir);
        switch ($type) {
            case 'copy':
                return copy($old_path, $new_path);
            case 'move':
                return @rename($old_path, $new_path);
        }
    }

    /**
     * Xử lý thư mục (sao chép/di chuyển)
     * @param string $old_path Đường dẫn thư mục cần xử lý
     * @param string $aimDir Đường dẫn thư mục mới
     * @param string $type Loại thao tác
     * @param boolean $overWrite Có ghi đè file và thư mục không
     * @param array $ignore Lọc theo tên thư mục
     * @return boolean
     */
    public function handleDir(string $old_path, string $new_path, string $type = 'copy', bool $overWrite = FALSE, array $ignore = [])
    {
        $new_path = $this->checkPath($new_path);
        $old_path = $this->checkPath($old_path);
        if (!is_dir($old_path)) return FALSE;

        if (!file_exists($new_path)) $this->createDir($new_path);

        $dirHandle = opendir($old_path);

        if (!$dirHandle) return FALSE;

        $boolean = TRUE;

        while (FALSE !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') continue;

            if (!is_dir($old_path . $file)) {
                $boolean = $this->handleFile($old_path . $file, $new_path . $file, $type, $overWrite);
            } else {
                if ($ignore && in_array($file, $ignore)) {
                    break;
                }
                $this->handleDir($old_path . $file, $new_path . $file, $type, $overWrite);
            }
        }
        switch ($type) {
            case 'copy':
                closedir($dirHandle);
                return $boolean;
            case 'move':
                closedir($dirHandle);
                return @rmdir($old_path);
        }
    }

    /**
     * Thay thế ký tự tương ứng
     * @param string $path Đường dẫn
     * @return string
     */
    public static function dirReplace(string $path)
    {
        return str_replace('//', '/', str_replace('\\', '/', $path));
    }

    /**
     * Đọc file mẫu tại đường dẫn chỉ định
     * @param string $path File tại đường dẫn chỉ định
     * @return string $rstr
     */
    public static function getTempltes(string $path)
    {
        $path = self::dirReplace($path);
        if (file_exists($path)) {
            $fp = fopen($path, 'r');
            $rstr = fread($fp, filesize($path));
            fclose($fp);
            return $rstr;
        } else {
            return '';
        }
    }

    /**
     * @param string $oldname Tên gốc
     * @param string $newname Tên mới
     * @return bool
     */
    public function rename(string $oldname, string $newname)
    {
        if (($newname != $oldname) && is_writable($oldname)) {
            return rename($oldname, $newname);
        }
    }

    /**
     * Lấy thông tin tại đường dẫn chỉ định
     * @param string $dir Đường dẫn
     * @return ArrayObject
     */
    public function getDirInfo(string $dir)
    {
        $handle = @opendir($dir);//Mở thư mục chỉ định
        $directory_count = 0;
        $total_size = 5;
        $file_cout = 0;
        $file_cout = 0;
        while (FALSE !== ($file_path = readdir($handle))) {
            if ($file_path != "." && $file_path != "..") {
                //is_dir("$dir/$file_path") ? $sizeResult += $this->get_dir_size("$dir/$file_path") : $sizeResult += filesize("$dir/$file_path");
                $next_path = $dir . '/' . $file_path;
                if (is_dir($next_path)) {
                    $directory_count++;
                    $result_value = self::getDirInfo($next_path);
                    $total_size += $result_value['size'];
                    $file_cout += $result_value['filecount'];
                    $directory_count += $result_value['dircount'];
                } elseif (is_file($next_path)) {
                    $total_size += filesize($next_path);
                    $file_cout++;
                }
            }
        }
        closedir($handle);//Đóng thư mục chỉ định
        $result_value['size'] = $total_size;
        $result_value['filecount'] = $file_cout;
        $result_value['dircount'] = $directory_count;
        return $result_value;
    }

    /**
     * Chuyển đổi bảng mã của file chỉ định
     * @param string $path Đường dẫn tệp
     * @param string $input_code Bảng mã gốc
     * @param string $out_code Bảng mã đầu ra
     * @return boolean
     */
    public function changeFileCode(string $path, string $input_code, string $out_code)
    {
        if (is_file($path))//Kiểm tra file có tồn tại không, nếu có thì thực hiện chuyển mã, trả về true
        {
            $content = file_get_contents($path);
            $content = string::chang_code($content, $input_code, $out_code);
            $fp = fopen($path, 'w');
            fclose($fp);
            return (bool)fputs($fp, $content);
        }
    }

    /**
     * Chuyển đổi bảng mã file thỏa điều kiện trong thư mục chỉ định
     * @param string $dirname Đường dẫn thư mục
     * @param string $input_code Bảng mã gốc
     * @param string $out_code Bảng mã đầu ra
     * @param boolean $is_all Có chuyển đổi bảng mã file trong tất cả thư mục con không
     * @param string $exts Loại tệp
     * @return boolean
     */
    public function changeDirFilesCode(string $dirname, string $input_code, string $out_code, bool $is_all = TRUE, string $exts = '')
    {
        if (is_dir($dirname)) {
            $fh = opendir($dirname);
            while (($file = readdir($fh)) !== FALSE) {
                if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) {
                    continue;
                }
                $filepath = $dirname . '/' . $file;

                if (is_dir($filepath) && $is_all == TRUE) {
                    $files = $this->changeDirFilesCode($filepath, $input_code, $out_code, $is_all, $exts);
                } else {
                    if ($this->getExt($filepath) == $exts && is_file($filepath)) {
                        $boole = $this->changeFileCode($filepath, $input_code, $out_code, $is_all, $exts);
                        if (!$boole) continue;
                    }
                }
            }
            closedir($fh);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Liệt kê file và thư mục thỏa điều kiện trong thư mục chỉ định
     * @param string $dirname Đường dẫn
     * @param boolean $is_all Có liệt kê file trong thư mục con không
     * @param string $exts File theo phần mở rộng cần liệt kê
     * @param string $sort Sắp xếp mảng
     * @return ArrayObject
     */
    public function listDirInfo(string $dirname, bool $is_all = FALSE, string $exts = '', string $sort = 'ASC')
    {
        //Xử lý ký tự / thừa
        $new = strrev($dirname);
        if (strpos($new, '/') == 0) {
            $new = substr($new, 1);
        }
        $dirname = strrev($new);

        $sort = strtolower($sort);//Chuyển ký tự thành chữ thường

        $files = [];
        $subfiles = [];

        if (is_dir($dirname)) {
            $fh = opendir($dirname);
            while (($file = readdir($fh)) !== FALSE) {
                if (strcmp($file, '.') == 0 || strcmp($file, '..') == 0) continue;

                $filepath = $dirname . '/' . $file;

                switch ($exts) {
                    case '*':
                        if (is_dir($filepath) && $is_all == TRUE) {
                            $files = array_merge($files, self::listDirInfo($filepath, $is_all, $exts, $sort));
                        }
                        array_push($files, $filepath);
                        break;
                    case 'folder':
                        if (is_dir($filepath) && $is_all == TRUE) {
                            $files = array_merge($files, self::listDirInfo($filepath, $is_all, $exts, $sort));
                            array_push($files, $filepath);
                        } elseif (is_dir($filepath)) {
                            array_push($files, $filepath);
                        }
                        break;
                    case 'file':
                        if (is_dir($filepath) && $is_all == TRUE) {
                            $files = array_merge($files, self::listDirInfo($filepath, $is_all, $exts, $sort));
                        } elseif (is_file($filepath)) {
                            array_push($files, $filepath);
                        }
                        break;
                    default:
                        if (is_dir($filepath) && $is_all == TRUE) {
                            $files = array_merge($files, self::listDirInfo($filepath, $is_all, $exts, $sort));
                        } elseif (preg_match("/\.($exts)/i", $filepath) && is_file($filepath)) {
                            array_push($files, $filepath);
                        }
                        break;
                }

                switch ($sort) {
                    case 'asc':
                        sort($files);
                        break;
                    case 'desc':
                        rsort($files);
                        break;
                    case 'nat':
                        natcasesort($files);
                        break;
                }
            }
            closedir($fh);
            return $files;
        } else {
            return FALSE;
        }
    }

    /**
     * Trả về thông tin thư mục tại đường dẫn chỉ định, bao gồm file và thư mục trong đường dẫn đó
     * @param string $dir
     * @return ArrayObject
     */
    public function dirInfo(string $dir)
    {
        return scandir($dir);
    }

    /**
     * Kiểm tra thư mục có trống không
     * @param string $dir
     * @return boolean
     */
    public function isEmpty(string $dir)
    {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..') {
                closedir($handle);
                return true;
            }
        }
        closedir($handle);
        return false;
    }

    /**
     * Trả về thông tin của file và thư mục chỉ định
     * @param string $file
     * @return ArrayObject
     */
    public static function listInfo(string $file)
    {
        $dir = [];
        $dir['filename'] = basename($file);//Trả về phần tên file trong đường dẫn.
        $dir['pathname'] = strstr(php_uname('s'), 'Windows') ? str_replace('\\', '\\\\', realpath($file)) : realpath($file);//Trả về tên đường dẫn tuyệt đối.
        $dir['owner'] = fileowner($file);//User ID của file (chủ sở hữu).
        $dir['perms'] = fileperms($file);//Trả về số inode của file.
        $dir['inode'] = fileinode($file);//Trả về số inode của file.
        $dir['group'] = filegroup($file);//Trả về group ID của file.
        $dir['path'] = dirname($file);//Trả về phần tên thư mục trong đường dẫn.
        $dir['atime'] = fileatime($file);//Trả về thời gian truy cập lần cuối của file.
        $dir['ctime'] = filectime($file);//Trả về thời gian thay đổi lần cuối của file.
        $dir['perms'] = fileperms($file);//Trả về quyền của file.
        $dir['size'] = self::byteFormat(filesize($file), 2);//Trả về kích thước file.
        $dir['type'] = filetype($file);//Trả về loại file.
        $dir['ext'] = is_file($file) ? pathinfo($file, PATHINFO_EXTENSION) : '';//Trả về phần mở rộng của file
        $dir['mtime'] = filemtime($file);//Trả về thời gian sửa đổi lần cuối của file.
        $dir['isDir'] = is_dir($file);//Kiểm tra tên file chỉ định có phải là một thư mục không.
        $dir['isFile'] = is_file($file);//Kiểm tra file chỉ định có phải là file thông thường không.
        $dir['isLink'] = is_link($file);//Kiểm tra file chỉ định có phải là liên kết (link) không.
        $dir['isReadable'] = is_readable($file);//Kiểm tra file có đọc được không.
        $dir['isWritable'] = is_writable($file);//Kiểm tra file có ghi được không.
        $dir['isUpload'] = is_uploaded_file($file);//Kiểm tra file có phải được tải lên qua HTTP POST không.
        return $dir;
    }

    /**
     * Trả về thông tin về file đang mở
     * @param $file
     * @return ArrayObject
     * Chỉ số   Tên khóa liên kết (từ PHP 4.0.6)   Giải thích
     * 0   dev   Tên thiết bị
     * 1   ino   Số hiệu
     * 2   mode   Chế độ bảo vệ inode
     * 3   nlink   Số lượng liên kết
     * 4   uid   ID người dùng của chủ sở hữu
     * 5   gid   ID nhóm của chủ sở hữu
     * 6   rdev   Loại thiết bị, nếu là thiết bị inode
     * 7   size   Số byte kích thước file
     * 8   atime   Thời gian truy cập lần cuối (Unix timestamp)
     * 9   mtime   Thời gian sửa đổi lần cuối (Unix timestamp)
     * 10   ctime   Thời gian thay đổi lần cuối (Unix timestamp)
     * 11   blksize   Kích thước block của IO hệ thống file
     * 12   blocks   Số lượng block đã chiếm
     */
    public function openInfo(string $file)
    {
        $file = fopen($file, "r");
        $result = fstat($file);
        fclose($file);
        return $result;
    }

    /**
     * Thay đổi thuộc tính liên quan của file và thư mục
     * @param string $file Đường dẫn tệp
     * @param string $type Loại thao tác
     * @param string $ch_info Thông tin thao tác
     * @return boolean
     */
    public function change_file($file, $type, $ch_info)
    {
        switch ($type) {
            case 'group' :
                $is_ok = chgrp($file, $ch_info);//Thay đổi nhóm của file.
                break;
            case 'mode' :
                $is_ok = chmod($file, $ch_info);//Thay đổi chế độ (mode) của file.
                break;
            case 'ower' :
                $is_ok = chown($file, $ch_info);//Thay đổi chủ sở hữu của file.
                break;
        }
    }

    /**
     * Lấy thông tin đường dẫn file
     * @param $full_path Đường dẫn đầy đủ
     * @return ArrayObject
     */
    public function getFileType(string $path)
    {
        //Hàm pathinfo() trả về thông tin đường dẫn file dưới dạng mảng.
        //---------$file_info = pathinfo($path); echo file_info['extension'];----------//
        //extension lấy phần mở rộng của file [pathinfo($path,PATHINFO_EXTENSION)]-----dirname lấy đường dẫn file [pathinfo($path,PATHINFO_DIRNAME)]-----basename lấy tên file đầy đủ [pathinfo($path,PATHINFO_BASENAME)]-----filename lấy tên file [pathinfo($path,PATHINFO_FILENAME)]
        return pathinfo($path);
    }

    /**
     * Lấy thông tin file tải lên
     * @param $file Thông tin thuộc tính file
     * @return array
     */
    public function getUploadFileInfo($file)
    {
        $file_info = request()->file($file);//Lấy thông tin cơ bản của file tải lên
        $info = [];
        $info['type'] = strtolower(trim(stripslashes(preg_replace("/^(.+?);.*$/", "\\1", $file_info['type'])), '"'));//Lấy loại file
        $info['temp'] = $file_info['tmp_name'];//Lấy thư mục tạm lưu file tải lên trên server
        $info['size'] = $file_info['size'];//Lấy kích thước file tải lên
        $info['error'] = $file_info['error'];//Lấy lỗi tải lên file
        $info['name'] = $file_info['name'];//Lấy tên file tải lên
        $info['ext'] = $this->getExt($file_info['name']);//Lấy phần mở rộng file tải lên
        return $info;
    }

    /**
     * Đặt quy tắc đặt tên file
     * @param string $type Quy tắc đặt tên
     * @param string $filename Tên tệp
     * @return string
     */
    public function setFileName(string $type)
    {
        switch ($type) {
            case 'hash' :
                $new_file = md5(uniqid(mt_rand()));//mt_srand() dùng số ngẫu nhiên mã hóa md5 để đặt tên
                break;
            case 'time' :
                $new_file = time();
                break;
            default :
                $new_file = date($type, time());//Đặt tên theo định dạng thời gian
                break;
        }
        return $new_file;
    }

    /**
     * Xử lý đường dẫn lưu file
     * @return string
     */
    public function checkPath($path)
    {
        return (preg_match('/\/$/', $path)) ? $path : $path . '/';
    }

    /**
     * Tải file xuống
     * $save_dir đường dẫn lưu
     * $filename tên file
     * @return array
     */
    public static function downRemoteFile(string $url, string $save_dir = '', string $filename = '', int $type = 0)
    {

        if (trim($url) == '') {
            return ['file_name' => '', 'save_path' => '', 'error' => 1];
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//Lưu tên file
            $ext = strrchr($url, '.');
            //    if($ext!='.gif'&&$ext!='.jpg'){
            //        return ['file_name'=>'','save_path'=>'','error'=>3];
            //    }
            $filename = time() . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //Tạo thư mục lưu
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return ['file_name' => '', 'save_path' => '', 'error' => 5];
        }
        //Phương thức dùng để lấy file từ xa
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //Kích thước file
        $fp2 = fopen($save_dir . $filename, 'a');

        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return ['file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0];
    }

    /**
     * Giải nén file zip
     * @param string $filename
     * @param string $savename
     * @return bool
     */
    public static function zipOpen(string $filename, string $savename)
    {
        $zip = new \ZipArchive;
        $zipfile = $filename;
        $res = $zip->open($zipfile);
        $toDir = $savename;
        if (!file_exists($toDir)) mkdir($toDir, 0777);
        $docnum = $zip->numFiles;
        for ($i = 0; $i < $docnum; $i++) {
            $statInfo = $zip->statIndex($i);
            if ($statInfo['crc'] == 0 && $statInfo['comp_size'] != 2) {
                //Tạo mới thư mục
                mkdir($toDir . '/' . substr($statInfo['name'], 0, -1), 0777);
            } else {
                //Sao chép file
                copy('zip://' . $zipfile . '#' . $statInfo['name'], $toDir . '/' . $statInfo['name']);
            }
        }
        $zip->close();
        return true;
    }

    /**
     *Đặt định dạng font chữ
     * @param $title string Bắt buộc chọn
     * return string
     */
    public static function setUtf8($title)
    {
        return iconv('utf-8', 'gb2312', $title);
    }

    /**
     *Kiểm tra file chỉ định có ghi được không
     * @param $file string Bắt buộc chọn
     * return boole
     */
    public static function isWritable($file)
    {
        $file = str_replace('\\', '/', $file);
        if (!file_exists($file)) return false;
        return is_writable($file);
    }

    /**
     * Đọc nội dung file excel
     * @param $filePath
     * @param $type
     * @param int $row_num
     * @param string $suffix
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function readExcel($filePath, $type, int $row_num = 1, string $suffix = 'Xlsx')
    {
        if (!$filePath) return false;
        $pathInfo = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!$pathInfo || ($pathInfo != "xlsx" && $pathInfo != "xls")) throw new AdminException(400728);
        //Nạp model đọc
        $readModel = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($suffix);
        // Tạo thao tác đọc
        // Mở file, nạp bảng excel

        try {
            $spreadsheet = $readModel->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getHighestColumn();
            $highestRow = $sheet->getHighestRow();
            $lines = $highestRow - 1;
            if ($lines <= 0) {
                throw new AdminException(400729);
            }
            // Dùng để lưu dữ liệu bảng
            $data = [];
            for ($i = $row_num; $i <= $highestRow; $i++) {
                if ($type == 'card') {
                    $t1 = $this->objToStr($sheet->getCellByColumnAndRow(1, $i)->getValue()) ?? '';
                    $t2 = $this->objToStr($sheet->getCellByColumnAndRow(2, $i)->getValue());
                    if ($t2) {
                        $data[] = [
                            'key' => $t1,
                            'value' => $t2
                        ];
                    }
                }
                if ($type == 'express') {
                    $t1 = $this->objToStr($sheet->getCellByColumnAndRow(1, $i)->getValue());
                    $t3 = $this->objToStr($sheet->getCellByColumnAndRow(3, $i)->getValue());
                    $t4 = $this->objToStr($sheet->getCellByColumnAndRow(4, $i)->getValue());
                    $t5 = $this->objToStr($sheet->getCellByColumnAndRow(5, $i)->getValue());
                    if ($t3 && $t5) {
                        $data[] = [
                            'id' => $t1,
                            'delivery_name' => $t3,
                            'delivery_code' => $t4,
                            'delivery_id' => $t5,
                        ];
                    }
                }
                if ($type == 'product') {
                    $t1 = $this->objToStr($sheet->getCellByColumnAndRow(1, $i)->getValue());
                    $t2 = $this->objToStr($sheet->getCellByColumnAndRow(2, $i)->getValue());
                    $t3 = $this->objToStr($sheet->getCellByColumnAndRow(3, $i)->getValue());
                    $t4 = $this->objToStr($sheet->getCellByColumnAndRow(4, $i)->getValue());
                    $t5 = $this->objToStr($sheet->getCellByColumnAndRow(5, $i)->getValue());
                    $t6 = $this->objToStr($sheet->getCellByColumnAndRow(6, $i)->getValue());
                    $t7 = $this->objToStr($sheet->getCellByColumnAndRow(7, $i)->getValue());
                    $t8 = $this->objToStr($sheet->getCellByColumnAndRow(8, $i)->getValue());
                    $t9 = $this->objToStr($sheet->getCellByColumnAndRow(9, $i)->getValue());
                    $t10 = $this->objToStr($sheet->getCellByColumnAndRow(10, $i)->getValue());
                    $t11 = $this->objToStr($sheet->getCellByColumnAndRow(11, $i)->getValue());
                    $t12 = $this->objToStr($sheet->getCellByColumnAndRow(12, $i)->getValue());
                    $t13 = $this->objToStr($sheet->getCellByColumnAndRow(13, $i)->getValue());
                    $t14 = $this->objToStr($sheet->getCellByColumnAndRow(14, $i)->getValue());
                    $t15 = $this->objToStr($sheet->getCellByColumnAndRow(15, $i)->getValue());
                    $t16 = $this->objToStr($sheet->getCellByColumnAndRow(16, $i)->getValue());
                    $t17 = $this->objToStr($sheet->getCellByColumnAndRow(17, $i)->getValue());
                    $t18 = $this->objToStr($sheet->getCellByColumnAndRow(18, $i)->getValue());
                    $t19 = $this->objToStr($sheet->getCellByColumnAndRow(19, $i)->getValue());
                    $t20 = $this->objToStr($sheet->getCellByColumnAndRow(20, $i)->getValue());
                    $t21 = $this->objToStr($sheet->getCellByColumnAndRow(21, $i)->getValue());
                    $t22 = $this->objToStr($sheet->getCellByColumnAndRow(22, $i)->getValue());
                    $t23 = $this->objToStr($sheet->getCellByColumnAndRow(23, $i)->getValue());
                    $t24 = $this->objToStr($sheet->getCellByColumnAndRow(24, $i)->getValue());
                    $t25 = $this->objToStr($sheet->getCellByColumnAndRow(25, $i)->getValue());
                    $t26 = $this->objToStr($sheet->getCellByColumnAndRow(26, $i)->getValue());
                    $t27 = $this->objToStr($sheet->getCellByColumnAndRow(27, $i)->getValue());
                    $t28 = $this->objToStr($sheet->getCellByColumnAndRow(28, $i)->getValue());
                    if ($i == 1) {
                        $header = [
                            $t1,
                            $t2, $t3, $t4, $t5, $t6,
                            $t7, $t8, $t9,
                            $t10, $t11,
                            $t12, $t13, $t14, $t15, $t16, $t17, $t18, $t19, $t20, $t21, $t22, $t23, $t24,
                            $t25, $t26, $t27,
                            $t28
                        ];
                        $verify = [
                            'ID sản phẩm',
                            'Tên sản phẩm', 'Loại sản phẩm', 'Danh mục sản phẩm (cấp 1)', 'Danh mục sản phẩm (cấp 2)', 'Đơn vị tính',
                            'Hình ảnh sản phẩm', 'Video sản phẩm', 'Chi tiết sản phẩm',
                            'Số lượng đã bán', 'Số lượng mua tối thiểu',
                            'Loại quy cách', 'Giá trị loại quy cách', 'Tên quy cách', 'Tổ hợp giá trị quy cách', 'Hình ảnh quy cách', 'Giá bán', 'Giá gốc', 'Giá vốn', 'Tồn kho', 'Trọng lượng', 'Thể tích', 'Mã sản phẩm', 'Mã vạch',
                            'Mô tả ngắn sản phẩm', 'Từ khóa sản phẩm', 'Mã chia sẻ sản phẩm',
                            'Mua hàng tặng điểm thưởng'
                        ];
                        if ($header !== $verify) {
                            throw new AdminException('Cấu trúc dữ liệu không đúng');
                        }
                    } else {
                        $data[] = [
                            'id' => $t1,
                            'store_name' => $t2,
                            'virtual_type' => $t3,
                            'cate_name_one' => $t4,
                            'cate_name_two' => $t5,
                            'unit_name' => $t6,
                            'slider_image' => $t7,
                            'video_link' => $t8,
                            'description' => $t9,
                            'ficti' => $t10,
                            'min_qty' => $t11,
                            'spec_type' => $t12,
                            'sku_type_value' => $t13,
                            'sku_name' => $t14,
                            'sku_value' => $t15,
                            'pic' => $t16,
                            'price' => $t17,
                            'ot_price' => $t18,
                            'cost' => $t19,
                            'stock' => $t20,
                            'weight' => $t21,
                            'volume' => $t22,
                            'bar_code' => $t23,
                            'bar_code_number' => $t24,
                            'store_info' => $t25,
                            'keyword' => $t26,
                            'command_word' => $t27,
                            'give_integral' => $t28,
                        ];
                    }

                }
            }
            return $data;
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**Chuyển object thành chuỗi
     * @param $value
     * @return mixed
     */
    public function objToStr($value)
    {
        return is_object($value) ? $value->__toString() : $value;
    }

    /**
     * Nén thư mục và file
     * @param string $source Đường dẫn thư mục/file cần nén
     * @param string $destination Địa chỉ lưu sau khi nén
     * @param string $folder Tiền tố thư mục, thư mục cha cần bỏ khi lưu
     * @return boolean
     */
    function addZip($source, $destination, $folder = '')
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive;
        if (!$zip->open($destination, $zip::CREATE)) {
            return false;
        }
        $source = str_replace('\\', '/', $source);
        $folder = str_replace('\\', '/', $folder);
        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) continue;
                if (is_dir($file) === true) {
                    $fileName = $folder ? str_replace($folder . '/', '', $file . '/') : $file . '/';
                    $zip->addEmptyDir($fileName);
                } else if (is_file($file) === true) {
                    $fileName = $folder ? str_replace($folder . '/', '', $file) : $file;
                    $zip->addFromString($fileName, file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }
        return $zip->close();
    }

    /**
     * Giải nén thư mục và file
     * @param string $source Đường dẫn file cần giải nén
     * @param string $folder Tiền tố thư mục, thư mục cha cần bỏ khi lưu
     * @return boolean
     */
    public function extractFile(string $source, string $folder): bool
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive;
        $zip->open($source);
        return $zip->extractTo($folder);
    }

    /**
     * Ghi file theo lô (batch)
     * @param array $make
     * @return bool
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/18
     */
    public static function batchMakeFiles(array $make)
    {

        $files = [];
        $dirnames = [];
        foreach ($make as $item) {
            if ($item instanceof Make) {
                $files[] = $item = $item->toArray();
            }
            try {
                $dirnames[] = $dirname = dirname($item['path']);
                if (!is_dir($dirname)) {
                    mkdir($dirname, 0755, true);
                }
            } catch (\Throwable $e) {
                if ($dirnames) {
                    foreach ($dirnames as $dirname) {
                        if (strstr($dirname, app()->getRootPath() . 'backup') !== false) {
                            rmdir($dirname);
                        }
                    }
                }
                throw new \RuntimeException($e->getMessage());
            }
        }
        $res = true;
        foreach ($files as $item) {
            $res = $res && file_put_contents($item['path'], $item['content'], LOCK_EX);
        }

        return $res;
    }

}
