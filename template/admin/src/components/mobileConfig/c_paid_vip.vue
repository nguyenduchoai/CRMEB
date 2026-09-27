<template>
  <div class="mobile-config pro">
    <div v-for="(item, key) in rCom" :key="key">
      <component
        :is="item.components.name"
        :configObj="configObj"
        ref="childData"
        :configNme="item.configNme"
        :key="key"
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
  name: 'c_paid_vip',
  cname: 'Thành viên trả phí',
  componentsName: 'home_paid_vip',
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
      setUp: 0,
      rCom: [
        {
          components: toolCom.c_set_up,
          configNme: 'setUp',
        },
      ],
      // Mục cấu hình cài đặt nội dung
      contentConfig: [
        {
          components: toolCom.c_title,
          configNme: 'titleContent',
        },
        {
          components: toolCom.c_upload_img,
          configNme: 'imgConfig',
        },
        {
          components: toolCom.c_input_item,
          configNme: 'rightBntConfig',
        },
      ],
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
    'configObj.toneConfig.tabVal': {
      handler(nVal, oVal) {
        if (this.setUp === 1) {
          // Only update if currently in style tab
          this.updateRCom();
        }
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
      if (!data.paddingConfig) {
        this.$set(data, 'paddingConfig', {
          title: 'Lề trong',
          isAll: false,
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
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          max: 100,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
        if (data.mbConfig) data.marginConfig.valList[0].val = data.mbConfig.val;
      }
      if (!data.borderConfig) {
        this.$set(data, 'borderConfig', {
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
        });
      }
      if (!data.shadowConfig) {
        this.$set(data, 'shadowConfig', {
          title: 'Cài đặt đổ bóng',
          tabVal: 0,
          tabList: [{ name: 'Ẩn' }, { name: 'Hiện' }],
          val: 0, // 0: Hide, 1: Show
          colorConfig: {
            title: 'Màu đổ bóng',
            default: [{ item: '#e5e5e5' }],
            color: [{ item: '#e5e5e5' }],
          },
        });
      }
      if (!data.componentBgConfig) {
        this.$set(data, 'componentBgConfig', {
          title: 'Nền thành phần',
          tabVal: 0,
          tabList: [{ name: 'Màu sắc' }, { name: 'Hình ảnh' }],
          colorConfig: {
            title: 'Màu nền',
            default: [{ item: '#fff' }],
            color: [{ item: '#fff' }],
          },
          imageConfig: {
            url: '',
            type: 'code',
            name: 'Ảnh nền',
          },
        });
      }
      if (!data.fillet) {
        this.$set(data, 'fillet', {
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
        });
      }
      if (!data.imgConfig) {
        this.$set(data, 'imgConfig', {
          info: 'Đề xuất: 36px * 36px',
          url: require('@/assets/images/goods_vip.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh thành viên',
        });
      }
      if (!data.rightBntConfig) {
        this.$set(data, 'rightBntConfig', {
          title: 'Nút bên phải',
          value: 'Kích hoạt ngay',
          place: 'Vui lòng nhập chữ trên nút',
          max: 6,
        });
      }
      if (!data.c_common_style) {
        this.$set(data, 'c_common_style', {
          color: {
            title: 'Màu nền',
            val: '',
            name: 'bgColor',
          },
          color2: {
            title: 'Màu đường kẻ',
            val: '',
            name: 'lineColor',
          },
          lr: {
            title: 'Lề trái phải',
            val: 0,
            min: 0,
            max: 100,
          },
          type: 0,
        });
      }
      return data;
    },
    // Cập nhật danh sách thành phần cấu hình
    updateRCom() {
      var arr = [this.rCom[0]]; // Giữ lại thành phần setUp đầu tiên

      if (this.setUp == 0) {
        // Cài đặt nội dung
        this.rCom = arr.concat(this.contentConfig);
      } else {
        // Cài đặt kiểu
        let styleArr = [
          {
            components: toolCom.c_title,
            configNme: 'titleStyle',
          },
          {
            components: toolCom.c_radio,
            configNme: 'toneConfig',
          },
        ];

        // Show color settings if Custom Tone (tabVal == 1)
        if (this.configObj.toneConfig && this.configObj.toneConfig.tabVal == 1) {
          styleArr = styleArr.concat([
            {
              components: toolCom.c_bg_color,
              configNme: 'tipsColor',
            },
            {
              components: toolCom.c_bg_color,
              configNme: 'moneyColor',
            },
            {
              components: toolCom.c_bg_color,
              configNme: 'btnColor',
            },
            {
              components: toolCom.c_slider,
              configNme: 'btnConfig',
            },
          ]);
        }

        // Common Styles
        styleArr = styleArr.concat([
          {
            components: toolCom.c_common_style,
            configNme: 'c_common_style',
          },
        ]);

        this.rCom = arr.concat(styleArr);
      }
    },
    handleSubmit(name) {
      let obj = {};
      obj.activeIndex = this.activeIndex;
      obj.data = this.configObj;
      this.add(obj);
    },
    ...mapMutations({
      add: 'mobildConfig/UPDATEARR',
    }),
  },
};
</script>

<style scoped></style>
