<template>
  <common_wrapper v-if="configObj" :config="configObj">
    <div class="pictrue">
      <img
        :src="imgUrl"
        v-if="imgUrl"
        :style="{
          borderRadius: bgRadius,
        }"
      />
      <div
        class="empty-box"
        v-else
        :style="{
          borderRadius: bgRadius,
        }"
      >
        <img src="../../assets/images/shan.png" />
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'home_hotspot',
  cname: 'Vùng nóng',
  configName: 'c_hotspot',
  icon: '#iconzujian-requ',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'hotspot', // Tên khớp bên ngoài
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
        cname: 'Vùng nóng',
        name: 'hotspot',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt nội dung',
        titleRight: 'Kiểu chung',
        zIndexConfig: {
          title: 'Thứ tự lớp thành phần',
          val: 0,
          min: 0,
        },
        picStyle: {
          url: '',
          list: [],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          name: 'bottomBgColor',
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
        paddingConfig: {
          title: 'Lề trong',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
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
        marginConfig: {
          title: 'Lề ngoài',
          val: 0,
          min: 0,
          isAll: false,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        mbConfig: {
          title: 'Lề trên trang',
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
      configObj: null,
      confObj: {},
      pageData: {},
      bgRadius: 0,
      imgUrl: '',
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
        if (this.configObj[key] === undefined) {
          this.$set(this.configObj, key, JSON.parse(JSON.stringify(this.defaultConfig[key])));
        }
      }
      if (data.mbConfig) {
        this.imgUrl = data.picStyle.url;

        if (!data.paddingConfig) {
          let paddingConfig = {
            title: 'Lề trong',
            val: 0,
            min: 0,
            isAll: false,
            valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
          };
          paddingConfig.valList[0].val = data.topConfig.val;
          paddingConfig.valList[2].val = data.bottomConfig.val;
          paddingConfig.valList[1].val = data.prConfig.val;
          paddingConfig.valList[3].val = data.prConfig.val;
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
          marginConfig.valList[0].val = data.mbConfig.val;
          this.$set(this.configObj, 'marginConfig', marginConfig);
        }

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
.mobile-page {
  display: inline-block;
  width: -webkit-fill-available;
}
.pictrue {
  width: 100%;
  height: 100%;
  .empty-box {
    width: 100%;
    height: 375px;
    border-radius: 0;
    background: #f3f9ff;

    img {
      width: 65px;
      height: 50px;
    }
  }
  img {
    width: 100%;
    height: 100%;
  }
}
</style>
