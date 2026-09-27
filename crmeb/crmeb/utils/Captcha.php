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
namespace crmeb\utils;


use crmeb\services\CacheService;
use think\facade\Config;
use think\Response;

/**
 * Class Captcha
 * @package crmeb\utils
 */
class Captcha
{
    // Instance ảnh mã captcha
    private $im = null;
    // Màu chữ mã captcha
    private $color = null;
    // Tập ký tự mã captcha
    protected $codeSet = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
    // Thời gian hết hạn mã captcha (giây)
    protected $expire = 1800;
    // Dùng mã captcha tiếng Trung
    protected $useZh = false;
    // Chuỗi mã captcha tiếng Trung
    protected $zhSet = '们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借';
    // Dùng ảnh nền
    protected $useImgBg = false;
    // Cỡ chữ mã captcha (px)
    protected $fontSize = 25;
    // Có vẽ đường cong gây nhiễu không
    protected $useCurve = false;
    // Có thêm nhiễu hạt không
    protected $useNoise = true;
    // Chiều cao ảnh mã captcha
    protected $imageH = 0;
    // Chiều rộng ảnh mã captcha
    protected $imageW = 0;
    // Số ký tự mã captcha
    protected $length = 4;
    // Font chữ mã captcha, không thiết lập thì lấy ngẫu nhiên
    protected $fontttf = '';
    // Màu nền
    protected $bg = [243, 251, 254];
    //Mã captcha dạng phép tính
    protected $math = false;
    //Mã xác thực
    protected $generator;

    /**
     * Phương thức khởi tạo (constructor), thiết lập tham số
     * Captcha constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->length = $config['length'] ?? Config::get('captcha.length', $this->length);
        $this->imageW = $config['imageW'] ?? $this->imageW;
        $this->imageH = $config['imageH'] ?? $this->imageH;
        $this->useCurve = $config['useCurve'] ?? $this->useCurve;
        $this->fontSize = $config['fontSize'] ?? $this->fontSize;
        $this->useImgBg = $config['useImgBg'] ?? $this->useImgBg;
        $this->useZh = $config['useZh'] ?? $this->useZh;
        $this->expire = $config['expire'] ?? $this->expire;
        $this->math = $config['math'] ?? $this->math;
        $this->zhSet = $config['zhSet'] ?? $this->zhSet;
        $this->codeSet = $config['codeSet'] ?? $this->codeSet;
    }

    /**
     * Tạo mã xác thực
     * @return array
     * @throws Exception
     */
    public function generate(): array
    {
        $bag = '';

        if ($this->math) {
            $this->useZh = false;

            $x = random_int(10, 30);
            $y = random_int(1, 9);
            $bag = "{$x} + {$y} = ";
            $key = $x + $y;
            $key .= '';
        } else {
            if ($this->useZh) {
                $characters = preg_split('/(?<!^)(?!$)/u', $this->zhSet);
            } else {
                $characters = str_split($this->codeSet);
            }

            for ($i = 0; $i < $this->length; $i++) {
                $bag .= $characters[rand(0, count($characters) - 1)];
            }

            $key = mb_strtolower($bag, 'UTF-8');
        }

        $hash = password_hash($key, PASSWORD_BCRYPT, ['cost' => 10]);

        $generator = [
            'value' => $bag,
            'key' => $hash,
        ];
        CacheService::set('captcha_' . $key, $generator, $this->expire);
        return $generator;
    }

    /**
     * Xác thực mã xác thực có đúng không
     * @access public
     * @param string $code Mã captcha người dùng nhập
     * @return bool Mã captcha người dùng nhập có đúng không
     */
    public function check(string $code): bool
    {
        $code = mb_strtolower(trim($code), 'UTF-8');
        $name = 'captcha_' . $code;
        if (!CacheService::has($name) || !($generator = CacheService::get($name))) {
            return false;
        }
        $key = $generator['key'] ?? '';
        $res = password_verify($code, $key);

        if ($res) {
            CacheService::delete($name);
        }

        return $res;
    }

    /**
     * Xuất mã captcha
     * @param array|null $generator
     * @return $this
     */
    public function create(array $generator = null): Response
    {
        if (!$generator) {
            $generator = $this->generate();
        }

        // Chiều rộng ảnh (px)
        $this->imageW || $this->imageW = $this->length * $this->fontSize * 1.5 + $this->length * $this->fontSize / 2;
        // Chiều cao ảnh (px)
        $this->imageH || $this->imageH = $this->fontSize * 2.5;
        // Tạo một ảnh kích thước $this->imageW x $this->imageH
        $this->im = imagecreate($this->imageW, $this->imageH);
        // Thiết lập nền
        imagecolorallocate($this->im, $this->bg[0], $this->bg[1], $this->bg[2]);

        // Màu chữ ngẫu nhiên cho mã captcha
        $this->color = imagecolorallocate($this->im, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));

        //TODO Cần kiểm tra (test)
        // Mã captcha dùng font chữ ngẫu nhiên
        $ttfPath = dirname(app()->getThinkPath(), 2) . DS . 'think-captcha' . DS . 'assets/' . ($this->useZh ? 'zhttfs' : 'ttfs') . '/';
        if (empty($this->fontttf)) {
            $dir = dir($ttfPath);
            $ttfs = [];
            while (false !== ($file = $dir->read())) {
                if ('.' != $file[0] && substr($file, -4) == '.ttf') {
                    $ttfs[] = $file;
                }
            }
            $dir->close();
            $this->fontttf = $ttfs[array_rand($ttfs)];
        }


        $fontttf = $ttfPath . $this->fontttf;

        if ($this->useImgBg) {
            $this->background();
        }

        if ($this->useNoise) {
            // Vẽ nhiễu hạt
            $this->writeNoise();
        }
        if ($this->useCurve) {
            // Vẽ đường nhiễu
            $this->writeCurve();
        }
        // Vẽ mã captcha
        $text = $this->useZh ? preg_split('/(?<!^)(?!$)/u', $generator['value']) : str_split($generator['value']); // Mã xác thực

        foreach ($text as $index => $char) {

            $x = $this->fontSize * ($index + 1) * mt_rand(1.2, 1.6) * ($this->math ? 1 : 1.5);
            $y = $this->fontSize + mt_rand(10, 20);
            $angle = $this->math ? 0 : mt_rand(-40, 40);

            imagettftext($this->im, $this->fontSize, $angle, $x, $y, $this->color, $fontttf, $char);
        }

        ob_start();
        // Xuất ảnh
        imagepng($this->im);
        $content = ob_get_clean();
        imagedestroy($this->im);
        return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/png');
    }

    /**
     * Vẽ một đường nhiễu là đường cong hàm sin ngẫu nhiên ghép từ hai đoạn (bạn có thể đổi thành hàm đường cong đẹp hơn)
     *
     *      Công thức toán cấp 3 sao mà quên hết rồi nhỉ, ghi ra đây
     *        Công thức hàm số dạng sin: y=Asin(ωx+φ)+b
     *      Ảnh hưởng của từng hằng số đến đồ thị hàm số:
     *        A: quyết định biên độ (tức là hệ số co giãn theo chiều dọc)
     *        b: biểu thị vị trí của dạng sóng trên trục Y hoặc khoảng dịch chuyển theo chiều dọc (cộng lên trên, trừ xuống dưới)
     *        φ: quyết định vị trí dạng sóng so với trục X hoặc khoảng dịch chuyển theo chiều ngang (cộng sang trái, trừ sang phải)
     *        ω: quyết định chu kỳ (chu kỳ dương nhỏ nhất T=2π/∣ω∣)
     *
     */
    protected function writeCurve(): void
    {
        $px = $py = 0;

        // Phần đầu đường cong
        $A = mt_rand(1, $this->imageH / 2); // Biên độ
        $b = mt_rand(-$this->imageH / 4, $this->imageH / 4); // Độ lệch theo hướng trục Y
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4); // Độ lệch theo hướng trục X
        $T = mt_rand($this->imageH, $this->imageW * 2); // Chu kỳ
        $w = (2 * M_PI) / $T;

        $px1 = 0; // Vị trí bắt đầu theo hoành độ của đường cong
        $px2 = mt_rand($this->imageW / 2, $this->imageW * 0.8); // Vị trí kết thúc theo hoành độ của đường cong

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2; // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->im, $px + $i, $py + $i, $this->color); // Ở đây dùng vòng lặp (while) vẽ từng điểm ảnh, hiệu suất tốt hơn nhiều so với dùng imagettftext và imagestring vẽ theo cỡ chữ một lần (không cần vòng lặp while này)
                    $i--;
                }
            }
        }

        // Phần sau đường cong
        $A = mt_rand(1, $this->imageH / 2); // Biên độ
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4); // Độ lệch theo hướng trục X
        $T = mt_rand($this->imageH, $this->imageW * 2); // Chu kỳ
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->imageH / 2;
        $px1 = $px2;
        $px2 = $this->imageW;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2; // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->im, $px + $i, $py + $i, $this->color);
                    $i--;
                }
            }
        }
    }

    /**
     * Vẽ nhiễu hạt
     * Viết chữ cái hoặc số với màu khác nhau lên ảnh
     */
    protected function writeNoise(): void
    {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for ($i = 0; $i < 10; $i++) {
            //Màu nhiễu hạt
            $noiseColor = imagecolorallocate($this->im, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 5; $j++) {
                // Vẽ nhiễu hạt
                imagestring($this->im, 5, mt_rand(-10, $this->imageW), mt_rand(-10, $this->imageH), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }

    /**
     * Vẽ ảnh nền
     * Chú ý: nếu ảnh xuất ra của mã captcha khá lớn thì sẽ chiếm nhiều tài nguyên hệ thống
     */
    protected function background(): void
    {
        $path = dirname(app()->getThinkPath(), 2) . DS . 'think-captcha' . DS . '/assets/bgs/';
        $dir = dir($path);

        $bgs = [];
        while (false !== ($file = $dir->read())) {
            if ('.' != $file[0] && substr($file, -4) == '.jpg') {
                $bgs[] = $path . $file;
            }
        }
        $dir->close();

        $gb = $bgs[array_rand($bgs)];

        list($width, $height) = @getimagesize($gb);
        // Resample
        $bgImage = @imagecreatefromjpeg($gb);
        @imagecopyresampled($this->im, $bgImage, 0, 0, 0, 0, $this->imageW, $this->imageH, $width, $height);
        @imagedestroy($bgImage);
    }
}
