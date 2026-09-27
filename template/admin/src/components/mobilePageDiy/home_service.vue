<template>
  <div class="service-box" :style="{ marginTop: mTop + 'px' }">
    <div class="img-box">
      <img :src="imgUrl" alt="" v-if="imgUrl" />
      <div class="empty-box on" v-else><span class="iconfont-diy icontupian"></span></div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'home_service',
  cname: 'CSKH trực tuyến',
  configName: 'c_home_service',
  icon: 'iconkefu1',
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
      defaultConfig: {
        name: 'customerService',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        logoConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 100 * 100px',
          url: '',
        },
        // Lề trang
        topConfig: {
          title: 'Tỷ lệ cách mép trên',
          val: 0,
          min: 0,
        },
      },
      imgUrl: '',
      pageData: {},
      mTop: 0,
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
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.service-box {
  width: 100%;
  display: flex;
  justify-content: flex-end;
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
      .iconfont-diy {
        font-size: 20px;
      }
    }
  }
}
</style>
