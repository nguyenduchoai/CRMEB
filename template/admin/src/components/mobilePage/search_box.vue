<template>
  <common_wrapper :config="configObj">
    <div class="search-box" :style="[searchBoxStyle]">
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
  </common_wrapper>
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
    searchBoxStyle() {
      if (this.configObj && this.configObj.moduleColor) {
        return {
          background: `linear-gradient(90deg, ${this.configObj.moduleColor.color[0].item} 0%, ${this.configObj.moduleColor.color[1].item} 100%)`,
        };
      }
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
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
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
        paddingConfig: {
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        componentBgConfig: {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#F5F5F5' }, { item: '#F5F5F5' }],
            color: [{ item: '#F5F5F5' }, { item: '#F5F5F5' }],
          },
          colorDirection: {
            title: 'Hướng chuyển màu',
            tabVal: 0,
            tabList: [{ name: 'Ngang' }, { name: 'Dọc' }, { name: 'Chéo trái' }, { name: 'Chéo phải' }],
          },
          imageConfig: {
            header: 'Ảnh nền',
            title: '',
            name: 'Tải lên ảnh',
            type: 'code',
            url: '',
            info: 'Kích thước đề xuất: 750px * 400px',
          },
        },
        borderConfig: {
          title: 'Cài đặt viền',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0, // 0: Hide, 1: Show
          styleConfig: {
            title: 'Kiểu viền',
            tabVal: 0,
            tabList: [
              { name: 'Nét liền', style: 'solid' },
              { name: 'Nét đứt', style: 'dashed' },
              { name: 'Nét chấm', style: 'dotted' },
            ],
          },
          widthConfig: {
            title: 'Độ dày viền',
            val: 1,
            min: 1,
          },
          colorConfig: {
            title: 'Màu viền',
            default: [{ item: '#e5e5e5' }],
            color: [{ item: '#e5e5e5' }],
          },
        },
        shadowConfig: {
          title: 'Cài đặt đổ bóng',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0,
          colorConfig: {
            title: 'Màu đổ bóng',
            default: [{ item: 'rgba(0,0,0,0.1)' }],
            color: [{ item: 'rgba(0,0,0,0.1)' }],
          },
          xConfig: {
            title: 'Độ lệch trục X',
            val: 0,
            min: -50,
          },
          yConfig: {
            title: 'Độ lệch trục Y',
            val: 0,
            min: -50,
          },
          blurConfig: {
            title: 'Bán kính làm mờ',
            val: 10,
            min: 0,
          },
          spreadConfig: {
            title: 'Bán kính lan tỏa',
            val: 0,
            min: -50,
          },
        },
      },
      pageData: {},
      logoUrl: '',
      styleConfig: 0,
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
      paddingConfig: null,
      marginConfig: null,
      borderConfig: null,
      shadowConfig: null,
      componentBgConfig: null,
      configObj: null,
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
      let dataClone = JSON.parse(JSON.stringify(data));
      for (let key in this.defaultConfig) {
        if (dataClone[key] === undefined) {
          this.$set(dataClone, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }
      this.configObj = dataClone;

      this.paddingConfig = dataClone.paddingConfig;
      this.marginConfig = dataClone.marginConfig;
      this.borderConfig = dataClone.borderConfig;
      this.shadowConfig = dataClone.shadowConfig;
      this.componentBgConfig = dataClone.componentBgConfig;

      this.logoUrl = dataClone.logoConfig.url;
      this.styleConfig = dataClone.styleConfig.tabVal;
      this.styleTypeConfig = dataClone.styleTypeConfig.tabVal;
      this.txtFixConfig = dataClone.txtFixConfig.tabVal;
      this.txtStyleConfig = dataClone.txtStyleConfig.tabList[dataClone.txtStyleConfig.tabVal].style;
      this.txtSize = dataClone.txtSize.val;
      this.txtColor = dataClone.txtColor.color[0].item;
      this.titleConfig = dataClone.titleConfig.value;
      this.searchBoxColor = dataClone.searchBoxColor.color[0].item;
      this.tipConfig = dataClone.tipConfig.value;
      this.hotWords = dataClone.hotWords.list.length ? dataClone.hotWords.list[0].val : '';
      this.tipColor = dataClone.tipColor.color[0].item;
      this.hotWordsColor = dataClone.hotWordsColor.color[0].item;
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-page {
  display: inline-block;
  width: -webkit-fill-available;
}
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
