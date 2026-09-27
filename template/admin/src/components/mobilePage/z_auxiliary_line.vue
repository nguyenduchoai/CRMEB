<template>
  <div
    class="mobile-page"
    :style="{
      marginTop: udEdge + 'px',
      backgroundColor: bgColor,
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: edge + 'px',
      paddingRight: edge + 'px',
    }"
  >
    <div
      class="box"
      :style="{
        borderBottomColor: lineColor,
        borderBottomStyle: style,
      }"
    ></div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_auxiliary_line',
  cname: 'Đường phân cách',
  configName: 'c_auxiliary_line',
  icon: '#iconzujian-fuzhuxian',
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
        cname: 'Đường phân cách',
        name: 'guide',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleRight: 'Kiểu đường kẻ',
        titleCurrent: 'Kiểu chung',
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
        lineBgColor: {
          title: 'Nền phía dưới',
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
        lineStyle: {
          title: 'Chọn kiểu',
          tabVal: 1,
          tabList: [
            {
              name: 'Nét đứt',
              style: 'dashed',
            },
            {
              name: 'Nét liền',
              style: 'solid',
            },
            {
              name: 'Nét chấm',
              style: 'dotted',
            },
          ],
        },
        topConfig: {
          title: 'Lề trên',
          val: 6,
          min: 0,
        },
        bottomConfig: {
          title: 'Lề dưới',
          val: 6,
          min: 0,
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
      bgColor: '',
      confObj: {},
      pageData: {},
      edge: '',
      udEdge: '',
      topConfig: '',
      bottomConfig: '',
      style: '',
      lineColor: '',
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
        let styleType = data.lineStyle.tabVal;
        this.bgColor = data.lineBgColor.color[0].item;
        this.lineColor = data.lineColor.color[0].item;
        this.edge = data.lrEdge.val;
        this.udEdge = data.mbConfig.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.style = data.lineStyle.tabList[styleType].style;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-page {
  padding: 7px 0;
}
.box {
  border-bottom-width: 1px;
}
</style>
