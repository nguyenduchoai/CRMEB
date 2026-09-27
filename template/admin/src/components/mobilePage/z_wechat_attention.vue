<template>
  <div class="mobile-page">
    <div
      class="flex-box"
      :style="{
        background: `linear-gradient(90deg,${bgColor[0].item} 0%,${bgColor[1].item} 100%)`,
        marginTop: mTOP + 'px',
        marginLeft: mlConfig + 'px',
        marginRight: mlConfig + 'px',
        borderRadius: fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px',
      }"
    >
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
  </div>
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
  },
  watch: {
    pageData: {
      handler(nVal, oVal) {},
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
        prConfig: {
          title: 'Lề trái phải',
          val: 0,
          min: 0,
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
      cSlider: '',
      bgColor: '',
      confObj: {},
      pageData: {},
      edge: '',
      udEdge: '',
      themeColor: '',
      mTOP: 0,
      imgUrl: '',
      txt: '',
      mlConfig: 0,
      fillet: 0,
      filletVal: 0,
      valList: [],
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
      if (data.mbConfig) {
        this.bgColor = data.bgColor.color;
        this.themeColor = data.themeColor.color[0].item;
        this.mTOP = data.mbConfig.val;
        this.imgUrl = data.imgConfig.url;
        this.txt = data.titleConfig.value;
        this.mlConfig = data.prConfig.val;
        this.fillet = data.fillet.type;
        this.filletVal = data.fillet.val;
        this.valList = data.fillet.valList;
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

  .iconfont-diy {
    font-size: 20px;
  }
}
</style>
