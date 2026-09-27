<template>
  <div
    class="mobile-page"
    :style="{
      background: bottomBgColor,
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
      marginTop: mbConfig + 'px',
    }"
  >
    <div
      class="signIn"
      :style="{
        background: styleConfig
          ? `linear-gradient(90deg,${bgColorRight} 0%,${bgColorLeft} 100%)`
          : `linear-gradient(90deg,${bgColorRight2} 0%,${bgColorLeft2} 100%)`,
        borderRadius: bgRadius,
      }"
    >
      <div class="signInBg acea-row row-middle row-around" v-if="styleConfig == 0">
        <div class="item">
          <img src="../../assets/images/gift4.png" />
          <div>Hôm nay</div>
        </div>
        <div class="item">
          <img src="../../assets/images/points.png" />
          <div>Thứ 3</div>
        </div>
        <div class="item">
          <img src="../../assets/images/points.png" />
          <div>Thứ 4</div>
        </div>
        <div class="item">
          <img src="../../assets/images/gift3.png" />
          <div>Thứ 5</div>
        </div>
        <div class="item">
          <img src="../../assets/images/gift2.png" />
          <div>Thứ 6</div>
        </div>
        <div class="item">
          <img src="../../assets/images/points.png" />
          <div>Thứ 7</div>
        </div>
        <div class="item gift">
          <img src="../../assets/images/gift.png" />
          <div>Chủ nhật</div>
        </div>
        <div
          class="bnt"
          :style="{
            color: toneConfig ? bntTxtColor : '#fff',
            background: toneConfig ? `linear-gradient(90deg,${bntBgColorRight} 0%,${bntBgColorLeft} 100%)` : themeColor,
          }"
        >
          Điểm danh
        </div>
      </div>
      <div class="signInBg on acea-row row-between-wrapper" v-else>
        <div class="acea-row row-middle">
          <div class="pictrue">
            <img src="../../assets/images/signInGift.png" />
          </div>
          <div>
            <div class="acea-row row-middle">
              <span class="name">Điểm danh để nhận ngay</span>
              <div
                class="points acea-row row-center-wrapper"
                :style="{
                  color: toneConfig ? labelTxtColor : colorStyle.theme,
                  background: toneConfig ? labelBgColor : colorStyle.theme,
                }"
              >
                <div
                  class="pointsBg acea-row row-center-wrapper"
                  :style="{
                    background: toneConfig ? '' : 'rgba(255,255,255,0.9)',
                  }"
                >
                  <img src="../../assets/images/points.png" />
                  <span>+20</span>
                </div>
              </div>
            </div>
            <div class="tips">Điểm danh liên tục 3 ngày, nhận thêm 15 điểm thưởng</div>
          </div>
        </div>
        <div
          class="bnt"
          :style="{
            color: toneConfig ? bntTxtColor : '#fff',
            background: toneConfig ? `linear-gradient(90deg,${bntBgColorRight} 0%,${bntBgColorLeft} 100%)` : themeColor,
          }"
        >
          Điểm danh ngay
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'sign_in',
  cname: 'Điểm danh',
  configName: 'c_sign_in',
  icon: '#iconzujian-qiandao',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'signIn', // Tên khớp bên ngoài
  props: {
    index: {
      type: null,
      default: -1,
    },
    num: {
      type: null,
    },
    colorStyle: {
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
  // mixins: [theme],
  data() {
    return {
      // Dữ liệu khởi tạo mặc định, không được sửa
      defaultConfig: {
        cname: 'Điểm danh',
        name: 'signIn',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleRight: 'Kiểu điểm danh',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
          type: 'signIn',
        },
        toneConfig: {
          title: 'Tông màu',
          tabVal: 0,
          tabList: [
            {
              name: 'Theo phong cách chủ đề',
            },
            {
              name: 'Tùy chỉnh',
            },
          ],
        },
        bntBgColor: {
          title: 'Nền nút',
          name: 'bntBgColor',
          default: [
            {
              item: '#FF7931',
            },
            {
              item: '#E93323',
            },
          ],
          color: [
            {
              item: '#FF7931',
            },
            {
              item: '#E93323',
            },
          ],
        },
        bntTxtColor: {
          title: 'Chữ trên nút',
          name: 'bntTxtColor',
          default: [
            {
              item: '#FFF',
            },
          ],
          color: [
            {
              item: '#FFF',
            },
          ],
        },
        labelBgColor: {
          title: 'Nền nhãn',
          name: 'labelBgColor',
          default: [
            {
              item: '#FCEAE9',
            },
          ],
          color: [
            {
              item: '#FCEAE9',
            },
          ],
        },
        labelTxtColor: {
          title: 'Chữ nhãn',
          name: 'labelBgColor',
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
        moduleColor: {
          title: 'Nền thành phần',
          name: 'moduleColor',
          default: [
            {
              item: '#FF7931',
            },
            {
              item: '#E93323',
            },
          ],
          color: [
            {
              item: '#FF7931',
            },
            {
              item: '#E93323',
            },
          ],
        },
        moduleColor2: {
          title: 'Nền thành phần',
          name: 'moduleColor2',
          default: [
            {
              item: '#FFF',
            },
            {
              item: '#FFF',
            },
          ],
          color: [
            {
              item: '#FFF',
            },
            {
              item: '#FFF',
            },
          ],
        },
        bottomBgColor: {
          title: 'Nền phía dưới',
          name: 'bgColor',
          default: [
            {
              item: '#f5f5f5',
            },
          ],
          color: [
            {
              item: '#f5f5f5',
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
      pageData: {},
      bottomBgColor: '',
      topConfig: '',
      bottomConfig: '',
      prConfig: 0,
      styleConfig: 0,
      toneConfig: 0,
      bntBgColorLeft: '',
      bntBgColorRight: '',
      bntTxtColor: '',
      labelBgColor: '',
      labelTxtColor: '',
      bgColorLeft: '',
      bgColorRight: '',
      bgColorLeft2: '',
      bgColorRight2: '',
      mbConfig: 0,
      bgRadius: 0,
      themeColor: '',
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
        this.styleConfig = data.styleConfig.tabVal;
        this.toneConfig = data.toneConfig.tabVal;
        this.bntBgColorLeft = data.bntBgColor.color[0].item;
        this.bntBgColorRight = data.bntBgColor.color[1].item;
        this.bntTxtColor = data.bntTxtColor.color[0].item;
        this.labelBgColor = data.labelBgColor.color[0].item;
        this.labelTxtColor = data.labelTxtColor.color[0].item;
        this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
        this.bgColorLeft = data.moduleColor.color[0].item;
        this.bgColorRight = data.moduleColor.color[1].item;
        this.bgColorLeft2 = data.moduleColor2.color[0].item;
        this.bgColorRight2 = data.moduleColor2.color[1].item;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.prConfig = data.prConfig.val;
        this.mbConfig = data.mbConfig.val;
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
.signIn {
  width: 100%;
  padding: 12px;
  background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);

  .signInBg {
    width: 100%;
    height: 70px;
    border-radius: 8px;
    background: linear-gradient(90deg, #ffe8f5 0%, #f1fbfd 100%);
    padding: 0 3px;
    .item {
      text-align: center;
      font-size: 11px;
      color: #999999;
      padding-top: 5px;
      &.gift {
        padding-bottom: 3px;
        img {
          width: 32px;
          height: 32px;
          margin-bottom: 2px;
        }
      }
      img {
        width: 24px;
        height: 24px;
        display: block;
        margin-bottom: 6px;
      }
    }
    .bnt {
      width: 44px;
      height: 24px;
      background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);
      border-radius: 12px;
      text-align: center;
      line-height: 24px;
      font-size: 12px;
      color: #fff;
    }
    &.on {
      background: #fff;
      padding: 0 10px;
      .bnt {
        width: 70px;
        height: 26px;
        background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);
        border-radius: 13px;
        font-size: 12px;
        color: #ffffff;
        text-align: center;
        line-height: 26px;
      }
      .pictrue {
        width: 44px;
        height: 44px;
        margin-right: 10px;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .name {
        color: #282828;
        font-size: 15px;
        font-weight: 600;
        line-height: 1;
      }
      .points {
        width: 40px;
        height: 16px;
        background: #fceae9;
        border-radius: 12px;
        font-size: 10px;
        color: #e93323;
        margin-left: 2px;

        .pointsBg {
          width: 100%;
          height: 100%;
        }

        img {
          width: 14px;
          height: 14px;
          display: block;
        }
      }
      .tips {
        color: #999999;
        font-size: 12px;
        margin-top: 6px;
      }
    }
  }
}
</style>
