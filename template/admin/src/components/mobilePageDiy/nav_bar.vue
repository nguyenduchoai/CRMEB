<template>
  <div class="mobile-page">
    <div
      class="menus"
      :style="{
        background: `linear-gradient(90deg,${bgColor[0].item} 0%,${bgColor[1].item} 100%)`,
        marginTop: `${cSlider}px`,
      }"
      v-if="bgColor.length > 0"
    >
      <div class="item" v-for="(item, index) in list" :class="{ on: curIndex == index }" :style="{ color: txtColor }">
        {{ item.name }} <span :style="{ background: txtColor }"></span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'nav_bar',
  configName: 'c_nav_bar',
  cname: 'Danh mục sản phẩm',
  icon: 'iconfenleidaohang1',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'tabNav', // Tên khớp bên ngoài
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
        name: 'tabNav',
        timestamp: this.num,
        status: {
          title: 'Công tắc',
          default: {
            status: false,
          },
        },
        txtColor: {
          title: 'Màu chữ',
          name: 'txtColor',
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
        bgColor: {
          title: 'Màu nền',
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
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
      },
      list: [
        {
          name: 'Tuyển chọn',
        },
        {
          name: 'Mỹ phẩm làm đẹp',
        },
        {
          name: 'Mẹ và bé',
        },
        {
          name: 'Trang sức',
        },
        {
          name: 'Thời trang nam',
        },
      ],
      curIndex: 0,
      bgColor: [],
      cSlider: 0,
      txtColor: '',
      status: false,
      pageData: {},
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
        this.cSlider = data.mbConfig.val;
        this.txtColor = data.txtColor.color[0].item;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.menus {
  display: flex;
  align-items: center;
  width: 100%;
  height: 46px;
  cursor: pointer;
  background: linear-gradient(90deg, #f62c2c 0%, #f96e29 100%);
  .item {
    position: relative;
    flex: 1;
    text-align: center;
    color: #fff;
    &.on span {
      display: block;
      position: absolute;
      left: 50%;
      bottom: -5px;
      width: 16px;
      height: 2px;
      transform: translateX(-50%);
      background: #fff;
    }
  }
}
</style>
