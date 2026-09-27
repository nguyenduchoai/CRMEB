<template>
  <common_wrapper :config="configObj">
    <div
      class="box"
      :style="{
        background: bgColor,
        borderRadius: fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px',
      }"
      v-html="richText"
    ></div>
  </common_wrapper>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_ueditor',
  cname: 'Văn bản định dạng',
  configName: 'c_ueditor_box',
  icon: '#iconzujian-fuwenben',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'richText', // Tên khớp bên ngoài
  props: {
    index: {
      type: null,
      default: -1,
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
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Văn bản định dạng',
        name: 'richText',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
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
            default: [{ item: '#e5e5e5' }],
            color: [{ item: '#e5e5e5' }],
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
        titleLeft: 'Nội dung văn bản định dạng',
        titleRight: 'Kiểu chung',
        bgColor: {
          title: 'Nền thành phần',
          name: 'bgColor',
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
        bottomBgColor: {
          title: 'Nền phía dưới',
          name: 'bgColor',
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
        richText: {
          val: '',
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
      cSlider: '',
      configObj: null,
      bgColor: '',
      confObj: {},
      pageData: {},
      richText: '',
      bottomBgColor: '',
      fillet: 0,
      filletVal: 0,
      valList: [],
      paddingConfig: {
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
      marginConfig: {
        valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      },
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
        if (data[key] == undefined) {
          this.$set(data, key, this.defaultConfig[key]);
        }
      }
      this.bgColor = data.bgColor.color[0].item;
      this.bottomBgColor = data.bottomBgColor.color[0].item;
      this.richText = data.richText.val;
      this.fillet = data.fillet.type;
      this.filletVal = data.fillet.val;
      this.valList = data.fillet.valList;
      this.paddingConfig = data.paddingConfig || {
        valList: [
          { val: data.topConfig ? data.topConfig.val : 0 },
          { val: data.lrConfig ? data.lrConfig.val : 0 },
          { val: data.bottomConfig ? data.bottomConfig.val : 0 },
          { val: data.lrConfig ? data.lrConfig.val : 0 },
        ],
      };
      this.marginConfig = data.marginConfig || {
        valList: [{ val: data.udConfig ? data.udConfig.val : 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
      };
    },
  },
};
</script>

<style scoped lang="scss">
.mobile-page {
  display: inline-block;
  width: -webkit-fill-available;
}
.mobile-page ::v-deep video {
  width: 100% !important;
}
.box {
  min-height: 42px;
  padding: 10px;
  background-color: #f5f5f5;
  word-break: break-all;
  border-radius: 12px;
  img {
    max-width: 100%;
    height: auto;
  }
}
</style>
