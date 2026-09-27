<template>
  <div class="mobile-config pro">
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
    <el-dialog :visible.sync="modals" title="Thiết kế thành phần" width="60%">
      <!-- Placeholder for custom content -->
      <div>Đây là nội dung thành phần thiết kế</div>
    </el-dialog>
  </div>
</template>

<script>
import toolCom from '@/components/mobileConfigRight/index.js';
import { mapState, mapMutations, mapActions } from 'vuex';
import rightBtn from '@/components/rightBtn/index.vue';
export default {
  name: 'c_home_menu',
  cname: 'Nhóm điều hướng',
  componentsName: 'home_menu',
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
  components: {
    ...toolCom,
    rightBtn,
  },
  data() {
    return {
      configObj: {},
      rCom: [
        {
          components: toolCom.c_card_select,
          configNme: 'menuStyleConfig',
        },
        {
          components: toolCom.c_set_up,
          configNme: 'setUp',
        },
      ],
      rComContent: [
        {
          components: toolCom.c_title,
          configNme: 'titleLeft',
        },
        {
          components: toolCom.c_radio,
          configNme: 'navDisplayStyle',
        },
        {
          components: toolCom.c_header_switch,
          configNme: 'headerConfig',
        },
        {
          components: toolCom.c_text_config,
          configNme: 'leftTopText',
        },
        {
          components: toolCom.c_text_config,
          configNme: 'rightTopText',
        },
      ],
      oneContent: [
        {
          components: toolCom.c_radio,
          configNme: 'rowsNum',
        },
      ],
      twoContent: [
        {
          components: toolCom.c_title,
          configNme: 'titleContent',
        },
        {
          components: toolCom.c_menu_list,
          configNme: 'menuConfig',
        },
      ],
      oneStyle: [
        {
          components: toolCom.c_title,
          configNme: 'titleRight',
        },
        {
          components: toolCom.c_fillet,
          configNme: 'filletImg',
        },
      ],
      iconStyle: [
        {
          components: toolCom.c_icon_style,
          configNme: 'iconStyleConfig',
        },
      ],
      gridItemStyleConfig: [
        {
          components: toolCom.c_grid_item_style,
          configNme: 'gridItemStyle',
        },
      ],
      twoStyle: [
        {
          components: toolCom.c_title,
          configNme: 'titlePointer',
        },
        {
          components: toolCom.c_radio,
          configNme: 'toneConfig',
        },
      ],
      threeStyle: [
        {
          components: toolCom.c_bg_color,
          configNme: 'pointerColor',
        },
        {
          components: toolCom.c_bg_color,
          configNme: 'pointerBgColor',
        },
      ],
      fourStyle: [
        {
          components: toolCom.c_common_style,
          configNme: 'commonStyle',
        },
      ],
      type: 0, //Chỉ mục kiểu hiển thị
      setUp: 0, //0: nội dung; 1: kiểu
      type2: 0, //Chỉ mục kiểu điều hướng
      type3: 0, //Chỉ mục tông màu
      headerEnable: false, //Trạng thái bật/tắt phần đầu
      modals: false,
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
    'configObj.headerConfig.enable': {
      handler(nVal, oVal) {
        this.headerEnable = nVal;
        this.updateContentConfig();
      },
      deep: true,
    },
    'configObj.setUp.tabVal': {
      handler(nVal, oVal) {
        this.setUp = nVal;
        this.updateContentConfig();
      },
      deep: true,
    },
    'configObj.menuStyleConfig.tabVal': {
      handler(nVal, oVal) {
        this.type2 = nVal;
        this.updateContentConfig();
      },
      deep: true,
    },
    'configObj.showConfig.tabVal': {
      handler(nVal, oVal) {
        this.type = nVal;
        this.updateContentConfig();
      },
      deep: true,
    },
    'configObj.toneConfig.tabVal': {
      handler(nVal, oVal) {
        this.type3 = nVal;
        this.updateContentConfig();
      },
      deep: true,
    },
    'configObj.menuConfig.listStyle': {
      handler(nVal, oVal) {
        this.updateContentConfig();
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
    getConfig(data) {
      if (data.name && data.name == 'custom_btn_click') {
        // Handle custom button click
        // Since there's no specific API provided for "custom component operations",
        // we can trigger a placeholder action or emit an event.
        // The user mentioned "click open popup, perform custom component operations".
        // I'll set modals to true to open a dialog.
        this.modals = true;
      }
    },
    patchConfig(data) {
      if (!data) return data;
      if (!data.paddingConfig) {
        this.$set(data, 'paddingConfig', {
          isAll: false,
          title: 'Lề trong',
          val: 0,
          min: 0,
          max: 100,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
        if (data.topConfig) data.paddingConfig.valList[0].val = data.topConfig.val;
        if (data.prConfig) {
          data.paddingConfig.valList[1].val = data.prConfig.val;
          data.paddingConfig.valList[3].val = data.prConfig.val;
        }
        if (data.bottomConfig) data.paddingConfig.valList[2].val = data.bottomConfig.val;
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
        if (data.mbConfig) data.marginConfig.valList[0].val = data.mbConfig.val;
      }
      if (!data.customBtnConfig) {
        this.$set(data, 'customBtnConfig', {
          title: 'Thiết kế thành phần',
        });
      }
      if (!data.fillet) {
        this.$set(data, 'fillet', {
          title: 'Cài đặt bo góc',
          type: 0,
          val: 0,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      if (!data.bgColor) {
        this.$set(data, 'bgColor', {
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
        });
      }
      if (!data.headerStyle) {
        this.$set(data, 'headerStyle', {
          title: 'Kiểu phần đầu',
          fontSize: 16,
          rightFontSize: 12,
          leftColor: '#333',
          rightColor: '#999',
          leftWeight: 'normal',
          rightWeight: 'normal',
          topPadding: 10,
          bottomPadding: 10,
          leftRightPadding: 12,
        });
      }
      if (data.headerStyle && !data.headerStyle.rightFontSize) {
        this.$set(data.headerStyle, 'rightFontSize', 12);
      }
      return data;
    },

    // Phương thức tạo cấu hình thống nhất
    generateContentConfig() {
      let config = [
        {
          components: toolCom.c_title,
          configNme: 'titleLeft',
        },
        {
          components: toolCom.c_radio,
          configNme: 'navDisplayStyle',
        },

        {
          components: toolCom.c_radio,
          configNme: 'showConfig',
        },
        {
          components: toolCom.c_header_switch,
          configNme: 'headerConfig',
        },
      ];

      // Khi bật phần đầu thì hiển thị cấu hình chữ phần đầu
      if (this.headerEnable) {
        config.push(
          {
            components: toolCom.c_text_config,
            configNme: 'leftTopText',
          },
          {
            components: toolCom.c_text_config,
            configNme: 'rightTopText',
          },
        );
      }

      // Kiểu danh sách không hiển thị số lượng mỗi hàng và kiểu lưới
      if (this.type2 !== 2) {
        // Hiển thị dạng xếp hàng thì hiện số lượng mỗi hàng
        if (this.type2 === 0) {
          config.splice(3, 0, {
            components: toolCom.c_radio,
            configNme: 'number',
          });
        }
        // Hiển thị dạng lưới thì hiện kiểu lưới
        if (this.type2 === 1) {
          config.splice(3, 0, {
            components: toolCom.c_radio,
            configNme: 'gridStyle',
          });
        }
      }

      // config.push({
      //   components: toolCom.c_radio,
      //   configNme: 'showConfig',
      // });

      return config;
    },

    // Tạo cấu hình cài đặt kiểu
    generateStyleConfig() {
      let config = [];

      // Khi bật phần đầu thì hiển thị cấu hình kiểu phần đầu
      if (this.headerEnable) {
        config.push({
          components: toolCom.c_header_style,
          configNme: 'headerStyle',
        });
      }

      // Khi hiển thị dạng lưới thì hiện cấu hình kiểu ô lưới
      if (this.type2 === 1) {
        config = config.concat(this.gridItemStyleConfig);
      }

      return config;
    },

    // Cập nhật cấu hình nội dung
    updateContentConfig() {
      var arr = [
        {
          components: toolCom.c_card_select,
          configNme: 'menuStyleConfig',
        },
        {
          components: toolCom.c_set_up,
          configNme: 'setUp',
        },
      ];

      if (this.setUp == 0) {
        let contentConfig = this.generateContentConfig();
        let rCom = arr.concat(contentConfig);

        if (this.type == 0) {
          this.rCom = rCom.concat(this.twoContent);
        } else {
          let rCom2 = rCom.concat(this.oneContent);
          this.rCom = rCom2.concat(this.twoContent);
        }
      } else {
        // Cài đặt kiểu
        let styleConfig = this.generateStyleConfig();
        let listStyle = this.configObj.menuConfig ? this.configObj.menuConfig.listStyle : 0;
        let middleStyle = [];
        if (listStyle === 1) {
          middleStyle = this.iconStyle;
        }
        middleStyle = this.oneStyle;

        let base = arr.concat(styleConfig).concat(middleStyle);

        if (this.type == 0) {
          this.rCom = base.concat(this.fourStyle);
        } else {
          if (this.type3 == 0) {
            this.rCom = base.concat(this.twoStyle).concat(this.fourStyle);
          } else {
            this.rCom = base.concat(this.twoStyle).concat(this.threeStyle).concat(this.fourStyle);
          }
        }
      }
    },
  },
};
</script>

<style scoped></style>
