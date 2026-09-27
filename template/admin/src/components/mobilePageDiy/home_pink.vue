<template>
  <div :style="{ padding: '0 ' + prConfig + 'px' }">
    <div class="home_pink" :class="conStyle ? '' : 'pinkOn'" :style="{ background: bgColor, marginTop: mTop + 'px' }">
      <div class="title-wrapper">
        <div class="left">
          <!--                    -->
          <img class="icon" :src="imgUrl" alt="" />
          <span>Hoạt động mua chung</span>
          <div class="avatar-wrapper">
            <img src="@/assets/images/ren1.png" alt="" />
            <img src="@/assets/images/ren2.png" alt="" />
          </div>
          <p class="num">1234 người mua chung thành công</p>
        </div>
        <div class="right">Xem thêm</div>
      </div>
      <div class="list-wrapper">
        <div class="item" v-for="(item, index) in list" :key="index" :style="{ marginRight: listRight + 'px' }">
          <div class="img-box">
            <img v-if="item.img" :src="item.img" alt="" />
            <div v-else class="empty-box"><span class="iconfont-diy icontupian"></span></div>
            <div class="num" v-if="joinShow">{{ item.num }} người tham gia</div>
          </div>
          <div class="info">
            <div class="title line1" v-if="titleShow">{{ item.name }}</div>
            <div class="price">
              <span class="label" :style="{ background: txtBg, color: txtColor }" v-if="pinkShow">Giá mua chung</span>
              <p class="num" :style="{ color: txtColor }" v-if="priceShow">
                <span>￥</span>
                {{ item.price }}
              </p>
            </div>
          </div>
          <div class="btn" :style="{ background: txtColor }" v-if="bntShow">Tham gia mua chung</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_pink',
  cname: 'Mua chung',
  icon: 'iconpintuan1',
  configName: 'c_home_pink',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'combination', // Tên khớp bên ngoài
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
        name: 'combination',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        numConfig: {
          val: 3,
        },
        priceShow: {
          title: 'Hiển thị giá',
          val: true,
        },
        bntShow: {
          title: 'Hiển thị nút',
          val: true,
        },
        titleShow: {
          title: 'Hiển thị tên',
          val: true,
        },
        pinkShow: {
          title: 'Hiển thị nhãn mua chung',
          val: true,
        },
        joinShow: {
          title: 'Hiển thị nhãn số người tham gia',
          val: true,
        },
        txtColor: {
          title: 'Màu nền chữ',
          name: 'themeColor',
          default: [
            {
              item: 'rgba(255,68,68,0.1)',
            },
          ],
          color: [
            {
              item: 'rgba(255,68,68,0.1)',
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
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        // Khoảng cách trái phải
        lrConfig: {
          title: 'Lề trái phải',
          val: 10,
          min: 0,
        },
        imgConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 18 * 18px',
          url: 'http://pro.crmeb.net/static/images/group02.gif',
        },
      },
      list: [
        {
          img: '',
          name: 'Nồi cơm điện gia đình Xiaomi',
          price: '234',
          num: '1234',
        },
        {
          img: '',
          name: 'Nồi cơm điện gia đình Xiaomi',
          price: '234',
          num: '1234',
        },
        {
          img: '',
          name: 'Nồi cơm điện gia đình Xiaomi',
          price: '234',
          num: '1234',
        },
      ],
      txtColor: '',
      listRight: '',
      mTop: '',
      txtBg: '',
      pageData: {},
      imgUrl: '',
      priceShow: true,
      bntShow: true,
      titleShow: true,
      pinkShow: true,
      joinShow: true,
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
        this.txtColor = data.themeColor.color[0].item;
        this.listRight = data.lrConfig.val;
        this.mTop = data.mbConfig.val;
        this.txtBg = data.txtColor.color[0].item;
        this.imgUrl = data.imgConfig.url;
        this.priceShow = data.priceShow.val;
        this.bntShow = data.bntShow.val;
        this.titleShow = data.titleShow.val;
        this.pinkShow = data.pinkShow.val;
        this.joinShow = data.joinShow.val;
        this.prConfig = data.prConfig.val;
        this.bgColor = data.bgColor.color[0].item;
        this.conStyle = data.conStyle.type;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.pinkOn {
  border-radius: 0 !important;
}
.pageOn {
  border-radius: 10px !important;
}
.mobile-page {
  padding-top: 10px;
}
.home_pink {
  padding: 10px 12px;
  background: #fff;
  border-radius: 10px;
  .title-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    .left {
      display: flex;
      align-items: center;
      span {
        margin-left: 5px;
        font-size: 16px;
      }
      .icon {
        width: 18px;
        height: 18px;
        border-radius: 50%;
      }
      .avatar-wrapper {
        display: flex;
        align-items: center;
        margin-left: 14px;
        img {
          width: 15px;
          height: 15px;
          margin-left: -5px;
          border: 1px solid #fff;
          border-radius: 50%;
        }
      }
      .num {
        margin-left: 3px;
        color: #999;
        font-size: 13px;
      }
    }
  }
  .list-wrapper {
    display: flex;
    margin-top: 10px;
    overflow: hidden;
    .item {
      flex-shrink: 0;
      width: 110px;
      background: #fff;
      border-radius: 8px;
      .img-box {
        position: relative;
        width: 100%;
        height: 110px;
        img,
        .box {
          width: 100%;
          height: 100%;
          border-radius: 8px 8px 0px 0px;
        }
        .box {
          background: #d8d8d8;
        }
        .num {
          position: absolute;
          left: 6px;
          top: 6px;
          width: 70px;
          height: 16px;
          line-height: 16px;
          text-align: center;
          background: rgba(0, 0, 0, 0.1);
          box-shadow: 1px 1px 4px 0px rgba(0, 0, 0, 0.06);
          border-radius: 8px;
          color: #fff;
          font-size: 12px;
        }
      }
      .info {
        padding: 5px 7px;
        .title {
          font-size: 12px;
          color: #282828;
        }
        .price {
          display: flex;
          align-items: center;
          .label {
            display: inline-block;
            height: 20px;
            line-height: 20px;
            padding: 0 3px;
            margin-right: 3px;
            font-size: 9px;
            font-weight: 400;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.06);
            color: #ff4444;
          }
          .num {
            color: #ff4444;
            font-size: 16px;
            font-weight: bold;
            span {
              font-size: 12px;
            }
          }
        }
      }
      .btn {
        width: 110px;
        height: 24px;
        box-shadow: 1px 1px 4px 0px rgba(0, 0, 0, 0.06);
        border-radius: 0px 0px 8px 8px;
        text-align: center;
        line-height: 24px;
        color: #fff;
      }
    }
  }
}
</style>
