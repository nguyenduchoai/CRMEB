<template>
  <div
    class="search-box"
    :style="{
      background: `linear-gradient(90deg,${bgColor[0].item} 0%,${bgColor[1].item} 100%)`,
      marginTop: `${slider}px`,
      paddingLeft: `${prConfig}px`,
    }"
    v-if="bgColor.length > 0"
  >
    <img :src="logoUrl" alt="" v-if="logoUrl" />
    <div class="box" :class="{ on: rollStyle, center: txtPosition }">Tìm kiếm sản phẩm</div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'search_box',
  cname: 'Ô tìm kiếm',
  icon: 'iconsousukuang1',
  configName: 'c_search_box',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'headerSerch', // Tên khớp bên ngoài
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
        name: 'headerSerch',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        // tabConfig: {
        //     tabVal: 0,
        //     type: 1,
        //     tabList: [
        //         {
        //             name: 'Kiểu 1',
        //             icon:'iconsearch_1'
        //         },
        //         {
        //             name: 'Kiểu 2',
        //             icon:'iconsearch_2'
        //         }
        //     ]
        // },
        bgColor: {
          title: 'Màu nền (chuyển màu)',
          name: 'bgColor',
          default: [
            {
              item: '#F62C2C',
            },
            {
              item: '#F96E29',
            },
          ],
          color: [
            {
              item: '#F62C2C',
            },
            {
              item: '#F96E29',
            },
          ],
        },
        boxStyle: {
          title: 'Kiểu viền',
          name: 'boxStyle',
          type: 0,
          list: [
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
          ],
        },
        txtStyle: {
          title: 'Vị trí văn bản',
          name: 'txtStyle',
          type: 0,
          list: [
            {
              val: 'Căn trái',
              icon: 'icondoc_left',
            },
            {
              val: 'Căn giữa',
              icon: 'icondoc_center',
            },
          ],
        },
        prConfig: {
          title: 'Lề nền',
          val: 10,
          min: 0,
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        hotWords: {
          list: [
            {
              val: '',
            },
          ],
        },
        logoConfig: {
          type: 1,
          header: 'Cài đặt logo',
          title: '',
          url: '',
        },
      },
      // tabVal: '',
      bgColor: [],
      rollStyle: '',
      txtPosition: '',
      slider: '',
      pageData: {},
      prConfig: 0,
      logoUrl: '',
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
        this.bgColor = data.bgColor.color;
        this.rollStyle = data.boxStyle.type;
        this.txtPosition = data.txtStyle.type;
        this.slider = data.mbConfig.val;
        this.logoUrl = data.logoConfig.url;
        this.prConfig = data.prConfig.val;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.search-box {
  display: flex;
  align-items: center;
  width: 100%;
  height: 48px;
  padding: 10px 10px 10px 0;
  cursor: pointer;
  img {
    width: 76px;
    height: 30px;
    margin-right: 10px;
  }
  .box {
    flex: 1;
    height: 30px;
    line-height: 30px;
    color: #999;
    font-size: 12px;
    padding-left: 10px;
    background: #fff;
    border-radius: 15px;
    &.on {
      border-radius: 0;
    }
    &.center {
      text-align: center;
      padding-left: 0;
    }
  }
}
</style>
