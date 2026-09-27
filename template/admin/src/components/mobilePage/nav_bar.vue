<template>
  <common_wrapper :config="configObj">
    <div
      class="menusCon"
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: bgRadius,
      }"
    >
      <div class="menus">
        <div
          class="item on"
          v-if="styleConfig == 0"
          :style="{
            color: toneConfig ? textColor : '#333',
          }"
        >
          Trang chủ<span
            :style="{
              background: toneConfig
                ? `linear-gradient(90deg,${decorateColorLeft} 0%,${decorateColorRight} 100%)`
                : themeColor,
            }"
          ></span>
        </div>
        <div
          class="item on3"
          v-else-if="styleConfig == 1"
          :style="{
            color: toneConfig ? textColor2 : colorStyle.theme,
          }"
        >
          Trang chủ<span
            :style="{
              borderColor: toneConfig ? decorateColor : colorStyle.theme,
            }"
          ></span>
        </div>
        <div
          class="item on2"
          v-else
          :style="{
            color: toneConfig ? textColor3 : '#fff',
            background: toneConfig
              ? `linear-gradient(90deg,${decorateColorLeft} 0%,${decorateColorRight} 100%)`
              : themeColor,
          }"
        >
          Trang chủ
        </div>
        <div class="item" v-for="(item, index) in navList.slice(0, 20)" :key="index">
          {{ item.text.val }}
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'nav_bar',
  configName: 'c_nav_bar',
  cname: 'Tab',
  icon: '#iconzujian-xuanxiangka1',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'tabNav', // Tên khớp bên ngoài
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
        cname: 'Tab',
        desc: 'Giới thiệu tab',
        name: 'tabNav',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleTab: 'Cài đặt tab',
        titleRight: 'Kiểu tab',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          tabList: [
            {
              name: 'Phong cách 1',
            },
            {
              name: 'Phong cách 2',
            },
            {
              name: 'Phong cách 3',
            },
          ],
        },
        stickyConfig: {
          title: 'Ghim đầu trang khi cuộn',
          tabVal: 0,
          tabList: [
            {
              name: 'Kích hoạt',
            },
            {
              name: 'Không bật',
            },
          ],
        },
        tabListConfig: {
          title: 'Kéo thả khối để điều chỉnh thứ tự tab',
          max: 100,
          list: [
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Trang chủ',
                max: 6,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Tiêu đề mẫu',
                max: 6,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Tiêu đề mẫu',
                max: 6,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
            {
              text: {
                title: 'Chữ hiển thị',
                val: 'Tiêu đề mẫu',
                max: 6,
                pla: 'Vui lòng nhập tên danh mục',
              },
              dataType: {
                title: 'Loại dữ liệu',
                tabVal: 0,
                tabList: [
                  {
                    name: 'Trang tùy chỉnh',
                  },
                  {
                    name: 'Danh mục sản phẩm',
                  },
                ],
              },
              microPage: {
                name: '',
                id: 0,
              },
              classPage: {
                name: '',
                id: 0,
              },
            },
          ],
        },
        toneConfig: {
          title: 'Tông màu',
          tabVal: 0,
          tabList: [
            {
              name: 'Theo phong cách chủ đề',
            },
            {
              name: 'Tùy chỉnh',
            },
          ],
        },
        decorateColor: {
          title: 'Phần tử trang trí',
          default: [
            {
              item: '#E93323',
            },
            {
              item: '#FF7931',
            },
          ],
          color: [
            {
              item: '#E93323',
            },
            {
              item: '#FF7931',
            },
          ],
        },
        decorateColor2: {
          title: 'Phần tử trang trí',
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
        textColor: {
          title: 'Chữ khi được chọn',
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
        textColor2: {
          title: 'Chữ khi được chọn',
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
        textColor3: {
          title: 'Chữ khi được chọn',
          default: [
            {
              item: '#FFFFFF',
            },
          ],
          color: [
            {
              item: '#FFFFFF',
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
        paddingConfig: {
          title: 'Lề trong',
          isAll: false,
          val: 0,
          min: 0,

          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          isAll: false,
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
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
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
        borderConfig: {
          title: 'Cài đặt viền',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0,
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
          tabList: [
            {
              name: 'Ẩn',
            },
            {
              name: 'Hiện',
            },
          ],
          val: 0,
          colorConfig: {
            title: 'Màu đổ bóng',
            default: [
              {
                item: 'rgba(0,0,0,0.1)',
              },
            ],
            color: [
              {
                item: 'rgba(0,0,0,0.1)',
              },
            ],
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
      configObj: null,
      pageData: {},
      navList: [],
      toneConfig: 0,
      decorateColorLeft: '',
      decorateColorRight: '',
      decorateColor: '',
      bgColorLeft: '',
      bgColorRight: '',
      textColor: '',
      textColor2: '',
      textColor3: '',
      cSlider: 0,
      bgRadius: 0,
      bottomBgColor: '',
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      styleConfig: 0,
      themeColor: '',
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
      this.configObj = data;
      for (let key in this.defaultConfig) {
        if (data[key] === undefined) {
          this.$set(data, key, this.defaultConfig[key]);
        }
      }

      if (!data.paddingConfig) {
        let paddingConfig = {
          title: 'Lề trong',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        if (data.topConfig) paddingConfig.valList[0].val = data.topConfig.val;
        if (data.prConfig) {
          paddingConfig.valList[1].val = data.prConfig.val;
          paddingConfig.valList[3].val = data.prConfig.val;
        }
        if (data.bottomConfig) paddingConfig.valList[2].val = data.bottomConfig.val;
        this.$set(this.configObj, 'paddingConfig', paddingConfig);
      }

      if (!data.marginConfig) {
        let marginConfig = {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        if (data.mbConfig) marginConfig.valList[0].val = data.mbConfig.val;
        this.$set(this.configObj, 'marginConfig', marginConfig);
      }

      this.navList = data.tabListConfig.list;
      this.toneConfig = data.toneConfig.tabVal;
      this.decorateColorLeft = data.decorateColor.color[0].item;
      this.decorateColorRight = data.decorateColor.color[1].item;
      this.decorateColor = data.decorateColor2.color[0].item;
      this.textColor = data.textColor.color[0].item;
      this.textColor2 = data.textColor2.color[0].item;
      this.textColor3 = data.textColor3.color[0].item;
      this.bgColorLeft = data.moduleColor.color[0].item;
      this.bgColorRight = data.moduleColor.color[1].item;
      this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
      this.bottomBgColor = data.bottomBgColor.color[0].item;
      this.styleConfig = data.styleConfig.tabVal;
      let fillet = data.fillet.type;
      let filletVal = data.fillet.val;
      let valList = data.fillet.valList;
      this.bgRadius = fillet
        ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
        : filletVal + 'px';
    },
  },
};
</script>

<style scoped lang="scss">
.menusCon {
  overflow: hidden;
}
.menus {
  display: flex;
  align-items: center;
  width: 10000%;
  height: 40px;
  cursor: pointer;
  padding-left: 12px;

  .item {
    position: relative;
    color: #333;
    font-size: 14px;
    margin-right: 28px;
    z-index: 9;

    &.on {
      font-size: 16px;
      font-weight: 600;

      span {
        display: block;
        position: absolute;
        left: 50%;
        bottom: 4px;
        width: 100%;
        height: 4px;
        border-radius: 100px;
        transform: translateX(-50%);
        background: #fff;
        background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);
        z-index: -1;
      }
    }

    &.on2 {
      width: 46px;
      height: 24px;
      text-align: center;
      line-height: 24px;
      color: #fff;
      background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);
      border-radius: 50px;
    }

    &.on3 {
      font-size: 16px;
      font-weight: 600;
      color: #e93323;

      span {
        position: absolute;
        width: 30px;
        height: 30px;
        border: 3px solid #e93323;
        border-left: 3px solid transparent !important;
        border-top: 3px solid transparent !important;
        border-right: 3px solid transparent !important;
        border-radius: 50%;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%);
      }
    }
  }
}
</style>
