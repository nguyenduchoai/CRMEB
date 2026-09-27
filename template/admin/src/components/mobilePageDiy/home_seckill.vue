<template>
  <div :style="{ padding: '0 ' + prConfig + 'px' }">
    <div
      class="seckill-box"
      :class="conStyle ? '' : 'seckillOn'"
      :style="{ background: bgColor, marginTop: mTOP + 'px' }"
    >
      <div class="hd">
        <div class="left">
          <img :src="imgUrl" alt="" />
          <p>Flash sale giờ vàng</p>
          <div class="time">
            <span :style="{ background: countDownColor, color: themeColor }">00</span>
            <em>:</em>
            <span :style="{ background: countDownColor, color: themeColor }">00</span>
            <em>:</em>
            <span :style="{ background: countDownColor, color: themeColor }">00</span>
          </div>
        </div>
        <div class="right">Xem thêm</div>
      </div>
      <div class="list-wrapper">
        <div class="list-item" v-for="(item, index) in list" :index="index" :style="{ marginRight: listRight + 'px' }">
          <div class="img-box">
            <img :src="item.img" alt="" v-if="item.img" />
            <div class="empty-box"><span class="iconfont-diy icontupian"></span></div>
            <div v-if="discountShow" class="discount" :style="{ borderColor: themeColor, color: themeColor }">
              Chỉ từ {{ item.discount }}/10 giá gốc
            </div>
          </div>
          <div class="title line1" v-if="titleShow">{{ item.name }}</div>
          <div class="price">
            <span class="label" :style="{ background: themeColor }" v-if="seckillShow">Mua</span>
            <span class="num-label" :style="{ color: themeColor }" v-if="priceShow">￥</span>
            <span class="num" :style="{ color: themeColor }" v-if="priceShow">{{ item.price }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'home_seckill',
  cname: 'Flash sale',
  configName: 'c_home_seckill',
  icon: 'iconmiaosha1',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'seckill', // Tên khớp bên ngoài
  props: {
    index: {
      type: null,
    },
    num: {
      type: null,
    },
  },
  computed: {
    ...mapState('mobildConfig', ['defaultArray']),
  },

  watch: {
    pageData: {
      handler(nVal, oVal) {
        this.setConfig(nVal);
      },
      deep: true,
    },
    num: {
      handler(nVal, oVal) {
        let data = this.$store.state.mobildConfig.defaultArray[nVal];
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler(nVal, oVal) {
        let data = this.$store.state.mobildConfig.defaultArray[this.num];
        this.setConfig(data);
      },
      deep: true,
    },
  },
  data() {
    return {
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        name: 'seckill',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        countDownColor: {
          title: 'Màu nền đồng hồ đếm ngược',
          name: 'countDownColor',
          default: [
            {
              item: 'rgba(252,60,62,0.09)',
            },
          ],
          color: [
            {
              item: 'rgba(252,60,62,0.09)',
            },
          ],
        },
        themeColor: {
          title: 'Chủ đề giao diện',
          name: 'themeColor',
          default: [
            {
              item: '#E93323',
            },
          ],
          color: [
            {
              item: '#E93323',
            },
          ],
        },
        conStyle: {
          title: 'Kiểu nền',
          name: 'conStyle',
          type: 1,
          list: [
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
          ],
        },
        bgColor: {
          title: 'Màu nền',
          name: 'themeColor',
          default: [
            {
              item: '#fff',
            },
          ],
          color: [
            {
              item: '#fff',
            },
          ],
        },
        prConfig: {
          title: 'Lề nền',
          val: 10,
          min: 0,
        },
        priceShow: {
          title: 'Hiển thị giá',
          val: true,
        },
        discountShow: {
          title: 'Hiển thị nhãn chiết khấu',
          val: true,
        },
        titleShow: {
          title: 'Hiển thị tên',
          val: true,
        },
        seckillShow: {
          title: 'Nhãn mua ngay',
          val: true,
        },
        numberConfig: {
          val: 3,
        },
        lrConfig: {
          title: 'Lề trái phải',
          val: 10,
          min: 0,
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        imgConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 18 * 18px',
          url: 'http://pro.crmeb.net/static/images/spike-icon-002.gif',
        },
      },
      list: [
        {
          img: '',
          name: 'Nồi cơm điện gia đình Xiaomi Nồi cơm điện gia đình Xiaomi',
          price: '234',
          discount: '1.2',
        },
        {
          img: '',
          name: 'Nồi cơm điện gia đình Xiaomi Nồi cơm điện gia đình Xiaomi',
          price: '234',
          discount: '1.2',
        },
        {
          img: '',
          name: 'Nồi cơm điện gia đình Xiaomi Nồi cơm điện gia đình Xiaomi',
          price: '234',
          discount: '1.2',
        },
      ],
      mTOP: 0,
      listRight: 0,
      countDownColor: '',
      themeColor: '',
      pageData: {},
      imgUrl: '',
      priceShow: true,
      discountShow: true,
      titleShow: true,
      seckillShow: true,
      prConfig: 0,
      bgColor: '',
      conStyle: 1,
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
    });
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      if (data.mbConfig) {
        this.mTOP = data.mbConfig.val;
        this.listRight = data.lrConfig.val;
        this.countDownColor = data.countDownColor.color[0].item;
        this.themeColor = data.themeColor.color[0].item;
        this.imgUrl = data.imgConfig.url;
        this.priceShow = data.priceShow.val;
        this.discountShow = data.discountShow.val;
        this.titleShow = data.titleShow.val;
        this.seckillShow = data.seckillShow.val;
        this.prConfig = data.prConfig.val;
        this.bgColor = data.bgColor.color[0].item;
        this.conStyle = data.conStyle.type;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.seckillOn {
  border-radius: 0 !important;
}
.pageOn {
  border-radius: 10px !important;
}
.seckill-box {
  padding: 15px 10px;
  background: #fff;
  border-radius: 10px;
  .hd {
    display: flex;
    justify-content: space-between;
    align-items: center;
    .left {
      display: flex;
      align-items: center;
      img {
        width: 18px;
        height: 18px;
        margin-right: 5px;
        border-radius: 50%;
      }
      p {
        font-size: 16px;
        color: #282828;
        font-weight: 600;
      }
      .time {
        display: flex;
        align-items: center;
        margin-left: 5px;
        color: #ff4444;
        span {
          width: 20px;
          height: 16px;
          font-size: 12px;
          text-align: center;
          line-height: 16px;
        }
        em {
          font-size: 12px;
          margin: 0 3px;
          font-style: initial;
          font-weight: bold;
        }
      }
    }
  }
  .list-wrapper {
    display: flex;
    margin-top: 8px;
    overflow: inherit;
    .list-item {
      flex-shrink: 0;
      width: 110px;
      background-color: #fff;
      .img-box {
        position: relative;
        width: 100%;
        height: 110px;
        img,
        .box {
          width: 100%;
          height: 100%;
          border-radius: 8px;
        }
        .box {
          background: #d8d8d8;
        }
        .discount {
          position: absolute;
          left: 8px;
          bottom: 8px;
          height: 18px;
          padding: 0 3px;
          line-height: 18px;
          background: rgba(255, 255, 255, 1);
          border-radius: 2px;
          border: 1px solid transparent;
          font-size: 12px;
        }
      }
      .title {
        margin-top: 5px;
        font-size: 13px;
        color: #282828;
        padding: 0 3px;
      }
      .price {
        display: flex;
        align-items: center;
        padding: 0 3px;
        .label {
          font-size: 9px;
          width: 16px;
          height: 16px;
          color: #fff;
          text-align: center;
          line-height: 16px;
        }
        .num-label {
          color: #ff4444;
          font-size: 12px;
          font-weight: 600;
          margin: 1px 2px 0;
        }
        .num {
          color: #ff4444;
          font-size: 16px;
          font-weight: 600;
        }
      }
    }
  }
}
</style>
