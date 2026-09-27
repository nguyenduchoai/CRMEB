<template>
  <div
    class="title-box"
    :class="bgStyle === 0 ? '' : 'titleOn'"
    :style="[
      { textAlign: txtPosition },
      { fontStyle: txtStyle != 'bold' ? txtStyle : '' },
      { fontWeight: txtStyle == 'bold' ? txtStyle : '' },
      { fontSize: fontSize + 'px' },
      { marginTop: mTOP + 'px' },
      { background: titleColor },
      { margin: '0 ' + prConfig + 'px' },
      { color: themeColor },
    ]"
  >
    {{ titleTxt }}
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_title',
  cname: 'Tiêu đề',
  icon: 'iconbiaoti1',
  configName: 'c_home_title',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'titles', // Tên khớp bên ngoài
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
      defaultConfig: {
        name: 'titles',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        titleConfig: {
          title: 'Tiêu đề',
          value: 'Tiêu đề',
          place: 'Vui lòng nhập tiêu đề',
          max: 10,
        },
        linkConfig: {
          title: 'Liên kết',
          value: '',
          place: 'Vui lòng nhập địa chỉ liên kết',
          max: 100,
        },
        themeColor: {
          title: 'Màu chữ',
          name: 'themeColor',
          default: [
            {
              item: '#282828',
            },
          ],
          color: [
            {
              item: '#282828',
            },
          ],
        },
        titleColor: {
          title: 'Màu nền',
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
        bgStyle: {
          title: 'Kiểu nền',
          name: 'bgStyle',
          type: 0,
          list: [
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
          ],
        },
        prConfig: {
          title: 'Lề nền',
          val: 0,
          min: 0,
        },
        textPosition: {
          title: 'Vị trí văn bản',
          type: 0,
          list: [
            {
              val: 'Căn trái',
              style: 'left',
              icon: 'icondoc_left',
            },
            {
              val: 'Căn giữa',
              style: 'center',
              icon: 'icondoc_center',
            },
            {
              val: 'Căn phải',
              style: 'right',
              icon: 'icondoc_right',
            },
          ],
        },
        textStyle: {
          title: 'Kiểu văn bản',
          type: 0,
          list: [
            {
              val: 'Bình thường',
              style: 'normal',
              icon: 'icondoc_general',
            },
            {
              val: 'In nghiêng',
              style: 'italic',
              icon: 'icondoc_skew',
            },
            {
              val: 'In đậm',
              style: 'bold',
              icon: 'icondoc_bold',
            },
          ],
        },
        fontSize: {
          title: 'Cỡ chữ',
          val: 12,
          min: 12,
        },
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
      },
      titleTxt: '',
      link: '',
      txtPosition: '',
      txtStyle: '',
      fontSize: 0,
      mTOP: 0,
      titleColor: '',
      themeColor: '',
      prConfig: 0,
      bgStyle: 0,
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
        this.titleTxt = data.titleConfig.value;
        this.link = data.linkConfig.value;
        this.txtPosition = data.textPosition.list[data.textPosition.type].style;
        this.txtStyle = data.textStyle.list[data.textStyle.type].style;
        this.themeColor = data.themeColor.color[0].item;
        this.fontSize = data.fontSize.val;
        this.mTOP = data.mbConfig.val;
        this.prConfig = data.prConfig.val;
        this.bgStyle = data.bgStyle.type;
        this.titleColor = data.titleColor.color[0].item;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.titleOn {
  border-radius: 10px !important;
}
.title-box {
  color: #282828;
  padding: 5px 10px;
}
</style>
