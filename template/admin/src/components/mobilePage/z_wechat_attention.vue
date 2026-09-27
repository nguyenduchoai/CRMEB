<template>
  <common_wrapper :config="wrapperConfig">
    <div class="flex-box" v-if="configObj">
      <div class="left">
        <div class="img-box">
          <div class="empty-box on">
            <img :src="imgUrl" alt="" v-if="imgUrl" />
            <img src="../../assets/images/noPictrue.png" v-else />
          </div>
        </div>
        <div class="name">{{ txt }}</div>
      </div>
      <div class="right">
        <div class="btn" :style="{ borderColor: themeColor, color: themeColor }">Đã theo dõi</div>
        <div class="iconfont iconguanbi5"></div>
      </div>
    </div>
  </common_wrapper>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
export default {
  name: 'z_wechat_attention',
  cname: 'Theo dõi OA WeChat',
  configName: 'c_wechat_attention',
  icon: '#iconzujian-gongzhonghao',
  type: 2, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'follow', // Tên khớp bên ngoài
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
    wrapperConfig() {
      return {
        ...this.configObj,
      };
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
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Theo dõi OA WeChat',
        name: 'follow',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt tiêu đề',
        positionTitle: 'Cài đặt vị trí',
        pictrueTitle: 'Cài đặt ảnh',
        codeTitle: 'Mã QR theo dõi',
        titleRight: 'Nút theo dõi',
        titleCurrency: 'Kiểu chung',
        positionConfig: {
          title: 'Vị trí hiển thị',
          tabVal: 0,
          tabList: [
            {
              name: 'Trên cùng',
            },
            {
              name: 'Dưới cùng',
            },
          ],
        },
        titleConfig: {
          title: 'Tên tiêu đề',
          value: 'Tiêu đề',
          place: 'Vui lòng nhập tiêu đề',
          max: 10,
        },
        imgConfig: {
          info: 'Đề xuất: kích thước ảnh 92px * 92px',
          url: '',
          type: 'code',
          name: 'Tải lên ảnh',
        },
        codeConfig: {
          url: '',
          type: 'code',
          name: 'Tải lên mã QR',
        },
        themeColor: {
          title: 'Màu nút',
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
        bgColor: {
          title: 'Màu nền',
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
        paddingConfig: {
          title: 'Lề trong',
          isAll: false,
          val: 0,
          min: 0,
          max: 100,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        marginConfig: {
          title: 'Lề ngoài',
          isAll: false,
          val: 0,
          min: 0,
          max: 100,
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
      },
      cSlider: '',
      bgColor: '',
      confObj: {},
      pageData: {},
      themeColor: '',
      imgUrl: '',
      txt: '',
      fillet: 0,
      filletVal: 0,
      valList: [],
      bgRadius: 0,
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
      this.bgColor = data.bgColor.color;
      this.themeColor = data.themeColor.color[0].item;
      this.imgUrl = data.imgConfig.url;
      this.txt = data.titleConfig.value;
      this.fillet = data.fillet.type;
      this.filletVal = data.fillet.val;
      this.valList = data.fillet.valList;
      this.bgRadius = this.fillet
        ? this.valList[0].val +
          'px ' +
          this.valList[1].val +
          'px ' +
          this.valList[3].val +
          'px ' +
          this.valList[2].val +
          'px'
        : this.filletVal + 'px';

      if (!this.configObj.paddingConfig) {
        this.$set(this.configObj, 'paddingConfig', {
          title: 'Lề trong',
          isAll: false,
          val: 0,
          min: 0,
          max: 100,
          valList: [
            { val: 0 },
            { val: data.prConfig ? data.prConfig.val : 0 },
            { val: 0 },
            { val: data.prConfig ? data.prConfig.val : 0 },
          ],
        });
      }
      if (!this.configObj.marginConfig) {
        this.$set(this.configObj, 'marginConfig', {
          title: 'Lề ngoài',
          isAll: false,
          val: 0,
          min: 0,
          max: 100,
          valList: [{ val: data.mbConfig ? data.mbConfig.val : 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        });
      }
      for (let key in this.defaultConfig) {
        if (this.configObj[key] === undefined) {
          this.$set(this.configObj, key, this.defaultConfig[key]);
        }
      }
    },
  },
};
</script>

<style scoped lang="scss">
.flex-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 10px;
  height: 60px;

  .iconfont {
    color: #999;
    font-size: 15px;
    margin-left: 10px;
  }

  .right {
    display: flex;
    align-items: center;
    margin-left: 10px;
  }

  .left {
    display: flex;
    align-items: center;

    .img-box,
    .empty-box {
      width: 46px;
      height: 46px;
      border-radius: 50%;

      img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
      }
    }

    .name {
      margin-left: 10px;
      font-size: 15px;
      color: #333;
    }
  }

  .btn {
    width: 56px;
    height: 28px;
    border: 1px solid #e93323;
    opacity: 1;
    border-radius: 25px;
    color: #e93323;
    font-size: 12px;
    text-align: center;
    line-height: 28px;
  }

  .iconfont {
    font-size: 20px;
  }
}
</style>
