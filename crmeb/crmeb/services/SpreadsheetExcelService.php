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
namespace crmeb\services;


use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class SpreadsheetExcelService
{
    //
    private static $instance = null;
    //Đối tượng khởi tạo (instance) của PHPSpreadsheet
    private static $spreadsheet = null;
    //Đối tượng khởi tạo (instance) của sheet
    private static $sheet = null;
    //Đếm số dòng tiêu đề bảng
    protected static $count;
    //Số dòng chiếm bởi tiêu đề bảng
    protected static $topNumber = 3;
    //Chữ cái ứng với dòng bảng có thể chiếm, tương ứng self::$cellkey
    protected static $cells;
    //Dữ liệu tiêu đề bảng
    protected static $data = [];
    //Tên tệp
    protected static $title = 'Xuất đơn hàng';
    //Chiều rộng dòng
    protected static $width = 20;
    //Chiều cao dòng
    protected static $height = 50;
    //Thư mục lưu file
    protected static $path = './phpExcel/';
    //Đặt style
    private static $styleArray = [
//         'borders' => [
//             'allBorders' => [
// //                PHPExcel_Style_Border có nhiều thuộc tính, muốn dùng cái khác thì tự xem
//                // 'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,//viền dày
// //                'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,//kiểu đôi
// //                'style' => \PHPExcel_Style_Border::BORDER_HAIR,//đường nét đứt mảnh
// //                'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,//đường liền vừa
// //                'style' => \PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT,//đường nét đứt vừa
// //                'style' => \PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT,//đường chấm gạch vừa
//                 'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,//viền mỏng
//                 // 'color' => ['argb' => 'FFFF0000'],
//             ],
//         ],
        'font' => [
            'bold' => true
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$spreadsheet = $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            self::$sheet = $spreadsheet->getActiveSheet();
        }
        return self::$instance;
    }

    /**
     *Đặt định dạng font chữ
     * @param $title string Bắt buộc chọn
     * return string
     */
    public static function setUtf8(string $title)
    {
        return iconv('utf-8', 'gb2312', $title);
    }

    /**
     *  Tạo thư mục lưu excel
     *  return string
     */
    public static function savePath()
    {
        if (!is_dir(self::$path)) {
            if (mkdir(self::$path, 0700) == false) {
                return false;
            }
        }
        //Thư mục cấp 1 theo năm-tháng
        $mont_path = self::$path . date('Ym');
        if (!is_dir($mont_path)) {
            if (mkdir($mont_path, 0700) == false) {
                return false;
            }
        }
        //Thư mục cấp 2 theo ngày
        $day_path = $mont_path . '/' . date('d');
        if (!is_dir($day_path)) {
            if (mkdir($day_path, 0700) == false) {
                return false;
            }
        }
        return $day_path;
    }

    /**
     * Đặt tiêu đề
     * @param $title string || array ['title'=>'','name'=>'','info'=>[]]
     * @param $Name string
     * @param $info string || array;
     * @return $this
     */
    public function setExcelTile(string $title = '', string $Name = '', $info = [])
    {
        //Đặt tham số
        if (is_array($title)) {
            if (isset($title['title'])) $title = $title['title'];
            if (isset($title['name'])) $Name = $title['name'];
            if (isset($title['info'])) $info = $title['info'];
        }
        if (empty($title))
            $title = self::$title;
        else
            self::$title = $title;

        if (empty($Name)) $Name = time();
        //Đặt thuộc tính Excel
        self::$spreadsheet->getProperties()
            ->setCreator("Neo")
            ->setLastModifiedBy("Neo")
            ->setTitle(self::setUtf8($title))
            ->setSubject($Name)
            ->setDescription("")
            ->setKeywords($Name)
            ->setCategory("");
        self::$sheet->setTitle($Name);
        self::$sheet->setCellValue('A1', $title);
        self::$sheet->setCellValue('A2', self::setCellInfo($info));
        //Căn giữa văn bản
        self::$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        self::$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        //Gộp ô tiêu đề bảng
        self::$sheet->mergeCells('A1:' . self::$cells . '1');
        self::$sheet->mergeCells('A2:' . self::$cells . '2');

        self::$sheet->getRowDimension(1)->setRowHeight(40);
        self::$sheet->getRowDimension(2)->setRowHeight(20);

        //Đặt font tiêu đề bảng
        self::$sheet->getStyle('A1')->getFont()->setName('Arial');
        self::$sheet->getStyle('A1')->getFont()->setSize(20);
        self::$sheet->getStyle('A1')->getFont()->setBold(true);
        self::$sheet->getStyle('A2')->getFont()->setName('Arial');
        self::$sheet->getStyle('A2')->getFont()->setSize(14);
        self::$sheet->getStyle('A2')->getFont()->setBold(true);

        self::$sheet->getStyle('A3:' . self::$cells . '3')->getFont()->setBold(true);
        return $this;
    }

    /**
     * Đặt nội dung tiêu đề dòng thứ hai
     * @param $info
     * @return string|void
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/8/7
     */
    private static function setCellInfo($info)
    {
        $content = ['Người thao tác:', 'Ngày xuất:' . date('Y-m-d', time()), 'Địa chỉ:', 'Điện thoại:'];
        if (is_array($info) && !empty($info)) {
            if (isset($info['name'])) {
                $content[0] .= $info['name'];
            } else {
                $content[0] .= $info[0] ?? '';
            }
            if (isset($info['site'])) {
                $content[2] .= $info['site'];
            } else {
                $content[2] .= $info[1] ?? '';
            }
            if (isset($info['phone'])) {
                $content[3] .= $info['phone'];
            } else {
                $content[3] .= $info[2] ?? '';
            }
            return implode(' ', $content);
        } else if (is_string($info)) {
            return empty($info) ? implode(' ', $content) : $info;
        }
    }

    /**
     * Đặt thông tin phần đầu
     * @param $data array
     * @return $this
     */
    public static function setExcelHeader(array $data)
    {
        $span = 'A';
        foreach ($data as $value) {
            self::$sheet->getColumnDimension($span)->setWidth(self::$width);
            self::$sheet->setCellValue($span . self::$topNumber, $value);
            $span++;
        }
        self::$sheet->getRowDimension(3)->setRowHeight(self::$height);
        self::$cells = $span;
        return new self;
    }

    /**
     *
     * Xuất dữ liệu excl
     * @param  $data Dữ liệu cần xuất, định dạng vẫn giống như trước
     *
     * Xử lý đặc biệt: gộp ô cần xử lý dữ liệu trước
     */
    public function setExcelContent($data = [])
    {
        if (!empty($data) && is_array($data)) {
            $span = '';
            $column = self::$topNumber + 1;
            // Ghi dòng
            foreach ($data as $rows) {
                $span = 'A';
                // Ghi cột
                foreach ($rows as $value) {
                    self::$sheet->setCellValue($span . $column, $value);
                    $span++;
                }
                $column++;
            }
            self::$sheet->getDefaultRowDimension()->setRowHeight(self::$height);
            //Đặt kiểu font nội dung
            self::$sheet->getStyle('A1:' . $span . $column)->applyFromArray(self::$styleArray);
            //Đặt viền
            self::$sheet->getStyle('A1:' . $span . $column)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            //Đặt tự động xuống dòng
            self::$sheet->getStyle('A4:' . $span . $column)->getAlignment()->setWrapText(true);
        }
        return new self;
    }

    /**
     * Lưu dữ liệu bảng, tải xuống trực tiếp
     * @param string $fileName
     * @param string $suffix Phần mở rộng file
     * @param bool $is_save Có lưu file không
     * @return string string
     * @throws Exception
     */
    public function excelSave(string $fileName = '', string $suffix = 'xlsx', bool $is_save = false)
    {
        if (empty($fileName)) {
            $fileName = date('YmdHis') . time();
        }
        if (empty($suffix)) {
            $suffix = 'xlsx';
        }
        // Đổi tên bảng (bảng mã UTF8 thì không cần bước này)
        if (mb_detect_encoding($fileName) != "UTF-8") {
            $fileName = iconv("utf-8", "gbk//IGNORE", $fileName);
        }
        if ($suffix == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $class = "\PhpOffice\PhpSpreadsheet\Writer\Xlsx";
        } elseif ($suffix == 'xls') {
            header('Content-Type:application/vnd.ms-excel');
            $class = "\PhpOffice\PhpSpreadsheet\Writer\Xls";
        }
        // Dọn cache
//        ob_end_clean();
        $spreadsheet = self::$spreadsheet;
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if (!$is_save) {//Tải xuống trực tiếp

            header('Content-Disposition: attachment;filename="' . $fileName . '.' . $suffix . '"');
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            // Xóa sạch, giải phóng bộ nhớ
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            exit;
        } else {//Lưu tệp
            $path = self::savePath() . '/' . $fileName . '.' . $suffix;
            //$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            //$writer->save($path);
            $writer->save(public_path() . $path);
            // Xóa sạch, giải phóng bộ nhớ
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            return $path;
        }
    }

}
