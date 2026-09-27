<template>
  <div
    :style="{
      background: bottomBgColor,
      marginTop: slider + 'px',
      paddingTop: topConfig + 'px',
      paddingBottom: bottomConfig + 'px',
      paddingLeft: prConfig + 'px',
      paddingRight: prConfig + 'px',
    }"
  >
    <div
      :style="{
        background: `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`,
        borderRadius: fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px',
      }"
    >
      <div class="mobile-page">
        <div class="list_menu">
          <div
            class="item"
            :class="number === 1 ? 'four' : number === 2 ? 'five' : ''"
            v-for="(item, index) in vuexMenu"
            :key="index"
            v-if="item.show"
          >
            <div class="img-box" :class="menuStyleConfig == 1 ? 'on' : ''" v-if="menuStyleConfig != 2">
              <img
                :src="item.img"
                alt=""
                v-if="item.img"
                :style="{
                  borderRadius: filletImg
                    ? valListImg[0].val +
                      'px ' +
                      valListImg[1].val +
                      'px ' +
                      valListImg[3].val +
                      'px ' +
                      valListImg[2].val +
                      'px'
                    : filletValImg + 'px',
                }"
              />
              <div
                class="empty-box on"
                :style="{
                  borderRadius: filletImg
                    ? valListImg[0].val +
                      'px ' +
                      valListImg[1].val +
                      'px ' +
                      valListImg[3].val +
                      'px ' +
                      valListImg[2].val +
                      'px'
                    : filletValImg + 'px',
                }"
                v-else
              >
                <img src="../../assets/images/shan.png" />
              </div>
            </div>
            <p v-if="menuStyleConfig != 1" :style="'color:' + textColor">{{ item.info[0].value }}</p>
          </div>
        </div>
      </div>
      <!--Hiển thị một dòng-->
      <!-- <div class="mobile-page" v-else>
		        <div class="home_menu">
		            <div class="menu-item" v-for="(item,index) in vuexMenu" :key="index">
		                <div class="img-box" :class="menuStyle?'on':''">
		                    <img :src="item.img" alt="" v-if="item.img">
		                    <div class="empty-box on" v-else> <span class="iconfont-diy icontupian"></span> </div>
		                </div>
		                <p :style="{color:txtColor}">{{item.info[0].value}}</p>
		            </div>
		        </div>
		    </div> -->
      <div class="dot" v-if="showConfig">
        <div class="dot-item" :style="{ background: toneConfig ? `${pointerColor}` : `${colorStyle.theme}` }"></div>
        <div class="dot-item" :style="{ background: toneConfig ? `${pointerBgColor}` : '' }" v-for="item in 2"></div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
// import theme from "@/mixins/theme";
export default {
  name: 'home_menu',
  cname: 'Nhóm điều hướng',
  icon: '#iconzujian-daohangzu',
  configName: 'c_home_menu',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'menus', // Tên khớp bên ngoài
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
        cname: 'Nhóm điều hướng',
        name: 'menus',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt hiển thị',
        titleContent: 'Cài đặt nội dung',
        titleRight: 'Kiểu ảnh',
        titlePointer: 'Cài đặt chỉ báo',
        titleCurrency: 'Kiểu chung',
        menuStyleConfig: {
          title: 'Kiểu điều hướng',
          tabVal: 0,
          tabList: [
            {
              name: 'Ảnh kèm chữ',
            },
            {
              name: 'Hình ảnh',
            },
            {
              name: 'Văn bản',
            },
          ],
        },
        number: {
          title: 'Hiển thị một dòng',
          tabVal: 1,
          tabList: [
            {
              name: '3 mục',
            },
            {
              name: '4 mục',
            },
            {
              name: '5 mục',
            },
          ],
        },
        showConfig: {
          title: 'Kiểu hiển thị',
          tabVal: 0,
          tabList: [
            {
              name: 'Hiển thị cố định',
            },
            {
              name: 'Vuốt theo trang',
            },
          ],
        },
        rowsNum: {
          title: 'Số dòng hiển thị',
          tabVal: 0,
          tabList: [
            {
              name: '1 dòng',
            },
            {
              name: '2 dòng',
            },
            {
              name: '3 dòng',
            },
            {
              name: '4 dòng',
            },
          ],
        },
        filletImg: {
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
        pointerBgColor: {
          title: 'Kiểu thường',
          default: [
            {
              item: '#DDDDDD',
            },
          ],
          color: [
            {
              item: '#DDDDDD',
            },
          ],
        },
        pointerColor: {
          title: 'Kiểu khi được chọn',
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
        textColor: {
          title: 'Màu chữ',
          default: [
            {
              item: '#333',
            },
          ],
          color: [
            {
              item: '#333',
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
        // Lề trang
        mbConfig: {
          title: 'Lề trên trang',
          val: 0,
          min: 0,
        },
        menuConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 90 * 90px',
          bnt: 'Thêm',
          type: 1,
          maxList: 100,
          list: [
            {
              img: '',
              show: true,
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
              show: true,
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
              show: true,
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
              show: true,
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
      vuexMenu: [],
      boxStyle: '',
      slider: '',
      bgColorLeft: '',
      bgColorRight: '',
      bottomBgColor: '',
      number: 0,
      rowsNum: 0,
      textColor: '',
      pointerColor: '',
      pointerBgColor: '',
      pageData: {},
      prConfig: 0,
      menuStyleConfig: 0,
      showConfig: 0,
      filletImg: 0,
      filletValImg: 0,
      valListImg: [],
      fillet: 0,
      filletVal: 0,
      valList: [],
      toneConfig: 0,
      topConfig: 0,
      bottomConfig: 0,
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.pageData = this.$store.state.mobildConfig.defaultArray[this.num];
      this.setConfig(this.pageData);
    });
  },
  methods: {
    // Chuyển object thành mảng
    objToArr(data) {
      let obj = Object.keys(data);
      let m = obj.map((key) => data[key]);
      return m;
    },
    setConfig(data) {
      if (!data) return;
      if (data.mbConfig) {
        this.menuStyleConfig = data.menuStyleConfig.tabVal;
        this.showConfig = data.showConfig.tabVal;
        this.filletImg = data.filletImg.type;
        this.filletValImg = data.filletImg.val;
        this.valListImg = data.filletImg.valList;
        this.fillet = data.fillet.type;
        this.filletVal = data.fillet.val;
        this.valList = data.fillet.valList;
        this.toneConfig = data.toneConfig.tabVal;
        this.pointerColor = data.pointerColor.color[0].item;
        this.pointerBgColor = data.pointerBgColor.color[0].item;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.textColor = data.textColor.color[0].item;
        this.slider = data.mbConfig.val;
        this.prConfig = data.prConfig.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.bgColorLeft = data.bgColor.color[0].item;
        this.bgColorRight = data.bgColor.color[1].item;
        let rowsNum = data.rowsNum.tabVal;
        let number = data.number.tabVal;
        let lists = this.objToArr(data.menuConfig.list);
        let list = [];
        lists.forEach((item) => {
          if (item.show) {
            list.push(item);
          }
        });
        this.number = number;
        this.rowsNum = rowsNum;
        if (this.showConfig) {
          this.vuexMenu = list.splice(0, (rowsNum + 1) * (number + 3));
        } else {
          this.vuexMenu = lists;
        }
      }
    },
  },
};
</script>

<style scoped lang="scss">
.list_menu {
  padding: 0 12px 12px;
  display: flex;
  flex-wrap: wrap;

  .item {
    margin-top: 12px;
    font-size: 12px;
    color: #333;
    text-align: center;
    width: 33.3333%;

    &.four {
      width: 25%;
    }

    &.five {
      width: 20%;
    }

    .img-box {
      width: 45px;
      height: 45px;
      margin: 0 auto 8px auto;

      &.on {
        margin-bottom: 0;
      }

      img {
        width: 100%;
        height: 100%;
      }
    }
  }

  .icontupian {
    font-size: 16px;
  }
}

.home_menu {
  padding: 0 12px 12px;
  display: flex;
  overflow: hidden;

  .menu-item {
    margin-top: 12px;
    font-size: 11px;
    color: #282828;
    text-align: center;
    margin-right: 27px;

    .img-box {
      width: 50px;
      height: 50px;

      &.on {
        border-radius: 50%;

        img,
        .empty-box {
          border-radius: 50%;
        }
      }
    }

    .box,
    img {
      width: 100%;
      height: 100%;
    }

    .box {
      background: #d8d8d8;
    }

    p {
      margin-top: 5px;
    }
  }

  &.on {
    .menu-item {
      margin-right: 51px;

      &:nth-child(5n) {
        margin-right: 51px;
      }

      &:nth-child(4n) {
        margin-right: 0;
      }
    }
  }

  .icontupian {
    font-size: 16px;
  }
}

.dot {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-bottom: 10px;

  &.number {
    bottom: 15px;
  }

  .num {
    width: 25px;
    height: 18px;
    line-height: 18px;
    background-color: #000;
    color: #fff;
    opacity: 0.3;
    border-radius: 8px;
    font-size: 12px;
    text-align: center;
  }

  .dot-item {
    width: 10px;
    height: 3px;
    background: #dddddd;
    border-radius: 50%;
    margin: 0 3px;
  }
}

.empty-box {
  background: #f3f9ff;

  img {
    width: 26px !important;
    height: 20px !important;
  }
}
</style>
