<template>
  <view class="page-design" :class="bgClass">
    <view v-if="!errorNetwork" :style="colorStyle">
      <!-- #ifdef MP -->
      <view
        class="fixed z-1000"
        :style="[appletStyle]"
        v-if="myApplet && isHome"
      >
        <view
          class="myApplet w-324 h-62 text-center rd-12rpx lh-62rpx fs-24 bg--w111-fff text-w111-303133"
        >
          Nhấn để thêm vào Mini Program của tôi
          <text
            class="iconfont icon-ic_close2 text--w111-ccc ml-16"
            @click="myApplet = false"
          ></text>
        </view>
      </view>
      <!-- #endif -->
      <!-- Tìm kiếm kèm ảnh trình chiếu -->
      <homeComb
        v-if="showHomeComb"
        :dataConfig="homeCombData"
        :belongIndex="belongIndex"
        @bindSortId="bindSortId"
        :isScrolled="isScrolled"
        @storeTap="storeTap"
      ></homeComb>

      <!-- Ô tìm kiếm phía trên -->
      <headerSerch
        v-if="isHeaderSerch"
        :dataConfig="headerSerchCombData"
        :belongIndex="belongIndex"
        @storeTap="storeTap"
      ></headerSerch>

      <tabNav
        v-if="showCateNav"
        :dataConfig="cateNavData"
        @bindHeight="bindHeight"
        @bindSortId="bindSortId"
        :isFixed="isFixed && !cateNavData.stickyConfig.tabVal"
      ></tabNav>

      <view class="index">
        <!-- Style tùy chỉnh -->
        <block v-for="(item, index) in styleConfig" :key="index">
          <view :id="item.id">
            <userInfor
              v-if="item.name == 'userInfor'"
              :dataConfig="item"
              @changeLogin="changeLogin"
            ></userInfor>
            <homeUserInfor
              v-else-if="item.name == 'member'"
              :dataConfig="item"
              @changeLogin="changeLogin"
            ></homeUserInfor>
            <newVip
              v-else-if="item.name == 'newVip'"
              :dataConfig="item"
            ></newVip>
            <!-- Danh sách bài viết -->
            <articleList
              v-else-if="item.name == 'articleList'"
              :dataConfig="item"
            ></articleList>
            <bargain
              v-else-if="item.name == 'bargain'"
              :dataConfig="item"
              @changeBarg="changeBarg"
            ></bargain>
            <blankPage
              v-else-if="item.name == 'blankPage'"
              :dataConfig="item"
            ></blankPage>
            <combination
              v-else-if="item.name == 'combination'"
              :dataConfig="item"
            ></combination>
            <!-- Phiếu giảm giá -->
            <coupon
              v-else-if="item.name == 'coupon'"
              :dataConfig="item"
              @changeLogin="changeLogin"
            ></coupon>
            <!-- Chăm sóc khách hàng -->
            <customerService
              v-else-if="item.name == 'customerService'"
              :dataConfig="item"
            ></customerService>
            <!-- Danh sách sản phẩm -->
            <goodList
              ref="goodLists"
              v-else-if="
                item.name == 'goodList' || item.name == 'goodRecommend'
              "
              :dataConfig="item"
              :list="goodList"
            ></goodList>
            <!-- <homeGoodRecommend
              v-else-if="
                item.name == 'goodList' || item.name == 'goodRecommend'
              "
              :dataConfig="item"
            ></homeGoodRecommend> -->
            <guide v-else-if="item.name == 'guide'" :dataConfig="item"></guide>
            <!-- Module livestream -->
            <!-- #ifdef  MP-WEIXIN -->
            <liveBroadcast
              v-else-if="item.name == 'liveBroadcast'"
              :dataConfig="item"
            ></liveBroadcast>
            <!-- #endif -->
            <menus v-else-if="item.name == 'menus'" :dataConfig="item"></menus>
            <!-- Tin nhắn thời gian thực -->
            <news v-else-if="item.name == 'news'" :dataConfig="item"></news>
            <!-- Thư viện ảnh -->
            <pictureCube
              v-else-if="item.name == 'pictureCube'"
              :dataConfig="item"
            ></pictureCube>
            <!-- Danh sách khuyến mãi -->
            <promotionList
              ref="promotionLists"
              v-else-if="item.name == 'promotionList'"
              :dataConfig="item"
              :productVideoStatus="productVideoStatus"
              :positionTop="positionTop"
            ></promotionList>
            <seckill
              v-else-if="item.name == 'seckill'"
              :dataConfig="item"
            ></seckill>
            <!-- Ảnh trình chiếu-->
            <swiperBg
              v-else-if="item.name == 'swiperBg'"
              :dataConfig="item"
            ></swiperBg>
            <swipers
              v-else-if="item.name == 'swipers'"
              :dataConfig="item"
            ></swipers>

            <!-- Tiêu đề -->
            <titles
              v-else-if="item.name == 'titles'"
              :dataConfig="item"
            ></titles>
            <presale
              v-else-if="item.name == 'presale'"
              :dataConfig="item"
            ></presale>
            <pointsMall
              v-else-if="item.name == 'pointsMall'"
              :dataConfig="item"
            ></pointsMall>
            <!-- #ifndef APP -->
            <richText
              v-else-if="item.name == 'richText'"
              :dataConfig="item"
            ></richText>
            <videos
              v-else-if="item.name == 'videos'"
              :dataConfig="item"
            ></videos>
            <!-- #endif -->
            <signIn
              v-else-if="item.name == 'signIn'"
              :dataConfig="item"
            ></signIn>
            <hotspot
              v-else-if="item.name == 'hotspot'"
              :dataConfig="item"
            ></hotspot>
            <follow
              v-else-if="item.name == 'follow'"
              :dataConfig="item"
            ></follow>
            <!-- Chi tiết sản phẩm -->
            <productInfo
              v-else-if="item.name == 'productInfo'"
              :dataConfig="item"
              :productData="productData"
              :priceData="priceData"
              :skuList="skuList"
              @changeSpec="onChangeSpec"
              @showSpecModal="onShowSpecModal"
              @share="onShare"
              @goActivity="onGoActivity"
            ></productInfo>
            <homePaidVip
              v-else-if="item.name == 'home_paid_vip'"
              :dataConfig="item"
              :productData="productData"
              :isShowPaidVip="isShowPaidVip"
              :priceData="priceData"
            ></homePaidVip>
            <homeProductService
              v-else-if="item.name == 'productService'"
              :dataConfig="item"
              :productData="productData"
              :couponList="couponList"
              :activity="activity"
              :attr="attr"
              :attrTxt="attrTxt"
              :attrValue="attrValue"
              @showCoupon="onShowCoupon"
              @showSpecModal="onShowSpecModal"
              @openModal="onOpenModal"
              @goActivity="onGoActivity"
            ></homeProductService>
            <homeReviews
              v-else-if="item.name == 'reviews'"
              :dataConfig="item"
              :reply="reply"
              :replyCount="replyCount"
              :replyChance="replyChance"
              :productId="productId"
            ></homeReviews>
            <productDesc
              v-else-if="item.name == 'productDesc'"
              :dataConfig="item"
              :productData="productData"
            ></productDesc>
            <customComponent
              v-else-if="item.name == 'customComponent'"
              :dataConfig="item"
              @changeLogin="changeLogin"
            ></customComponent>
          </view>
        </block>

        <!-- Slot: dùng để hiển thị danh sách sản phẩm theo danh mục hoặc nội dung khác ở phía dưới -->
        <slot name="bottom"></slot>

        <view class="pb-safe" :style="[pdHeights]" v-if="isFooter"></view>
        <pageFooter
          v-if="footerConfigData"
          :configData="footerConfigData"
          @newDataStatus="newDataStatus"
        ></pageFooter>
      </view>
    </view>

    <view v-else>
      <view class="error-network">
        <image :src="imgHost + '/statics/images/error-network.gif'"></image>
        <view class="title">{{ $t(`Mất kết nối mạng`) }}</view>
        <view class="btn" @click="reconnect">{{ $t(`Kết nối lại`) }}</view>
      </view>
    </view>
  </view>
</template>

<script>
import pageFooter from "@/components/pageFooter/index.vue";
import { HTTP_REQUEST_URL } from "@/config/app";
import colors from "@/mixins/color";
// diyComponents - cùng thư mục nên dùng đường dẫn tương đối
import homeComb from "./homeComb.vue";
import headerSerch from "./headerSerch.vue";
import tabNav from "./tabNav.vue";
import userInfor from "./userInfor.vue";
import homeUserInfor from "./homeUserInfor.vue";
import newVip from "./newVip.vue";
import articleList from "./articleList.vue";
import bargain from "./bargain.vue";
import blankPage from "./blankPage.vue";
import combination from "./combination.vue";
import coupon from "./coupon.vue";
import customerService from "./customerService.vue";
import goodList from "./goodList.vue";
import guide from "./guide.vue";
import liveBroadcast from "./liveBroadcast.vue";
import menus from "./menus.vue";
import news from "./news.vue";
import pictureCube from "./pictureCube.vue";
import promotionList from "./promotionList.vue";
import seckill from "./seckill.vue";
import swiperBg from "./swiperBg.vue";
import swipers from "./swipers.vue";
import titles from "./titles.vue";
import presale from "./presale.vue";
import pointsMall from "./pointsMall.vue";
import richText from "./richText.vue";
import videos from "./videos.vue";
import signIn from "./signIn.vue";
import hotspot from "./hotspot.vue";
import follow from "./follow.vue";
import productInfo from "./productInfo.vue";
import homePaidVip from "./homePaidVip.vue";
import homeProductService from "./homeProductService.vue";
import homeReviews from "./homeReviews.vue";
import productDesc from "./productDesc.vue";
import customComponent from "./customComponent.vue";

export default {
  name: "PageDesign",
  components: {
    pageFooter,
    homeComb,
    headerSerch,
    tabNav,
    userInfor,
    homeUserInfor,
    newVip,
    articleList,
    bargain,
    blankPage,
    combination,
    coupon,
    customerService,
    goodList,
    guide,
    liveBroadcast,
    menus,
    news,
    pictureCube,
    promotionList,
    seckill,
    swiperBg,
    swipers,
    titles,
    presale,
    pointsMall,
    richText,
    videos,
    signIn,
    hotspot,
    follow,
    productInfo,
    homePaidVip,
    homeProductService,
    homeReviews,
    productDesc,
    customComponent,
  },
  mixins: [colors],
  props: {
    // Dữ liệu cấu hình DIY
    diyData: {
      type: Object,
      default: () => ({}),
    },
    // Có phải trang chủ không (dùng để điều khiển lời nhắc “Thêm vào Mini Program của tôi”, v.v.)
    isHome: {
      type: Boolean,
      default: false,
    },
    // Trạng thái cuộn trang
    isScrolled: {
      type: Boolean,
      default: false,
    },
    // Có cố định không (dùng để ghim ở đầu trang)
    isFixed: {
      type: Boolean,
      default: false,
    },
    productData: {
      type: Object,
      default: () => ({}),
    },
    priceData: {
      type: Object,
      default: () => ({}),
    },
    skuList: {
      type: Array,
      default: () => [],
    },
    reply: {
      type: Array,
      default: () => [],
    },
    replyCount: {
      type: Number,
      default: 0,
    },
    replyChance: {
      type: String | Number,
      default: 0,
    },
    productId: {
      type: Number | String,
      default: 0,
    },
    goodList: {
      type: Array,
      default: () => [],
    },
    // Trạng thái phát video
    productVideoStatus: {
      type: Boolean,
      default: false,
    },
    // Vị trí sắp xếp của cửa hàng được gán theo quy tắc vào cửa hàng
    belongIndex: {
      type: Number,
      default: 0,
    },
    // Trạng thái lỗi mạng
    errorNetwork: {
      type: Boolean,
      default: false,
    },
    couponList: {
      type: Array,
      default: () => [],
    },
    activity: {
      type: Array,
      default: () => [],
    },
    attr: {
      type: Object,
      default: () => ({}),
    },
    attrTxt: {
      type: String,
      default: "",
    },
    attrValue: {
      type: String,
      default: "",
    },
    // Trang tùy chỉnh
    microPage: {
      type: Boolean,
      default: false,
    },
    // Module vip của sản phẩm
    isShowPaidVip: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      styleConfig: [],
      homeCombData: {},
      headerSerchCombData: {},
      cateNavData: {},
      footerConfigData: null,
      showHomeComb: false,
      isHeaderSerch: false,
      showCateNav: false,
      bgColor: "",
      bgPic: "",
      bgTabVal: "",
      positionTop: 0,
      isFooter: false,
      pdHeight: 0,
      myApplet: true,
      getHeight: this.$util.getWXStatusHeight(),
      imgHost: HTTP_REQUEST_URL,
    };
  },
  computed: {
    // #ifdef MP
    appletStyle() {
      return {
        top: this.getHeight.menuButtonInfo.bottom + 8 + "px",
        right: "10px",
      };
    },
    // #endif
    pageStyle() {
      return {
        backgroundColor: this.bgColor,
        backgroundImage: this.bgPic ? `url(${this.bgPic})` : "",
        minHeight: "100vh", // Đảm bảo nền phủ kín
      };
    },
    bgClass() {
      if (this.bgTabVal == 2) return "fullsize noRepeat";
      if (this.bgTabVal == 1) return "repeat ysize";
      return "noRepeat ysize";
    },
    pdHeights() {
      let H = `${this.pdHeight * 2 + 100}rpx`;
      return {
        height: this.isFooter ? H : "100rpx",
      };
    },
  },
  watch: {
    diyData: {
      handler(val) {
        if (val && Object.keys(val).length > 0) {
          this.setDiyData(val);
        } else {
          // Đặt lại dữ liệu
          this.styleConfig = [];
          this.homeCombData = {};
          this.headerSerchCombData = {};
          this.cateNavData = {};
          this.footerConfigData = null;
          this.showHomeComb = false;
          this.isHeaderSerch = false;
          this.showCateNav = false;
          this.bgColor = "";
          this.bgPic = "";
          this.bgTabVal = "";
        }
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    reconnect() {
      this.$emit("reconnect");
    },
    onChangeSpec(item) {
      this.$emit("changeSpec", item);
    },
    onShowSpecModal() {
      this.$emit("showSpecModal");
    },
    onShowCoupon() {
      this.$emit("showCoupon");
    },
    onOpenModal(type) {
      this.$emit("openModal", type);
    },
    onGoActivity(item) {
      this.$emit("goActivity", item);
    },
    onShare() {
      this.$emit("share");
    },
    // Chuyển object thành mảng
    objToArr(data) {
      if (!data) return [];
      let obj = Object.keys(data).sort();
      let m = obj.map((key) => data[key]);
      return m;
    },
    setDiyData(data) {
      if (!data) return;
      if (data.is_bg_color) {
        this.bgColor = data.color_picker;
      }
      if (data.is_bg_pic) {
        this.bgPic = data.bg_pic;
        this.bgTabVal = data.bg_tab_val;
      }

      let temp = [];
      // Đặt lại trạng thái
      this.showHomeComb = false;
      this.isHeaderSerch = false;
      this.showCateNav = false;
      this.footerConfigData = null;
      if (data.value) {
        let lastArr = this.objToArr(data.value);
        lastArr.forEach((item) => {
          if (item.name == "pageFoot" && !this.microPage) {
            this.footerConfigData = item;
          }
          if (item.name === "homeComb" && !item.isHide) {
            this.showHomeComb = true;
            this.homeCombData = item;
            if (item.searchConfig && item.searchConfig.tabVal) {
              this.positionTop = uni.getWindowInfo().statusBarHeight + 43;
            }
          }
          if (item.name == "headerSerch" && !item.isHide) {
            this.isHeaderSerch = true;
            this.headerSerchCombData = item;
          }
          if (item.name == "tabNav" && !item.isHide) {
            this.showCateNav = true;
            this.cateNavData = item;
          }
          if (!item.isHide) {
            temp.push(item);
          }
        });

        // Thứ tự sắp xếp
        temp.sort((a, b) => a.timestamp - b.timestamp);
        this.styleConfig = temp;
      }
    },
    bindSortId(data) {
      this.$emit("bindSortId", data);
    },
    bindHeight(data) {
      this.$emit("bindHeight", data);
    },
    storeTap(id) {
      this.$emit("storeTap", id);
    },
    changeLogin() {
      this.$emit("changeLogin");
    },
    changeBarg(item) {
      this.$emit("changeBarg", item);
    },
    newDataStatus(val, num) {
      this.isFooter = val ? true : false;
      this.pdHeight = num;
      this.$emit("newDataStatus", { val, num });
    },
    reconnect() {
      this.$emit("reconnect");
    },
  },
};
</script>

<style lang="scss" scoped>
.page-design {
  overflow-y: scroll;
  overflow-x: hidden;
  min-height: 100vh;
}

.myApplet {
  position: relative;
  &::after {
    position: absolute;
    right: 55px;
    top: -5px;
    content: "";
    width: 0;
    height: 0;
    border-left: 7px solid transparent;
    border-right: 7px solid transparent;
    border-bottom: 7px solid #fff;
  }
}

.ysize {
  background-size: 100%;
}

.fullsize {
  background-size: 100% 100%;
}

.repeat {
  background-repeat: repeat;
}

.noRepeat {
  background-repeat: no-repeat;
}

.error-network {
  position: fixed;
  left: 0;
  top: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  height: 100%;
  padding-top: 40rpx;
  background: #fff;

  image {
    width: 414rpx;
    height: 336rpx;
  }

  .title {
    position: relative;
    top: -40rpx;
    font-size: 32rpx;
    color: #666;
  }

  .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 508rpx;
    height: 86rpx;
    margin-top: 100rpx;
    border: 1px solid #d74432;
    color: #e93323;
    font-size: 30rpx;
    border-radius: 120rpx;
  }
}
</style>
