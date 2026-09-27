<template>
  <div class="mobile-page">
    <div class="flex-box" :style="{ background: bgColor, marginTop: mTOP + 'px' }">
      <div class="left">
        <div class="img-box">
          <img :src="imgUrl" alt="" v-if="imgUrl" />
          <div class="empty-box on" v-else><span class="iconfont-diy icontupian"></span></div>
        </div>
        <div class="name">{{ txt }}</div>
      </div>
      <div class="btn" :style="{ borderColor: themeColor, color: themeColor }">Đã theo dõi</div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_wechat_attention',
  cname: 'Theo dõi OA WeChat',
  configName: 'c_wechat_attention',
  icon: 'iconguanzhugongzhonghao1',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'follow', // Tên khớp bên ngoài
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
      handler(nVal, oVal) {},
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
        name: 'follow',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        titleConfig: {
          title: 'Tên',
          value: 'Tiêu đề',
          place: 'Vui lòng nhập tiêu đề',
          max: 30,
        },
        imgConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 92 * 92px',
          url: '',
        },
        codeConfig: {
          title: 'Thêm mã QR, kích thước đề xuất 92 * 92p',
          url: '',
        },
        themeColor: {
          title: 'Màu chủ đề',
          default: [
            {
              item: '#F96E29',
            },
          ],
          color: [
            {
              item: '#F96E29',
            },
          ],
        },
        bgColor: {
          title: 'Màu nền',
          default: [
            {
              item: '#f5f5f5',
            },
          ],
          color: [
            {
              item: '#f5f5f5',
            },
          ],
        },
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
      },
      cSlider: '',
      bgColor: '',
      confObj: {},
      pageData: {},
      edge: '',
      udEdge: '',
      themeColor: '',
      mTOP: 0,
      imgUrl: '',
      txt: '',
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
        this.bgColor = data.bgColor.color[0].item;
        this.themeColor = data.themeColor.color[0].item;
        this.mTOP = data.mbConfig.val;
        this.imgUrl = data.imgConfig.url;
        this.txt = data.titleConfig.value;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.flex-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 10px;
  height: 70px;
  background: #dddddd;
  .left {
    display: flex;
    align-items: center;
    .img-box {
      width: 46px;
      height: 46px;
      overflow: hidden;
      border-radius: 50%;
      img {
        width: 100%;
        height: 100%;
      }
    }
    .name {
      width: 230px;
      margin-left: 10px;
      font-size: 18px;
      color: #000;
    }
  }
  .btn {
    width: 60px;
    height: 26px;
    border: 1px solid rgba(2, 160, 232, 1);
    opacity: 1;
    border-radius: 3px;
    color: #02a0e8;
    font-size: 14px;
    text-align: center;
    line-height: 26px;
  }
  .iconfont-diy {
    font-size: 20px;
  }
}
</style>
