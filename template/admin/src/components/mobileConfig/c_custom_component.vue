<template>
  <div class="mobile-config">
    <div v-for="(item, key) in rCom" :key="key">
      <component
        :is="item.components.name"
        :configObj="configObj"
        ref="childData"
        :configNme="item.configNme"
        :key="key"
        @getConfig="getConfig"
        :index="activeIndex"
        :num="item.num"
      ></component>
    </div>

    <!-- <rightBtn :activeIndex="activeIndex" :configObj="configObj"></rightBtn> -->
    <el-dialog
      custom-class="custom-design-dialog"
      :visible.sync="modals"
      width="100%"
      fullscreen
      :close-on-click-modal="false"
      :show-close="false"
    >
      <CustomDesign
        v-if="modals"
        :initialData="configObj.customComponents"
        :columnNum="columnNum"
        :type="type"
        @save="handleSave"
        @close="modals = false"
      />
    </el-dialog>
  </div>
</template>

<script>
import toolCom from '@/components/mobileConfigRight/index.js';
import rightBtn from '@/components/rightBtn/index.vue';
import CustomDesign from '@/components/CustomDesign';
import { mapState, mapMutations } from 'vuex';

export default {
  name: 'c_custom_component',
  componentsName: 'home_custom_component',
  components: {
    ...toolCom,
    rightBtn,
    CustomDesign,
  },
  props: {
    activeIndex: {
      type: null,
    },
    num: {
      type: null,
    },
    index: {
      type: null,
    },
  },
  data() {
    return {
      configObj: {},
      rCom: [],
      setUp: 0,
      type: 'user',
      modals: false,
    };
  },
  computed: {
    columnNum() {
      let styleConfig = null;
      if (this.type === 'article') styleConfig = this.configObj.articleColumnStyle;
      else if (this.type === 'goods') styleConfig = this.configObj.goodsColumnStyle;
      else if (this.type === 'coupon') styleConfig = this.configObj.couponColumnStyle;

      if (styleConfig) {
        return (styleConfig.tabVal || 0) + 1;
      }
      return 1;
    },
  },
  watch: {
    num(nVal) {
      let value = JSON.parse(JSON.stringify(this.$store.state.mobildConfig.defaultArray[nVal]));
      this.configObj = this.patchConfig(value);
    },
    configObj: {
      handler(nVal, oVal) {
        this.$store.commit('mobildConfig/UPDATEARR', { num: this.num, val: nVal });
      },
      deep: true,
    },
    'configObj.setUp.tabVal': {
      handler(nVal, oVal) {
        this.setUp = nVal;
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.selectType.activeValue': {
      handler(nVal, oVal) {
        this.type = nVal;
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.couponUserType.activeValue': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.goodsDataSource.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.couponDataSource.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.couponThreshold.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.articleDataSource.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
  },
  mounted() {
    this.$nextTick(() => {
      let value = JSON.parse(JSON.stringify(this.$store.state.mobildConfig.defaultArray[this.num]));
      this.configObj = this.patchConfig(value);
    });
  },
  methods: {
    patchConfig(data) {
      if (!data) return data;
      // Ensure structure exists if missing
      if (!data.setUp) {
        this.$set(data, 'setUp', { tabVal: 0 });
      }
      if (!data.customBtnConfig) {
        this.$set(data, 'customBtnConfig', {
          title: 'Thiết kế thành phần',
        });
      }
      if (!data.fillet) {
        this.$set(data, 'fillet', {
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
        });
      }
      if (!data.filletDataConfig) {
        this.$set(data, 'filletDataConfig', {
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
        });
      }
      if (!data.componentBgConfig) {
        this.$set(data, 'componentBgConfig', {
          title: 'Kiểu nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            color: [
              {
                item: '#fff',
              },
            ],
            default: [
              {
                item: '#fff',
              },
            ],
          },
          imageConfig: {
            title: 'Ảnh nền',
            url: '',
          },
          colorDirection: {
            title: 'Hướng chuyển màu',
            tabVal: 0,
            tabList: [{ name: 'Ngang' }, { name: 'Dọc' }, { name: 'Chéo trái' }, { name: 'Chéo phải' }],
          },
        });
      }
      if (!data.componentBgDataConfig) {
        this.$set(data, 'componentBgDataConfig', {
          title: 'Kiểu nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            color: [
              {
                item: '#fff',
              },
            ],
            default: [
              {
                item: '#fff',
              },
            ],
          },
          imageConfig: {
            title: 'Ảnh nền',
            url: '',
          },
          colorDirection: {
            title: 'Hướng chuyển màu',
            tabVal: 0,
            tabList: [{ name: 'Ngang' }, { name: 'Dọc' }, { name: 'Chéo trái' }, { name: 'Chéo phải' }],
          },
        });
      }
      if (!data.marginConfig) {
        this.$set(data, 'marginConfig', {
          title: 'Cài đặt lề',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      if (!data.marginDataConfig) {
        this.$set(data, 'marginDataConfig', {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      if (!data.paddingConfig) {
        this.$set(data, 'paddingConfig', {
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      if (!data.paddingDataConfig) {
        this.$set(data, 'paddingDataConfig', {
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      if (!data.borderConfig) {
        this.$set(data, 'borderConfig', {
          title: 'Cài đặt viền',
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
          styleConfig: {
            title: 'Kiểu viền',
            tabVal: 0,
            tabList: [
              {
                name: 'Nét liền',
                style: 'solid',
              },
              {
                name: 'Nét đứt',
                style: 'dashed',
              },
              {
                name: 'Nét chấm',
                style: 'dotted',
              },
            ],
          },
          widthConfig: {
            title: 'Độ dày viền',
            val: 1,
            min: 1,
          },
          colorConfig: {
            title: 'Màu viền',
            default: [
              {
                item: '#e5e5e5',
              },
            ],
            color: [
              {
                item: '#e5e5e5',
              },
            ],
          },
        });
      }
      if (!data.borderDataConfig) {
        this.$set(data, 'borderDataConfig', {
          title: 'Cài đặt viền',
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
          styleConfig: {
            title: 'Kiểu viền',
            tabVal: 0,
            tabList: [
              {
                name: 'Nét liền',
                style: 'solid',
              },
              {
                name: 'Nét đứt',
                style: 'dashed',
              },
              {
                name: 'Nét chấm',
                style: 'dotted',
              },
            ],
          },
          widthConfig: {
            title: 'Độ dày viền',
            val: 1,
            min: 1,
          },
          colorConfig: {
            title: 'Màu viền',
            default: [
              {
                item: '#e5e5e5',
              },
            ],
            color: [
              {
                item: '#e5e5e5',
              },
            ],
          },
        });
      }
      if (!data.shadowConfig) {
        this.$set(data, 'shadowConfig', {
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
        });
      }
      if (!data.shadowDataConfig) {
        this.$set(data, 'shadowDataConfig', {
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
        });
      }
      if (!data.bottomBgColor) {
        this.$set(data, 'bottomBgColor', {
          title: 'Nền phía dưới',
          color: [
            {
              item: '#f5f5f5',
            },
          ],
          default: [
            {
              item: '#f5f5f5',
            },
          ],
        });
      }
      if (!data.selectType) {
        this.$set(data, 'selectType', {
          title: 'Chọn thông tin',
          activeValue: 'user',
          list: [
            { activeValue: 'user', title: 'Người dùng' },
            { activeValue: 'article', title: 'Bài viết' },
            { activeValue: 'coupon', title: 'Phiếu giảm giá' },
            { activeValue: 'goods', title: 'Sản phẩm' },
          ],
        });
      }
      return data;
    },
    updateRCom() {
      if (this.setUp === 0) {
        // Content Config
        let arr = [
          {
            components: toolCom.c_set_up,
            configNme: 'setUp',
          },
          {
            components: toolCom.c_title,
            configNme: 'messageTitle',
          },
          {
            components: toolCom.c_select,
            configNme: 'selectType',
          },
        ];

        if (this.type === 'article') {
          arr = arr.concat([
            { components: toolCom.c_radio, configNme: 'articleDisplayMode' },
            { components: toolCom.c_radio, configNme: 'articleColumnStyle' },
            {
              components: toolCom.c_title,
              configNme: 'dataTitle',
            },
            { components: toolCom.c_radio, configNme: 'articleDataSource' },
          ]);

          if (this.configObj.articleDataSource && this.configObj.articleDataSource.tabVal === 0) {
            // Specific Data
            arr.push({ components: toolCom.c_article, configNme: 'articleList' });
          } else if (this.configObj.articleDataSource && this.configObj.articleDataSource.tabVal === 1) {
            // Filtered Data
            arr.push(
              { components: toolCom.c_classify, configNme: 'articleClass' },
              { components: toolCom.c_input_number, configNme: 'articleNum' },
              { components: toolCom.c_radio, configNme: 'articleSort' },
              { components: toolCom.c_radio, configNme: 'articleSortRule' },
            );
            arr.push({ components: toolCom.c_input_number, configNme: 'articleNum' });
          }
        } else if (this.type === 'coupon') {
          arr = arr.concat([
            { components: toolCom.c_radio, configNme: 'couponDisplayMode' },
            { components: toolCom.c_radio, configNme: 'couponColumnStyle' },
            {
              components: toolCom.c_title,
              configNme: 'dataTitle',
            },
            { components: toolCom.c_radio, configNme: 'couponDataSource' },
          ]);

          if (this.configObj.couponDataSource && this.configObj.couponDataSource.tabVal === 0) {
            // Manual Selection
            arr.push({ components: toolCom.c_coupon_select, configNme: 'couponList' });
          } else if (this.configObj.couponDataSource && this.configObj.couponDataSource.tabVal === 1) {
            // Filtered Data
            arr.push(
              { components: toolCom.c_select, configNme: 'couponType' },
              { components: toolCom.c_select, configNme: 'couponUserType' },
            );
            if (this.configObj.couponUserType.activeValue != 2) {
              arr.push({ components: toolCom.c_select, configNme: 'couponSendType' });
            }
            arr.push({ components: toolCom.c_radio, configNme: 'couponThreshold' });
            if (this.configObj.couponThreshold && this.configObj.couponThreshold.tabVal === 1) {
              arr.push({ components: toolCom.c_input_number, configNme: 'couponThresholdValue' });
            }
            arr.push(
              { components: toolCom.c_datetime_picker, configNme: 'couponTime' },
              { components: toolCom.c_radio, configNme: 'couponSort' },
              { components: toolCom.c_radio, configNme: 'couponSortRule' },
            );
          }

          // arr.push({ components: toolCom.c_input_number, configNme: 'couponNum' });
        } else if (this.type === 'goods') {
          arr = arr.concat([
            { components: toolCom.c_radio, configNme: 'goodsDisplayMode' },
            { components: toolCom.c_radio, configNme: 'goodsColumnStyle' },
            {
              components: toolCom.c_title,
              configNme: 'dataTitle',
            },
            { components: toolCom.c_radio, configNme: 'goodsDataSource' },
          ]);

          if (this.configObj.goodsDataSource && this.configObj.goodsDataSource.tabVal === 0) {
            // Specific Data
            arr.push({ components: toolCom.c_goods, configNme: 'goodsList' });
          } else if (this.configObj.goodsDataSource && this.configObj.goodsDataSource.tabVal === 1) {
            // Category Data
            arr.push(
              { components: toolCom.c_classify, configNme: 'goodsClass' },
              { components: toolCom.c_radio, configNme: 'goodsSort' },
              { components: toolCom.c_radio, configNme: 'goodsSortRule' },
            );
            arr.push({ components: toolCom.c_input_number, configNme: 'goodsNum' });
          }
        }

        arr.push(
          {
            components: toolCom.c_title,
            configNme: 'designTitle',
          },
          { components: toolCom.c_custom_btn, configNme: 'customBtnConfig' },
        );

        this.rCom = arr;
      } else {
        // Style Config
        this.rCom = [
          {
            components: toolCom.c_set_up,
            configNme: 'setUp',
          },
          {
            components: toolCom.c_data_style,
            configNme: 'c_data_style',
          },
          {
            components: toolCom.c_title,
            configNme: 'commonTitle',
          },
          {
            components: toolCom.c_common_style,
            configNme: 'c_common_style',
          },
          // Can add padding/margin config here if c_slider/c_margin_style is available and configured
        ];
      }
    },
    getConfig(data) {
      if (data?.name == 'custom_btn_click') {
        this.modals = true;
      }
    },
    handleSave(data) {
      this.$set(this.configObj, 'customComponents', data);
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-config {
  padding-bottom: 20px;
}
::v-deep .custom-design-dialog {
  border-radius: 0px !important;
  .el-dialog__header {
    display: none;
  }
  .el-dialog__body {
    padding: 0 !important;
    height: 100%;
    overflow: hidden;
    max-height: 100vh !important;
  }
}
</style>
