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
          <div class="pictrue">
            <img src="../../assets/images/pinkHead.png" />
          </div>
          <div
            class="tips"
            :style="{
              color: styleConfig ? tipsColor : tipsColor2,
            }"
          >
            134 người tham gia
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
                Cam hạng sang nhãn đen New Knight vụ muộn Cam hạng sang nhãn đen New Knight vụ muộn...
              </div>
              <div
                class="label"
                v-if="checkboxInfo.indexOf(1) != -1"
                :style="{
                  background: toneConfig ? labelColor : colorStyle.theme,
                  color: toneConfig ? labelColor : colorStyle.theme,
                }"
              >
                <div class="labelBg">
                  <div
                    class="num"
                    :style="{
                      background: toneConfig ? labelColor : colorStyle.theme,
                    }"
                  >
                    Nhóm 2 người
                  </div>
                  Đã mua chung 148 suất
                </div>
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
                  color: toneConfig ? pinkPriceColor : colorStyle.theme,
                }"
              >
                Giá mua chung<span class="label">¥</span><span class="num">3200.00</span>
              </div>
              <div
                class="yprice"
                v-if="checkboxInfo.indexOf(3) != -1"
                :style="{
                  color: goodsPriceColor,
                }"
              >
                <span class="num line-through">¥4233</span>
              </div>
            </div>
            <div
              class="bnt"
              v-if="!pinkConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              Mua chung ngay
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
              (checkboxInfo.indexOf(0) != -1 && checkboxInfo.length == 1 && !pinkConfig) ||
              (checkboxInfo.indexOf(0) != -1 &&
                checkboxInfo.indexOf(1) != -1 &&
                checkboxInfo.length == 2 &&
                !pinkConfig)
                ? 'item'
                : (!checkboxInfo.length || (checkboxInfo.indexOf(1) != -1 && checkboxInfo.length == 1)) && !pinkConfig
                ? 'item2'
                : ''
            "
          >
            <div
              class="title acea-row row-middle"
              :style="{
                fontWeight: goodsName,
                color: goodsNameColor,
              }"
            >
              <div
                class="label"
                v-if="checkboxInfo.indexOf(1) != -1"
                :style="{
                  background: toneConfig ? labelColor : colorStyle.theme,
                  color: toneConfig ? labelColor : colorStyle.theme,
                }"
              >
                <div class="labelBg">Nhóm 5 người</div>
              </div>
              <div class="name line1" v-if="checkboxInfo.indexOf(0) != -1">Cam hạng sang nhãn đen New Knight...</div>
            </div>
            <div
              class="price"
              :class="checkboxInfo.indexOf(3) == -1 && !pinkConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(2) != -1"
              :style="{
                color: toneConfig ? pinkPriceColor : colorStyle.theme,
              }"
            >
              ¥<span class="num">3200.00</span>
            </div>
            <div
              class="yprice"
              :class="checkboxInfo.indexOf(2) == -1 && !pinkConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(3) != -1"
              :style="{
                color: goodsPriceColor,
              }"
            >
              ¥3699.00
            </div>
            <div
              class="bnt"
              :class="checkboxInfo.indexOf(2) == -1 && !pinkConfig ? 'on' : ''"
              v-if="!pinkConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              Mua chung ngay
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
              <div
                class="label"
                v-if="checkboxInfo.indexOf(1) != -1"
                :style="{
                  background: toneConfig ? labelColor : colorStyle.theme,
                  color: toneConfig ? labelColor : colorStyle.theme,
                }"
              >
                <div class="labelBg">Nhóm 5 người</div>
              </div>
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
            Cam hạng sang nhãn đen...
          </div>
          <div
            class="price"
            v-if="checkboxInfo.indexOf(2) != -1"
            :style="{
              color: toneConfig ? pinkPriceColor : colorStyle.theme,
            }"
          >
            <span>¥</span>3500.00
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
            <div
              class="label"
              v-if="checkboxInfo.indexOf(1) != -1"
              :style="{
                background: toneConfig ? labelColor : colorStyle.theme,
                color: toneConfig ? labelColor : colorStyle.theme,
              }"
            >
              <div class="labelBg">Nhóm 5 người</div>
            </div>
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
              Cam hạng sang nhãn đen New Knight...
            </div>
            <div
              class="price"
              :class="checkboxInfo.indexOf(3) == -1 && !pinkConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(2) != -1"
              :style="{
                color: toneConfig ? pinkPriceColor : colorStyle.theme,
              }"
            >
              ¥<span class="num">3200.00</span>
            </div>
            <div
              class="yprice"
              :class="checkboxInfo.indexOf(2) == -1 && !pinkConfig ? 'on' : ''"
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
  name: 'home_pink',
  cname: 'Mua chung',
  icon: '#iconzujian-pintuan',
  configName: 'c_home_pink',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'combination', // Tên khớp bên ngoài
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
        cname: 'Mua chung',
        name: 'combination',
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
          url: Setting.apiBaseURL.replace(/adminapi/, '') + 'statics/images/pinkBg.png',
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
          url: require('@/assets/images/pink02.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh tiêu đề',
        },
        imgColorConfig: {
          info: 'Đề xuất: 154px * 32px',
          url: require('@/assets/images/pink01.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh tiêu đề',
        },
        titleTxtConfig: {
          title: 'Chữ tiêu đề',
          value: 'Mua chung giá hời',
          place: 'Vui lòng nhập chữ tiêu đề',
          max: 6,
        },
        rightBntConfig: {
          title: 'Nút bên phải',
          value: 'Xem thêm',
          place: 'Vui lòng nhập chữ nút bên phải',
          max: 6,
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
              name: 'Nhãn sản phẩm',
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
        pinkConfig: {
          title: 'Nút mua chung',
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
              item: '#333333',
            },
          ],
          color: [
            {
              item: '#333333',
            },
          ],
        },
        titleNumber: {
          title: 'Cỡ chữ tiêu đề',
          val: 16,
          min: 0,
        },
        labelColor: {
          title: 'Màu nhãn',
          name: 'labelColor',
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
        pinkPriceColor: {
          title: 'Giá mua chung',
          name: 'pinkPriceColor',
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
      pinkConfig: 0,
      numberConfig: 1,
      titleColor: '',
      titleNumber: 0,
      labelColor: '',
      pinkPriceColor: '',
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
        this.imgUrl = data.imgConfig.url;
        this.imgBgUrl = data.imgBgConfig.url;
        this.imgColorUrl = data.imgColorConfig.url;
        this.tipsColor = data.tipsColor.color[0].item;
        this.tipsColor2 = data.tipsColor2.color[0].item;
        this.dividerColor = data.dividerColor.color[0].item;
        this.rightBntTxt = data.rightBntConfig.value;
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
        this.pinkConfig = data.pinkConfig.tabVal;
        this.numberConfig = data.numberConfig.val;
        this.titleColor = data.titleColor.color[0].item;
        this.titleNumber = data.titleNumber.val;
        this.labelColor = data.labelColor.color[0].item;
        this.pinkPriceColor = data.pinkPriceColor.color[0].item;
        this.themeColor = `linear-gradient(90deg,${this.colorStyle.theme} 0%,${this.colorStyle.gradient} 100%)`;
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
        margin-right: 8px;
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
      .pictrue {
        width: 43px;
        height: 18px;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .tips {
        font-size: 13px;
        color: #fff;
        font-weight: 400;
        margin-left: 6px;
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
        height: 20px;
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
        .name {
          flex: 1;
        }
        .label {
          width: 40px;
          height: 15px;
          border-radius: 3px;
          margin-right: 5px;
          .labelBg {
            width: 100%;
            height: 100%;
            text-align: center;
            line-height: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            font-size: 11px;
            border-radius: 2px;
          }
        }
      }
      .price {
        font-weight: 600;
        font-size: 12px;
        &.on {
          margin-top: 8px;
        }
        .num {
          font-size: 16px;
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
        position: relative;
        .label {
          width: 42px;
          height: 15px;
          border-radius: 8px;
          font-size: 11px;
          text-align: center;
          line-height: 15px;
          left: 5px;
          top: 5px;
          position: absolute;
          .labelBg {
            background-color: rgba(255, 255, 255, 0.9);
          }
        }
      }
      .title {
        font-size: 13px;
        margin-top: 6px;
      }
      .price {
        font-size: 11px;
        height: 20px;
        &.on {
          margin-top: 1px;
        }
      }
      .yprice {
        &.on {
          margin-top: 1px;
        }
      }
    }

    .itemOne {
      position: relative;

      & ~ .itemOne {
        margin-top: 15px;
      }
      .empty-box {
        width: 120px;
        height: 120px;
        margin-right: 12px;
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
          height: 78px;
          .label {
            width: 96px;
            font-size: 11px;
            border-radius: 3px;
            .labelBg {
              display: flex;
              background-color: rgba(255, 255, 255, 0.9);
              border-radius: 3px;
              .num {
                border-radius: 3px 0 0 3px;
                color: #fff;
                width: 37px;
                text-align: center;
                margin-right: 4px;
              }
            }
          }
        }
        .bottom {
          height: 42px;
        }
        .name {
          font-size: 14px;
          color: #333333;
          margin-bottom: 6px;
        }
        .price {
          font-size: 12px;
          color: #e93323;
          .label {
            font-weight: 600;
            margin-left: 4px;
          }
          .num {
            font-size: 16px;
            font-weight: 600;
          }
        }
        .yprice {
          color: #999999;
          font-size: 12px;
          .num {
            // margin-left: 4px;
          }
        }
        .bnt {
          width: 60px;
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
          position: relative;
          img {
            width: 65px;
            height: 50px;
            display: block;
          }
          .label {
            width: 42px;
            height: 15px;
            border-radius: 8px;
            font-size: 11px;
            text-align: center;
            line-height: 15px;
            left: 5px;
            top: 5px;
            position: absolute;
            .labelBg {
              background-color: rgba(255, 255, 255, 0.9);
            }
          }
        }
      }
      .title {
        margin-top: 8px;
        font-size: 13px;
        color: #333;
      }
      .price {
        line-height: 22px;
        position: relative;
        border-radius: 0 4px 4px 0;
        font-size: 16px;
        margin-top: 1px;
        font-weight: 600;
        height: 20px;

        span {
          font-size: 11px;
          margin-right: 2px;
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
