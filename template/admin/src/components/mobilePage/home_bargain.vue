<template>
  <div>
    <div
      class="seckill-box"
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
        class="hd"
        :style="
          (styleConfig
            ? 'backgroundImage:url(' + imgBgUrl + ')'
            : `background:linear-gradient(90deg,${headerBgColorLeft} 0%,${headerBgColorRight} 100%)`) +
          ';borderRadius:' +
          bgRadius
        "
      >
        <div class="left acea-row row-middle">
          <div
            class="text"
            v-if="titleConfig"
            :style="
              (titleTabVal == 2 ? 'fontStyle:' : 'fontWeight:') +
              titleText +
              ';color:' +
              titleColor +
              ';fontSize:' +
              titleNumber +
              'px;'
            "
          >
            {{ titleTxtConfig }}
          </div>
          <img v-else :src="styleConfig ? imgUrl : imgColorUrl" alt="" />
          <div
            class="line"
            :style="{
              background: dividerColor,
            }"
          ></div>
          <div
            class="tips"
            :style="{
              color: styleConfig ? tipsColor : tipsColor2,
            }"
          >
            {{ tipTxt }}
          </div>
        </div>
        <div
          class="right"
          :style="{
            color: styleConfig ? headerBntColor : headerBntColor2,
            fontSize: bntNumber + 'px',
          }"
        >
          {{ rightBntTxt }}
          <span
            class="iconfont iconjinru"
            :style="{
              fontSize: bntNumber + 'px',
            }"
          ></span>
        </div>
      </div>
      <div
        class="list-wrapper"
        :class="
          goodStyleConfig == 0
            ? 'on'
            : goodStyleConfig == 1 || goodStyleConfig == 2
            ? 'on2'
            : goodStyleConfig == 3
            ? 'on3'
            : ''
        "
        :style="{
          background: bgColor,
          borderRadius: bgRadius2,
        }"
      >
        <div v-if="goodStyleConfig == 0" class="itemOne acea-row" v-for="(item, index) in numberConfig" :key="index">
          <div
            class="empty-box"
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img src="../../assets/images/shan.png" />
          </div>
          <div class="text">
            <div class="top">
              <div
                class="name line2"
                v-if="checkboxInfo.indexOf(0) != -1"
                :style="{
                  fontWeight: goodsName,
                  color: goodsNameColor,
                }"
              >
                Quần túi hộp ống côn màu xanh than Ximi Jia les phong cách unisex áo thun cá tính phi giới tính nhiều túi...
              </div>
              <div
                class="num"
                v-if="checkboxInfo.indexOf(1) != -1"
                :style="{
                  color: toneConfig ? joinNumColor : colorStyle.theme,
                }"
              >
                <span class="iconfont iconic_fire"></span>1223 người đang tham gia
              </div>
            </div>
            <div
              class="bottom"
              :class="checkboxInfo.indexOf(2) != -1 && checkboxInfo.indexOf(3) != -1 ? '' : 'acea-row row-bottom'"
            >
              <div
                class="price"
                v-if="checkboxInfo.indexOf(2) != -1"
                :style="{
                  color: toneConfig ? bargainPriceColor : colorStyle.theme,
                }"
              >
                <span class="label">¥</span><span class="num">2690.00</span>
              </div>
              <div
                class="yprice"
                v-if="checkboxInfo.indexOf(3) != -1"
                :style="{
                  color: goodsPriceColor,
                }"
              >
                ¥1233423.00
              </div>
            </div>
            <div
              class="bnt"
              v-if="!bargainConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(270deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              Tham gia săn giảm giá
            </div>
          </div>
        </div>
        <div class="itemTwo" v-if="goodStyleConfig == 1" v-for="(item2, index2) in numberConfig" :key="index2">
          <div
            class="empty-box"
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img src="../../assets/images/shan.png" />
          </div>
          <div
            :class="
              (checkboxInfo.indexOf(0) != -1 && checkboxInfo.length == 1 && !bargainConfig) ||
              (checkboxInfo.indexOf(0) != -1 &&
                checkboxInfo.indexOf(1) != -1 &&
                checkboxInfo.length == 2 &&
                !bargainConfig)
                ? 'item'
                : (!checkboxInfo.length || (checkboxInfo.indexOf(1) != -1 && checkboxInfo.length == 1)) &&
                  !bargainConfig
                ? 'item2'
                : ''
            "
          >
            <div
              class="title line1"
              v-if="checkboxInfo.indexOf(0) != -1"
              :style="{
                fontWeight: goodsName,
                color: goodsNameColor,
              }"
            >
              Quần túi hộp ống côn màu xanh than Ximi Jia les phong cách unisex áo thun cá tính phi giới tính nhiều túi...
            </div>
            <div
              class="price"
              :class="checkboxInfo.indexOf(3) == -1 && !bargainConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(2) != -1"
              :style="{
                color: toneConfig ? bargainPriceColor : colorStyle.theme,
              }"
            >
              ¥<span class="num">3200.00</span>
            </div>
            <div
              class="yprice"
              :class="checkboxInfo.indexOf(2) == -1 && !bargainConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(3) != -1"
              :style="{
                color: goodsPriceColor,
              }"
            >
              ¥3699.00
            </div>
            <div
              class="bnt"
              :class="checkboxInfo.indexOf(2) == -1 && !bargainConfig ? 'on' : ''"
              v-if="!bargainConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              Săn giảm giá
            </div>
          </div>
        </div>
        <div v-if="goodStyleConfig == 2" class="list-item" v-for="(item, index) in numberConfig" :key="index">
          <div class="img-box">
            <div
              class="empty-box"
              :style="{
                borderRadius: imgRadius,
              }"
            >
              <img src="../../assets/images/shan.png" />
            </div>
          </div>
          <div
            class="title line1"
            v-if="checkboxInfo.indexOf(0) != -1"
            :style="{
              fontWeight: goodsName,
              color: goodsNameColor,
            }"
          >
            Quần túi hộp ống côn màu xanh than Ximi Jia les phong cách unisex áo thun cá tính phi giới tính nhiều túi...
          </div>
          <div
            class="price"
            v-if="checkboxInfo.indexOf(2) != -1"
            :style="{
              color: toneConfig ? bargainPriceColor : colorStyle.theme,
            }"
          >
            Chỉ từ<span class="lable">¥</span><span class="num">350.00</span>
          </div>
          <div
            class="yprice"
            v-if="checkboxInfo.indexOf(3) != -1"
            :style="{
              color: goodsPriceColor,
            }"
          >
            ¥3699.00
          </div>
        </div>
        <div class="itemThree" v-if="goodStyleConfig == 3" v-for="(item2, index2) in numberConfig" :key="index2">
          <div
            class="empty-box"
            :style="{
              borderRadius: imgRadius,
            }"
          >
            <img src="../../assets/images/shan.png" />
          </div>
          <div>
            <div
              class="title line1"
              v-if="checkboxInfo.indexOf(0) != -1"
              :style="{
                fontWeight: goodsName,
                color: goodsNameColor,
              }"
            >
              Quần túi hộp ống côn màu xanh than Ximi Jia les phong cách unisex áo thun cá tính phi giới tính nhiều túi...
            </div>
            <div
              class="joinNum"
              v-if="checkboxInfo.indexOf(1) != -1"
              :style="{
                color: toneConfig ? joinNumColor2 : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${joinBgColorLeft} 0%,${joinBgColorRight} 100%)`
                  : themeColor2,
              }"
            >
              175 người tham gia chương trình
            </div>
            <div
              class="price"
              :class="checkboxInfo.indexOf(3) == -1 && !bargainConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(2) != -1"
              :style="{
                color: toneConfig ? bargainPriceColor : colorStyle.theme,
              }"
            >
              ¥<span class="num">3200.00</span>
            </div>
            <div
              class="yprice"
              :class="checkboxInfo.indexOf(2) == -1 && !bargainConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(3) != -1"
              :style="{
                color: goodsPriceColor,
              }"
            >
              ¥3699.00
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
// import theme from "@/mixins/theme";
import Setting from '@/setting';
export default {
  name: 'home_bargain',
  cname: 'Săn giảm giá',
  icon: '#iconzujian-kanjia',
  configName: 'c_home_bargain',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'bargain', // Tên khớp bên ngoài
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
        cname: 'Săn giảm giá',
        name: 'bargain',
        timestamp: this.num,
        isHide: false,
        setUp: {
          tabVal: 0,
        },
        titleLeft: 'Cài đặt phần đầu',
        titleGoodsList: 'Danh sách sản phẩm',
        titleGoods: 'Cài đặt sản phẩm',
        titleRight: 'Kiểu phần đầu',
        titleGoodsStyle: 'Kiểu sản phẩm',
        titleCurrency: 'Kiểu chung',
        styleConfig: {
          title: 'Chọn phong cách',
          tabVal: 1,
          tabList: [
            {
              name: 'Màu nền',
            },
            {
              name: 'Ảnh nền',
            },
          ],
        },
        imgBgConfig: {
          info: 'Đề xuất: 710px * 96px',
          url: Setting.apiBaseURL.replace(/adminapi/, '') + 'statics/images/bargainBg.png',
          type: 'code',
          delType: 0,
          name: 'Ảnh nền',
        },
        titleConfig: {
          title: 'Loại tiêu đề',
          tabVal: 0,
          tabList: [
            {
              name: 'Hình ảnh',
            },
            {
              name: 'Văn bản',
            },
          ],
        },
        imgConfig: {
          info: 'Đề xuất: 154px * 32px',
          url: require('@/assets/images/bargain02.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh tiêu đề',
        },
        imgColorConfig: {
          info: 'Đề xuất: 154px * 32px',
          url: require('@/assets/images/bargain01.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh tiêu đề',
        },
        titleTxtConfig: {
          title: 'Chữ tiêu đề',
          value: 'Săn giảm giá cực sốc',
          place: 'Vui lòng nhập chữ tiêu đề',
          max: 6,
        },
        tipTxtConfig: {
          title: 'Chữ gợi ý',
          value: 'Giảm còn 0đ, nhận miễn phí',
          place: 'Vui lòng nhập chữ gợi ý',
          max: 10,
        },
        rightBntConfig: {
          title: 'Nút bên phải',
          value: 'Xem thêm',
          place: 'Vui lòng nhập chữ nút bên phải',
          max: 4,
        },
        goodStyleConfig: {
          title: 'Chọn phong cách',
          tabVal: 0,
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
              name: 'Hiển thị vuốt ngang',
            },
          ],
        },
        numberConfig: {
          title: 'Số lượng sản phẩm',
          val: 3,
          min: 1,
        },
        checkboxInfo: {
          title: 'Thông tin hiển thị',
          name: 'checkboxInfo',
          type: [0, 1, 2, 3],
          list: [
            {
              id: 0,
              name: 'Tên sản phẩm',
            },
            {
              id: 1,
              name: 'Số người tham gia',
            },
            {
              id: 2,
              name: 'Giá sản phẩm',
            },
            {
              id: 3,
              name: 'Giá gốc',
            },
          ],
        },
        bargainConfig: {
          title: 'Nút săn giảm giá',
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
        headerBgColor: {
          title: 'Màu nền',
          name: 'headerBgColor',
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
        titleText: {
          title: 'Chữ tiêu đề',
          tabVal: 0,
          tabList: [
            {
              name: 'In đậm',
              style: 'bold',
            },
            {
              name: 'Bình thường',
              style: 'normal',
            },
            {
              name: 'In nghiêng',
              style: 'italic',
            },
          ],
        },
        titleColor: {
          title: 'Màu tiêu đề',
          name: 'titleColor',
          default: [
            {
              item: '#282828',
            },
          ],
          color: [
            {
              item: '#282828',
            },
          ],
        },
        titleNumber: {
          title: 'Cỡ chữ tiêu đề',
          val: 16,
          min: 0,
        },
        headerBntColor: {
          title: 'Màu nút',
          name: 'headerBntColor',
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
        headerBntColor2: {
          title: 'Màu nút',
          name: 'headerBntColor2',
          default: [
            {
              item: '#999',
            },
          ],
          color: [
            {
              item: '#999',
            },
          ],
        },
        bntNumber: {
          title: 'Cỡ chữ nút',
          val: 12,
          min: 0,
        },
        tipsColor: {
          title: 'Chữ gợi ý',
          name: 'tipsColor',
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
        tipsColor2: {
          title: 'Chữ gợi ý',
          name: 'tipsColor2',
          default: [
            {
              item: '#999',
            },
          ],
          color: [
            {
              item: '#999',
            },
          ],
        },
        dividerColor: {
          title: 'Đường phân cách',
          name: 'dividerColor',
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
          title: 'Giá gốc',
          name: 'goodsPriceColor',
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
        joinNumColor: {
          title: 'Số người tham gia',
          name: 'joinNumColor',
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
        joinNumColor2: {
          title: 'Số người tham gia',
          name: 'joinNumColor2',
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
        joinBgColor: {
          title: 'Nền lượt tham gia',
          name: 'progressColor',
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
        bargainPriceColor: {
          title: 'Giá săn giảm giá',
          name: 'bargainPriceColor',
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
        goodsBntColor: {
          title: 'Màu nút',
          name: 'goodsBntColor',
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
        goodsBntTxtColor: {
          title: 'Chữ trên nút',
          name: 'goodsBntTxtColor',
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
      },
      pageData: {},
      imgUrl: '',
      imgBgUrl: '',
      tipsColor: '',
      tipsColor2: '',
      dividerColor: '',
      rightBntTxt: '',
      tipTxt: '',
      headerBntColor: '',
      headerBntColor2: '',
      bntNumber: 0,
      styleConfig: 0,
      headerBgColorLeft: '',
      headerBgColorRight: '',
      imgColorUrl: '',
      titleConfig: 0,
      titleTxtConfig: '',
      bgColor: '',
      bottomBgColor: '',
      mTop: 0,
      topConfig: 0,
      bottomConfig: 0,
      prConfig: 0,
      titleText: '',
      titleTabVal: 0,
      checkboxInfo: [],
      imgRadius: 0,
      bgRadius: 0,
      bgRadius2: 0,
      goodsName: '',
      goodsNameColor: '',
      goodsPriceColor: '',
      toneConfig: 0,
      goodsBntColorLeft: '',
      goodsBntColorRight: '',
      goodStyleConfig: 0,
      goodsBntTxtColor: '',
      bargainConfig: 0,
      numberConfig: 1,
      titleColor: '',
      titleNumber: 0,
      joinNumColor: '',
      joinNumColor2: '',
      bargainPriceColor: '',
      joinBgColorLeft: '',
      joinBgColorRight: '',
      themeColor: '',
      themeColor2: '',
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
        this.imgUrl = data.imgConfig.url;
        this.imgBgUrl = data.imgBgConfig.url;
        this.imgColorUrl = data.imgColorConfig.url;
        this.tipsColor = data.tipsColor.color[0].item;
        this.tipsColor2 = data.tipsColor2.color[0].item;
        this.dividerColor = data.dividerColor.color[0].item;
        this.rightBntTxt = data.rightBntConfig.value;
        this.tipTxt = data.tipTxtConfig.value;
        this.headerBntColor = data.headerBntColor.color[0].item;
        this.headerBntColor2 = data.headerBntColor2.color[0].item;
        this.bntNumber = data.bntNumber.val;
        this.styleConfig = data.styleConfig.tabVal;
        this.headerBgColorLeft = data.headerBgColor.color[0].item;
        this.headerBgColorRight = data.headerBgColor.color[1].item;
        this.titleConfig = data.titleConfig.tabVal;
        this.titleTxtConfig = data.titleTxtConfig.value;
        let bgColorLeft = data.moduleColor.color[0].item;
        let bgColorRight = data.moduleColor.color[1].item;
        this.bgColor = `linear-gradient(90deg,${bgColorLeft} 0%,${bgColorRight} 100%)`;
        this.bottomBgColor = data.bottomBgColor.color[0].item;
        this.mTop = data.mbConfig.val;
        this.topConfig = data.topConfig.val;
        this.bottomConfig = data.bottomConfig.val;
        this.prConfig = data.prConfig.val;
        let tabVal = data.titleText.tabVal;
        this.titleTabVal = tabVal;
        this.titleText = data.titleText.tabList[tabVal].style;
        this.checkboxInfo = data.checkboxInfo.type;
        let filletImg = data.filletImg.type;
        let filletValImg = data.filletImg.val;
        let valListImg = data.filletImg.valList;
        this.imgRadius = filletImg
          ? valListImg[0].val + 'px ' + valListImg[1].val + 'px ' + valListImg[3].val + 'px ' + valListImg[2].val + 'px'
          : filletValImg + 'px';
        let fillet = data.fillet.type;
        let filletVal = data.fillet.val;
        let valList = data.fillet.valList;
        this.bgRadius = fillet
          ? valList[0].val + 'px ' + valList[1].val + 'px 0 0'
          : filletVal + 'px ' + filletVal + 'px 0 0';
        this.bgRadius2 = fillet
          ? '0 0 ' + valList[3].val + 'px ' + valList[2].val + 'px'
          : '0 0 ' + filletVal + 'px ' + filletVal + 'px';
        let goodsTabVal = data.goodsName.tabVal;
        this.goodsName = data.goodsName.tabList[goodsTabVal].style;
        this.goodsNameColor = data.goodsNameColor.color[0].item;
        this.goodsPriceColor = data.goodsPriceColor.color[0].item;
        this.toneConfig = data.toneConfig.tabVal;
        this.goodsBntColorLeft = data.goodsBntColor.color[0].item;
        this.goodsBntColorRight = data.goodsBntColor.color[1].item;
        this.goodStyleConfig = data.goodStyleConfig.tabVal;
        this.goodsBntTxtColor = data.goodsBntTxtColor.color[0].item;
        this.bargainConfig = data.bargainConfig.tabVal;
        this.numberConfig = data.numberConfig.val;
        this.titleColor = data.titleColor.color[0].item;
        this.titleNumber = data.titleNumber.val;
        this.joinNumColor = data.styleConfig.tabVal
          ? data.joinNumColor.color[0].item
          : data.joinNumColor2.color[0].item;
        this.joinNumColor2 = data.joinNumColor.color[0].item;
        this.bargainPriceColor = data.bargainPriceColor.color[0].item;
        this.joinBgColorLeft = data.joinBgColor.color[0].item;
        this.joinBgColorRight = data.joinBgColor.color[1].item;
        this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
        this.themeColor2 = `linear-gradient(270deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.seckill-box {
  background: #fff;
  .hd {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    width: 100%;
    height: 48px;
    padding: 0 12px;
    .right {
      color: #fff;
      font-size: 12px;
      .iconfont {
        font-size: 12px;
      }
    }
    .left {
      display: flex;
      align-items: center;
      .text {
        font-size: 16px;
      }
      .line {
        width: 1px;
        height: 14px;
        background: #dddddd;
        margin: 0 10px;
      }
      img {
        width: 70px;
        height: 16px;
      }
      .tips {
        font-size: 13px;
        color: #fff;
        font-weight: 400;
      }
    }
  }
  .list-wrapper {
    display: flex;
    justify-content: center;
    overflow: hidden;
    padding: 10px;
    width: 100%;
    &.on {
      display: block;
    }
    &.on2 {
      flex-wrap: wrap;
      justify-content: flex-start;
    }
    &.on3 {
      justify-content: flex-start;
      padding-right: 0;
    }
    .itemTwo,
    .itemThree {
      width: 48%;
      position: relative;
      margin-right: 11px;
      margin-top: 15px;

      .item {
        height: 50px;
      }

      .item2 {
        height: 30px;
      }

      &:nth-child(1) {
        margin-top: 0;
      }

      &:nth-child(2) {
        margin-top: 0;
      }

      &:nth-of-type(2n) {
        margin-right: 0;
      }

      .empty-box {
        width: 100%;
        height: 162px;
        background-color: #f3f9ff;
        img {
          width: 64px;
          height: 50px;
          display: block;
        }
      }
      .title {
        font-size: 14px;
        color: #333333;
        margin-top: 8px;
      }
      .price {
        font-weight: 600;
        font-size: 12px;
        &.on {
          margin-top: 8px;
        }
        .num {
          font-size: 15px;
        }
      }
      .yprice {
        font-size: 11px;
        text-decoration: line-through;
        &.on {
          margin-top: 9px;
        }
      }
      .bnt {
        width: 57px;
        height: 26px;
        border-radius: 13px;
        text-align: center;
        line-height: 26px;
        position: absolute;
        right: 0;
        bottom: 0;
        font-size: 12px;
        color: #ffffff;
        &.on {
          bottom: -4px;
        }
      }
    }

    .itemThree {
      width: 112px;
      margin-top: 0;
      margin-right: 10px !important;
      .item {
        height: 45px;
      }
      .item2 {
        height: 29px;
      }
      .empty-box {
        height: 112px;
        width: 112px;
      }
      .title {
        font-size: 13px;
        margin-top: 6px;
      }
      .joinNum {
        width: 76px;
        height: 13px;
        border-radius: 7px;
        font-size: 10px;
        text-align: center;
        line-height: 13px;
        margin-top: 3px;
      }
      .price {
        font-size: 11px;
        margin-top: 3px;
        &.on {
          margin-top: 6px;
        }
      }
    }

    .itemOne {
      position: relative;

      & ~ .itemOne {
        margin-top: 15px;
      }
      .empty-box {
        width: 140px;
        height: 140px;
        margin-right: 10px;
        background-color: #f3f9ff;
        img {
          width: 64px;
          height: 50px;
          display: block;
        }
      }
      .text {
        flex: 1;
        .top {
          height: 98px;
          .num {
            font-size: 12px;
            color: #e93323;
            .iconfont {
              font-size: 12px;
              margin-right: 2px;
            }
          }
        }
        .bottom {
          margin-top: 8px;
          height: 40px;
        }
        .name {
          font-size: 14px;
          color: #333333;
          margin-bottom: 9px;
        }
        .price {
          font-size: 12px;
          color: #e93323;
          .label {
            margin-right: 2px;
          }
          .num {
            font-size: 16px;
            font-weight: 500;
            font-family: SemiBold;
          }
        }
        .yprice {
          color: #999999;
          font-size: 11px;
          text-decoration: line-through;
        }
        .bnt {
          width: 72px;
          height: 28px;
          background: linear-gradient(90deg, #ff7931 0%, #e93323 100%);
          border-radius: 25px;
          text-align: center;
          line-height: 28px;
          color: #ffffff;
          font-size: 12px;
          position: absolute;
          right: 0;
          bottom: 0;
        }
      }
    }

    .list-item {
      width: 31.47%;
      margin-top: 10px;

      & ~ .list-item {
        margin-left: 9px;
      }

      &:nth-of-type(3n-2) {
        margin-left: 0;
      }

      &:nth-child(1),
      &:nth-child(2),
      &:nth-child(3) {
        margin-top: 0;
      }

      .img-box {
        border-radius: 6px;
        position: relative;
        width: 100%;
        height: 106px;

        .empty-box {
          background-color: #f3f9ff;
          img {
            width: 65px;
            height: 50px;
            display: block;
          }
        }
      }
      .title {
        margin-top: 8px;
        font-size: 13px;
        color: #333;
      }
      .price {
        position: relative;
        color: #fff;
        font-size: 11px;

        .lable {
          font-size: 11px;
          font-weight: 600;
          margin-left: 2px;
        }

        .num {
          font-size: 15px;
          font-weight: 600;
        }

        img {
          width: 12px;
          height: 22px;
          display: block;
          position: absolute;
          left: -4px;
          top: 0;
        }
      }
      .yprice {
        color: #999;
        font-size: 12px;
        text-decoration: line-through;
      }
    }
  }
}
</style>
