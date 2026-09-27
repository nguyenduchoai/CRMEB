<template>
  <div class="mobile-page">
    <div
      class="box"
      :style="{ background: bgColor, marginLeft: edge + 'px', marginRight: edge + 'px', marginTop: udEdge + 'px' }"
      v-html="richText"
    ></div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_ueditor',
  cname: 'Văn bản định dạng',
  configName: 'c_ueditor_box',
  icon: 'iconfuwenben1',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'richText', // Tên khớp bên ngoài
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
        name: 'richText',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
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
        lrConfig: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
        },
        udConfig: {
          title: 'Lề trên dưới',
          val: 0,
          min: 0,
        },
        richText: {
          val: '',
        },
      },
      cSlider: '',
      bgColor: '',
      confObj: {},
      pageData: {},
      edge: '',
      udEdge: '',
      richText: '',
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
      if (data.lrConfig) {
        this.bgColor = data.bgColor.color[0].item;
        this.edge = data.lrConfig.val;
        this.udEdge = data.udConfig.val;
        this.richText = data.richText.val;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.mobile-page ::v-deepvideo {
  width: 100% !important;
}
.box {
  min-height: 100px;
  padding: 10px;
  background: #f5f5f5;
  img {
    max-width: 100%;
    height: auto;
  }
}
</style>
