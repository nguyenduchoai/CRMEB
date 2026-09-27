<template>
  <div
    class="mobile-page"
    :style="{
      background: bottomBgColor,
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: lrEdge + 'px',
      paddingRight: lrEdge + 'px',
    }"
  >
    <div
      class="box"
      :style="{
        height: cSlider + 'px',
        background: bgColor,
        borderRadius: fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px',
      }"
    ></div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_auxiliary_box',
  cname: 'Khoảng trống',
  configName: 'c_auxiliary_box',
  icon: '#iconzujian-fuzhukongbai',
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
        cname: 'Khoảng trống',
        name: 'blankPage',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt chiều cao',
        titleRight: 'Kiểu chung',
        bgColor: {
          title: 'Nền thành phần',
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
        bottomBgColor: {
          title: 'Nền phía dưới',
          name: 'bgColor',
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
        heightConfig: {
          title: 'Chiều cao thành phần',
          val: 10,
          min: 1,
        },
        topConfig: {
          title: 'Lề trên',
          val: 0,
          min: 0,
        },
        bottomConfig: {
          title: 'Lề dưới',
          val: 0,
          min: 0,
        },
        lrEdge: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
        },
        fillet: {
          title: 'Bo góc nền',
          type: 0,
          list: [
            {
              val: 'Tất cả',
              icon: 'iconcaozuo-zhengti',
            },
            {
              val: 'Từng góc',
              icon: 'iconcaozuo-bianjiao',
            },
          ],
          valName: 'Giá trị bo góc',
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
      },
      cSlider: '',
      bgColor: '',
      bottomBgColor: '',
      confObj: {},
      pageData: {},
      topConfig: '',
      bottomConfig: '',
      lrEdge: '',
      fillet: 0,
      filletVal: 0,
      valList: [],
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
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.lrEdge = data.lrEdge.val;
        this.fillet = data.fillet.type;
        this.filletVal = data.fillet.val;
        this.valList = data.fillet.valList;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.box {
  height: 20px;
  background: #f5f5f5;
}
</style>
