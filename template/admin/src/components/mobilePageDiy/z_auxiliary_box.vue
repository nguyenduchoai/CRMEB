<template>
  <div class="mobile-page">
    <div class="box" :style="{ height: cSlider + 'px', background: bgColor }"></div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_auxiliary_box',
  cname: 'Khoảng trống',
  configName: 'c_auxiliary_box',
  icon: 'iconfuzhukongbai1',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'blankPage', // Tên khớp bên ngoài
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
        name: 'blankPage',
        timestamp: this.num,
        bgColor: {
          title: 'Màu nền',
          name: 'bgColor',
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
        heightConfig: {
          title: 'Chiều cao thành phần',
          val: 10,
          min: 1,
        },
      },
      cSlider: '',
      bgColor: '',
      confObj: {},
      pageData: {},
      edge: '',
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
      if (data.heightConfig) {
        this.cSlider = data.heightConfig.val;
        this.bgColor = data.bgColor.color[0].item;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.box {
  height: 20px;
  background: #f5f5f5;
}
</style>
