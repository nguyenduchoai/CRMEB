<template>
  <div class="mobile-page">
    <div
      class="box"
      :style="{
        borderBottomWidth: cSlider + 'px',
        borderBottomColor: bgColor,
        borderBottomStyle: style,
        marginLeft: edge + 'px',
        marginRight: edge + 'px',
        marginTop: udEdge + 'px',
      }"
    ></div>
    <!--        {{pageData.styleList[0].list[pageData.styleList[0].type].style}}-->
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_auxiliary_line',
  cname: 'Đường phân cách',
  configName: 'c_auxiliary_line',
  icon: 'iconfuzhuxian1',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'guide', // Tên khớp bên ngoài
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
        name: 'guide',
        timestamp: this.num,
        lineColor: {
          title: 'Màu đường kẻ',
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
        lineStyle: {
          title: 'Kiểu đường kẻ',
          type: 0,
          list: [
            {
              val: 'Nét đứt',
              style: 'dashed',
              icon: '',
            },
            {
              val: 'Nét liền',
              style: 'solid',
            },
            {
              val: 'Nét chấm',
              style: 'dotted',
            },
          ],
        },
        heightConfig: {
          title: 'Chiều cao thành phần',
          val: 1,
          min: 1,
        },
        lrEdge: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
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
      styleType: '',
      style: '',
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
        let styleType = data.lineStyle.type;
        this.cSlider = data.heightConfig.val;
        this.bgColor = data.lineColor.color[0].item;
        this.edge = data.lrEdge.val;
        this.udEdge = data.mbConfig.val;
        this.style = data.lineStyle.list[styleType].style;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.mobile-page {
  padding: 7px 0;
}

/*.box*/
/*    height 20px*/
/*    background #F5F5F5*/
</style>
