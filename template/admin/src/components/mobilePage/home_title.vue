<template>
  <div
    :style="{
      background: bottomBgColor,
      marginTop: mbConfig + 'px',
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
  >
    <div
      class="title"
      :style="{
        background: `linear-gradient(90deg,${titleColorLeft} 0%,${titleColorRight} 100%)`,
        borderRadius: fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px',
      }"
    >
      <div
        class="title-box"
        :class="buttonConfig ? 'on' : ''"
        :style="{
          color: themeColor,
          fontSize: fontSize + 'px',
          fontStyle: txtStyle != 'bold' ? txtStyle : '',
          fontWeight: txtStyle == 'bold' ? txtStyle : '',
          textAlign: txtPosition,
        }"
      >
        {{ titleTxt }}
      </div>
      <div
        v-if="!buttonConfig"
        :style="{
          color: buttonColor,
          fontSize: buttonSize + 'px',
        }"
      >
        {{ buttonTitle }}<span class="iconfont iconjinru"></span>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_title',
  cname: 'Tiêu đề văn bản',
  icon: '#iconzujian-biaoti',
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
        cname: 'Tiêu đề văn bản',
        name: 'titles',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt tiêu đề',
        titleRight: 'Cài đặt chữ',
        titleCurrency: 'Kiểu chung',
        titleConfig: {
          title: 'Tên tiêu đề',
          value: 'Tiêu đề',
          place: 'Vui lòng nhập tiêu đề',
          max: 10,
        },
        titleConfigRight: {
          title: 'Chữ bên phải',
          value: 'Xem thêm',
          place: 'Vui lòng nhập chữ bên phải',
          max: 5,
        },
        buttonConfig: {
          title: 'Nút bên phải',
          tabVal: 0,
          tabList: [
            {
              name: 'Hiện',
            },
            {
              name: 'Ẩn',
            },
          ],
        },
        linkConfig: {
          title: 'Liên kết',
          value: '',
          place: 'Vui lòng nhập địa chỉ liên kết',
          max: 100,
          type: 'link',
        },
        themeColor: {
          title: 'Màu tiêu đề',
          name: 'themeColor',
          default: [
            {
              item: '#333333',
            },
          ],
          color: [
            {
              item: '#333333',
            },
          ],
        },
        buttonColor: {
          title: 'Màu nút',
          default: [
            {
              item: '#999999',
            },
          ],
          color: [
            {
              item: '#999999',
            },
          ],
        },
        titleColor: {
          title: 'Nền thành phần',
          default: [
            {
              item: '#fff',
            },
            {
              item: '#fff',
            },
          ],
          color: [
            {
              item: '#fff',
            },
            {
              item: '#fff',
            },
          ],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
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
        buttonText: {
          title: 'Chữ trên nút',
          val: 12,
          min: 6,
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
        prConfig: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
        },
        textPosition: {
          title: 'Vị trí tiêu đề',
          tabVal: 0,
          tabList: [
            {
              name: 'Căn trái',
              style: 'left',
              icon: 'icondoc_left',
            },
            {
              name: 'Căn giữa',
              style: 'center',
              icon: 'icondoc_center',
            },
            {
              name: 'Căn phải',
              style: 'right',
              icon: 'icondoc_right',
            },
          ],
        },
        textStyle: {
          title: 'Kiểu tiêu đề',
          tabVal: 0,
          tabList: [
            {
              name: 'Bình thường',
              style: 'normal',
              icon: 'icondoc_general',
            },
            {
              name: 'In nghiêng',
              style: 'italic',
              icon: 'icondoc_skew',
            },
            {
              name: 'In đậm',
              style: 'bold',
              icon: 'icondoc_bold',
            },
          ],
        },
        fontSize: {
          title: 'Chữ tiêu đề',
          val: 16,
          min: 8,
        },
        mbConfig: {
          title: 'Lề trên trang',
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
      titleTxt: '',
      link: '',
      txtPosition: '',
      txtStyle: '',
      fontSize: 0,
      titleColorLeft: '',
      titleColorRight: '',
      themeColor: '',
      prConfig: 0,
      pageData: {},
      bottomBgColor: '',
      mbConfig: 0,
      buttonConfig: 0,
      buttonTitle: '',
      buttonColor: '',
      buttonSize: 0,
      topConfig: 0,
      bottomConfig: 0,
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
      if (data.mbConfig) {
        this.titleTxt = data.titleConfig.value;
        this.link = data.linkConfig.value;
        this.txtPosition = data.textPosition.tabList[data.textPosition.tabVal].style;
        this.txtStyle = data.textStyle.tabList[data.textStyle.tabVal].style;
        this.themeColor = data.themeColor.color[0].item;
        this.fontSize = data.fontSize.val;
        this.mbConfig = data.mbConfig.val;
        this.prConfig = data.prConfig.val;
        this.titleColorLeft = data.titleColor.color[0].item;
        this.titleColorRight = data.titleColor.color[1].item;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.buttonConfig = data.buttonConfig.tabVal;
        this.buttonTitle = data.titleConfigRight.value;
        this.buttonColor = data.buttonColor.color[0].item;
        this.buttonSize = data.buttonText.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.fillet = data.fillet.type;
        this.filletVal = data.fillet.val;
        this.valList = data.fillet.valList;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.title {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.titleOn {
  border-radius: 10px !important;
}
.title {
  padding: 13px 12px;
  .title-box {
    &.on {
      width: 100%;
    }
  }
}
.iconfont {
  font-size: 14px;
}
</style>
