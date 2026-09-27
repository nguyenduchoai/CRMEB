<template>
  <div
    :style="{
      marginLeft: prConfig + 'px',
      marginRight: prConfig + 'px',
      marginTop: slider + 'px',
      background: bgColor,
    }"
    :class="bgStyle ? 'pageOn' : ''"
  >
    <!--Hiển thị nhiều dòng-->
    <div class="mobile-page" v-if="isOne">
      <div class="list_menu">
        <div
          class="item"
          :class="number === 1 ? 'four' : number === 2 ? 'five' : ''"
          v-for="(item, index) in vuexMenu"
          :key="index"
        >
          <div class="img-box" :class="menuStyle ? 'on' : ''">
            <img :src="item.img" alt="" v-if="item.img" />
            <div class="empty-box on" v-else><span class="iconfont-diy icontupian"></span></div>
          </div>
          <p :style="{ color: txtColor }">{{ item.info[0].value }}</p>
        </div>
      </div>
    </div>
    <!--Hiển thị một dòng-->
    <div class="mobile-page" v-else>
      <div class="home_menu">
        <div class="menu-item" v-for="(item, index) in vuexMenu" :key="index">
          <div class="img-box" :class="menuStyle ? 'on' : ''">
            <img :src="item.img" alt="" v-if="item.img" />
            <div class="empty-box on" v-else><span class="iconfont-diy icontupian"></span></div>
          </div>
          <p :style="{ color: txtColor }">{{ item.info[0].value }}</p>
        </div>
      </div>
    </div>
    <div
      class="dot"
      :class="{ 'line-dot': pointerStyle === 0, '': pointerStyle === 1 }"
      v-if="isOne && pointerStyle < 2"
    >
      <div class="dot-item" :style="{ background: `${pointerColor}` }"></div>
      <div class="dot-item"></div>
      <div class="dot-item"></div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_menu',
  cname: 'Nhóm điều hướng',
  icon: 'icondaohangzu1',
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
        name: 'menus',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        tabConfig: {
          title: 'Kiểu hiển thị',
          tabVal: 0,
          type: 1,
          tabList: [
            {
              name: 'Hiển thị một dòng',
              icon: 'icondanhang',
            },
            {
              name: 'Hiển thị nhiều dòng',
              icon: 'iconduohang',
            },
          ],
        },
        rowsNum: {
          title: 'Số dòng hiển thị',
          name: 'rowsNum',
          type: 0,
          list: [
            {
              val: '2 dòng',
              icon: 'icon2hang',
            },
            {
              val: '3 dòng',
              icon: 'icon3hang',
            },
            {
              val: '4 dòng',
              icon: 'icon4hang',
            },
          ],
        },
        menuStyle: {
          title: 'Kiểu biểu tượng',
          name: 'menuStyle',
          type: 0,
          list: [
            {
              val: 'Hình vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Hình tròn',
              icon: 'icondayuanjiao',
            },
          ],
        },
        number: {
          title: 'Số mục hiển thị',
          name: 'number',
          type: 0,
          list: [
            {
              val: '3 mục',
              icon: 'icon3ge',
            },
            {
              val: '4 mục',
              icon: 'icon4ge1',
            },
            {
              val: '5 mục',
              icon: 'icon5ge1',
            },
          ],
        },
        pointerStyle: {
          title: 'Kiểu chỉ báo',
          name: 'pointerStyle',
          type: 0,
          list: [
            {
              val: 'Thanh dài',
              icon: 'iconSquarepoint',
            },
            {
              val: 'Hình tròn',
              icon: 'iconDot',
            },
            {
              val: 'Không có chỉ báo',
              icon: 'iconjinyong',
            },
          ],
        },
        bgStyle: {
          title: 'Kiểu nền',
          name: 'bgStyle',
          type: 0,
          list: [
            {
              val: 'Góc vuông',
              icon: 'iconPic_square',
            },
            {
              val: 'Bo góc',
              icon: 'iconPic_fillet',
            },
          ],
        },
        prConfig: {
          title: 'Lề nền',
          val: 0,
          min: 0,
        },
        menuConfig: {
          title: 'Có thể thêm tối đa 1 ảnh, kích thước đề xuất 90 * 90px',
          maxList: 100,
          list: [
            {
              img: '',
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Gợi ý hôm nay',
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
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Top bán chạy',
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
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Hàng mới ra mắt',
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
              info: [
                {
                  title: 'Tiêu đề',
                  value: 'Sản phẩm khuyến mãi',
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
        pointerColor: {
          title: 'Màu chỉ báo',
          name: 'pointerColor',
          default: [
            {
              item: '#f44',
            },
          ],
          color: [
            {
              item: '#f44',
            },
          ],
        },
        bgColor: {
          title: 'Màu nền',
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
        titleColor: {
          title: 'Màu chữ',
          name: 'themeColor',
          default: [
            {
              item: '#333333',
            },
          ],
          color: [
            {
              item: '#333333',
            },
          ],
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
      },
      vuexMenu: '',
      txtColor: '',
      boxStyle: '',
      slider: '',
      bgColor: '',
      menuStyle: 0,
      isOne: 0,
      number: 0,
      rowsNum: 0,
      pointerStyle: 0,
      pointerColor: '',
      pageData: {},
      bgStyle: 0,
      prConfig: 0,
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
        this.txtColor = data.titleColor.color[0].item;
        this.menuStyle = data.menuStyle.type;
        this.pointerStyle = data.pointerStyle.type;
        this.pointerColor = data.pointerColor.color[0].item;
        // this.boxStyle = data.rowStyle.type;
        this.slider = data.mbConfig.val;
        this.bgStyle = data.bgStyle.type;
        this.prConfig = data.prConfig.val;
        this.bgColor = data.bgColor.color[0].item;
        this.isOne = data.tabConfig.tabVal;
        let rowsNum = data.rowsNum.type;
        let number = data.number.type;
        let list = this.objToArr(data.menuConfig.list);
        this.number = number;
        this.rowsNum = rowsNum;
        let vuexMenu = [];
        if (rowsNum === 0) {
          if (number === 0) {
            vuexMenu = list.splice(0, 6);
          } else if (number === 1) {
            vuexMenu = list.splice(0, 8);
          } else {
            vuexMenu = list.splice(0, 10);
          }
        } else if (rowsNum === 1) {
          if (number === 0) {
            vuexMenu = list.splice(0, 9);
          } else if (number === 1) {
            vuexMenu = list.splice(0, 12);
          } else {
            vuexMenu = list.splice(0, 15);
          }
        } else {
          if (number === 0) {
            vuexMenu = list.splice(0, 12);
          } else if (number === 1) {
            vuexMenu = list.splice(0, 16);
          } else {
            vuexMenu = list.splice(0, 20);
          }
        }
        this.vuexMenu = vuexMenu;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.pageOn {
  border-radius: 10px !important;
}
.list_menu {
  padding: 0 12px 12px;
  display: flex;
  flex-wrap: wrap;
  .item {
    margin-top: 12px;
    font-size: 11px;
    color: #282828;
    text-align: center;
    width: 33.3333%;
    &.four {
      width: 25%;
    }
    &.five {
      width: 20%;
    }
    .img-box {
      width: 50px;
      height: 50px;
      margin: 0 auto 5px auto;
      &.on {
        border-radius: 50%;
        img,
        .empty-box {
          border-radius: 50%;
        }
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
    margin-right: 30px;
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
    &:nth-child(5n) {
      margin-right: 0;
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
    width: 5px;
    height: 5px;
    background: #aaaaaa;
    border-radius: 50%;
    margin: 0 3px;
  }
  &.line-dot {
    .dot-item {
      width: 8px;
      height: 2px;
      background: #aaaaaa;
      margin: 0 3px;
    }
  }
}
</style>
