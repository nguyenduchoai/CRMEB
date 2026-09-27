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
namespace crmeb\utils;


/**
 * Class Canvas
 * @package crmeb\utils
 * @method $this setFileName(string $fileName) Thiết lập tên file
 * @method $this setPath(string $path) Thiết lập đường dẫn lưu trữ
 * @method $this setImageType(string $imageType) Thiết lập loại ảnh
 * @method $this setBackgroundHeight(int $backgroundHeight) Thiết lập chiều cao nền
 * @method $this setBackgroundWidth(int $backgroundWidth) Thiết lập chiều rộng nền
 * @method $this setFontSize(int $fontSize) Thiết lập cỡ chữ
 * @method $this setFontColor($fontColor) Thiết lập màu chữ
 * @method $this setFontLeft(int $fontLeft) Thiết lập khoảng cách chữ tới lề trái
 * @method $this setFontTop(int $fontTop) Thiết lập khoảng cách chữ tới lề trên
 * @method $this setFontText(string $fontText) Thiết lập chữ
 * @method $this setFontPath(string $fontPath) Thiết lập đường dẫn file font chữ
 * @method $this setFontAngle(int $fontAngle) Thiết lập góc xoay chữ
 * @method $this setImageUrl(string $imageUrl) Thiết lập đường dẫn ảnh
 * @method $this setImageLeft(int $imageLeft) Thiết lập khoảng cách ảnh tới lề trái
 * @method $this setImageTop(int $imageTop) Thiết lập khoảng cách ảnh tới lề trên
 * @method $this setImageRight(int $imageRight) Thiết lập khoảng cách ảnh tới lề trái
 * @method $this setImageStream(bool $imageStream) Thiết lập ảnh có phải là file dạng luồng (stream) không
 * @method $this setImageBottom(int $imageBottom) Thiết lập khoảng cách ảnh tới lề dưới
 * @method $this setImageWidth(int $imageWidth) Thiết lập chiều rộng ảnh
 * @method $this setImageHeight(int $imageHeight) Thiết lập chiều cao ảnh
 * @method $this setImageOpacity(int $imageOpacity) Thiết lập độ trong suốt ảnh
 */
class Canvas
{

    const FONT = 'statics/font/Alibaba-PuHuiTi-Regular.otf';

    /**
     * Chiều rộng nền
     * @var int
     */
    protected $backgroundWidth = 600;

    /**
     * Chiều cao nền
     * @var int
     */
    protected $backgroundHeight = 1000;

    /**
     * Loại ảnh
     * @var string
     */
    protected $imageType = 'jpeg';

    /**
     * Địa chỉ lưu
     * @var string
     */
    protected $path = 'uploads/routine/';

    /**
     * Tên tệp
     * @var string
     */
    protected $fileName;

    /**
     * Quy tắc
     * @var array
     */
    protected $propsRule = ['fileName', 'path', 'imageType', 'backgroundHeight', 'backgroundWidth'];

    /**
     * Tập dữ liệu font chữ
     * @var array
     */
    protected $fontValue = [];

    /**
     * Giá trị (vlaue) mặc định có thể thiết lập cho font chữ
     * @var array
     */
    protected $defaultFontValue = [
        'fontSize' => 0,
        'fontColor' => '231,180,52',
        'fontLeft' => 0,
        'fontTop' => 0,
        'fontText' => '',
        'fontPath' => self::FONT,
        'fontAngle' => 0,
    ];

    protected $defaultFont;
    /**
     * Tập dữ liệu ảnh
     * @var array
     */
    protected $imageValue = [];

    /**
     * Thuộc tính ảnh có thể thiết lập
     * @var array
     */
    protected $defaultImageValue = [
        'imageUrl' => '',
        'imageLeft' => 0,
        'imageTop' => 0,
        'imageRight' => 0,
        'imageBottom' => 0,
        'imageWidth' => 0,
        'imageHeight' => 0,
        'imageOpacity' => 0,
        'imageStream' => false,
    ];

    /**
     * Khởi tạo instance của bản thân (self)
     * @var self
     */
    protected static $instance;

    protected $defaultImage;

    protected function __construct()
    {
        $this->defaultImage = $this->defaultImageValue;
        $this->defaultFont = $this->defaultFontValue;
    }

    /**
     * Khởi tạo instance của class này
     * @return Canvas
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Tạo một ảnh mới
     * @param string $file
     * @return array
     */
    public function createFrom(string $file): array
    {
        $file = str_replace('https', 'http', $file);
        $imagesize = getimagesize($file);
        $type = image_type_to_extension($imagesize[2], true);
        $canvas = NULL;
        switch ($type) {
            case '.png':
                $canvas = imagecreatefrompng($file);
                break;
            case '.jpg':
            case '.jpeg':
                $canvas = imagecreatefromjpeg($file);
                break;
            case '.gif':
                $canvas = imagecreatefromgif($file);
                break;
        }
        return [$canvas, $imagesize];

    }

    /**
     * Thêm font chữ vào
     * @return $this
     */
    public function pushFontValue()
    {
        array_push($this->fontValue, $this->defaultFontValue);
        $this->defaultFontValue = $this->defaultFont;
        return $this;
    }

    /**
     * Thêm ảnh vào
     * @return $this
     */
    public function pushImageValue()
    {
        array_push($this->imageValue, $this->defaultImageValue);
        $this->defaultImageValue = $this->defaultImage;
        return $this;
    }

    /**
     * Tạo nền
     * @param int $w
     * @param int $h
     * @return false|resource
     */
    public function createTrueColor(int $w = 0, int $h = 0)
    {
        return imagecreatetruecolor($w ?: $this->backgroundWidth, $h ?: $this->backgroundHeight);
    }


    /**
     * Bắt đầu vẽ
     * @param bool $force Có ném exception khi tạo lỗi không
     * @return string
     * @throws \Exception
     */
    public function starDrawChart(bool $force = false): string
    {
        try {
            $image = $this->createTrueColor();

            foreach ($this->imageValue as $item) {
                if ($item['imageUrl']) {
                    if ($item['imageStream']) {
                        $res = getimagesizefromstring($item['imageUrl']);
                        $mer = imagecreatefromstring($item['imageUrl']);
                    } else {
                        [$mer, $res] = $this->createFrom($item['imageUrl']);
                    }
                    if ($mer && $res) {
                        $scrW = $res[0] ?? 0;
                        $scrH = $res[1] ?? 0;
                        $imageWidth = $item['imageWidth'] ?: $scrW;
                        $imageHeight = $item['imageHeight'] ?: $scrH;
                        imagecopyresampled($image, $mer, $item['imageLeft'], $item['imageTop'], $item['imageRight'], $item['imageBottom'], $imageWidth, $imageHeight, $scrW, $scrH);
                        unset($scrW, $scrH, $imageWidth, $imageHeight, $res, $mer);
                    }

                }
            }

            foreach ($this->fontValue as $val) {
                if (!is_array($val['fontColor']))
                    $fontColor = explode(',', $val['fontColor']);
                else
                    $fontColor = $val['fontColor'];
                if (count($fontColor) < 3)
                    throw new \RuntimeException('fontColor Separation of thousand bits');
                [$r, $g, $b] = $fontColor;
                $fontColor = imagecolorallocate($image, $r, $g, $b);
                $val['fontLeft'] = $val['fontLeft'] < 0 ? $this->backgroundWidth - abs($val['fontLeft']) : $val['fontLeft'];
                $val['fontTop'] = $val['fontTop'] < 0 ? $this->backgroundHeight - abs($val['fontTop']) : $val['fontTop'];
                imagettftext($image, $val['fontSize'], $val['fontAngle'], $val['fontLeft'], $val['fontTop'], $fontColor, $val['fontPath'], $val['fontText']);
                unset($r, $g, $b, $fontColor);
            }
            if (is_null($this->fileName)) {
                $this->fileName = md5(time());
            }

            $strlen = stripos($this->path, 'uploads');
            $path = $this->path;
            if ($strlen !== false) {
                $path = substr($this->path, 8);
            }

            if (make_path($path, 4, true) === '') {
                throw new \RuntimeException(400555);
            }

            $save_file = $this->path . $this->fileName . '.' . $this->imageType;
            switch ($this->imageType) {
                case 'jpeg':
                case 'jpg':
                    imagejpeg($image, public_path().$save_file, 70);
                    break;
                case 'png':
                    imagepng($image, public_path().$save_file, 70);
                    break;
                case 'gif':
                    imagegif($image, public_path().$save_file, 70);
                    break;
                default:
                    throw new \RuntimeException('Incorrect type set:' . $this->imageType);
            }
            imagedestroy($image);

            return $save_file;
        } catch (\Throwable $e) {
            if ($force || $e instanceof \RuntimeException)
                throw new \Exception($e->getMessage());
            return '';
        }
    }

    /**
     * Magic access..
     *
     * @param $method
     * @param $args
     * @return $this
     */
    public function __call($method, $args): self
    {

        if (0 === stripos($method, 'set') && strlen($method) > 3) {
            $method = lcfirst(substr($method, 3));
        }

        $imageValueKes = array_keys($this->defaultImageValue);
        $fontValueKes = array_keys($this->defaultFontValue);

        if (in_array($method, $imageValueKes)) {
            $this->defaultImageValue[$method] = array_shift($args);
        }

        if (in_array($method, $fontValueKes)) {
            $this->defaultFontValue[$method] = array_shift($args);
        }

        if (in_array($method, $this->propsRule)) {
            $this->{$method} = array_shift($args);
        }

        return $this;
    }


}
