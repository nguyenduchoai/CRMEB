<!doctype html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $Title; ?> - <?php echo $Powered; ?></title>
    <link rel="stylesheet" href="./css/install.css?v=9.0"/>
    <link rel="stylesheet" href="./css/step2.css"/>
    <!-- Import style -->
    <link rel="stylesheet" href="./css/theme-chalk.css">
    <!-- import Vue before Element -->
    <script src="./js/vue2.6.11.js"></script>
    <!-- import JavaScript -->
    <script src="./js/element-ui.js?v=9.0"></script>
</head>
<body>
<div class="wrap" id="step2">
    <!--    --><?php //require './templates/header.php'; ?>
    <div class="title">
        Kiểm tra cài đặt
    </div>
    <div class="content">
        <div class="menu">
            <div class="head">
                <h1>Kiểm tra cài đặt</h1>
                <a class="again" href="<?php echo $_SERVER['PHP_SELF']; ?>?step=2">Kiểm tra lại
                    <img class="upload" src="./images/install/upload.png" alt="">
                </a>
            </div>
            <div class="p8">Môi trường cài đặt cần đáp ứng yêu cầu vận hành của hệ thống</div>
            <div>
                <div class="tab" :class="{'on': index === 0}" @click="index = 0">
                    <div class="left-img">
                        <img class="env" src="./images/install/environment.png" alt="">
                        <img v-if="`<?php echo $passOne; ?>` == 'no'" class="warring"
                             src="./images/install/warring.png" alt="">
                        <img v-else class="warring" src="./images/install/sure.png" alt="">

                    </div>
                    <div>
                        <div>Môi trường và cấu hình</div>
                        <div class="p8">Môi trường vận hành cơ bản của hệ thống</div>
                    </div>
                </div>
                <div class="tab" :class="{'on': index === 1}" @click="index = 1">
                    <div class="left-img">
                        <img class="jur" src="./images/install/jurisdiction.png" alt="">
                        <img v-if="`<?php echo $passTwo; ?>` == 'no'" class="warring btn-warning"
                             src="./images/install/warring.png" alt="">
                        <img v-else class="warring btn-warning" src="./images/install/sure.png" alt="">
                    </div>
                    <div>
                        <div>Kiểm tra quyền</div>
                        <div class="p8">Kiểm tra quyền thư mục và tệp</div>
                    </div>
                </div>

            </div>
        </div>
        <section class="config-list">
            <!--        <div class="step">-->
            <!--            <ul>-->
            <!--                <li class="current"><em>1</em>Kiểm tra môi trường</li>-->
            <!--                <li><em>2</em>Tạo dữ liệu</li>-->
            <!--                <li><em>3</em>Hoàn tất cài đặt</li>-->
            <!--            </ul>-->
            <!--        </div>-->
            <div class="server">
                <table width="100%" v-if="index === 0">
                    <tr>
                        <td class="td1">Kiểm tra môi trường</td>
                        <td class="td1" width="25%">Cấu hình đề xuất</td>
                        <td class="td1" width="25%">Yêu cầu tối thiểu</td>
                        <td class="td1" width="25%">Trạng thái hiện tại</td>
                    </tr>
                    <tr>
                        <td>Hệ điều hành</td>
                        <td>Tương tự UNIX</td>
                        <td>Không giới hạn</td>
                        <td><div class="ls-td"><img class="yes" src="./images/install/yes.png" alt="Đúng"><?php echo $os; ?></div></td>

                    </tr>
                    <tr>
                        <td>Môi trường máy chủ</td>
                        <td>apache/nginx</td>
                        <td>apache2.0 trở lên/nginx1.6 trở lên</td>
                        <td><div class="ls-td"><img class="yes" src="./images/install/yes.png" alt="Đúng"><?php echo $server; ?></div></td>

                    </tr>
                    <tr>
                        <td>Phiên bản PHP</td>
                        <td>><?php echo PHP_EDITION; ?></td>
                        <td><?php echo PHP_EDITION; ?> trở lên</td>
                        <td><div class="ls-td"><img class="yes" src="./images/install/yes.png" alt="Đúng"><?php echo $phpv; ?></div></td>
                    </tr>
                    <tr>
                        <td>Tải lên tệp đính kèm</td>
                        <td>>2M</td>
                        <td>Không giới hạn</td>
                        <td><div class="ls-td"><?php echo $uploadSize; ?></div></td>

                    </tr>
                    <tr>
                        <td>session</td>
                        <td>Kích hoạt</td>
                        <td>Kích hoạt</td>
                        <td><div class="ls-td"><?php echo $session; ?></div></td>

                    </tr>
                    <tr>
                        <td>safe_mode</td>
                        <td>Cấu hình cơ bản</td>
                        <td>Kích hoạt</td>
                        <td><div class="ls-td"><?php echo $safe_mode; ?></div></td>

                    </tr>
                    <tr>
                        <td>Thư viện GD</td>
                        <td>Bắt buộc bật</td>
                        <td>1.0 trở lên</td>
                        <td><div class="ls-td"><?php echo $gd; ?></div></td>

                    </tr>
                    <tr>
                        <td>mysqli</td>
                        <td>Bắt buộc bật</td>
                        <td>Kích hoạt</td>
                        <td><div class="ls-td"><?php echo $mysql; ?></div></td>

                    </tr>
                    <tr>
                        <td>curl_init</td>
                        <td>Tiện ích mở rộng bắt buộc</td>
                        <td>Kích hoạt</td>
                        <td><div class="ls-td"><?php echo $curl; ?></div></td>

                    </tr>
                    <tr>
                        <td>bcmath</td>
                        <td>Tiện ích mở rộng bắt buộc</td>
                        <td>Kích hoạt</td>
                        <td><div class="ls-td"><?php echo $bcmath; ?></div></td>
                    </tr>
                    <tr>
                        <td>openssl</td>
                        <td>Tiện ích mở rộng bắt buộc</td>
                        <td>Kích hoạt</td>
                        <td><div class="ls-td"><?php echo $openssl; ?></div></td>
                    </tr>
                </table>


                <table width="100%" v-else>
                    <tr>
                        <td class="td1">Kiểm tra quyền</td>
                        <td class="td1" width="25%">Cấu hình đề xuất</td>
                        <td class="td1" width="25%">Ghi</td>
                        <td class="td1" width="25%">Đọc</td>
                    </tr>
                    <?php
                    foreach ($folder as $dir) {
                        $Testdir = APP_DIR . $dir;
                        if (!is_file($Testdir)) {
                            if (!is_dir($Testdir)) {
                                dir_create($Testdir);
                            }
                        }

                        if (testwrite($Testdir)) {
                            $w = '<img class="yes" src="./images/install/yes.png" alt="Đạt">Có thể ghi ';
                        } else {
                            $w = '<img class="no" src="./images/install/warring.png" alt="Lỗi">Không thể ghi ';
                        }


                        if (is_readable($Testdir)) {
                            $r = '<img class="yes" src="./images/install/yes.png" alt="Đạt">Có thể đọc';
                        } else {
                            $r = '<img class="no" src="./images/install/warring.png" alt="Lỗi">Không thể đọc';
                        }
                        ?>
                        <tr>
                            <td><?php echo $dir; ?></td>
                            <td>Đọc/ghi</td>
                            <td>
                                <div class="ls-td"><?php echo $w; ?></div>
                            </td>
                            <td>
                                <div class="ls-td"><?php echo $r; ?></div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <?php
                    foreach ($file as $filename) {
                        $filedir = APP_DIR . $filename;
                        if (is_writeable($filedir)) {
                            $w = '<img class="yes" src="./images/install/yes.png" alt="Đạt">Có thể ghi ';
                        } else {
                            $w = '<img class="no" src="./images/install/warring.png" alt="Lỗi">Không thể ghi ';
                        }
                        if (is_readable($filedir)) {
                            $r = '<img class="yes" src="./images/install/yes.png" alt="Đạt">Có thể đọc';
                        } else {
                            $r = '<img class="no" src="./images/install/warring.png" alt="Lỗi">Không thể đọc';
                        }
                        ?>

                        <tr>
                            <td><?php echo $filename; ?></td>
                            <td>Đọc/ghi</td>
                            <td>
                                <div class="ls-td"><?php echo $w; ?></div>
                            </td>
                            <td>
                                <div class="ls-td"><?php echo $r; ?></div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>


                </table>
                <!--            <table width="100%">-->
                <!--                <tr>-->
                <!--                  <td class="td1" width="25%">Hàm kiểm tra bắt buộc phải bật</td>-->
                <!--                  <td class="td1" width="25%">Trạng thái hiện tại</td>-->
                <!--                  <td class="td1" width="25%">Hàm kiểm tra bắt buộc phải bật</td>-->
                <!--                  <td class="td1" width="25%">Trạng thái hiện tại</td>-->
                <!--                </tr>-->
                <!--              <tr>-->
                <!--                    <td>file_put_contents</td>-->
                <!--                    <td>--><?php //echo $file_put_contents; ?><!--</td>-->
                <!--                <td>imagettftext</td>-->
                <!--                <td>--><?php //echo $imagettftext; ?><!--</td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>proc_open</td>-->
                <!--                    <td>--><?php //echo $proc_open; ?><!--</td>-->
                <!--                    <td>pcntl_signal</td>-->
                <!--                    <td>--><?php //echo $pcntl_signal; ?><!--</td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>pcntl_signal_dispatch</td>-->
                <!--                    <td>--><?php //echo $pcntl_signal_dispatch; ?><!--</td>-->
                <!--                    <td>pcntl_fork</td>-->
                <!--                    <td>--><?php //echo $pcntl_fork; ?><!--</td>-->
                <!--                </tr>-->
                <!--                <tr>-->
                <!--                    <td>pcntl_wait</td>-->
                <!--                    <td>--><?php //echo $pcntl_wait; ?><!--</td>-->
                <!--                    <td>pcntl_alarm</td>-->
                <!--                    <td>--><?php //echo $pcntl_alarm; ?><!--</td>-->
                <!--                </tr>-->
                <!--            </table>-->
            </div>

        </section>
    </div>
    <div class="trip mid">
        <img src="./images/install/trip-icon.png" alt="">
        Lưu ý: Chương trình cần cấu hình rewrite URL (giả tĩnh) để hoạt động, nếu không sau khi cài đặt sẽ không thể sử dụng được
    </div>
    <div class="bottom-btn">
        <div class="bottom tac up-btn">
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=1" class="btn">Bước trước</a>
        </div>
        <div class="bottom tac">
            <?php if ($passOne == 'no' || $passTwo == 'no') { ?>
                <span class="next" @click="next" class="btn">Bước tiếp theo</span>
            <?php } else { ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=3" class="btn next">Bước tiếp theo</a>
            <?php } ?>
        </div>
    </div>

</div>
<?php require './templates/footer.php'; ?>
</body>
<script>
    new Vue({
        el: '#step2',
        data() {
            return {index: 0}
        },
        methods: {
            next() {
                this.$message({
                    message: 'Kiểm tra môi trường cài đặt không đạt, vui lòng kiểm tra lại',
                    type: 'warning'
                });
            }
        }
    })
</script>
</html>
