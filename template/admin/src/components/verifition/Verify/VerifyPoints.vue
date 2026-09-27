<template>
  <div style="position: relative">
    <div class="verify-img-out">
      <div
        class="verify-img-panel"
        :style="{
          width: setSize.imgWidth,
          height: setSize.imgHeight,
          'background-size': setSize.imgWidth + ' ' + setSize.imgHeight,
          'margin-bottom': vSpace + 'px',
        }"
      >
        <div v-show="showRefresh" class="verify-refresh" style="z-index: 3" v-db-click @click="refresh">
          <i class="iconfont icon-refresh" />
        </div>
        <img
          ref="canvas"
          :src="pointBackImgBase ? 'data:image/png;base64,' + pointBackImgBase : defaultImg"
          alt=""
          style="width: 100%; height: 100%; display: block"
          v-db-click
          @click="bindingClick ? canvasClick($event) : undefined"
        />

        <div
          v-for="(tempPoint, index) in tempPoints"
          :key="index"
          class="point-area"
          :style="{
            'background-color': '#1abd6c',
            color: '#fff',
            'z-index': 9999,
            width: '20px',
            height: '20px',
            'text-align': 'center',
            'line-height': '20px',
            'border-radius': '50%',
            position: 'absolute',
            top: parseInt(tempPoint.y - 10) + 'px',
            left: parseInt(tempPoint.x - 10) + 'px',
          }"
        >
          {{ index + 1 }}
        </div>
      </div>
    </div>
    <!-- 'height': this.barSize.height, -->
    <div
      class="verify-bar-area"
      :style="{
        width: setSize.imgWidth,
        color: this.barAreaColor,
        'border-color': this.barAreaBorderColor,
        'line-height': this.barSize.height,
      }"
    >
      <span class="verify-msg">{{ text }}</span>
    </div>
  </div>
</template>
<script type="text/babel">
/**
 * VerifyPoints
 * @description Nhấn chọn
 * */
import { resetSize, _code_chars, _code_color1, _code_color2 } from './../utils/util';
import { aesEncrypt } from './../utils/ase';
import { ajCaptcha, ajCaptchaCheck } from '../../../api/common';

export default {
  name: 'VerifyPoints',
  props: {
    // Dạng pop-up (pop), cố định (fixed)
    mode: {
      type: String,
      default: 'fixed',
    },
    captchaType: {
      type: String,
    },
    // Khoảng cách
    vSpace: {
      type: Number,
      default: 5,
    },
    imgSize: {
      type: Object,
      default() {
        return {
          width: '310px',
          height: '155px',
        };
      },
    },
    barSize: {
      type: Object,
      default() {
        return {
          width: '310px',
          height: '40px',
        };
      },
    },
    defaultImg: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      secretKey: '', // Khóa mã hóa AES do backend trả về
      checkNum: 3, // Số ký tự cần bấm theo mặc định
      fontPos: [], // Thông tin tọa độ đã chọn
      checkPosArr: [], // Tọa độ người dùng bấm vào
      num: 1, // Số lần bấm
      pointBackImgBase: '', // Ảnh nền lấy được từ backend
      poinTextList: [], // Thứ tự chữ cần bấm do backend trả về
      backToken: '', // Giá trị token do backend trả về
      setSize: {
        imgHeight: 0,
        imgWidth: 0,
        barHeight: 0,
        barWidth: 0,
      },
      tempPoints: [],
      text: '',
      barAreaColor: undefined,
      barAreaBorderColor: undefined,
      showRefresh: true,
      bindingClick: true,
    };
  },
  computed: {
    resetSize() {
      return resetSize;
    },
  },
  watch: {
    // Khi type thay đổi thì làm mới toàn bộ
    type: {
      immediate: true,
      handler() {
        this.init();
      },
    },
  },
  mounted() {
    // Không cho kéo thả
    this.$el.onselectstart = function () {
      return false;
    };
  },
  methods: {
    init() {
      // Tải trang
      this.fontPos.splice(0, this.fontPos.length);
      this.checkPosArr.splice(0, this.checkPosArr.length);
      this.num = 1;
      this.getPictrue();
      this.$nextTick(() => {
        this.setSize = this.resetSize(this); // Đặt lại chiều rộng chiều cao
        this.$parent.$emit('ready', this);
      });
    },
    canvasClick(e) {
      this.checkPosArr.push(this.getMousePos(this.$refs.canvas, e));
      if (this.num == this.checkNum) {
        this.num = this.createPoint(this.getMousePos(this.$refs.canvas, e));
        // Chuyển đổi giá trị tọa độ theo tỷ lệ
        this.checkPosArr = this.pointTransfrom(this.checkPosArr, this.setSize);
        // Chờ tạo tọa độ thực hiện xong
        setTimeout(() => {
          // var flag = this.comparePos(this.fontPos, this.checkPosArr);
          // Gửi request tới backend
          var captchaVerification = this.secretKey
            ? aesEncrypt(this.backToken + '---' + JSON.stringify(this.checkPosArr), this.secretKey)
            : this.backToken + '---' + JSON.stringify(this.checkPosArr);
          const data = {
            captchaType: this.captchaType,
            pointJson: this.secretKey
              ? aesEncrypt(JSON.stringify(this.checkPosArr), this.secretKey)
              : JSON.stringify(this.checkPosArr),
            token: this.backToken,
          };
          ajCaptchaCheck(data).then((res) => {
            if (res.repCode == '0000') {
              this.barAreaColor = '#4cae4c';
              this.barAreaBorderColor = '#5cb85c';
              this.text = 'Xác thực thành công';
              this.bindingClick = false;
              if (this.mode == 'pop') {
                setTimeout(() => {
                  this.$parent.clickShow = false;
                  this.refresh();
                }, 1500);
              }
              this.$parent.$emit('success', { captchaVerification });
            } else {
              this.$parent.$emit('error', this);
              this.barAreaColor = '#d9534f';
              this.barAreaBorderColor = '#d9534f';
              this.text = 'Xác minh thất bại';
              setTimeout(() => {
                this.refresh();
              }, 700);
            }
          });
        }, 400);
      }
      if (this.num < this.checkNum) {
        this.num = this.createPoint(this.getMousePos(this.$refs.canvas, e));
      }
    },

    // Lấy tọa độ
    getMousePos: function (obj, e) {
      var x = e.offsetX;
      var y = e.offsetY;
      return { x, y };
    },
    // Tạo điểm tọa độ
    createPoint: function (pos) {
      this.tempPoints.push(Object.assign({}, pos));
      return ++this.num;
    },
    refresh: function () {
      this.tempPoints.splice(0, this.tempPoints.length);
      this.barAreaColor = '#000';
      this.barAreaBorderColor = '#ddd';
      this.bindingClick = true;
      this.fontPos.splice(0, this.fontPos.length);
      this.checkPosArr.splice(0, this.checkPosArr.length);
      this.num = 1;
      this.getPictrue();
      this.text = 'Xác minh thất bại';
      this.showRefresh = true;
    },

    // Yêu cầu ảnh nền và ảnh xác thực
    getPictrue() {
      const data = {
        captchaType: this.captchaType,
        clientUid: localStorage.getItem('point'),
        ts: Date.now(), // Timestamp hiện tại
      };
      ajCaptcha(data).then((res) => {
        if (res.repCode == '0000') {
          this.pointBackImgBase = res.repData.originalImageBase64;
          this.backToken = res.repData.token;
          this.secretKey = res.repData.secretKey;
          this.poinTextList = res.repData.wordList;
          this.text = 'Vui lòng nhấn lần lượt vào [' + this.poinTextList.join(',') + '】';
        } else {
          this.text = res.repMsg;
        }

        // Kiểm tra số lần gọi API đã hết hiệu lực chưa
        if (res.repCode == '6201') {
          this.pointBackImgBase = null;
        }
      });
    },
    // Hàm chuyển đổi tọa độ
    pointTransfrom(pointArr, imgSize) {
      var newPointArr = pointArr.map((p) => {
        const x = Math.round((310 * p.x) / parseInt(imgSize.imgWidth));
        const y = Math.round((155 * p.y) / parseInt(imgSize.imgHeight));
        return { x, y };
      });
      return newPointArr;
    },
  },
};
</script>
