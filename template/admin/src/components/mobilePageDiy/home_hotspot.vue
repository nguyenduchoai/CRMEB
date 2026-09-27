<template>
  <div
    class="mobile-page"
    :style="{
      background: bottomBgColor,
      marginTop: mTop + 'px',
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
  >
    <div class="pictrue">
      <img
        :src="imgUrl"
        v-if="imgUrl"
        :style="{
          borderRadius: bgRadius,
        }"
      />
      <div
        class="empty-box"
        v-else
        :style="{
          borderRadius: bgRadius,
        }"
      >
        <img src="@/assets/images/image_default.png" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'home_hotspot',
  cname: 'Vùng nóng',
  configName: 'c_hotspot',
  icon: 'iconrequ1',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'hotspot', // Tên khớp bên ngoài
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
        cname: 'Vùng nóng',
        name: 'hotspot',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt nội dung',
        titleRight: 'Kiểu chung',
        picStyle: {
          url: '',
          list: [],
        },
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        bgColor: {
          title: 'Màu nền',
          name: 'bgColor',
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
        // bottomBgColor: {
        //   title: 'Nền phía dưới',
        //   name: 'bottomBgColor',
        //   default: [
        //     {
        //       item: '#F5F5F5',
        //     },
        //   ],
        //   color: [
        //     {
        //       item: '#F5F5F5',
        //     },
        //   ],
        // },
        // topConfig: {
        //   title: 'Khoảng cách trên',
        //   val: 0,
        //   min: 0,
        // },
        // bottomConfig: {
        //   title: 'Khoảng cách dưới',
        //   val: 0,
        //   min: 0,
        // },
        // prConfig: {
        //   title: 'Khoảng cách trái phải',
        //   val: 0,
        //   min: 0,
        // },
        // mbConfig: {
        //   title: 'Khoảng cách trên trang',
        //   val: 0,
        //   min: 0,
        // },
        // fillet: {
        //   title: 'Góc tròn nền',
        //   type: 0,
        //   list: [
        //     {
        //       val: 'Tất cả',
        //       icon: 'iconcaozuo-zhengti',
        //     },
        //     {
        //       val: 'Đơn lẻ',
        //       icon: 'iconcaozuo-bianjiao',
        //     },
        //   ],
        //   valName: 'Giá trị góc tròn',
        //   val: 0,
        //   min: 0,
        //   valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        // },
      },
      bottomBgColor: '',
      confObj: {},
      pageData: {},
      topConfig: '',
      bottomConfig: '',
      prConfig: 0,
      bgRadius: 0,
      imgUrl: '',
      mTop: 0,
      list: [],
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
      this.imgUrl = data.picStyle.url;
      this.list = data.picStyle?.list;
      // this.bottomBgColor = data.bottomBgColor.color[0].item;
      // this.topConfig = data.topConfig.val;
      // this.bottomConfig = data.bottomConfig.val;
      // this.prConfig = data.prConfig.val;
      this.mTop = data.mbConfig.val;
      // let fillet = data.fillet.type;
      // let filletVal = data.fillet.val;
      // let valList = data.fillet.valList;
      // this.bgRadius = fillet
      //   ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
      //   : filletVal + 'px';
    },
  },
};
</script>

<style lang="scss" scoped>
.pictrue {
  width: 100%;
  height: 100%;
  .empty-box {
    width: 100%;
    height: 379px;
    border-radius: 0;
    background: #f3f9ff;

    img {
      width: 65px;
      height: 50px;
    }
  }
  img {
    width: 100%;
    height: 100%;
  }
}
</style>
