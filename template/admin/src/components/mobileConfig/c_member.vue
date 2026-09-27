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
    <rightBtn :activeIndex="activeIndex" :configObj="configObj"></rightBtn>
  </div>
</template>

<script>
import toolCom from '@/components/mobileConfigRight/index.js';
import rightBtn from '@/components/rightBtn/index.vue';
import { mapState, mapMutations, mapActions } from 'vuex';
export default {
  name: 'c_member',
  componentsName: 'home_member',
  components: {
    ...toolCom,
    rightBtn,
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
      rCom: [
        {
          components: toolCom.c_card_select,
          configNme: 'styleConfig',
        },
        {
          components: toolCom.c_set_up,
          configNme: 'setUp',
        },
      ],
      setUp: 0,
    };
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
    'configObj.styleConfig.tabVal': {
      handler(nVal, oVal) {
        if (nVal == 3 || nVal == 4) {
          if (this.configObj.nameColor.color[0].item == '#fff') this.configObj.nameColor.color[0].item = '#333';
          if (this.configObj.numColor.color[0].item == '#fff') this.configObj.numColor.color[0].item = '#333';
          // Màu nền cũng đổi thành 333
          if (this.configObj.compo.img - box - girdnentBgConfig.colorConfig.color[0].item == '#E93323')
            this.configObj.componentBgConfig.colorConfig.color[0].item = '#333';
          if (this.configObj.componentBgConfig.colorConfig.color[1].item == '#E93323')
            this.configObj.componentBgConfig.colorConfig.color[1].item = '#333';
          if (this.configObj.dataNumColor.color[0].item == '#fff') this.configObj.dataNumColor.color[0].item = '#333';
          if (this.configObj.dataTitleColor.color[0].item == '#fff')
            this.configObj.dataTitleColor.color[0].item = '#333';
        } else {
          if (this.configObj.dataNumColor.color[0].item == '#333') this.configObj.dataNumColor.color[0].item = '#fff';
          if (this.configObj.dataTitleColor.color[0].item == '#333')
            this.configObj.dataTitleColor.color[0].item = '#fff';
          if (this.configObj.nameColor.color[0].item == '#333') this.configObj.nameColor.color[0].item = '#fff';
          if (this.configObj.numColor.color[0].item == '#333') this.configObj.numColor.color[0].item = '#fff';
          if (this.configObj.componentBgConfig.colorConfig.color[0].item == '#333')
            this.configObj.componentBgConfig.colorConfig.color[0].item = '#E93323';
          if (this.configObj.componentBgConfig.colorConfig.color[1].item == '#333')
            this.configObj.componentBgConfig.colorConfig.color[1].item = '#E93323';
        }
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.memberStyleConfig.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.assetMode.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.menuConfig.listStyle': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.shortcutConfig.listStyle': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.assetMode.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.ms2TitleType.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.ms3BgMode.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
    'configObj.ms4BgMode.tabVal': {
      handler(nVal, oVal) {
        this.updateRCom();
      },
      deep: true,
    },
  },
  mounted() {
    this.$nextTick(() => {
      let value = JSON.parse(JSON.stringify(this.$store.state.mobildConfig.defaultArray[this.num]));
      this.configObj = value;
      this.configObj = this.patchConfig(this.configObj);
    });
  },
  methods: {
    updateRCom() {
      let arr = [
        {
          components: toolCom.c_card_select,
          configNme: 'styleConfig',
        },
        {
          components: toolCom.c_set_up,
          configNme: 'setUp',
        },
      ];
      if (this.setUp == 0) {
        let tempArr = [
          {
            components: toolCom.c_title,
            configNme: 'titleLeft',
          },
          {
            components: toolCom.c_radio,
            configNme: 'userInfoConfig',
          },
          {
            components: toolCom.c_radio,
            configNme: 'memberStyleConfig',
          },
        ];

        // Menu Config - Available for Style 1, 3, 5 (Index 0, 2, 4)
        if (
          this.configObj.styleConfig.tabVal == 0 ||
          this.configObj.styleConfig.tabVal == 2 ||
          this.configObj.styleConfig.tabVal == 4
        ) {
          tempArr.push({
            components: toolCom.c_menu_list,
            configNme: 'menuConfig',
          });
        }

        // Shortcut Config - Left Top
        // if (this.configObj.styleConfig.tabVal == 0 || this.configObj.styleConfig.tabVal == 2) {
        //   tempArr.push({
        //     components: toolCom.c_menu_list,
        //     configNme: 'shortcutConfig',
        //   });
        // }

        tempArr.push({
          components: toolCom.c_radio,
          configNme: 'assetMode',
        });
        if (this.configObj.assetMode.tabVal == 0) {
          tempArr.push({
            components: toolCom.c_radio,
            configNme: 'dataStyle',
          });
          tempArr.push({
            components: toolCom.c_checkbox,
            configNme: 'checkboxInfo',
          });
        } else {
          tempArr.push({
            components: toolCom.c_menu_list,
            configNme: 'assetConfig',
          });
        }
        // Member Style 1 (Index 0)
        if (this.configObj.memberStyleConfig.tabVal == 0) {
          tempArr.push({
            components: toolCom.c_menu_list,
            configNme: 'memberConfig',
          });
        }
        // Member Style 2 (Index 1)
        if (this.configObj.memberStyleConfig.tabVal == 1) {
          tempArr.push({ components: toolCom.c_title, configNme: 'infoStyleText' });
          tempArr.push({ components: toolCom.c_radio, configNme: 'ms2TitleType' });
          if (this.configObj.ms2TitleType.tabVal == 0) {
            tempArr.push({ components: toolCom.c_input_item, configNme: 'ms2TitleText' });
          } else {
            tempArr.push({ components: toolCom.c_upload_img, configNme: 'ms2TitleImage' });
          }
          tempArr.push({ components: toolCom.c_input_item, configNme: 'ms2IntroText' });
          tempArr.push({ components: toolCom.c_menu_list, configNme: 'ms2RightsList' });
          tempArr.push({ components: toolCom.c_upload_img, configNme: 'ms2ExplainIcons' });
          tempArr.push({ components: toolCom.c_input_item, configNme: 'ms2ExplainText' });
          // tempArr.push({ components: toolCom.c_bg_color, configNme: 'ms2ExplainColor' });
          tempArr.push({ components: toolCom.c_input_item, configNme: 'ms2ButtonText' });
          tempArr.push({ components: toolCom.c_input_item, configNme: 'ms2ButtonLink' });
          // tempArr.push({ components: toolCom.c_bg_color, configNme: 'ms2ButtonColor' });
          // tempArr.push({ components: toolCom.c_bg_color, configNme: 'ms2ButtonBgColor' });
        }
        // Member Style 3 (Index 2)
        if (this.configObj.memberStyleConfig.tabVal == 2) {
          tempArr.push(
            {
              components: toolCom.c_title,
              configNme: 'infoStyleText',
            },
            { components: toolCom.c_input_item, configNme: 'ms3TitleText' },
            { components: toolCom.c_input_item, configNme: 'ms3ButtonText' },
          );
        }
        // Info Style 4 (Index 3)
        if (this.configObj.styleConfig.tabVal == 3) {
          tempArr.push({
            components: toolCom.c_menu_list,
            configNme: 'rightEntryConfig',
          });
        }
        this.rCom = arr.concat(tempArr);
      } else {
        // Style Settings
        let styleArr = [
          {
            components: toolCom.c_title,
            configNme: 'infoStyleText',
          },
          {
            components: toolCom.c_bg_color,
            configNme: 'nameColor',
          },
          {
            components: toolCom.c_slider,
            configNme: 'nameSize',
          },
          {
            components: toolCom.c_bg_color,
            configNme: 'numColor',
          },
          {
            components: toolCom.c_slider,
            configNme: 'numSize',
          },

          {
            components: toolCom.c_title,
            configNme: 'iconStyleText',
          },
          ...(this.configObj.assetMode.tabVal === 0
            ? [
                {
                  components: toolCom.c_bg_color,
                  configNme: 'dataTitleColor',
                },
                {
                  components: toolCom.c_bg_color,
                  configNme: 'dataNumColor',
                },
              ]
            : [
                {
                  components: toolCom.c_bg_color,
                  configNme: 'assetIconColor',
                },
                {
                  components: toolCom.c_slider,
                  configNme: 'assetIconSize',
                },
                {
                  components: toolCom.c_bg_color,
                  configNme: 'assetTextColor',
                },
                {
                  components: toolCom.c_slider,
                  configNme: 'assetTextSize',
                },
              ]),

          {
            components: toolCom.c_title,
            configNme: 'memberStyleText',
          },
          {
            components: toolCom.c_common_style,
            configNme: 'c_common_style',
          },
        ];
        const memberStyleIndex = styleArr.findIndex((item) => item.configNme === 'memberStyleText');
        if (this.configObj.memberStyleConfig.tabVal !== 2 && this.configObj.memberStyleConfig.tabVal !== 3) {
          styleArr.splice(memberStyleIndex + 1, 0, {
            components: toolCom.c_bg_color,
            configNme: 'cardBgColor',
          });
        }
        // Style 4 Module Styles
        if (this.configObj.styleConfig.tabVal == 3) {
          let assetConfigIndex = styleArr.findIndex((item) => item.configNme === 'assetConfigText');
          if (assetConfigIndex !== -1) {
            styleArr.splice(assetConfigIndex, 0, {
              components: toolCom.c_title,
              configNme: 'moduleStyleText',
            });
            styleArr.splice(assetConfigIndex + 1, 0, {
              components: toolCom.c_bg_color,
              configNme: 'moduleBgColor',
            });
            styleArr.splice(assetConfigIndex + 2, 0, {
              components: toolCom.c_bg_color,
              configNme: 'moduleTextColor',
            });
            styleArr.splice(assetConfigIndex + 3, 0, {
              components: toolCom.c_fillet,
              configNme: 'moduleRadius',
            });
          }
          styleArr.push({
            components: toolCom.c_fillet,
            configNme: 'cardBgRadius',
          });
        }
        // Member Style 2 Styles
        if (this.configObj.memberStyleConfig.tabVal == 1) {
          let ms2StyleArr = [];
          if (this.configObj.ms2TitleType.tabVal == 0) {
            ms2StyleArr.push({
              components: toolCom.c_bg_color,
              configNme: 'ms2TitleColor',
            });
          }
          ms2StyleArr.push(
            {
              components: toolCom.c_bg_color,
              configNme: 'ms2IntroColor',
            },
            {
              components: toolCom.c_bg_color,
              configNme: 'ms2RightsColor',
            },
            {
              components: toolCom.c_bg_color,
              configNme: 'ms2ExplainColor',
            },
            {
              components: toolCom.c_bg_color,
              configNme: 'ms2ButtonBgColor',
            },
            {
              components: toolCom.c_bg_color,
              configNme: 'ms2ButtonColor',
            },
            {
              components: toolCom.c_fillet,
              configNme: 'cardBgRadius',
            },
          );
          let memberStyleIndex = styleArr.findIndex((item) => item.configNme === 'memberStyleText');
          if (memberStyleIndex !== -1) {
            styleArr.splice(memberStyleIndex + 1, 0, ...ms2StyleArr);
          }
        }
        // Member Style 3 Styles
        if (this.configObj.memberStyleConfig.tabVal == 2) {
          let memberStyleIndex = styleArr.findIndex((item) => item.configNme === 'memberStyleText');
          if (memberStyleIndex !== -1) {
            styleArr.splice(memberStyleIndex + 1, 0, {
              components: toolCom.c_radio,
              configNme: 'ms3BgMode',
            });
            if (this.configObj.ms3BgMode && this.configObj.ms3BgMode.tabVal === 0) {
              styleArr.splice(memberStyleIndex + 2, 0, {
                components: toolCom.c_bg_color,
                configNme: 'cardBgColor',
              });
            } else {
              styleArr.splice(memberStyleIndex + 2, 0, {
                components: toolCom.c_upload_img,
                configNme: 'ms3BackgroundImage',
              });
            }

            let ms3StyleArr = [
              {
                components: toolCom.c_bg_color,
                configNme: 'ms3TitleColor',
              },
              {
                components: toolCom.c_bg_color,
                configNme: 'ms3ButtonColor',
              },
              {
                components: toolCom.c_margin_style,
                configNme: 'ms3PaddingConfig',
              },
              {
                components: toolCom.c_fillet,
                configNme: 'cardBgRadius',
              },
            ];
            styleArr.splice(memberStyleIndex + 3, 0, ...ms3StyleArr);
          }
        }
        // Member Style 4 Styles
        if (this.configObj.memberStyleConfig.tabVal == 3) {
          let memberStyleIndex = styleArr.findIndex((item) => item.configNme === 'memberStyleText');
          if (memberStyleIndex !== -1) {
            styleArr.splice(memberStyleIndex + 1, 0, {
              components: toolCom.c_radio,
              configNme: 'ms4BgMode',
            });
            if (this.configObj.ms4BgMode && this.configObj.ms4BgMode.tabVal === 0) {
              styleArr.splice(memberStyleIndex + 2, 0, {
                components: toolCom.c_bg_color,
                configNme: 'cardBgColor',
              });
            } else {
              styleArr.splice(memberStyleIndex + 2, 0, {
                components: toolCom.c_upload_img,
                configNme: 'ms4BackgroundImage',
              });
            }
          }
        }
        if (
          this.configObj.styleConfig.tabVal == 0 ||
          this.configObj.styleConfig.tabVal == 2 ||
          this.configObj.styleConfig.tabVal == 4
        ) {
          let isMenuIcon = this.configObj.menuConfig && this.configObj.menuConfig.listStyle == 1;
          let isShortcutIcon = this.configObj.shortcutConfig && this.configObj.shortcutConfig.listStyle == 1;
          if (isMenuIcon || isShortcutIcon) {
            styleArr.splice(6, 0, {
              components: toolCom.c_icon_style,
              configNme: 'iconStyleConfig',
            });
          }
        }
        this.rCom = arr.concat(styleArr);
      }
    },
    patchConfig(data) {
      if (!data) return data;
      if (!data.assetIconColor) {
        this.$set(data, 'assetIconColor', {
          title: 'Màu biểu tượng',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        });
      }
      if (!data.assetIconSize) {
        this.$set(data, 'assetIconSize', {
          title: 'Kích thước biểu tượng',
          val: 24,
          min: 10,
          max: 32,
        });
      }
      if (!data.assetTextColor) {
        this.$set(data, 'assetTextColor', {
          title: 'Màu chữ',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        });
      }
      if (!data.assetTextSize) {
        this.$set(data, 'assetTextSize', {
          title: 'Cỡ chữ',
          val: 12,
          min: 10,
          max: 32,
        });
      }
      if (data.styleConfig && data.styleConfig.tabList.length < 5) {
        data.styleConfig.tabList.push({ name: 'Kiểu 5' });
      }
      if (!data.nameColor) {
        this.$set(data, 'nameColor', {
          title: 'Màu biệt danh',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        });
      }
      if (!data.nameSize) {
        this.$set(data, 'nameSize', {
          title: 'Chữ biệt danh',
          val: 16,
          min: 10,
          max: 30,
        });
      }
      if (!data.numColor) {
        this.$set(data, 'numColor', {
          title: 'ID/Số điện thoại',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        });
      }
      if (!data.numSize) {
        this.$set(data, 'numSize', {
          title: 'Cỡ chữ',
          val: 10,
          min: 10,
          max: 30,
        });
      }
      if (!data.userInfoConfig) {
        this.$set(data, 'userInfoConfig', {
          title: 'Thông tin người dùng',
          tabVal: 0,
          tabList: [{ name: 'Số điện thoại' }, { name: 'ID' }],
        });
      }
      if (!data.memberStyleConfig) {
        this.$set(data, 'memberStyleConfig', {
          title: 'Kiểu thành viên',
          tabVal: 0,
          tabList: [{ name: 'Kiểu 1' }, { name: 'Kiểu 2' }, { name: 'Kiểu 3' }, { name: 'Kiểu 4' }],
        });
      }
      if (!data.iconStyleConfig) {
        this.$set(data, 'iconStyleConfig', {
          title: 'Kiểu biểu tượng',
          name: 'iconStyleConfig',
          type: 1,
          color: {
            title: 'Màu sắc',
            default: [{ item: '#fff' }],
            color: [{ item: '#fff' }],
          },
          size: {
            title: 'Kích thước',
            val: 20,
            min: 12,
            max: 100,
          },
          padding: {
            title: 'Lề trong',
            val: 0,
            min: 0,
            max: 100,
          },
          rotate: {
            title: 'Xoay',
            val: 0,
            min: 0,
            max: 360,
          },
        });
      }
      if (!data.dataTitleColor) {
        this.$set(data, 'dataTitleColor', {
          title: 'Màu tiêu đề',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        });
      }
      if (!data.dataNumColor) {
        this.$set(data, 'dataNumColor', {
          title: 'Màu số',
          default: [{ item: '#fff' }],
          color: [{ item: '#fff' }],
        });
      }
      if (!data.zIndexConfig) {
        this.$set(data, 'zIndexConfig', {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        });
      }
      if (!data.componentBgConfig) {
        this.$set(data, 'componentBgConfig', {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#fff' }, { item: '#fff' }],
            color: [{ item: '#fff' }, { item: '#fff' }],
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
        });
      }
      if (!data.borderConfig) {
        this.$set(data, 'borderConfig', {
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
        });
      }
      if (data.borderConfig && data.borderConfig.styleConfig && !data.borderConfig.styleConfig.tabList) {
        this.$set(data.borderConfig, 'styleConfig', {
          title: 'Kiểu viền',
          tabVal: 0,
          tabList: [
            { name: 'Nét liền', style: 'solid' },
            { name: 'Nét đứt', style: 'dashed' },
            { name: 'Nét chấm', style: 'dotted' },
          ],
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
      if (!data.menuConfig) {
        this.$set(data, 'menuConfig', {
          title: 'Cài đặt nội dung thao tác',
          listStyleName: 'Kiểu hiển thị',
          bnt: 'Thêm',
          type: 1,
          listStyle: 0,
          maxList: 2,
          list: [
            {
              img: '',
              type: 0,
              show: true,
              icon: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Tiêu đề',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
            {
              img: '',
              type: 0,
              show: true,
              icon: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Tiêu đề',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
          ],
        });
      }

      if (!data.assetMode) {
        this.$set(data, 'assetMode', {
          title: 'Chế độ hiển thị',
          tabVal: 0,
          tabList: [{ name: 'Hiển thị dữ liệu' }, { name: 'Hiển thị ảnh kèm chữ' }],
        });
      }
      if (!data.dataStyle) {
        this.$set(data, 'dataStyle', {
          title: 'Bố cục dữ liệu',
          tabVal: 0,
          tabList: [{ name: 'Số - chữ (dọc)' }, { name: 'Chữ - số (ngang)' }, { name: 'Chữ - số (dọc)' }],
        });
      }
      if (!data.checkboxInfo) {
        this.$set(data, 'checkboxInfo', {
          title: 'Chọn dữ liệu',
          name: 'checkboxInfo',
          maxList: 5,
          type: [1, 2, 3],
          list: [
            { id: 1, name: 'Số dư' },
            { id: 2, name: 'Điểm thưởng' },
            { id: 3, name: 'Phiếu giảm giá' },
            { id: 4, name: 'Yêu thích' },
            { id: 5, name: 'Lịch sử xem' },
          ],
        });
      }
      if (!data.ms3BgMode) {
        this.$set(data, 'ms3BgMode', {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [
            { name: 'Màu nền', val: 0 },
            { name: 'Ảnh nền', val: 1 },
          ],
        });
      }
      if (!data.ms3BgColor) {
        this.$set(data, 'ms3BgColor', {
          title: 'Màu nền',
          default: [{ item: '#fff' }, { item: '#fff' }],
          color: [{ item: '#fff' }, { item: '#fff' }],
        });
      }
      if (!data.ms3BackgroundImage) {
        this.$set(data, 'ms3BackgroundImage', {
          title: '',
          url: '',
        });
      }
      if (!data.ms4BgMode) {
        this.$set(data, 'ms4BgMode', {
          title: 'Cài đặt nền',
          tabVal: 0,
          tabList: [
            { name: 'Màu nền', val: 0 },
            { name: 'Ảnh nền', val: 1 },
          ],
        });
      }
      if (!data.ms4BgColor) {
        this.$set(data, 'ms4BgColor', {
          title: 'Màu nền',
          default: [{ item: '#fff' }, { item: '#fff' }],
          color: [{ item: '#fff' }, { item: '#fff' }],
        });
      }
      if (!data.ms4BackgroundImage) {
        this.$set(data, 'ms4BackgroundImage', {
          title: '',
          url: '',
        });
      }

      if (!data.assetConfig) {
        this.$set(data, 'assetConfig', {
          title: 'Lối tắt',
          listStyle: 0,
          maxList: 5,
          bnt: 'Thêm',
          list: [
            {
              img: '',
              icon: 'icon-yue',
              info: [
                { title: 'Tiêu đề', value: 'Số dư', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 4 },
                { title: 'Liên kết', value: '/pages/users/user_money/index', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
            {
              img: '',
              icon: 'icon-jifen',
              info: [
                { title: 'Tiêu đề', value: 'Điểm thưởng', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 4 },
                { title: 'Liên kết', value: '/pages/users/user_integral/index', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
            {
              img: '',
              icon: 'icon-youhuiquan',
              info: [
                { title: 'Tiêu đề', value: 'Phiếu giảm giá', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 4 },
                { title: 'Liên kết', value: '/pages/users/user_coupon/index', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
            {
              img: '',
              icon: 'icon-shoucang',
              info: [
                { title: 'Tiêu đề', value: 'Yêu thích', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 4 },
                { title: 'Liên kết', value: '/pages/users/user_goods_collection/index', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
            {
              img: '',
              icon: 'icon-zuji',
              info: [
                { title: 'Tiêu đề', value: 'Lịch sử xem', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 4 },
                { title: 'Liên kết', value: '/pages/users/user_visit/index', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
          ],
        });
      }

      if (!data.paddingConfig) {
        this.$set(data, 'paddingConfig', {
          isAll: false,
          title: 'Lề trong',
          val: 15,
          min: 0,
          max: 100,
          valList: [{ val: 15 }, { val: 15 }, { val: 15 }, { val: 15 }],
        });
      }
      if (!data.marginConfig) {
        this.$set(data, 'marginConfig', {
          isAll: false,
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      if (!data.rightEntryConfig) {
        this.$set(data, 'rightEntryConfig', {
          title: 'Lối vào bên phải',
          listStyleName: 'Kiểu hiển thị',
          bnt: 'Thêm',
          type: 1,
          listStyle: -1,
          maxList: 1,
          list: [
            {
              img: '',
              type: 0,
              show: true,
              icon: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Cửa hàng đổi điểm',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Mô tả',
                  value: 'Dùng điểm đổi quà hay',
                  tips: 'Không bắt buộc, tối đa 6 ký tự',
                  max: 6,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
          ],
        });
      }
      if (!data.leftMenuConfig) {
        this.$set(data, 'leftMenuConfig', {
          title: 'Nội dung bên trái',
          listStyleName: 'Kiểu hiển thị',
          bnt: 'Thêm',
          type: 1,
          listStyle: 1,
          maxList: 3,
          list: [
            {
              img: '',
              type: 0,
              show: true,
              icon: 'icon-yue',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Số dư',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
            {
              img: '',
              type: 0,
              show: true,
              icon: 'icon-jifen',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Điểm thưởng',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
            {
              img: '',
              type: 0,
              show: true,
              icon: 'icon-youhuiquan',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Phiếu giảm giá',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
          ],
        });
      }
      if (!data.memberConfig) {
        this.$set(data, 'memberConfig', {
          listStyleName: 'Kiểu hiển thị',
          bnt: 'Thêm',
          type: 1,
          listStyle: -1,
          maxList: 2,
          list: [
            {
              img: '',
              type: 0,
              show: true,
              icon: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Trung tâm thành viên',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Mô tả',
                  value: 'Xem quyền lợi mới',
                  tips: 'Không bắt buộc, tối đa 6 ký tự',
                  max: 6,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
            {
              img: '',
              type: 0,
              show: true,
              icon: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Cửa hàng đổi điểm',
                  tips: 'Không bắt buộc, tối đa 4 ký tự',
                  max: 4,
                },
                {
                  title: 'Mô tả',
                  value: 'Đổi phiếu siêu hời, số lượng có hạn',
                  tips: 'Không bắt buộc, tối đa 6 ký tự',
                  max: 6,
                },
                {
                  title: 'Liên kết',
                  value: '',
                  tips: 'Vui lòng nhập liên kết',
                  max: 100,
                },
              ],
            },
          ],
        });
      }

      // Member Style 2 Configs
      if (!data.ms2TitleType) {
        this.$set(data, 'ms2TitleType', {
          title: 'Loại tiêu đề',
          tabVal: 0,
          tabList: [{ name: 'Văn bản' }, { name: 'Hình ảnh' }],
        });
      }
      if (!data.ms2TitleText) {
        this.$set(data, 'ms2TitleText', {
          title: 'Chữ tiêu đề',
          value: 'Thành viên SVIP',
          max: 10,
        });
      }
      if (!data.ms2TitleColor) {
        this.$set(data, 'ms2TitleColor', {
          title: 'Màu tiêu đề',
          default: [{ item: '#8B572A' }],
          color: [{ item: '#8B572A' }],
        });
      }
      if (!data.ms2TitleImage) {
        this.$set(data, 'ms2TitleImage', {
          header: '',
          title: '',
          name: 'Ảnh tiêu đề',
          type: 'code',
          url: '',
          info: 'Kích thước đề xuất: 162px * 36px',
        });
      }
      if (!data.ms2IntroText) {
        this.$set(data, 'ms2IntroText', {
          title: 'Chữ mô tả ngắn',
          value: 'Mua sắm tại cửa hàng được giảm 2%',
          max: 20,
        });
      }
      if (!data.ms2IntroColor) {
        this.$set(data, 'ms2IntroColor', {
          title: 'Màu mô tả ngắn',
          default: [{ item: '#8B572A' }],
          color: [{ item: '#8B572A' }],
        });
      }
      if (!data.ms2RightsList) {
        this.$set(data, 'ms2RightsList', {
          title: 'Biểu tượng quyền lợi',
          listStyleName: 'Đề xuất: 40px*40px; kéo thả khối để điều chỉnh thứ tự ảnh',
          bnt: 'Thêm',
          type: 1,
          listStyle: -1,
          maxList: 2,
          list: [
            {
              img: '',
              icon: 'icon-zk',
              info: [
                { title: 'Tiêu đề', value: 'Chiết khấu mua sắm', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 6 },
                { title: 'Liên kết', value: '', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
            {
              img: '',
              icon: 'icon-mz',
              info: [
                { title: 'Tiêu đề', value: 'Huy hiệu độc quyền', tips: 'Không bắt buộc, tối đa 4 ký tự', max: 6 },
                { title: 'Liên kết', value: '', tips: 'Vui lòng nhập liên kết', max: 100 },
              ],
            },
          ],
        });
      }
      if (!data.ms2ExplainIcons) {
        this.$set(data, 'ms2ExplainIcons', {
          header: '',
          title: '',
          name: 'Ảnh chú thích',
          type: 'code',
          url: '',
          delType: 1,
          info: 'Đề xuất: 94px * 32px',
        });
      }
      if (!data.ms2ExplainText) {
        this.$set(data, 'ms2ExplainText', {
          title: 'Chữ chú thích',
          value: 'Nắm thêm nhiều mẹo lên hạng nhanh',
          max: 20,
        });
      }
      if (!data.ms2ExplainColor) {
        this.$set(data, 'ms2ExplainColor', {
          title: 'Màu chú thích',
          default: [{ item: '#8B572A' }],
          color: [{ item: '#8B572A' }],
        });
      }
      if (!data.ms2ButtonText) {
        this.$set(data, 'ms2ButtonText', {
          title: 'Chữ trên nút',
          value: 'Nhận ngay',
          max: 6,
        });
      }
      if (!data.ms2ButtonLink) {
        this.$set(data, 'ms2ButtonLink', {
          title: 'Liên kết nút',
          value: '/pages/users/user_vip/index',
          max: 100,
          type: 'link',
        });
      }
      if (!data.ms2ButtonColor) {
        this.$set(data, 'ms2ButtonColor', {
          title: 'Màu chữ',
          default: [{ item: '#5A350C' }],
          color: [{ item: '#5A350C' }],
        });
      }
      if (!data.ms2ButtonBgColor) {
        this.$set(data, 'ms2ButtonBgColor', {
          title: 'Màu nền',
          default: [{ item: '#F6D99D' }],
          color: [{ item: '#F6D99D' }],
        });
      }
      if (!data.ms2RightsColor) {
        this.$set(data, 'ms2RightsColor', {
          title: 'Chữ quyền lợi',
          default: [{ item: '#8B572A' }],
          color: [{ item: '#8B572A' }],
        });
      }

      if (!data.moduleStyleText) {
        this.$set(data, 'moduleStyleText', 'Kiểu mô-đun');
      }
      if (!data.moduleBgColor) {
        this.$set(data, 'moduleBgColor', {
          title: 'Nền mô-đun',
          default: [{ item: '#fff' }, { item: '#fff' }],
          color: [{ item: '#fff' }, { item: '#fff' }],
        });
      }
      if (!data.moduleTextColor) {
        this.$set(data, 'moduleTextColor', {
          title: 'Chữ mô-đun',
          default: [{ item: '#333' }],
          color: [{ item: '#333' }],
        });
      }
      if (!data.moduleRadius) {
        this.$set(data, 'moduleRadius', {
          title: 'Bo góc mô-đun',
          type: 0,
          list: [
            { val: 'Tất cả', icon: 'iconcaozuo-zhengti' },
            { val: 'Từng góc', icon: 'iconcaozuo-bianjiao' },
          ],
          valName: 'Giá trị bo góc',
          val: 8,
          min: 0,
          max: 100,
          valList: [{ val: 8 }, { val: 8 }, { val: 8 }, { val: 8 }],
        });
      }
      if (!data.cardBgColor) {
        this.$set(data, 'cardBgColor', {
          title: 'Màu nền thành viên',
          default: [{ item: '#fff' }, { item: '#fff' }],
          color: [{ item: '#fff' }, { item: '#fff' }],
        });
      }
      if (!data.cardBgRadius) {
        this.$set(data, 'cardBgRadius', {
          title: 'Bo góc nền thành viên',
          type: 0,
          list: [
            { val: 'Tất cả', icon: 'iconcaozuo-zhengti' },
            { val: 'Từng góc', icon: 'iconcaozuo-bianjiao' },
          ],
          valName: 'Giá trị bo góc',
          val: 10,
          min: 0,
          max: 100,
          valList: [{ val: 10 }, { val: 10 }, { val: 10 }, { val: 10 }],
        });
      }
      // Member Style 3 Configs
      if (!data.ms3TitleText) {
        this.$set(data, 'ms3TitleText', {
          title: 'Chữ chú thích',
          value: 'Đăng ký thành viên, tận hưởng thêm nhiều quyền lợi',
          max: 20,
        });
      }
      if (!data.ms3TitleColor) {
        this.$set(data, 'ms3TitleColor', {
          title: 'Màu chú thích',
          default: [{ item: '#333' }],
          color: [{ item: '#333' }],
        });
      }
      if (!data.ms3ButtonText) {
        this.$set(data, 'ms3ButtonText', {
          title: 'Chữ trên nút',
          value: 'Kích hoạt ngay',
          max: 10,
        });
      }
      if (!data.ms3ButtonColor) {
        this.$set(data, 'ms3ButtonColor', {
          title: 'Màu nút',
          default: [{ item: '#e93323' }],
          color: [{ item: '#e93323' }],
        });
      }
      if (!data.ms3PaddingConfig) {
        this.$set(data, 'ms3PaddingConfig', {
          title: 'Lề trong',
          val: 10,
          min: 0,
          max: 100,
          valList: [{ val: 10 }, { val: 10 }, { val: 10 }, { val: 10 }],
        });
      }
      if (!data.ms3BackgroundImage) {
        this.$set(data, 'ms3BackgroundImage', {
          header: '',
          title: '',
          name: 'Tải lên ảnh',
          type: 'code',
          url: '',
          info: 'Đề xuất: rộng 662px*92px',
        });
      }
      if (!data.ms4BackgroundImage) {
        this.$set(data, 'ms4BackgroundImage', {
          header: '',
          title: '',
          name: 'Tải lên ảnh',
          type: 'code',
          url: '',
          info: 'Đề xuất: 750px * 200px',
        });
      }
      return data;
    },
    // Lấy tham số thành phần (component)
    getConfig(data) {},
  },
};
</script>
