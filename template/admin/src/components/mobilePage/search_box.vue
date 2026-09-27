<template>
  <div
    :style="{
      background: bottomBgColor,
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
  >
    <div
      class="search-box"
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: bgRadius,
      }"
    >
      <div class="search acea-row row-middle" :style="[txtPosition]">
        <img :src="logoUrl" alt="" v-if="logoUrl && styleConfig == 0 && styleTypeConfig == 1" />
        <div
          class="title"
          :style="[txtStyle]"
          v-if="titleConfig && (styleConfig == 1 || (styleConfig == 0 && styleTypeConfig == 0))"
        >
          {{ titleConfig }}
        </div>
        <div v-if="styleConfig === 0" class="box" :style="[searchStyle]">
          <span
            class="iconfont iconsousuo1"
            :style="{
              color: tipColor,
            }"
          ></span>
          <span
            class="hotWords"
            :style="{
              color: hotWordsColor,
            }"
            v-if="hotWords"
            >{{ hotWords }}</span
          >
          <span
            v-else
            :style="{
              color: tipColor,
            }"
            >{{ tipConfig }}</span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'search_box',
  cname: 'Ô tìm kiếm',
  icon: '#iconzujian-sousuokuang',
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
    colorStyle: {
      type: null,
    },
  },
  computed: {
    ...mapState('mobildConfig', ['defaultArray']),
    txtStyle() {
      let num = 0;
      if (this.styleConfig == 0 && this.styleTypeConfig != 1) {
        num = 15;
      }
      return {
        color: `${this.txtColor}`,
        fontStyle: `${this.txtStyleConfig != 'bold' ? this.txtStyleConfig : ''}`,
        fontWeight: `${this.txtStyleConfig == 'bold' ? this.txtStyleConfig : ''}`,
        fontSize: `${this.txtSize}px`,
        marginRight: `${num}px`,
      };
    },
    txtPosition() {
      return {
        justifyContent:
          this.styleConfig != 0 && this.txtFixConfig === 1
            ? 'center'
            : this.styleConfig != 0 && this.txtFixConfig === 2
            ? 'flex-end'
            : 'flex-start',
      };
    },
    searchStyle() {
      return {
        textAlign: this.txtFixConfig == 0 ? 'left' : this.txtFixConfig == 2 ? 'right' : 'center',
        background: this.searchBoxColor,
      };
    },
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
  // mixins: [theme],
  data() {
    return {
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Ô tìm kiếm',
        name: 'headerSerch',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleSearch: 'Nội dung tìm kiếm',
        titleHotWords: 'Từ khóa tìm kiếm phổ biến',
        titleRight: 'Ô tìm kiếm',
        titleCurrency: 'Kiểu chung',
        titleTxt: 'Cài đặt chữ',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          tabList: [
            {
              name: 'Tìm kiếm',
            },
            {
              name: 'Tiêu đề',
            },
          ],
        },
        styleTypeConfig: {
          title: 'Kiểu dáng',
          tabVal: 1,
          tabList: [
            {
              name: 'Tiêu đề',
            },
            {
              name: 'logo',
            },
          ],
        },
        logoConfig: {
          info: 'Đề xuất: 144px * 44px',
          url: '',
          type: 'code',
          delType: 1,
          name: 'Ảnh logo',
        },
        titleConfig: {
          title: 'Tiêu đề',
          value: 'Tiêu đề',
          place: 'Vui lòng nhập tiêu đề',
          max: 6,
        },
        linkConfig: {
          title: 'Liên kết',
          value: '',
          place: 'Vui lòng chọn liên kết',
          max: 100,
          type: 'link',
        },
        tipConfig: {
          title: 'Chữ gợi ý',
          value: 'Tìm kiếm sản phẩm',
          place: 'Điền nội dung',
          max: 20,
        },
        hotWords: {
          list: [
            {
              val: '',
            },
          ],
        },
        numConfig: {
          placeholder: 'Đặt thời gian hiển thị từ khóa phổ biến',
          title: 'Thời gian hiển thị',
          val: 3,
          type: 'words',
        },
        txtFixConfig: {
          title: 'Vị trí chữ',
          tabVal: 0,
          tabList: [
            {
              name: 'Căn trái',
            },
            {
              name: 'Căn giữa',
            },
            {
              name: 'Căn phải',
            },
          ],
        },
        txtStyleConfig: {
          title: 'Kiểu chữ',
          tabVal: 0,
          tabList: [
            {
              name: 'Bình thường',
              style: 'normal',
            },
            {
              name: 'In nghiêng',
              style: 'italic',
            },
            {
              name: 'In đậm',
              style: 'bold',
            },
          ],
        },
        txtColor: {
          title: 'Màu chữ',
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
        txtSize: {
          title: 'Cỡ chữ',
          val: 15,
          min: 0,
        },
        searchBoxColor: {
          title: 'Ô tìm kiếm',
          default: [
            {
              item: '#F5F5F5',
            },
          ],
          color: [
            {
              item: '#F5F5F5',
            },
          ],
        },
        tipColor: {
          title: 'Chữ gợi ý',
          default: [
            {
              item: '#CCCCCC',
            },
          ],
          color: [
            {
              item: '#CCCCCC',
            },
          ],
        },
        hotWordsColor: {
          title: 'Chữ từ khóa phổ biến',
          default: [
            {
              item: '#888',
            },
          ],
          color: [
            {
              item: '#888',
            },
          ],
        },
        moduleColor: {
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
      pageData: {},
      logoUrl: '',
      styleConfig: 0,
      bottomBgColor: '',
      bgColorLeft: '',
      bgColorRight: '',
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      bgRadius: 0,
      titleConfig: '',
      searchBoxColor: '',
      tipConfig: '',
      hotWords: '',
      tipColor: '',
      hotWordsColor: '',
      styleTypeConfig: 0,
      fixConfig: 0,
      txtFixConfig: 0,
      txtColor: '',
      txtStyleConfig: '',
      txtSize: 0,
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
      if (data.prConfig) {
        this.logoUrl = data.logoConfig.url;
        this.styleConfig = data.styleConfig.tabVal;
        this.styleTypeConfig = data.styleTypeConfig.tabVal;
        // this.fixConfig = data.fixConfig.tabVal || 0;
        this.txtFixConfig = data.txtFixConfig.tabVal;
        this.txtStyleConfig = data.txtStyleConfig.tabList[data.txtStyleConfig.tabVal].style;
        this.txtSize = data.txtSize.val;
        this.txtColor = data.txtColor.color[0].item;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.bgColorLeft = data.moduleColor.color[0].item;
        this.bgColorRight = data.moduleColor.color[1].item;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.prConfig = data.prConfig.val;
        this.titleConfig = data.titleConfig.value;
        this.searchBoxColor = data.searchBoxColor.color[0].item;
        this.tipConfig = data.tipConfig.value;
        this.hotWords = data.hotWords.list.length ? data.hotWords.list[0].val : '';
        this.tipColor = data.tipColor.color[0].item;
        this.hotWordsColor = data.hotWordsColor.color[0].item;
        let fillet = data.fillet.type;
        let filletVal = data.fillet.val;
        let valList = data.fillet.valList;
        this.bgRadius = fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px';
      }
    },
  },
};
</script>

<style scoped lang="scss">
.search-box {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 48px;
  padding: 9px 15px;
  cursor: pointer;
  .search {
    width: 100%;
    &.center {
      justify-content: center;
    }
    &.right {
      justify-content: right;
    }
    .hotWords {
      color: rgba(255, 255, 255, 0.8);
    }
  }
  .title {
    font-size: 15px;
    color: #333;
  }
  .map {
    color: #333;
    font-size: 14px;
    .iconfont {
      font-size: 16px;
    }
    .iconyou {
      font-size: 12px;
      opacity: 0.8;
    }
    .icondingwei {
      margin-right: 3px;
    }
  }
  img {
    width: 76px;
    height: 30px;
    margin-right: 11px;
  }
  .box {
    flex: 1;
    height: 30px;
    line-height: 30px;
    color: #ccc;
    font-size: 14px;
    background: #fff;
    border-radius: 15px;
    padding: 0 16px;

    .iconfont {
      margin-right: 5px;
      margin-top: -3px;
      display: inline-block;
      vertical-align: middle;
    }
  }
}
</style>
