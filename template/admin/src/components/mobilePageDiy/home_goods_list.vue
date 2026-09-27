<template>
  <div :style="{ padding: '0 ' + prConfig + 'px' }">
    <div
      class="mobile-page paddingBox"
      :class="bgStyle === 0 ? '' : 'pageOn'"
      :style="{ marginTop: slider + 'px', background: activeColor }"
    >
      <div class="home_product">
        <!-- Một cột -->
        <template v-if="itemStyle == 0">
          <div class="list-wrapper itemA" v-if="list.length > 0">
            <div class="item" :class="conStyle ? '' : 'itemOn'" v-for="(item, index) in list" :index="index">
              <div class="img-box">
                <img v-if="item.image" :src="item.image" alt="" />
                <div v-else class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }" v-if="index == 0">Nhãn</div>
              </div>
              <div class="info">
                <div class="hd">
                  <div class="title line2" v-if="titleShow">{{ item.store_name }}</div>
                  <div class="old-price" v-if="opriceShow">¥{{ item.ot_price }}</div>
                </div>
                <div class="price" :style="{ color: fontColor }">
                  <div class="num" v-if="priceShow">￥{{ item.price }}</div>
                  <div
                    class="label"
                    :style="'border:1px solid ' + labelColor + ';color:' + labelColor"
                    :class="priceShow ? '' : 'on'"
                    v-if="item.couponId.length && couponShow"
                  >
                    Coupon
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="list-wrapper itemA" v-else>
            <div class="item" :class="conStyle ? '' : 'itemOn'">
              <div class="img-box">
                <div class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }">Săn giảm giá</div>
              </div>
              <div class="info">
                <div class="hd">
                  <div class="title line2" v-if="titleShow">Tên sản phẩm</div>
                  <div class="old-price" v-if="opriceShow">¥99.99</div>
                </div>
                <div class="price" :style="{ color: fontColor }">
                  <div class="num" v-if="priceShow">￥66.66</div>
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
        </template>
        <!-- Hai cột -->
        <template v-if="itemStyle == 1">
          <div class="list-wrapper itemC" v-if="list.length > 0">
            <div class="item" :class="conStyle ? '' : 'itemOn'" v-for="(item, index) in list" :index="index">
              <div class="img-box">
                <img v-if="item.image" :src="item.image" alt="" />
                <div v-else class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }" v-if="index == 0">Nhãn</div>
              </div>
              <div class="info">
                <div class="hd">
                  <div class="title line1" v-if="titleShow">{{ item.store_name }}</div>
                  <div class="old-price" v-if="opriceShow">¥{{ item.ot_price }}</div>
                </div>
                <div class="price" :style="{ color: fontColor }">
                  <div class="num" v-if="priceShow">￥{{ item.price }}</div>
                  <div
                    class="label"
                    :style="'border:1px solid ' + labelColor + ';color:' + labelColor"
                    :class="priceShow ? '' : 'on'"
                    v-if="item.couponId.length && couponShow"
                  >
                    Coupon
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="list-wrapper" v-else>
            <div class="item" :class="conStyle ? '' : 'itemOn'">
              <div class="img-box">
                <div class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }">Săn giảm giá</div>
              </div>
              <div class="info">
                <div class="hd">
                  <div class="title line2" v-if="titleShow">Tên sản phẩm</div>
                  <div class="old-price" v-if="opriceShow">¥99.99</div>
                </div>
                <div class="price" :style="{ color: fontColor }">
                  <div class="num" v-if="priceShow">￥66.66</div>
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
        </template>
        <!-- Ba cột -->
        <template v-if="itemStyle == 2">
          <div class="list-wrapper itemB" v-if="list.length > 0">
            <div class="item" :class="conStyle ? '' : 'itemOn'" v-for="(item, index) in list" :index="index">
              <div class="img-box">
                <img v-if="item.image" :src="item.image" alt="" />
                <div v-else class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }" v-if="index == 0">Nhãn</div>
              </div>
              <div class="info">
                <div class="hd">
                  <div class="title line1" v-if="titleShow">{{ item.store_name }}</div>
                  <div class="old-price" v-if="opriceShow">¥{{ item.ot_price }}</div>
                </div>
                <div class="price" :style="{ color: fontColor }">
                  <div class="num" v-if="priceShow">￥{{ item.price }}</div>
                  <div
                    class="label"
                    :style="'border:1px solid ' + labelColor + ';color:' + labelColor"
                    :class="priceShow ? '' : 'on'"
                    v-if="item.couponId.length && couponShow"
                  >
                    Coupon
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="list-wrapper itemB" v-else>
            <div class="item" :class="conStyle ? '' : 'itemOn'">
              <div class="img-box">
                <div class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }">Săn giảm giá</div>
              </div>
              <div class="info">
                <div class="hd">
                  <div class="title line2" v-if="titleShow">Tên sản phẩm</div>
                  <div class="old-price" v-if="opriceShow">¥99.99</div>
                </div>
                <div class="price" :style="{ color: fontColor }">
                  <div class="num" v-if="priceShow">￥66.66</div>
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
        </template>
        <!-- Ảnh lớn -->
        <template v-if="itemStyle == 3">
          <div class="listBig" v-if="list.length > 0">
            <div class="itemBig" :class="conStyle ? '' : 'itemOn'" v-for="(item, index) in list" :key="index">
              <div class="img-box">
                <img v-if="item.recommend_image" :src="item.recommend_image" alt="" />
                <img v-else-if="item.image" :src="item.image" alt="" />
                <div v-else class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }" v-if="index == 0">Nhãn</div>
              </div>
              <div class="name line2">
                <span
                  class="coupon"
                  :style="'border:1px solid ' + labelColor + ';color:' + labelColor"
                  v-if="item.couponId.length && couponShow"
                  >Coupon</span
                ><span v-if="titleShow">{{ item.store_name }}</span>
              </div>
              <div class="price" :style="{ color: fontColor }">
                <span v-if="priceShow"
                  >￥<span class="num">{{ item.price }}</span></span
                ><span class="old-price" v-if="opriceShow">¥{{ item.ot_price }}</span>
              </div>
            </div>
          </div>
          <div class="listBig" v-else>
            <div class="itemBig" :class="conStyle ? '' : 'itemOn'">
              <div class="img-box">
                <div class="empty-box"><span class="iconfont-diy icontupian"></span></div>
                <div class="label" :style="{ background: labelColor }">Săn giảm giá</div>
              </div>
              <div class="name line2">
                <span
                  :style="'border:1px solid ' + labelColor + ';color:' + labelColor"
                  class="coupon"
                  v-if="couponShow"
                  >Coupon</span
                ><span v-if="titleShow">Tên sản phẩm</span>
              </div>
              <div class="price" :style="{ color: fontColor }">
                <span v-if="priceShow">￥<span class="num">66.66</span></span
                ><span class="old-price" v-if="opriceShow">¥99.99</span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'home_goods_list',
  cname: 'Danh sách sản phẩm',
  configName: 'c_home_goods_list',
  icon: 'iconshangpinliebiao1',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'goodList', // Tên khớp bên ngoài
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
        name: 'goodList',
        timestamp: this.num,
        setUp: {
          tabVal: 0,
        },
        tabConfig: {
          title: 'Chọn mẫu',
          tabVal: 0,
          type: 1,
          tabList: [
            {
              name: 'Chọn tự động',
              icon: 'iconzidongxuanze',
            },
            {
              name: 'Chọn thủ công',
              icon: 'iconshoudongxuanze',
            },
          ],
        },
        titleShow: {
          title: 'Hiển thị tên',
          val: true,
        },
        opriceShow: {
          title: 'Hiển thị giá gốc',
          val: true,
        },
        priceShow: {
          title: 'Hiển thị giá',
          val: true,
        },
        couponShow: {
          title: 'Hiển thị phiếu giảm giá',
          val: true,
        },
        selectConfig: {
          title: 'Danh mục sản phẩm',
          activeValue: [],
          list: [
            {
              value: '',
              label: '',
            },
            {
              value: '',
              label: '',
            },
          ],
        },
        goodsSort: {
          title: 'Sắp xếp sản phẩm',
          name: 'goodsSort',
          type: 0,
          list: [
            {
              val: 'Tổng hợp',
              icon: 'iconComm_whole',
            },
            {
              val: 'Lượt bán',
              icon: 'iconComm_number',
            },
            {
              val: 'Giá',
              icon: 'iconComm_Price',
            },
          ],
        },
        numConfig: {
          val: 6,
        },
        themeColor: {
          title: 'Màu nền',
          name: 'themeColor',
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
        itemStyle: {
          title: 'Kiểu hiển thị',
          name: 'itemSstyle',
          type: 0,
          list: [
            {
              val: 'Một cột',
              icon: 'iconzuoyoutuwen',
            },
            {
              val: 'Hai cột',
              icon: 'iconlianglie',
            },
            {
              val: 'Ba cột',
              icon: 'iconsanlie',
            },
            {
              val: 'Ảnh lớn',
              icon: 'icondanlie',
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
        conStyle: {
          title: 'Kiểu nội dung',
          name: 'conStyle',
          type: 1,
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
        mbConfig: {
          title: 'Khoảng cách sản phẩm',
          val: 0,
          min: 0,
        },
        productList: {
          title: 'Danh sách sản phẩm',
          list: [],
        },
        goodsList: {
          max: 20,
          list: [],
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
      itemStyle: 0,
      titleShow: true,
      opriceShow: true,
      priceShow: true,
      couponShow: true,
      prConfig: 0,
      bgStyle: 0,
      conStyle: 1,
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
        this.itemStyle = data.itemStyle.type || 0;
        this.activeColor = data.themeColor.color[0].item;
        this.fontColor = data.fontColor.color[0].item;
        this.labelColor = data.labelColor.color[0].item;
        this.slider = data.mbConfig.val;
        this.prConfig = data.prConfig.val;
        this.titleShow = data.titleShow.val;
        this.opriceShow = data.opriceShow.val;
        this.priceShow = data.priceShow.val;
        this.couponShow = data.couponShow.val;
        this.bgStyle = data.bgStyle.type;
        this.conStyle = data.conStyle.type;
        if (data.tabConfig.tabVal) {
          this.list = data.goodsList.list || [];
        } else {
          this.list = data.productList.list || [];
        }
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.itemOn {
  border-radius: 0 !important;
  img,
  .empty-box {
    border-radius: 0 !important;
  }
  .img-box {
    .label {
      border-radius: 0 0 8px 0 !important;
    }
  }
}
.pageOn {
  border-radius: 8px !important;
}
.listBig {
  width: 100%;
  padding: 10px 0 1px 0;
  .itemBig {
    width: 100%;
    margin-bottom: 15px;
    background-color: #fff;
    border-radius: 10px;
    .img-box {
      width: 100%;
      height: 160px;
      position: relative;
      border-radius: 50px;
      img {
        width: 100%;
        height: 160px;
        border-radius: 10px 10px 0 0;
        object-fit: unset;
      }
      .empty-box {
        border-radius: 8px 8px 0 0;
      }
      .label {
        position: absolute;
        top: 0;
        left: 0;
        width: 59px;
        height: 25px;
        line-height: 25px;
        text-align: center;
        color: #fff;
        font-size: 12px;
        border-radius: 8px 0 8px 0;
      }
    }
    .name {
      font-size: 15px;
      font-weight: bold;
      margin-top: 8px;
      padding: 0 10px;
    }
    .coupon {
      width: 16px;
      height: 18px;
      line-height: 18px;
      text-align: center;
      font-size: 12px;
      margin-right: 5px;
      display: inline-block;
    }
    .price {
      font-weight: bold;
      font-size: 12px;
      padding: 0 10px;
      .num {
        font-size: 18px;
        margin-right: 5px;
      }
      .old-price {
        color: #aaa !important;
        font-weight: normal;
        text-decoration: line-through;
      }
    }
  }
}
.paddingBox {
  padding-bottom: 0;
}
.home_product {
  .hd_nav {
    display: flex;
    height: 65px;
    padding: 0 5px;
    .item {
      display: flex;
      flex-direction: column;
      justify-content: center;
      width: 25%;
      .title {
        font-size: 16px;
        color: #282828;
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
    padding-top: 10px;
    .item {
      width: 48.5%;
      margin-bottom: 10px;
      background-color: #fff;
      border-radius: 10px;
      .img-box {
        position: relative;
        width: 100%;
        height: 173px;
        img,
        .box {
          width: 100%;
          height: 100%;
          border-radius: 10px 0 0 10px;
        }
        .empty-box {
          background: #f3f5f7;
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
            font-size: 15px;
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
            &.on {
              margin-left: 0;
            }
          }
        }
      }
    }
    &.itemA {
      /*background #fff*/
      .item {
        display: flex;
        width: 100%;
        .img-box {
          position: relative;
          width: 110px;
          height: 110px;
          img,
          .box,
          .empty-box {
            border-radius: 10px 0 0 10px;
          }
        }
        .info {
          display: flex;
          justify-content: space-between;
          flex-direction: column;
          flex: 1;
          margin-left: 5px;
          padding: 5px 10px;
          .title {
            height: 37px;
          }
        }
      }
    }
    &.itemB {
      justify-content: inherit;
      .item {
        width: 31.6%;
        margin-right: 8px;
        &:nth-child(3n) {
          margin-right: 0;
        }
        .img-box {
          position: relative;
          width: 100%;
          height: 110px;
          img,
          .box,
          .empty-box {
            border-radius: 10px 10px 0 0;
          }
        }
      }
    }
    &.itemC {
      .item {
        img,
        .box,
        .empty-box {
          border-radius: 10px 10px 0 0;
        }
      }
    }
  }
}
</style>
