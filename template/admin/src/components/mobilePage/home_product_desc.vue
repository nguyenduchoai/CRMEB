<template>
  <common_wrapper :config="configObj">
    <div class="product-desc-box">
      <div
        class="title"
        v-if="titleShow"
        :style="{
          color: titleColor,
          fontSize: titleSize + 'px',
          textAlign: titleAlign,
        }"
      >
        Giới thiệu sản phẩm
      </div>
      <div class="desc">Mô-đun này chưa có cài đặt nội dung, chỉ hỗ trợ cài đặt kiểu chung</div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_product_desc',
  cname: 'Giới thiệu sản phẩm',
  configName: 'c_product_desc',
  icon: '#iconzujian-wenzhangliebiao',
  type: 3,
  defaultName: 'productDesc',
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
    titleShow() {
      return this.configObj?.isShow?.tabVal == 0;
    },
    titleColor() {
      return this.configObj?.textColor?.color?.[0]?.item || '#333';
    },
    titleSize() {
      return this.configObj?.fontSize?.val || 16;
    },
    titleAlign() {
      return this.configObj?.textPosition.val || 'left';
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
      defaultConfig: {
        cname: 'Giới thiệu sản phẩm',
        name: 'productDesc',
        timestamp: this.num,
        contentTitle: 'Cài đặt nội dung',
        titleStyle: 'Kiểu tiêu đề',
        setUp: {
          tabVal: 0,
        },
        isShow: {
          title: 'Hiển thị tiêu đề',
          tabVal: 0,
          tabList: [{ name: 'Hiện' }, { name: 'Ẩn' }],
        },

        textPosition: {
          title: 'Căn chỉnh',
          val: 'center',
        },
        textColor: {
          title: 'Màu chữ',
          default: [{ item: '#333' }],
          color: [{ item: '#333' }],
        },
        fontSize: {
          title: 'Cỡ chữ',
          val: 16,
          min: 12,
          max: 40,
        },
        titleCurrency: 'Kiểu chung',
        moduleColor: {
          title: 'Màu nền',
          name: 'moduleColor',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          name: 'bottomBgColor',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        paddingConfig: {
          title: 'Lề trong',
          val: 10,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 10 }, { val: 10 }, { val: 10 }, { val: 10 }],
        },
        componentBgConfig: {
          title: 'Nền thành phần',
          tabVal: 0,
          colorConfig: {
            title: 'Cài đặt màu sắc',
            default: [{ item: '#fff' }],
            color: [{ item: '#fff' }],
          },
          imageConfig: {
            title: 'Cài đặt ảnh',
            url: '',
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
        borderRadius: '0',
        zIndexConfig: {
          title: 'Phân cấp',
          val: 0,
          min: 0,
        },
      },
    };
  },
  methods: {
    setConfig(data) {
      if (!data) return;
      if (data) {
        this.configObj = data;
      }
    },
  },
};
</script>

<style scoped>
.product-desc-box {
  text-align: center;
}
.title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}
.desc {
  font-size: 12px;
  color: #999;
  margin-top: 10px;
}
</style>
