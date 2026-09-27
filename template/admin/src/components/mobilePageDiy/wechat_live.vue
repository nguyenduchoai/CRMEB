<template>
  <div class="mobile-page" style="padding-bottom: 10px" :style="[{ background: bg }, { marginTop: cSlider + 'px' }]">
    <div class="title-box">
      <span>Phòng livestream {{ bg }}</span>
      <span>Xem thêm</span>
    </div>
    <div class="live-wrapper-a" v-if="listStyle == 0">
      <div
        class="live-item-a"
        v-for="(item, index) in live"
        :key="index"
        :style="[{ 'box-shadow': `0px 1px 6px ${boxShadow}` }]"
      >
        <div class="img-box">
          <div class="empty-box on">
            <span class="iconfont-diy icontupian"></span>
          </div>
          <div class="label bgblue" v-if="item.type == 1">
            <span class="txt">Sắp phát sóng</span>
            <span class="msg">7/29 10:00</span>
          </div>
          <div class="label bggary" v-if="item.type == 0">
            <span class="iconfont-diy iconyijieshu" style="margin-right: 5px"></span>Xem lại
          </div>
          <div class="label bgred" v-if="item.type == 2">
            <span class="iconfont-diy iconzhibozhong" style="margin-right: 5px"></span>Đang livestream
          </div>
        </div>
        <div class="info">
          <div class="title">Tiêu đề livestream tiêu đề livestream tiêu đề livestream</div>
          <div class="people">
            <img src="@/assets/images/ren.png" alt="" />
            <span>Nhóc Maruko</span>
          </div>
          <div class="goods-wrapper">
            <template v-if="item.goods.length > 0">
              <div class="goods-item" v-for="(goods, index) in item.goods" :key="index">
                <img :src="goods.img" alt="" />
                <span>￥{{ goods.price }}</span>
              </div>
            </template>
            <template v-else>
              <div class="empty-goods">Chưa có sản phẩm</div>
            </template>
          </div>
        </div>
      </div>
    </div>
    <div class="live-wrapper-b" v-if="listStyle == 1">
      <div
        class="live-item-b"
        v-for="(item, index) in live"
        :key="index"
        :style="[{ 'box-shadow': `0px 1px 6px ${boxShadow}` }]"
      >
        <div class="img-box">
          <div class="empty-box on">
            <span class="iconfont-diy icontupian"></span>
          </div>
          <div class="label bgblue" v-if="item.type == 1">
            <span class="txt">Sắp phát sóng</span>
            <span class="msg">7/29 10:00</span>
          </div>
          <div class="label bggary" v-if="item.type == 0">
            <span class="iconfont-diy iconyijieshu" style="margin-right: 5px"></span>Xem lại
          </div>
          <div class="label bgred" v-if="item.type == 2">
            <span class="iconfont-diy iconzhibozhong" style="margin-right: 5px"></span>Đang livestream
          </div>
        </div>
        <div class="info">
          <div class="title">Tiêu đề livestream tiêu đề livestream tiêu đề livestream</div>
          <div class="people">
            <img src="@/assets/images/ren.png" alt="" />
            <span>Nhóc Maruko</span>
          </div>
        </div>
      </div>
    </div>
    <div class="live-wrapper-a live-wrapper-c" v-if="listStyle == 2">
      <div
        class="live-item-a"
        v-for="(item, index) in live"
        :key="index"
        :style="[{ 'box-shadow': `0px 1px 6px ${boxShadow}` }]"
      >
        <div class="img-box">
          <div class="empty-box on">
            <span class="iconfont-diy icontupian"></span>
          </div>
          <div class="label bgblue" v-if="item.type == 1">
            <span class="txt">Sắp phát sóng</span>
            <span class="msg">7/29 10:00</span>
          </div>
          <div class="label bggary" v-if="item.type == 0">
            <span class="iconfont-diy iconyijieshu" style="margin-right: 5px"></span>Xem lại
          </div>
          <div class="label bgred" v-if="item.type == 2">
            <span class="iconfont-diy iconzhibozhong" style="margin-right: 5px"></span>Đang livestream
          </div>
        </div>
        <div class="info">
          <div class="left">
            <div class="title line1">Tiêu đề livestream tiêu đề livestream tiêu đề livestream</div>
            <div class="people">
              <img src="@/assets/images/ren.png" alt="" />
              <span>Nhóc Maruko</span>
            </div>
          </div>
          <div class="goods-wrapper">
            <template v-if="item.goods.length > 0">
              <div class="goods-item" v-for="(goods, index) in item.goods" :key="index" v-if="index < 2">
                <img :src="goods.img" alt="" />
                <span>￥{{ goods.price }}</span>
              </div>
            </template>
            <template v-else>
              <div class="empty-goods">Chưa có sản phẩm</div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'wechat_live',
  cname: 'Livestream Mini Program',
  configName: 'c_wechat_live',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'liveBroadcast', // Tên khớp bên ngoài
  icon: 'iconxiaochengxuzhibo1',
  props: {
    index: {
      type: null,
      default: -1,
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
        name: 'liveBroadcast',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        bg: {
          title: 'Màu nền',
          name: 'bg',
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
        boxShadow: {
          title: 'Màu đổ bóng',
          name: 'playBg',
          default: [
            {
              item: 'rgba(0, 0, 0, 0.06)',
            },
          ],
          color: [
            {
              item: 'rgba(0, 0, 0, 0.06)',
            },
          ],
        },
        limit: {
          title: 'Số mục hiển thị',
          val: 4,
        },
        listStyle: {
          title: 'Kiểu danh sách',
          name: 'listStyle',
          type: 0,
          list: [
            {
              val: 'Một cột',
              icon: 'iconPic_big',
            },
            {
              val: 'Hai cột',
              icon: 'iconPic_small',
            },
            {
              val: 'Ảnh lớn',
              icon: 'iconbanner_1',
            },
          ],
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
      },
      live: [
        {
          title: 'Đang livestream',
          name: 'playBg',
          type: 2,
          color: '',
          icon: 'iconzhibozhong',
          goods: [],
        },
        {
          title: 'Xem lại',
          name: 'endBg',
          type: 0,
          color: '',
          icon: 'iconyijieshu',
          goods: [
            {
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/4f17d0727e277eb86ecc6296e96c2c09.png',
              price: '199',
            },
            {
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/4f17d0727e277eb86ecc6296e96c2c09.png',
              price: '199',
            },
            {
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/4f17d0727e277eb86ecc6296e96c2c09.png',
              price: '199',
            },
          ],
        },
        {
          title: 'Sắp phát sóng',
          name: 'notBg',
          type: 1,
          color: '',
          icon: 'iconweikaishi',
          goods: [
            {
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/4f17d0727e277eb86ecc6296e96c2c09.png',
              price: '199',
            },
            {
              img: 'http://admin.crmeb.net/uploads/attach/2020/05/20200515/4f17d0727e277eb86ecc6296e96c2c09.png',
              price: '199',
            },
          ],
        },
      ],
      cSlider: '',
      confObj: {},
      pageData: {},
      listStyle: 0,
      bg: '',
      boxShadow: '',
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
        this.cSlider = data.mbConfig.val;
        this.listStyle = data.listStyle.type;
        this.bg = data.bg.color[0].item;
        this.boxShadow = data.boxShadow.color[0].item;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.mobile-page {
  background: #f5f5f5;
  font-size: 12px;
}
.live-wrapper-a {
  padding: 5px 10px 0;
  .live-item-a {
    display: flex;
    position: relative;
    margin-bottom: 10px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    .img-box {
      position: relative;
      width: 170px;
      height: 147px;
      border-radius: 8px 0 0 8px;
      overflow: hidden;
    }
    .info {
      flex: 1;
      padding: 5px;
      border-radius: 0px 8px 8px 0;
      overflow: hidden;
      .title {
        color: #333333;
        font-size: 14px;
      }
      .people {
        display: flex;
        align-items: center;
        font-size: 12px;
        margin-top: 5px;
        img {
          width: 32px;
          height: 32px;
          margin-right: 5px;
          border-radius: 50%;
        }
      }
      .goods-wrapper {
        margin-top: 10px;
        display: flex;
        .goods-item {
          position: relative;
          width: 48px;
          height: 48px;
          margin-right: 8px;
          &:nth-child(3n) {
            margin-right: 0;
          }
          img {
            width: 100%;
            height: 100%;
          }
          span {
            position: absolute;
            left: 0;
            bottom: 0;
            color: #fff;
            font-size: 12px;
          }
        }
      }
    }
  }
  &.live-wrapper-c {
    .live-item-a {
      display: flex;
      flex-direction: column;
      .img-box {
        width: 100%;
        border-radius: 8px 8px 0 0;
      }
      .info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        .left {
          width: 60%;
        }
      }
    }
  }
}
.live-wrapper-b {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  padding: 10px 10px 0;
  background: #fff;
  .live-item-b {
    display: flex;
    flex-direction: column;
    width: 171px;
    margin-bottom: 10px;
    border-radius: 8px;
    overflow: hidden;
    .img-box {
      position: relative;
      height: 137px;
    }
    .info {
      width: 100%;
      padding: 10px;
      .people {
        display: flex;
        align-items: center;
        img {
          width: 32px;
          height: 32px;
          margin-right: 5px;
          border-radius: 50%;
        }
      }
    }
  }
}
.iconfont-diy {
  font-size: 12px;
}
.icontupian {
  font-size: 24px;
}
.bggary {
  background: linear-gradient(270deg, #999999 0%, #666666 100%);
}
.bgred {
  background: linear-gradient(270deg, #f5742f 0%, #ff1717 100%);
}
.empty-goods {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 48px;
  color: #fff;
  background: #b2b2b2;
  font-size: 12px;
}
.label {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  left: 10px;
  top: 10px;
  width: 76px;
  height: 19px;
  border-radius: 11px 0px 11px 11px;
  color: #fff;
  font-size: 12px;
  &.bgblue {
    justify-content: inherit;
    width: 110px;
    background: rgba(0, 0, 0, 0.36);
    overflow: hidden;
    .txt {
      width: 38px;
      height: 100%;
      text-align: center;
      margin-right: 5px;
      background: linear-gradient(270deg, #2fa1f5 0%, #0076ff 100%);
    }
  }
  &.bggary {
    width: 54px;
  }
}
.title-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 10px 0;
  font-size: 16px;
  span:last-child {
    font-size: 13px;
  }
}
</style>
