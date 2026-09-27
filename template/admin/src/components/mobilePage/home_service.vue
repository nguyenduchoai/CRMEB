<template>
  <div class="service-box" :class="positions ? '' : 'on'" :style="{ marginTop: mTop + 'px' }">
    <div class="img-box">
      <img :src="imgUrl" alt="" v-if="imgUrl" />
      <div class="empty-box on" v-else>
        <img src="../../assets/images/shan.png" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'home_service',
  cname: 'Nút nổi',
  configName: 'c_home_service',
  icon: '#iconzujian-xuanfuanniu',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'customerService', // Tên khớp bên ngoài
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
        const data = this.$store.state.mobildConfig.defaultArray[nVal];
        this.setConfig(data);
      },
      deep: true,
    },
    defaultArray: {
      handler(nVal, oVal) {
        const data = this.$store.state.mobildConfig.defaultArray[this.num];
        this.setConfig(data);
      },
      deep: true,
    },
  },
  data() {
    return {
      defaultConfig: {
        cname: 'Nút nổi',
        name: 'customerService',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt nút',
        titleRight: 'Cài đặt vị trí',
        buttonConfig: {
          title: 'Liên kết của nút',
          tabVal: 0,
          tabList: [
            {
              name: 'Liên kết trang',
            },
            {
              name: 'Lối vào CSKH',
            },
          ],
        },
        locationConfig: {
          title: 'Vị trí hiển thị',
          tabVal: 1,
          tabList: [
            {
              name: 'Trái',
            },
            {
              name: 'Phải',
            },
          ],
        },
        logoConfig: {
          title: 'Đề xuất: tải lên ảnh 100*100px;',
          url: '',
          link: '',
        },
        // Lề trang
        topConfig: {
          title: 'Độ lệch dọc',
          val: 0,
          min: 0,
        },
      },
      imgUrl: '',
      pageData: {},
      mTop: 0,
      positions: 1, //Vị trí hiển thị
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
      if (data.topConfig) {
        this.mTop = data.topConfig.val;
        this.imgUrl = data.logoConfig.url;
        this.positions = data.locationConfig.tabVal;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.service-box {
  width: 100%;
  display: flex;
  justify-content: flex-end;
  padding-right: 10px;
  &.on {
    justify-content: flex-start;
    padding-left: 10px;
  }
  .img-box {
    width: 43px;
    height: 43px;
    img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
    }
    .empty-box {
      border-radius: 50%;
      background: #f3f9ff;
      img {
        width: 26px;
        height: 20px;
      }
      .iconfont-diy {
        font-size: 20px;
      }
    }
  }
}
</style>
