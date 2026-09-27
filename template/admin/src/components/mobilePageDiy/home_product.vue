<template>
  <div class="mobile-page paddingBox" :style="{ marginTop: slider + 'px' }">
    <div class="home_product">
      <div
        class="hd_nav"
        :style="{ justifyContent: titleConfig === 0 ? 'flex-start' : titleConfig === 1 ? 'space-around' : 'flex-end' }"
        v-if="navlist.length"
      >
        <div class="item" v-for="(item, index) in navlist" :index="index">
          <p class="title" :style="{ color: index == tabCur ? activeColor : '' }">{{ item.chiild[0].val }}</p>
          <span
            class="label"
            :style="{ background: index == tabCur ? activeColor : '', color: index == tabCur ? '#fff' : '' }"
            >{{ item.chiild[1].val }}</span
          >
        </div>
      </div>
      <div
        class="hd_nav"
        :style="{ justifyContent: titleConfig === 0 ? 'flex-start' : titleConfig === 1 ? 'space-around' : 'flex-end' }"
        v-else
      >
        <div class="item">
          <p class="title" :style="{ color: index == tabCur ? activeColor : '' }">Tiêu đề</p>
          <span
            class="label"
            :style="{ background: index == tabCur ? activeColor : '', color: index == tabCur ? '#fff' : '' }"
            >Mô tả tiêu đề</span
          >
        </div>
      </div>
      <div class="list-wrapper">
        <div class="item" v-for="(item, index) in list" :index="index">
          <div class="img-box">
            <img v-if="item.image" :src="item.image" alt="" />
            <div v-else class="empty-box"><span class="iconfont-diy icontupian"></span></div>
            <div
              v-permission="'seckill'"
              class="label"
              :style="{ background: labelColor }"
              v-if="item.activity && item.activity.type === '1'"
            >
              Flash sale
            </div>
            <div class="label" :style="{ background: labelColor }" v-if="item.activity && item.activity.type === '2'">
              Săn giảm giá
            </div>
            <div class="label" :style="{ background: labelColor }" v-if="item.activity && item.activity.type === '3'">
              Mua chung
            </div>
          </div>
          <div class="info">
            <div class="title line1" v-if="titleShow">{{ item.store_name }}</div>
            <div class="old-price" v-if="opriceShow">¥{{ item.ot_price }}</div>
            <div class="price">
              <div class="num" :style="{ color: fontColor }" v-if="priceShow"><span>￥</span>{{ item.price }}</div>
              <div
                class="label"
                :style="'border:1px solid ' + labelColor + ';color:' + labelColor"
                :class="priceShow ? '' : 'on'"
                v-if="couponShow"
              >
                Coupon
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_product',
  cname: 'Danh sách khuyến mãi',
  configName: 'c_home_product',
  icon: 'iconcuxiaoliebiao1',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'promotionList', // Tên khớp bên ngoài
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
        name: 'promotionList',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        productList: {
          title: 'Danh sách khuyến mãi',
          list: [],
        },
        titleConfig: {
          title: 'Vị trí tiêu đề',
          type: 0,
          list: [
            {
              val: 'Căn trái',
              icon: 'icondoc_left',
            },
            {
              val: 'Căn giữa',
              icon: 'icondoc_center',
            },
            {
              val: 'Căn phải',
              icon: 'icondoc_right',
            },
          ],
        },
        titleShow: {
          title: 'Hiển thị tên sản phẩm',
          val: true,
        },
        opriceShow: {
          title: 'Hiển thị giá gốc sản phẩm',
          val: true,
        },
        priceShow: {
          title: 'Hiển thị giá sản phẩm',
          val: true,
        },
        couponShow: {
          title: 'Hiển thị phiếu giảm giá',
          val: true,
        },
        tabConfig: {
          title: 'Có thể thêm tối đa 4 khối; kéo thả chấm tròn bên trái để điều chỉnh thứ tự khối',
          max: 4,
          tabCur: 0,
          list: [
            {
              chiild: [
                {
                  title: 'Tiêu đề',
                  val: 'Hàng mới ra mắt',
                  max: 4,
                  pla: 'Không bắt buộc, tối đa 4 ký tự',
                },
                {
                  title: 'Mô tả ngắn',
                  val: 'Mới ra lò',
                  max: 4,
                  pla: 'Không bắt buộc, tối đa 4 ký tự',
                },
              ],
              link: {
                title: 'Liên kết',
                activeVal: 0,
                optiops: [
                  {
                    type: 0,
                    value: 1,
                    label: 'Đề xuất nổi bật',
                  },
                  {
                    type: 1,
                    value: 2,
                    label: 'Top bán chạy',
                  },
                  {
                    type: 2,
                    value: 3,
                    label: 'Hàng mới ra mắt',
                  },
                  {
                    type: 3,
                    value: 4,
                    label: 'Sản phẩm khuyến mãi',
                  },
                ],
              },
            },
          ],
        },
        themeColor: {
          title: 'Chủ đề giao diện',
          name: 'themeColor',
          default: [
            {
              item: '#F95429',
            },
          ],
          color: [
            {
              item: '#F95429',
            },
          ],
        },
        fontColor: {
          title: 'Màu giá',
          name: 'fontColor',
          default: [
            {
              item: '#e93323',
            },
          ],
          color: [
            {
              item: '#e93323',
            },
          ],
        },
        labelColor: {
          title: 'Nhãn chương trình',
          name: 'labelColor',
          default: [
            {
              item: '#e93323',
            },
          ],
          color: [
            {
              item: '#e93323',
            },
          ],
        },
        // Lề trang
        mbConfig: {
          title: 'Lề trang',
          val: 0,
          min: 0,
        },
        numConfig: {
          val: 6,
        },
      },
      navlist: [],
      imgStyle: '',
      txtColor: '',
      slider: '',
      tabCur: 0,
      list: [],
      activeColor: '',
      fontColor: '',
      labelColor: '',
      pageData: {},
      titleConfig: 0,
      titleShow: true,
      opriceShow: true,
      priceShow: true,
      couponShow: true,
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
        this.navlist = data.tabConfig.list;
        // this.imgStyle = data.imgStyle.type
        this.activeColor = data.themeColor.color[0].item;
        this.fontColor = data.fontColor.color[0].item;
        this.labelColor = data.labelColor.color[0].item;
        this.slider = data.mbConfig.val;
        this.titleConfig = data.titleConfig.type;
        this.titleShow = data.titleShow.val;
        this.opriceShow = data.opriceShow.val;
        this.priceShow = data.priceShow.val;
        this.couponShow = data.couponShow.val;
        this.tabCur = data.tabConfig.tabCur || 0;
        let productList = data.productList.list || [];
        if (productList.length) {
          this.list = productList;
        } else {
          this.list = [
            {
              image: '',
              store_name: 'Loa Bluetooth di động Xiaomi',
              price: '59',
              ot_price: 135,
              checkCoupon: true,
              activity: { type: '2', id: 5 },
            },
            {
              image: '',
              store_name: 'Loa Bluetooth di động Xiaomi',
              price: '59',
              ot_price: 135,
              checkCoupon: true,
              activity: [],
            },
          ];
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.home_product {
  .hd_nav {
    display: flex;
    height: 65px;
    padding: 0 5px;
    .item {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      width: 25%;
      .title {
        font-size: 16px;
        color: #282828;
        width: 65px;
        text-align: center;
      }
      .label {
        width: 62px;
        height: 18px;
        line-height: 18px;
        text-align: center;
        background: transparent;
        border-radius: 8px;
        color: #999999;
        font-size: 12px;
      }
      &.active {
        .title {
          color: #ff4444;
        }
        .label {
          color: #fff;
          background: linear-gradient(270deg, rgba(255, 84, 0, 1) 0%, rgba(255, 0, 0, 1) 100%);
        }
      }
    }
  }
  .list-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    .item {
      width: 170px;
      margin-bottom: 10px;
      .img-box {
        position: relative;
        width: 100%;
        height: 173px;
        img,
        .box {
          width: 100%;
          height: 100%;
          border-radius: 10px 10px 0px 0px;
        }
        .box {
          background: #d8d8d8;
        }
        .label {
          position: absolute;
          left: 0;
          top: 0;
          width: 46px;
          height: 22px;
          border-radius: 10px 0px 10px 0px;
          color: #fff;
          font-size: 13px;
          text-align: center;
          line-height: 22px;
        }
      }
      .info {
        padding: 7px 10px;
        background: #fff;
        border-radius: 0px 0px 10px 10px;
        .title {
          font-size: 14px;
          color: #282828;
        }
        .old-price {
          color: #aaa;
          font-size: 13px;
          text-decoration: line-through;
        }
        .price {
          display: flex;
          align-items: center;
          .num {
            font-size: 16px;
            font-weight: bold;
            span {
              font-size: 12px;
            }
          }
          .label {
            width: 16px;
            height: 18px;
            margin-left: 5px;
            text-align: center;
            line-height: 18px;
            font-size: 11px;
            border-radius: 3px;
            &.on {
              margin-left: 0;
            }
          }
        }
      }
    }
  }
}
</style>
