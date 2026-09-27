<template>
  <common_wrapper :config="configObj">
    <div class="vip-container" :style="[containerStyle]">
      <div class="left-content">
        <img :src="imgUrl" class="vip-icon" v-if="imgUrl" />
        <div class="text-content" :style="{ color: tipsTextColor }">
          Đăng ký thành viên SVIP dự kiến tiết kiệm <span :style="{ color: moneyTextColor }">2.90</span> đ
        </div>
      </div>
      <div class="right-content">
        <div class="open-btn" :style="[btnStyle]">
          {{ rightBntText }}
          <span class="iconfont iconjinru"></span>
        </div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'home_paid_vip',
  cname: 'Thành viên trả phí',
  configName: 'c_paid_vip',
  icon: '#iconzujian-fufeihuiyuan',
  type: 3, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'home_paid_vip', // Tên khớp bên ngoài
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
    // Container Style (Page Spacing, Page Background)
    // Component Style (Background, Radius)
    containerStyle() {
      return {};
    },
    // Button Style
    btnStyle() {
      return {
        color: this.buttonColor,
        fontSize: this.buttonFontSize + 'px',
      };
    },
    imgUrl() {
      return this.imgConfig.url;
    },
    rightBntText() {
      return this.rightBntConfig.value;
    },
    // Colors
    tipsTextColor() {
      return this.isCustomTone ? this.tipsColor : '#333333';
    },
    moneyTextColor() {
      return this.isCustomTone ? this.moneyColor : '#F62C2C';
    },
    buttonColor() {
      return this.isCustomTone ? this.btnColor : '#FF6B00';
    },
    buttonFontSize() {
      return this.configObj && this.configObj.btnConfig ? this.configObj.btnConfig.val : 13;
    },
    isCustomTone() {
      return this.configObj && this.configObj.toneConfig && this.configObj.toneConfig.tabVal === 1;
    },
    // Config mappings
    imgConfig() {
      return this.configObj && this.configObj.imgConfig ? this.configObj.imgConfig : {};
    },
    rightBntConfig() {
      return this.configObj && this.configObj.rightBntConfig ? this.configObj.rightBntConfig : {};
    },
    // Common Style Mappings
    bottomBgColor() {
      return this.configObj &&
        this.configObj.bottomBgColor &&
        this.configObj.bottomBgColor.color &&
        this.configObj.bottomBgColor.color[0]
        ? this.configObj.bottomBgColor.color[0].item
        : '#FFF0D1';
    },
    filletVal() {
      if (!this.configObj) return '0px';
      const fillet = this.configObj.fillet;
      if (!fillet) return '0px';
      if (fillet.type === 0) {
        return fillet.val + 'px';
      } else {
        return fillet.valList.map((item) => item.val + 'px').join(' ');
      }
    },
    // Text Style Colors
    tipsColor() {
      return this.configObj &&
        this.configObj.tipsColor &&
        this.configObj.tipsColor.color &&
        this.configObj.tipsColor.color[0]
        ? this.configObj.tipsColor.color[0].item
        : '#333333';
    },
    moneyColor() {
      return this.configObj &&
        this.configObj.moneyColor &&
        this.configObj.moneyColor.color &&
        this.configObj.moneyColor.color[0]
        ? this.configObj.moneyColor.color[0].item
        : '#F62C2C';
    },
    btnColor() {
      return this.configObj &&
        this.configObj.btnColor &&
        this.configObj.btnColor.color &&
        this.configObj.btnColor.color[0]
        ? this.configObj.btnColor.color[0].item
        : '#FF6B00';
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
  data() {
    return {
      configObj: null,
      paddingConfig: null,
      marginConfig: null,
      borderConfig: null,
      shadowConfig: null,
      componentBgConfig: null,
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Thành viên trả phí',
        name: 'home_paid_vip',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        // Content Settings
        titleContent: 'Cài đặt nội dung',
        imgConfig: {
          info: 'Đề xuất: 36px * 36px',
          url: require('@/assets/images/goods_vip.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh thành viên',
        },
        rightBntConfig: {
          title: 'Nút bên phải',
          value: 'Kích hoạt ngay',
          place: 'Vui lòng nhập chữ trên nút',
          max: 6,
        },

        // Style Settings - Text
        titleStyle: 'Cài đặt chữ',
        toneConfig: {
          title: 'Tông màu',
          tabVal: 0,
          tabList: [{ name: 'Mặc định' }, { name: 'Tùy chỉnh' }],
        },
        tipsColor: {
          title: 'Chữ gợi ý',
          default: [{ item: '#333333' }],
          color: [{ item: '#333333' }],
        },
        moneyColor: {
          title: 'Chữ số tiền',
          default: [{ item: '#F62C2C' }],
          color: [{ item: '#F62C2C' }],
        },
        btnColor: {
          title: 'Màu nút',
          default: [{ item: '#FF6B00' }],
          color: [{ item: '#FF6B00' }],
        },
        btnConfig: {
          title: 'Cỡ chữ nút',
          val: 13,
          min: 12,
        },

        // Style Settings - Common
        titleCurrency: 'Kiểu chung',
        componentBgColor: {
          title: 'Nền thành phần',
          default: [{ item: '#FFF0D1' }],
          color: [{ item: '#FFF0D1' }],
        },
        paddingConfig: {
          title: 'Lề trong',
          val: 15,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 15 }, { val: 15 }, { val: 15 }, { val: 15 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
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
        componentBgConfig: {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#FFF0D1' }, { item: '#FFF0D1' }],
            color: [{ item: '#FFF0D1' }, { item: '#FFF0D1' }],
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
        fillet: {
          title: 'Bo góc nền',
          type: 0,
          list: [
            { val: 'Tất cả', icon: 'iconcaozuo-zhengti' },
            { val: 'Từng góc', icon: 'iconcaozuo-bianjiao' },
          ],
          valName: 'Giá trị bo góc',
          val: 8,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
      },
    };
  },
  mounted() {
    this.$nextTick(() => {
      if (this.num) {
        let data = this.$store.state.mobildConfig.defaultArray[this.num];
        this.setConfig(data);
      }
    });
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      let dataClone = JSON.parse(JSON.stringify(data));

      // Backward compatibility for legacy configs
      if (!dataClone.paddingConfig) {
        dataClone.paddingConfig = {
          title: 'Lề trong',
          val: 15,
          min: 0,
          max: 100,
          valList: [{ val: 15 }, { val: 15 }, { val: 15 }, { val: 15 }],
        };
        if (dataClone.topConfig) dataClone.paddingConfig.valList[0].val = dataClone.topConfig.val;
        if (dataClone.bottomConfig) dataClone.paddingConfig.valList[2].val = dataClone.bottomConfig.val;
        if (dataClone.prConfig) {
          dataClone.paddingConfig.valList[1].val = dataClone.prConfig.val;
          dataClone.paddingConfig.valList[3].val = dataClone.prConfig.val;
        }
      }
      if (!dataClone.marginConfig) {
        dataClone.marginConfig = {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        };
        if (dataClone.mbConfig) dataClone.marginConfig.valList[0].val = dataClone.mbConfig.val;
      }

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
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-page {
  display: inline-block;
  width: -webkit-fill-available;
  overflow: hidden;
}
.vip-container {
  display: flex;
  justify-content: space-between;
  align-items: center;

  .left-content {
    display: flex;
    align-items: center;

    .vip-icon {
      height: 18px;
      margin-right: 8px;
    }

    .text-content {
      font-size: 13px;
      font-weight: 400;
    }
  }

  .right-content {
    .open-btn {
      display: flex;
      align-items: center;
      font-weight: 500;
      cursor: pointer;

      .iconjinru {
        font-size: 12px;
        margin-left: 2px;
      }
    }
  }
}
</style>
