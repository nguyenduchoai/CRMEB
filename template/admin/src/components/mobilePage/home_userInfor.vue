<template>
  <div
    :style="{
      background: bottomBgColor,
      marginTop: mTop + 'px',
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
  >
    <div
      v-if="styleConfig"
      class="userInfor acea-row row-between-wrapper"
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: bgRadius,
      }"
    >
      <div class="left acea-row row-middle">
        <div class="pictrue acea-row row-center-wrapper">
          <img :src="logoConfig" v-if="logoConfig" />
          <div class="empty-box" v-else>
            <img src="../../assets/images/shan.png" />
          </div>
        </div>
        <div class="text">
          <div class="name acea-row row-middle">Tên người dùng<img src="../../assets/images/vip-diy.png" /></div>
          <div class="acea-row row-middle">
            <div
              class="progress"
              :style="{
                background: toneConfig ? progressBgColor : '#eee',
              }"
            >
              <div
                class="bgReds"
                :style="{
                  background: toneConfig
                    ? `linear-gradient(90deg,${progressLeft} 0%,${progressRight} 100%)`
                    : themeColor,
                }"
              ></div>
            </div>
            <div class="percent">3000/1000000</div>
          </div>
          <!--<div class="phone"><span class="iconfont iconshouji"></span>13000000000</div>-->
        </div>
      </div>
      <div class="right acea-row row-bottom">
        <div class="item" v-if="checkType.slice(0, 3).indexOf(1) != -1">
          <div class="num">20</div>
          <div>Điểm thưởng</div>
        </div>
        <div class="item" v-if="checkType.slice(0, 3).indexOf(2) != -1">
          <div class="num">200</div>
          <div>Số dư</div>
        </div>
        <div class="item" v-if="checkType.slice(0, 3).indexOf(0) != -1">
          <div class="num">2</div>
          <div>Phiếu giảm giá</div>
        </div>
        <div class="item" v-if="checkType.slice(0, 3).indexOf(4) != -1">
          <div class="num">80</div>
          <div>Yêu thích</div>
        </div>
        <div class="item" v-if="checkType.slice(0, 3).indexOf(5) != -1">
          <div class="num">80</div>
          <div>Xem</div>
        </div>
      </div>
    </div>
    <div
      v-else
      class="userInfor"
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: bgRadius,
      }"
    >
      <div class="acea-row row-between-wrapper">
        <div class="left acea-row row-middle">
          <div class="pictrue acea-row row-center-wrapper">
            <img :src="logoConfig" v-if="logoConfig" />
            <div class="empty-box" v-else>
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div class="text">
            <div class="name acea-row row-middle">Tên người dùng<img src="../../assets/images/vip-diy.png" /></div>
            <div class="acea-row row-middle">
              <div
                class="progress"
                :style="{
                  background: toneConfig ? progressBgColor : '#eee',
                }"
              >
                <div
                  class="bgReds"
                  :style="{
                    background: toneConfig
                      ? `linear-gradient(90deg,${progressLeft} 0%,${progressRight} 100%)`
                      : themeColor,
                  }"
                ></div>
              </div>
              <div class="percent">3000/1000000</div>
            </div>
            <!--<div class="phone"><span class="iconfont iconshouji"></span>13000000000</div>-->
          </div>
        </div>
        
      </div>
      <div class="list acea-row row-around">
        <div class="item" v-if="checkType.indexOf(1) != -1">
          <div>Điểm thưởng<span class="num">20000</span></div>
        </div>
        <div class="item" v-if="checkType.indexOf(2) != -1">
          <div>Số dư<span class="num">200</span></div>
        </div>
        <div class="item" v-if="checkType.indexOf(0) != -1">
          <div>Phiếu giảm giá<span class="num">2</span></div>
        </div>
        <div class="item" v-if="checkType.indexOf(4) != -1">
          <div>Yêu thích<span class="num">80</span></div>
        </div>
        <div class="item" v-if="checkType.indexOf(5) != -1">
          <div>Xem<span class="num">80</span></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'home_userInfor',
  cname: 'Thông tin người dùng',
  configName: 'c_userInfor',
  icon: '#iconzujian-yonghuxinxi',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'userInfor', // Tên khớp bên ngoài
  props: {
    index: {
      type: null,
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
        cname: 'Thông tin người dùng',
        name: 'userInfor',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleImg: 'Ảnh đại diện mặc định',
        titleRight: 'Thanh tiến độ',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 1,
          tabList: [
            {
              name: 'Kiểu 1',
            },
            {
              name: 'Kiểu 2',
            },
          ],
        },
        checkboxInfo: {
          title: 'Hiển thị',
          name: 'checkboxInfo',
          userType: 1,
          type: [1, 2],
          list: [
            {
              id: 1,
              name: 'Điểm thưởng',
            },
            {
              id: 2,
              name: 'Số dư',
            },
            {
              id: 4,
              name: 'Yêu thích',
            },
            {
              id: 0,
              name: 'Phiếu giảm giá',
            },
            {
              id: 5,
              name: 'Xem',
            },
          ],
        },
        logoConfig: {
          info: 'Đề xuất: kích thước ảnh 90px * 90px',
          url: '',
          type: 'code',
          delType: 1,
          name: 'Tải lên ảnh',
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
        progressColor: {
          title: 'Thanh tiến độ',
          default: [
            {
              item: '#e93323',
            },
            {
              item: '#ff8933',
            },
          ],
          color: [
            {
              item: '#e93323',
            },
            {
              item: '#ff8933',
            },
          ],
        },
        progressBgColor: {
          title: 'Nền thanh tiến độ',
          default: [
            {
              item: '#EEEEEE',
            },
          ],
          color: [
            {
              item: '#EEEEEE',
            },
          ],
        },
        moduleColor: {
          title: 'Nền thành phần',
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
        bottomBgColor: {
          title: 'Nền phía dưới',
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
          title: 'Lề trang',
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
      styleConfig: 0,
      checkType: [],
      logoConfig: '',
      progressLeft: '',
      progressRight: '',
      progressBgColor: '',
      bgColorLeft: '',
      bgColorRight: '',
      bottomBgColor: '',
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      mTop: '',
      bgRadius: 0,
      toneConfig: 0,
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
        this.checkType = data.checkboxInfo.type;
        this.logoConfig = data.logoConfig.url;
        this.toneConfig = data.toneConfig.tabVal;
        this.progressLeft = data.progressColor.color[0].item;
        this.progressRight = data.progressColor.color[1].item;
        this.progressBgColor = data.progressBgColor.color[0].item;
        this.bgColorLeft = data.moduleColor.color[0].item;
        this.bgColorRight = data.moduleColor.color[1].item;
        this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.prConfig = data.prConfig.val;
        this.mTop = data.mbConfig.val;
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
.userInfor {
  padding: 14px 10px;
  .list {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #eee;
    .item {
      font-size: 13px;
      color: #999;
      .num {
        font-size: 14px;
        font-weight: 600;
        color: #333333;
        margin-left: 4px;
      }
    }
  }
  .right {
    &.on {
      .item {
        color: #666666;
      }
    }
    .item {
      text-align: center;
      font-weight: 400;
      color: #999;
      font-size: 11px;
      margin-left: 16px;
      .num {
        font-size: 16px;
        margin-bottom: 2px;
        font-weight: 600;
        color: #333333;
      }
    }
    .iconfont {
      font-weight: 400;
      //color: #333333;
      font-size: 15px;
      margin-bottom: 2px;
    }
  }
  .left {
    .pictrue {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      margin-right: 10px;
      img {
        border-radius: 50%;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .empty-box {
        border-radius: 50%;
        background-color: #f3f9ff;
        border: 1px solid #eeeeee;
        .iconfont-diy {
          font-size: 20px;
        }
        img {
          width: 26px;
          height: 20px;
        }
      }
    }
    .text {
      font-weight: 400;
      color: #333333;
      font-size: 16px;
      .name {
        img {
          width: 38px;
          height: 16px;
          margin-left: 5px;
        }
      }
      .progress {
        overflow: hidden;
        background-color: #eeeeee;
        width: 60px;
        height: 7px;
        border-radius: 3px;
        position: relative;
        .bgReds {
          width: 80%;
          height: 100%;
          transition: width 0.6s ease;
          border-radius: 3px;
        }
      }
      .percent {
        font-size: 10px;
        margin-left: 4px;
      }
      .phone {
        font-weight: 400;
        //color: #333;
        font-size: 10px;
        margin-top: 3px;
        .iconshouji {
          margin-right: 2px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>
