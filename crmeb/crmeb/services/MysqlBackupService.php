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
use think\facade\Db;

class MysqlBackupService
{
    /**
     * Con trỏ file
     * @var resource
     */
    private $fp;
    /**
     * Thông tin file sao lưu: part - số volume, name - tên file
     * @var array
     */
    private $file;
    /**
     * Kích thước file đang mở
     * @var integer
     */
    private $size = 0;

    /**
     * Cấu hình cơ sở dữ liệu
     * @var integer
     */
    private $dbconfig = array();
    /**
     * Cấu hình sao lưu
     * @var integer
     */
    private $config = array(
        'path' => './Data/',
        //Đường dẫn sao lưu cơ sở dữ liệu
        'part' => 20971520,
        //Kích thước volume backup cơ sở dữ liệu
        'compress' => 0,
        //File backup cơ sở dữ liệu có bật nén không, 0 là không nén, 1 là nén
        'level' => 9,
    );

    /**
     * Phương thức khởi tạo sao lưu cơ sở dữ liệu
     *
     * @param array $file Thông tin file sao lưu hoặc khôi phục
     * @param array $config Thông tin cấu hình sao lưu
     */
    public function __construct($config = [])
    {
        $this->config['path'] = app()->getRootPath() . 'backup/';
        $this->config = array_merge($this->config, $config);
        //Khởi tạo tên file
        $this->setFile();
        //Khởi tạo tham số kết nối cơ sở dữ liệu
        $this->setDbConn();
        //Kiểm tra file có ghi được không
        if (!$this->checkPath($this->config['path'])) {
            throw new AdminException(400734);
        }
    }

    /**
     * Đặt thời gian timeout chạy script
     * 0 nghĩa là không giới hạn, hỗ trợ thao tác liên tiếp
     */
    public function setTimeout($time = null)
    {
        if (!is_null($time)) {
            set_time_limit($time) || ini_set("max_execution_time", $time);
        }
        return $this;
    }

    /**
     * Đặt tham số bắt buộc để kết nối cơ sở dữ liệu
     *
     * @param array $dbconfig Thông tin cấu hình kết nối cơ sở dữ liệu
     * @return $this
     */
    public function setDbConn($dbconfig = [])
    {
        if (empty($dbconfig)) {
            $this->dbconfig = config('database.connections.' . config('database.default'));
            //$this->dbconfig = Config::get('database');
        } else {
            $this->dbconfig = $dbconfig;
        }
        return $this;
    }

    /**
     * Đặt tên file sao lưu
     *
     * @param null $file
     * @return $this
     */
    public function setFile($file = null)
    {
        if (is_null($file)) {
            $this->file = ['name' => date('Ymd-His'), 'part' => 1];
        } else {
            if (!array_key_exists("name", $file) && !array_key_exists("part", $file)) {
                $this->file = $file['1'];
            } else {
                $this->file = $file;
            }
        }
        return $this;
    }

    //Kết nối class dữ liệu
    public static function connect()
    {
        return Db::connect();
    }

    /**
     * Danh sách bảng cơ sở dữ liệu
     *
     * @param null $table
     * @param int $type
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function dataList(?string $table = null, int $type = 1)
    {
        $db = self::connect();
        if (is_null($table)) {
            $list = $db->query("SHOW TABLE STATUS");
        } else {
            if ($type) {
                $list = $db->query("SHOW FULL COLUMNS FROM {$table}");
            } else {
                $list = $db->query("show columns from {$table}");
            }
        }
        return array_map('array_change_key_case', $list);
        //$list;
    }

    /**
     * Danh sách file sao lưu cơ sở dữ liệu
     *
     * @return array
     */
    public function fileList()
    {
        if (!is_dir($this->config['path'])) {
            mkdir($this->config['path'], 0755, true);
        }
        $path = realpath($this->config['path']);
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path, $flag);
        $list = array();
        foreach ($glob as $name => $file) {
            if (preg_match('/^\\d{8,8}-\\d{6,6}-\\d+\\.sql(?:\\.gz)?$/', $name)) {
                $info['filename'] = $name;
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];
                if (isset($list["{$date} {$time}"])) {
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                } else {
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }
                $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                $info['compress'] = $extension === 'SQL' ? '-' : $extension;
                $info['time'] = strtotime("{$date} {$time}");
                $list["{$date} {$time}"] = $info;
            }
        }
        return $list;
    }

    /**
     * @param string $type
     * @param int $time
     * @return array|false|string
     * @throws \Exception
     */
    public function getFile($type = '', $time = 0)
    {
        //
        if (!is_numeric($time)) {
            throw new AdminException(400735);
        }
        switch ($type) {
            case 'time':
                $name = date('Ymd-His', $time) . '-*.sql*';
                $path = realpath($this->config['path']) . DIRECTORY_SEPARATOR . $name;
                return glob($path);
            case 'timeverif':
                $name = date('Ymd-His', $time) . '-*.sql*';
                $path = realpath($this->config['path']) . DIRECTORY_SEPARATOR . $name;
                $files = glob($path);
                $list = array();
                foreach ($files as $name) {
                    $basename = basename($name);
                    $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                    $gz = preg_match('/^\\d{8,8}-\\d{6,6}-\\d+\\.sql.gz$/', $basename);
                    $list[$match[6]] = array($match[6], $name, $gz);
                }
                $last = end($list);
                if (count($list) === $last[0]) {
                    return $list;
                } else {
                    throw new AdminException(400736);
                }
            case 'pathname':
                return "{$this->config['path']}{$this->file['name']}-{$this->file['part']}.sql";
            case 'filename':
                return "{$this->file['name']}-{$this->file['part']}.sql";
            case 'filepath':
                return $this->config['path'];
            default:
                $arr = array('pathname' => "{$this->config['path']}{$this->file['name']}-{$this->file['part']}.sql", 'filename' => "{$this->file['name']}-{$this->file['part']}.sql", 'filepath' => $this->config['path'], 'file' => $this->file);
                return $arr;
        }
    }

    /**
     * Xóa file backup
     * @param $time
     * @return mixed
     * @throws \Exception
     */
    public function delFile($time)
    {
        if ($time) {
            $file = $this->getFile('time', $time);
            array_map("unlink", $this->getFile('time', $time));
            if (count($this->getFile('time', $time))) {
                throw new AdminException(100008);
            } else {
                return $time;
            }
        } else {
            throw new AdminException(400735);
        }
    }

    /**
     * Tải xuống file sao lưu
     *
     * @param $time
     * @param int $part
     * @throws \Exception
     */
    public function downloadFile($time, int $part = 0, bool $isFile = false)
    {
        $file = $this->getFile('time', $time);
        $fileName = $file[$part];
        if (file_exists($fileName)) {
            if ($isFile) {
                $key = password_hash(time() . $fileName, PASSWORD_DEFAULT);
                CacheService::set($key, ['path' => $fileName, 'fileName' => substr(strstr($fileName, 'backup'), 7)], 300);
                return $key;
            }
            ob_end_clean();
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header('Access-Control-Allow-Origin: ' . request()->domain());
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($fileName));
            header('Content-Disposition: attachment; filename=' . basename($fileName));
            return readfile($fileName);
        } else {
            throw new AdminException(400736);
        }
    }

    public function import($start)
    {
        //Khôi phục dữ liệu
        $db = self::connect();
        if ($this->config['compress']) {
            $gz = gzopen($this->file[1], 'r');
            $size = 0;
        } else {
            $size = filesize($this->file[1]);
            $gz = fopen($this->file[1], 'r');
        }
        $sql = '';
        if ($start) {
            $this->config['compress'] ? gzseek($gz, $start) : fseek($gz, $start);
        }
        for ($i = 0; $i < 1000; $i++) {
            $sql .= $this->config['compress'] ? gzgets($gz) : fgets($gz);
            if (preg_match('/.*;$/', trim($sql))) {
                if (false !== $db->execute($sql)) {
                    $start += strlen($sql);
                } else {
                    return false;
                }
                $sql = '';
            } elseif ($this->config['compress'] ? gzeof($gz) : feof($gz)) {
                return 0;
            }
        }
        return array($start, $size);
    }

    /**
     * Ghi dữ liệu khởi tạo
     *
     * @return boolean true - Ghi thành công, false - ghi thất bại
     */
    public function Backup_Init()
    {
        $sql = "-- -----------------------------\n";
        $sql .= "-- Think MySQL Data Transfer \n";
        $sql .= "-- \n";
        $sql .= "-- Host     : " . $this->dbconfig['hostname'] . "\n";
        $sql .= "-- Port     : " . $this->dbconfig['hostport'] . "\n";
        $sql .= "-- Database : " . $this->dbconfig['database'] . "\n";
        $sql .= "-- \n";
        $sql .= "-- Part : #{$this->file['part']}\n";
        $sql .= "-- Date : " . date("Y-m-d H:i:s") . "\n";
        $sql .= "-- -----------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        return $this->write($sql);
    }

    /**
     * Sao lưu cấu trúc bảng
     *
     * @param string $table
     * @param int $start
     * @return bool|int
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function backup(string $table, int $start, $sql = '')
    {
        $db = self::connect();
        // Sao lưu cấu trúc bảng
        if (0 == $start) {
            $result = $db->query("SHOW CREATE TABLE `{$table}`");
            $sql .= "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= trim($result[0]['Create Table']) . ";\n\n";
        }
        //Tổng số dữ liệu
        $result = $db->query("SELECT COUNT(*) AS count FROM `{$table}`");
        $count = $result['0']['count'];
        //Backup dữ liệu bảng
        if ($count) {
            //Ghi chú thích dữ liệu
            if (0 == $start) {
                $sql .= "-- -----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- -----------------------------\n";
            }
            //Sao lưu bản ghi dữ liệu
            $result = $db->query("SELECT * FROM `{$table}` LIMIT :MIN, 1000", ['MIN' => intval($start)]);
            foreach ($result as $row) {
                $row = array_map('addslashes', $row);
                $sql .= "INSERT INTO `{$table}` VALUES ('" . str_replace(array("\r", "\n"), array('\\r', '\\n'), implode("', '", $row)) . "');\n";
            }
            if (false === $this->write($sql)) {
                return false;
            }
            //Còn nhiều dữ liệu
            if ($count > $start + 1000) {
                //return array($start + 1000, $count);
                return $this->backup($table, $start + 1000);
            }
        }
        //Sao lưu bảng tiếp theo
        return 0;
    }

    /**
     * Tối ưu bảng
     *
     * @param array|string $tables
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function optimize($tables)
    {
        if ($tables) {
            $db = self::connect();
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = $db->query("OPTIMIZE TABLE `{$tables}`");
            } else {
                $list = $db->query("OPTIMIZE TABLE {$tables}");
            }
            if (!$list) {
                throw new AdminException(400737);
            }
            return $list;
        } else {
            throw new AdminException(400738);
        }
    }

    /**
     * Sửa chữa bảng
     *
     * @param string|null $tables
     * @return array
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function repair(?string $tables = null)
    {
        if ($tables) {
            $db = self::connect();
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = $db->query("REPAIR TABLE `{$tables}`");
            } else {
                $list = $db->query("REPAIR TABLE {$tables}");
            }
            if ($list) {
                return $list;
            } else {
                throw new AdminException(400737);
            }
        } else {
            throw new AdminException(400738);
        }
    }

    /**
     * Ghi câu lệnh SQL
     *
     * @param string $sql Câu lệnh SQL cần ghi
     * @return boolean     true - Ghi thành công, false - ghi thất bại!
     */
    private function write(string $sql)
    {
        $size = strlen($sql);
        //Vì lý do nén nên không thể tính chính xác độ dài sau khi nén, ở đây tạm giả định tỷ lệ nén là 50%,
        //Thông thường tỷ lệ nén sẽ cao hơn 50%;
        $size = $this->config['compress'] ? $size / 2 : $size;
        $this->open($size);
        return $this->config['compress'] ? @gzwrite($this->fp, $sql) : @fwrite($this->fp, $sql);
    }

    /**
     * Mở một volume, dùng để ghi dữ liệu
     *
     * @param integer $size Kích thước dữ liệu ghi
     */
    private function open(int $size)
    {
        if ($this->fp) {
            $this->size += $size;
            if ($this->size > $this->config['part']) {
                $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
                $this->fp = null;
                $this->file['part']++;
                session('backup_file', $this->file);
                $this->Backup_Init();
            }
        } else {
            $backuppath = $this->config['path'];
            $filename = "{$backuppath}{$this->file['name']}-{$this->file['part']}.sql";
            if ($this->config['compress']) {
                $filename = "{$filename}.gz";
                $this->fp = @gzopen($filename, "a{$this->config['level']}");
            } else {
                $this->fp = @fopen($filename, 'a');
            }
            $this->size = filesize($filename) + $size;
        }
    }

    /**
     * Kiểm tra thư mục có ghi được không
     *
     * @param string $path
     * @return bool
     */
    protected function checkPath(string $path)
    {
        if (is_dir($path)) {
            return true;
        }
        if (mkdir($path, 0755, true)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Phương thức destructor, dùng để đóng resource file
     */
    public function __destruct()
    {
        $this->config['compress'] ? @gzclose($this->fp) : @fclose($this->fp);
    }
}
