<template>
  <div>
    <div
      class="mobile-page paddingBox"
      :style="{
        background: bottomBgColor,
        marginTop: mTop + 'px',
        paddingTop: topConfig + 'px',
        paddingBottom: bottomConfig + 'px',
        paddingLeft: prConfig + 'px',
        paddingRight: prConfig + 'px',
      }"
    >
      <div class="home_product">
        <!-- Một cột -->
        <template v-if="styleConfig == 0">
          <div class="list-wrapper itemA">
            <div
              class="item"
              v-for="(item, index) in list"
              :index="index"
              :style="{
                borderRadius: bgRadius,
              }"
            >
              <div class="img-box">
                <img
                  class="img"
                  v-if="item.image"
                  :src="item.image"
                  alt=""
                  :style="{
                    borderRadius: imgRadius,
                  }"
                />
                <div
                  v-else
                  class="empty-box"
                  :style="{
                    borderRadius: imgRadius,
                  }"
                >
                  <img src="../../assets/images/shan.png" />
                </div>
              </div>
              <div class="info">
                <div class="hd">
                  <div
                    class="title line2"
                    v-if="checkboxInfo.indexOf(0) != -1"
                    :style="{
                      fontWeight: goodsName,
                      color: toneConfig ? goodsNameColor : '#333',
                    }"
                  >
                    {{ item.store_name || 'Dịch vụ thay màn hình máy tính bảng Huawei Honor Enjoy, thay kính ngoài, sửa bo mạch chủ' }}
                  </div>
                  <img v-if="checkboxInfo.indexOf(1) != -1" src="../../assets/images/goods01.png" />
                </div>
                <div
                  class="price acea-row row-middle"
                  :class="checkboxInfo.indexOf(3) == -1 && checkboxInfo.indexOf(4) == -1 ? 'on' : ''"
                >
                  <div
                    class="num"
                    v-if="checkboxInfo.indexOf(2) != -1"
                    :style="{
                      color: toneConfig ? goodsPriceColor : colorStyle.theme,
                    }"
                  >
                    <span>￥</span>{{ item.price ? $HandlePrice(item.price, 0) : 33
                    }}<span>{{ item.price ? $HandlePrice(item.price, 1) : '' }}</span>
                  </div>
                  <img class="img" v-if="checkboxInfo.indexOf(5) != -1" src="../../assets/images/goods02.png" />
                </div>
                <div class="bottom">
                  <span
                    class="mr8"
                    v-if="checkboxInfo.indexOf(3) != -1"
                    :style="{
                      color: toneConfig ? soldNumColor : '#999999',
                    }"
                    >Đã bán {{ item.sales || 0 }}</span
                  >
                  <span
                    v-if="checkboxInfo.indexOf(4) != -1"
                    :style="{
                      color: toneConfig ? scoreColor : '#999999',
                    }"
                    >Điểm đánh giá {{ item.star || 0 }}</span
                  >
                </div>
              </div>
              <div v-if="!cartConfig">
                <div
                  class="bnt"
                  v-if="bntStyleConfig == 0"
                  :style="{
                    background: toneCartConfig ? bntBgColor : themeColor,
                  }"
                >
                  Mua
                </div>
                <div
                  class="jia"
                  v-else
                  :style="{
                    background: toneCartConfig ? bntBgColor : themeColor,
                  }"
                >
                  <div class="jiaCon">
                    <span class="iconfont iconjiahao1" v-if="bntStyleConfig == 1"></span>
                    <span class="iconfont icongouwuche1" v-else></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <!-- Hai cột -->
        <template v-else-if="styleConfig == 1">
          <div class="list-wrapper itemC">
            <div class="item" v-for="(item, index) in list" :index="index">
              <div class="img-box">
                <img
                  class="img"
                  v-if="item.image"
                  :src="item.image"
                  alt=""
                  :style="{
                    borderRadius: imgRadius2,
                  }"
                />
                <div
                  v-else
                  class="empty-box"
                  :style="{
                    borderRadius: imgRadius2,
                  }"
                >
                  <img src="../../assets/images/shan.png" />
                </div>
              </div>
              <div
                class="info"
                :class="
                  checkboxInfo.length == 1 && checkboxInfo.indexOf(0) != -1 && !cartConfig
                    ? 'on'
                    : ((checkboxInfo.length == 1 && checkboxInfo.indexOf(4) != -1) || !checkboxInfo.length) &&
                      !cartConfig
                    ? 'on2'
                    : ''
                "
                :style="{
                  borderRadius: bgRadius2,
                }"
              >
                <div class="hd">
                  <div
                    class="title line2"
                    v-if="checkboxInfo.indexOf(0) != -1"
                    :style="{
                      fontWeight: goodsName,
                      color: toneConfig ? goodsNameColor : '#333',
                    }"
                  >
                    {{ item.store_name || 'Đây là khu vực hiển thị tên sản phẩm, khu vực hiển thị tên sản phẩm, khu vực hiển thị tên sản phẩm' }}
                  </div>
                  <img v-if="checkboxInfo.indexOf(1) != -1" src="../../assets/images/goods01.png" />
                </div>
                <div class="price acea-row row-middle">
                  <div
                    class="num mb-10"
                    v-if="checkboxInfo.indexOf(2) != -1"
                    :style="{
                      color: toneConfig ? goodsPriceColor : colorStyle.theme,
                    }"
                  >
                    <span>￥</span>{{ item.price ? $HandlePrice(item.price, 0) : 77
                    }}<span>{{ item.price ? $HandlePrice(item.price, 1) : '' }}</span>
                  </div>
                  <img class="img" v-if="checkboxInfo.indexOf(5) != -1" src="../../assets/images/goods02.png" />
                </div>
                <div
                  class="bottom"
                  v-if="checkboxInfo.indexOf(3) != -1"
                  :style="{
                    color: toneConfig ? soldNumColor : '#999999',
                  }"
                >
                  <span>Đã bán {{ item.sales || 0 }}</span>
                </div>
              </div>
              <div
                class="jia"
                v-if="!cartConfig"
                :style="{
                  background: toneCartConfig ? bntBgColor : themeColor,
                }"
              >
                <div class="jiaCon">
                  <span class="iconfont iconjiahao1" v-if="bntStyleConfig == 0"></span>
                  <span class="iconfont icongouwuche1" v-else></span>
                </div>
              </div>
            </div>
          </div>
        </template>
        <!-- Ảnh lớn -->
        <template v-else-if="styleConfig == 4">
          <div class="listBig">
            <div
              class="itemBig"
              v-for="(item, index) in list"
              :key="index"
              :style="{
                borderRadius: bgRadius,
              }"
            >
              <div class="img-box">
                <img
                  class="img"
                  v-if="item.image"
                  :src="item.image"
                  alt=""
                  :style="{
                    borderRadius: imgRadius,
                  }"
                />
                <div
                  v-else
                  class="empty-box"
                  :style="{
                    borderRadius: imgRadius,
                  }"
                >
                  <img src="../../assets/images/shan.png" />
                </div>
              </div>
              <div
                class="conter"
                :class="
                  ((checkboxInfo.length == 1 &&
                    (checkboxInfo.indexOf(1) != -1 ||
                      checkboxInfo.indexOf(3) != -1 ||
                      checkboxInfo.indexOf(4) != -1 ||
                      checkboxInfo.indexOf(5) != -1)) ||
                    !checkboxInfo.length) &&
                  !cartConfig
                    ? 'on'
                    : ''
                "
              >
                <div
                  class="name"
                  v-if="checkboxInfo.indexOf(0) != -1"
                  :style="{
                    fontWeight: goodsName,
                    color: toneConfig ? goodsNameColor : '#333',
                  }"
                >
                  {{ item.store_name || 'Tên sản phẩm tên sản phẩm tên sản...' }}
                </div>
                <img v-if="checkboxInfo.indexOf(1) != -1" src="../../assets/images/goods01.png" />
                <div class="price acea-row row-middle">
                  <div
                    class="num"
                    v-if="checkboxInfo.indexOf(2) != -1"
                    :style="{
                      color: toneConfig ? goodsPriceColor : colorStyle.theme,
                    }"
                  >
                    <span>￥</span>{{ item.price ? $HandlePrice(item.price, 0) : 77
                    }}<span>{{ item.price ? $HandlePrice(item.price, 1) : '' }}</span>
                  </div>
                  <img v-if="checkboxInfo.indexOf(5) != -1" src="../../assets/images/goods02.png" />
                </div>
                <div
                  class="bottom"
                  v-if="checkboxInfo.indexOf(3) != -1"
                  :style="{
                    color: toneConfig ? soldNumColor : '#999999',
                  }"
                >
                  <span>Đã bán {{ item.sales || 0 }}</span>
                </div>
              </div>
              <div v-if="!cartConfig">
                <div
                  class="bnt"
                  v-if="bntStyleConfig == 0"
                  :style="{
                    background: toneCartConfig ? bntBgColor : themeColor,
                  }"
                >
                  Mua
                </div>
                <div
                  class="jia"
                  v-else
                  :style="{
                    background: toneCartConfig ? bntBgColor : themeColor,
                  }"
                >
                  <div class="jiaCon">
                    <span class="iconfont iconjiahao1" v-if="bntStyleConfig == 1"></span>
                    <span class="iconfont icongouwuche1" v-else></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-else-if="styleConfig == 3">
          <div
            class="listCross acea-row row-between-wrapper"
            :style="{
              background: bgColor,
              borderRadius: bgRadius,
            }"
          >
            <div class="item acea-row row-middle" v-for="(item, index) in list" :index="index">
              <div
                class="pictrue acea-row row-center-wrapper"
                :style="{
                  borderRadius: imgRadius,
                }"
              >
                <img
                  class="img"
                  v-if="item.image"
                  :src="item.image"
                  alt=""
                  :style="{
                    borderRadius: imgRadius,
                  }"
                />
                <img v-else src="../../assets/images/shan.png" />
              </div>
              <div class="text">
                <div
                  class="name"
                  v-if="checkboxInfo.indexOf(0) != -1"
                  :style="{
                    fontWeight: goodsName,
                    color: toneConfig ? goodsNameColor : '#333',
                  }"
                >
                  <div class="line2">{{ item.store_name || 'Đây là tiêu đề đây là tiêu đề đây...' }}</div>
                </div>
                <div
                  class="price"
                  v-if="checkboxInfo.indexOf(2) != -1"
                  :style="{
                    color: toneConfig ? goodsPriceColor : colorStyle.theme,
                  }"
                >
                  <span>￥</span>{{ item.price ? $HandlePrice(item.price, 0) : 77
                  }}<span>{{ item.price ? $HandlePrice(item.price, 1) : '' }}</span>
                </div>
              </div>
            </div>
          </div>
        </template>
        <!-- Ba cột -->
        <template v-else>
          <div
            class="list-wrapper itemB"
            :class="styleConfig == 5 ? 'itemD' : ''"
            :style="{
              background: bgColor,
              borderRadius: bgRadius,
            }"
          >
            <div class="list">
              <div class="item" v-for="(item, index) in list" :index="index">
                <div class="img-box">
                  <img
                    class="img"
                    v-if="item.image"
                    :src="item.image"
                    alt=""
                    :style="{
                      borderRadius: imgRadius,
                    }"
                  />
                  <div
                    v-else
                    class="empty-box"
                    :style="{
                      borderRadius: imgRadius,
                    }"
                  >
                    <img src="../../assets/images/shan.png" />
                  </div>
                </div>
                <div
                  class="info"
                  :class="
                    checkboxInfo.indexOf(2) == -1 && checkboxInfo.indexOf(0) != -1 && !cartConfig
                      ? 'on'
                      : checkboxInfo.indexOf(2) == -1 && checkboxInfo.indexOf(0) == -1 && !cartConfig
                      ? 'on2'
                      : ''
                  "
                >
                  <div class="hd" v-if="checkboxInfo.indexOf(0) != -1">
                    <div
                      class="title line2"
                      :style="{
                        fontWeight: goodsName,
                        color: toneConfig ? goodsNameColor : '#333',
                      }"
                    >
                      {{ item.store_name || 'Tên sản phẩm tên sản phẩm tên sản...' }}
                    </div>
                  </div>
                  <div class="price" v-if="checkboxInfo.indexOf(2) != -1">
                    <div
                      class="num"
                      :style="{
                        color: toneConfig ? goodsPriceColor : colorStyle.theme,
                      }"
                    >
                      <span>￥</span>{{ item.price ? $HandlePrice(item.price, 0) : 77
                      }}<span>{{ item.price ? $HandlePrice(item.price, 1) : '' }}</span>
                    </div>
                  </div>
                </div>
                <div
                  class="jia"
                  v-if="!cartConfig"
                  :style="{
                    background: toneCartConfig ? bntBgColor : themeColor,
                  }"
                >
                  <div class="jiaCon">
                    <span class="iconfont iconjiahao1" v-if="bntStyleConfig == 0"></span>
                    <span class="iconfont icongouwuche1" v-else></span>
                  </div>
                </div>
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
// import theme from "@/mixins/theme";
export default {
  name: 'home_goods_list',
  cname: 'Danh sách sản phẩm',
  configName: 'c_home_goods_list',
  icon: '#iconzujian-shangpinliebiao',
  type: 0, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'goodList', // Tên khớp bên ngoài
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
        cname: 'Danh sách sản phẩm',
        name: 'goodList',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt danh sách',
        titleGoods: 'Cài đặt sản phẩm',
        titleContents: 'Nội dung hiển thị',
        titleCart: 'Nút giỏ hàng',
        titleRight: 'Kiểu sản phẩm',
        titleCart: 'Nút giỏ hàng',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 1,
          tabList: [
            {
              name: 'Hiển thị một cột',
            },
            {
              name: 'Hiển thị hai cột (dọc)',
            },
            {
              name: 'Hiển thị ba cột',
            },
            {
              name: 'Hiển thị hai cột (ngang)',
            },
            {
              name: 'Hiển thị ảnh lớn',
            },
            {
              name: 'Hiển thị vuốt ngang',
            },
          ],
        },
        typeConfig: {
          title: 'Cách chọn',
          activeValue: 1,
          list: [
            {
              activeValue: 1,
              title: 'Sản phẩm chỉ định',
            },
            {
              activeValue: 3,
              title: 'Danh mục chỉ định',
            },
            {
              activeValue: 4,
              title: 'Nhãn sản phẩm',
            },
          ],
        },
        goodsList: {
          max: 20,
          list: [],
        },
        goodsSort: {
          title: 'Sắp xếp sản phẩm',
          tabVal: 1,
          tabList: [
            {
              name: 'Tổng hợp',
            },
            {
              name: 'Lượt bán',
            },
            {
              name: 'Giá',
            },
          ],
        },
        numberConfig: {
          title: 'Số lượng sản phẩm',
          val: 3,
          min: 1,
        },
        classList: {
          title: 'Danh mục sản phẩm',
          classVal: [],
        },
        checkboxInfo: {
          title: 'Thông tin hiển thị',
          name: 'checkboxInfo',
          type: [0, 1, 2, 3, 4, 5],
          list: [
            {
              id: 0,
              name: 'Tên sản phẩm',
            },
            {
              id: 1,
              name: 'Nhãn sản phẩm',
            },
            {
              id: 2,
              name: 'Giá sản phẩm',
            },
            {
              id: 3,
              name: 'Lượt bán sản phẩm',
            },
            {
              id: 4,
              name: 'Điểm đánh giá sản phẩm',
            },
            {
              id: 5,
              name: 'Giá thành viên',
            },
          ],
        },
        cartConfig: {
          title: 'Hiển thị',
          tabVal: 0,
          tabList: [
            {
              name: 'Hiện',
            },
            {
              name: 'Ẩn',
            },
          ],
        },
        bntConfig: {
          title: 'Chức năng nút',
          tabVal: 1,
          tabList: [
            {
              name: 'Vào trang chi tiết sản phẩm',
            },
            {
              name: 'Thêm vào giỏ hàng',
            },
          ],
        },
        bntStyleConfig: {
          title: 'Kiểu nút',
          tabVal: 0,
        },
        filletImg: {
          title: 'Bo góc ảnh',
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
        goodsName: {
          title: 'Tên sản phẩm',
          tabVal: 1,
          tabList: [
            {
              name: 'In đậm',
              style: 'bold',
            },
            {
              name: 'Bình thường',
              style: 'normal',
            },
          ],
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
        goodsNameColor: {
          title: 'Tên sản phẩm',
          name: 'goodsNameColor',
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
        goodsPriceColor: {
          title: 'Giá sản phẩm',
          name: 'goodsPriceColor',
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
        soldNumColor: {
          title: 'Số lượng đã bán',
          name: 'soldNumColor',
          default: [
            {
              item: '#999999',
            },
          ],
          color: [
            {
              item: '#999999',
            },
          ],
        },
        scoreColor: {
          title: 'Màu điểm đánh giá',
          name: 'scoreColor',
          default: [
            {
              item: '#999999',
            },
          ],
          color: [
            {
              item: '#999999',
            },
          ],
        },
        toneCartConfig: {
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
          title: 'Màu nút',
          name: 'bntBgColor',
          default: [
            {
              item: '#E93323',
            },
            {
              item: '#FF7931',
            },
          ],
          color: [
            {
              item: '#E93323',
            },
            {
              item: '#FF7931',
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
          val: 10,
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
          val: 8,
          min: 0,
          valList: [{ val: 0 }, { val: 0 }, { val: 0 }, { val: 0 }],
        },
        goodsLabel: {
          title: 'Nhãn sản phẩm',
          activeValue: [],
          list: [],
        },
        productList: {
          title: 'Danh sách sản phẩm',
          list: [],
        },
      },
      list: [],
      pageData: {},
      styleConfig: 0,
      checkboxInfo: [],
      cartConfig: 0,
      bntStyleConfig: 0,
      imgRadius: 0,
      imgRadius2: 0,
      goodsName: '',
      toneConfig: 0,
      goodsNameColor: '',
      goodsPriceColor: '',
      soldNumColor: '',
      scoreColor: '',
      toneCartConfig: 0,
      bntBgColor: '',
      bntBgColorLeft: '',
      bgColor: '',
      bottomBgColor: '',
      mTop: 0,
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      bgRadius: 0,
      bgRadius2: 0,
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
        this.checkboxInfo = data.checkboxInfo.type;
        this.cartConfig = data.cartConfig.tabVal;
        this.bntStyleConfig = data.bntStyleConfig.tabVal;
        let filletImg = data.filletImg.type;
        let filletValImg = data.filletImg.val;
        let valListImg = data.filletImg.valList;
        this.imgRadius = filletImg
          ? valListImg[0].val + 'px ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + valListImg[2].val + 'px'
          : filletValImg + 'px';
        this.imgRadius2 = filletImg
          ? valListImg[0].val + 'px ' + valListImg[1].val + 'px 0 0'
          : filletValImg + 'px ' + filletValImg + 'px 0 0';
        let goodsTabVal = data.goodsName.tabVal;
        this.goodsName = data.goodsName.tabList[goodsTabVal].style;
        this.toneConfig = data.toneConfig.tabVal;
        this.goodsNameColor = data.goodsNameColor.color[0].item;
        this.goodsPriceColor = data.goodsPriceColor.color[0].item;
        this.soldNumColor = data.soldNumColor.color[0].item;
        this.scoreColor = data.scoreColor.color[0].item;
        this.toneCartConfig = data.toneCartConfig.tabVal;
        let bntBgColorLeft = data.bntBgColor.color[0].item;
        let bntBgColorRight = data.bntBgColor.color[1].item;
        this.bntBgColorLeft = bntBgColorLeft;
        this.bntBgColor = `linear-gradient(90deg,${bntBgColorLeft} 0%,${bntBgColorRight} 100%)`;
        let bgColorLeft = data.moduleColor.color[0].item;
        let bgColorRight = data.moduleColor.color[1].item;
        this.bgColor = `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
        this.mTop = data.mbConfig.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.prConfig = data.prConfig.val;
        let fillet = data.fillet.type;
        let filletVal = data.fillet.val;
        let valList = data.fillet.valList;
        this.bgRadius = fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : filletVal + 'px';
        this.bgRadius2 = fillet
          ? '0 0 ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : '0 0 ' + filletVal + 'px ' + filletVal + 'px';
        if (data.typeConfig.activeValue == 1) {
          this.list = data.goodsList.list.length ? data.goodsList.list : 4;
        } else {
          this.list = data.productList.list.length ? data.productList.list : 4;
        }
      }
    },
  },
};
</script>
<style scoped lang="scss">
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
.listCross {
  width: 100%;
  background: #fff;
  padding: 16px 12px 6px 12px;
  .item {
    width: 49%;
    margin-bottom: 10px;
    .pictrue {
      width: 72px;
      height: 72px;
      background: #f3f9ff;
      border-radius: 4px;
      margin-right: 10px;
      img {
        width: 26px;
        height: 20px;
        display: block;
      }
      .img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
      }
    }
    .text {
      flex: 1;
      .name {
        font-size: 13px;
        color: #282828;
        height: 45px;
      }
      .price {
        font-size: 18px;
        font-weight: 600;
        font-family: SemiBold;
        span {
          font-size: 12px;
        }
      }
    }
  }
}
.listBig {
  width: 100%;
  .itemBig {
    width: 100%;
    margin-bottom: 10px;
    background-color: #fff;
    border-radius: 10px;
    position: relative;
    .bnt {
      width: 48px;
      height: 28px;
      background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);
      border-radius: 25px;
      text-align: center;
      line-height: 28px;
      font-size: 12px;
      color: #fff;
      position: absolute;
      right: 10px;
      bottom: 12px;
    }
    .jia {
      width: 22px;
      height: 22px;
      background-color: #e93323;
      border-radius: 50%;
      position: absolute;
      right: 10px;
      bottom: 10px;
      .jiaCon {
        width: 100%;
        height: 100%;
        text-align: center;
        line-height: 22px;
        .iconfont {
          color: #fff;
          font-size: 13px;
        }
      }
    }
    .conter {
      padding: 0 12px 12px 12px;
      &.on {
        height: 45px;
      }
      .name {
        margin-top: 10px;
        font-weight: 400;
        color: #333333;
        font-size: 14px;
        padding: 0;
      }
      img {
        // width: 99px;
        height: 14px;
        display: block;
        margin-top: 5px;
      }
      .price {
        margin-top: 8px;
        margin-bottom: 8px;
        img {
          width: 70px;
          height: 15px;
        }
        .num {
          font-size: 20px;
          font-family: SemiBold;
          span {
            font-size: 12px;
          }
        }
      }
      .bottom {
        font-weight: 400;
        color: #999999;
        font-size: 11px;
      }
    }
    .img-box {
      width: 100%;
      height: 180px;
      position: relative;

      img {
        width: 65px;
        height: 50px;
      }
      .img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .empty-box {
        border-radius: 8px 8px 0 0;
        background: #f3f9ff;
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
      .num {
        font-size: 18px;
        margin-right: 5px;
      }
    }
  }
}
.paddingBox {
  padding-bottom: 0;
}
.home_product {
  overflow: hidden;
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
}
.list-wrapper {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  .item {
    width: 48.5%;
    margin-bottom: 10px;
    background-color: #fff;
    position: relative;
    .bnt {
      width: 48px;
      height: 28px;
      background: linear-gradient(90deg, #e93323 0%, #ff7931 100%);
      border-radius: 25px;
      color: #fff;
      text-align: center;
      line-height: 28px;
      font-size: 12px;
      position: absolute;
      right: 10px;
      bottom: 10px;
    }
    .jia {
      width: 22px;
      height: 22px;
      background-color: #e93323;
      border-radius: 50%;
      position: absolute;
      right: 10px;
      bottom: 10px;
      .jiaCon {
        width: 100%;
        height: 100%;
        text-align: center;
        line-height: 22px;
        .iconfont {
          color: #fff;
          font-size: 13px;
        }
      }
    }
    .img-box {
      position: relative;
      width: 100%;
      height: 173px;
      .img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      img,
      .box {
        width: 65px;
        height: 50px;
      }
      .empty-box {
        background: #f3f9ff;
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
        color: #333;
      }
      img {
        height: 14px;
        display: block;
        margin-top: 4px;
      }
      .bottom {
        color: #999999;
        font-size: 11px;
      }
      .price {
        display: flex;
        align-items: center;
        img {
          width: 70px;
          height: 15px;
          display: block;
        }
        .num {
          font-size: 20px;
          margin-right: 4px;
          font-family: SemiBold;
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
}

.itemA {
  /*background #fff*/
  .item {
    display: flex;
    width: 100%;
    padding: 10px;
    position: relative;

    .img-box {
      position: relative;
      width: 112px;
      height: 112px;
      img {
        width: 65px;
        height: 50px;
      }
      .empty-box {
        background: #f3f9ff;
      }
    }

    .info {
      display: flex;
      justify-content: space-between;
      flex-direction: column;
      flex: 1;
      margin-left: 10px;
      padding: 0;
      .hd {
        height: 63px;
      }
      .price {
        margin-top: 2px;
        &.on {
          margin-top: 20px;
        }
        .img {
          margin-top: 0;
        }
      }
    }
  }
}
.itemB {
  justify-content: inherit;
  background-color: #fff;
  padding: 16px 10px 0 10px;
  width: 100%;
  box-sizing: border-box;

  .list {
    display: flex;
    flex-wrap: wrap;
  }

  .item {
    width: 31.3%;
    margin-right: 10px;
    background: unset;
    .jia {
      right: 2px;
      bottom: 0;
    }
    .info {
      padding: 0;
      &.on {
        height: 70px;
      }
      &.on2 {
        height: 30px;
      }
      .hd {
        margin-top: 7px;
        height: 42px;
      }
      .price {
        margin-top: 7px;
        line-height: 1.2;
      }
    }
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
.itemD {
  flex-wrap: nowrap;
  display: inline-flex;
  overflow: hidden;
  .list {
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
  }
  .item {
    width: 100px;
    background: unset;
    &:nth-child(3n) {
      margin-right: 10px;
    }
    .img-box {
      height: 100px;
    }
  }
}
.itemC {
  .item {
    background-color: transparent;
    .info {
      background-color: #fff;
    }
  }
  .item .info.on {
    height: 67px;
  }
  .item .info.on2 {
    height: 40px;
  }
  .item .info .price {
    margin-top: 6px;
    margin-bottom: 8px;
  }
  .item .info .bottom {
    margin-top: 3px;
  }
}
</style>
