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
            class="tips"
            :style="{
              color: styleConfig ? tipsColor : tipsColor2,
            }"
          >
            Kết thúc sau
          </div>
          <div class="time">
            <span
              :style="{
                background: styleConfig ? numberBgColor : numberBgColor2,
                color: styleConfig ? numberColor : numberColor2,
              }"
              >17</span
            >
            <em
              :style="{
                color: styleConfig ? numberBgColorLeft : numberBgColorLeft2,
              }"
              >:</em
            >
            <span
              :style="{
                background: styleConfig ? numberBgColor : numberBgColor2,
                color: styleConfig ? numberColor : numberColor2,
              }"
              >32</span
            >
            <em
              :style="{
                color: styleConfig ? numberBgColorLeft : numberBgColorLeft2,
              }"
              >:</em
            >
            <span
              :style="{
                background: styleConfig ? numberBgColor : numberBgColor2,
                color: styleConfig ? numberColor : numberColor2,
              }"
              >45</span
            >
          </div>
        </div>
        <div
          class="right"
          :style="{
            color: styleConfig ? headerBntColor : headerBntColor2,
            fontSize: bntNumber + 'px',
          }"
        >
          {{ rightBntTxt
          }}<span
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
                Cam hạng sang nhãn đen New Knight vụ muộn, thứ bạn muốn sở hữu nhất
              </div>
              <div
                class="progressBg"
                v-if="checkboxInfo.indexOf(1) != -1"
                :style="{
                  background: toneConfig
                    ? `linear-gradient(45deg,${progressColorLeft} 0%,${progressColorRight} 100%)`
                    : themeColor2,
                }"
              >
                <div class="progressBar">
                  <div
                    class="progress"
                    :style="{
                      background: toneConfig
                        ? `linear-gradient(45deg,${progressColorLeft} 0%,${progressColorRight} 100%)`
                        : themeColor2,
                    }"
                  ></div>
                  <img src="../../assets/images/dian2.png" />
                </div>
                <div
                  class="progressTxt"
                  :style="{
                    color: toneConfig ? progressTxtColor : colorStyle.theme,
                  }"
                >
                  Đã bán 33%
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
                  color: toneConfig ? seckillPriceColor : colorStyle.theme,
                }"
              >
                Giá flash sale<span class="label">¥</span><span class="num">3200.00</span>
              </div>
              <div
                class="yprice"
                v-if="checkboxInfo.indexOf(3) != -1"
                :style="{
                  color: goodsPriceColor,
                }"
              >
                ¥1233.00
              </div>
            </div>
            <div
              class="bnt"
              v-if="!seckillConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              Mua ngay
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
              (checkboxInfo.indexOf(0) != -1 && checkboxInfo.length == 1 && !seckillConfig) ||
              (checkboxInfo.indexOf(0) != -1 &&
                checkboxInfo.indexOf(1) != -1 &&
                checkboxInfo.length == 2 &&
                !seckillConfig)
                ? 'item'
                : (!checkboxInfo.length || (checkboxInfo.indexOf(1) != -1 && checkboxInfo.length == 1)) &&
                  !seckillConfig
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
              Cam hạng sang nhãn đen New Knight...
            </div>
            <div
              class="price"
              :class="checkboxInfo.indexOf(3) == -1 && !seckillConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(2) != -1"
              :style="{
                color: toneConfig ? seckillPriceColor : colorStyle.theme,
              }"
            >
              ¥<span class="num">3200.00</span>
            </div>
            <div
              class="yprice"
              :class="checkboxInfo.indexOf(2) == -1 && !seckillConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(3) != -1"
              :style="{
                color: goodsPriceColor,
              }"
            >
              ¥3699.00
            </div>
            <div
              class="bnt"
              :class="checkboxInfo.indexOf(2) == -1 && !seckillConfig ? 'on' : ''"
              v-if="!seckillConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              Mua ngay
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
            Cam hạng sang nhãn đen...
          </div>
          <div
            class="price"
            v-if="checkboxInfo.indexOf(2) != -1"
            :style="{
              color: toneConfig ? seckillPriceColor2 : '#fff',
              background: toneConfig
                ? `linear-gradient(90deg,${goodsBntColorLeft} 0%,${goodsBntColorRight} 100%)`
                : themeColor2,
            }"
          >
            <img src="../../assets/images/dian.png" /><span>¥</span>350.00
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
          <div
            :class="
              (checkboxInfo.indexOf(0) != -1 && checkboxInfo.length == 1 && !seckillConfig) ||
              (checkboxInfo.indexOf(0) != -1 &&
                checkboxInfo.indexOf(1) != -1 &&
                checkboxInfo.length == 2 &&
                !seckillConfig)
                ? 'item'
                : (!checkboxInfo.length || (checkboxInfo.indexOf(1) != -1 && checkboxInfo.length == 1)) &&
                  !seckillConfig
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
              Cam hạng sang nhãn đen New Knight...
            </div>
            <div
              class="price"
              :class="checkboxInfo.indexOf(3) == -1 && !seckillConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(2) != -1"
              :style="{
                color: toneConfig ? seckillPriceColor : colorStyle.theme,
              }"
            >
              ¥<span class="num">3200.00</span>
            </div>
            <div
              class="yprice"
              :class="checkboxInfo.indexOf(2) == -1 && !seckillConfig ? 'on' : ''"
              v-if="checkboxInfo.indexOf(3) != -1"
              :style="{
                color: goodsPriceColor,
              }"
            >
              ¥3699.00
            </div>
            <div
              class="bnt"
              :class="checkboxInfo.indexOf(3) == -1 && !seckillConfig ? 'on2' : ''"
              v-if="!seckillConfig"
              :style="{
                color: toneConfig ? goodsBntTxtColor : '#fff',
                background: toneConfig
                  ? `linear-gradient(90deg,${goodsBntColorRight} 0%,${goodsBntColorLeft} 100%)`
                  : themeColor,
              }"
            >
              <div class="bntCon">
                Mua
                <img src="../../assets/images/dian.png" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
// import theme from "@/mixins/theme";
import Setting from '@/setting';
export default {
  name: 'home_seckill',
  cname: 'Flash sale',
  configName: 'c_home_seckill',
  icon: '#iconzujian-miaosha',
  type: 1, // 0 thành phần cơ bản 1 thành phần marketing 2 thành phần công cụ
  defaultName: 'seckill', // Tên khớp bên ngoài
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
        cname: 'Flash sale',
        name: 'seckill',
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
          url: Setting.apiBaseURL.replace(/adminapi/, '') + 'statics/images/seckillBg.png',
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
          url: require('@/assets/images/seckill02.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh tiêu đề',
        },
        imgColorConfig: {
          info: 'Đề xuất: 154px * 32px',
          url: require('@/assets/images/seckill01.png'),
          type: 'code',
          delType: 0,
          name: 'Ảnh tiêu đề',
        },
        titleTxtConfig: {
          title: 'Chữ tiêu đề',
          value: 'Flash sale giờ vàng',
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
              name: 'Tiến độ flash sale',
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
        seckillConfig: {
          title: 'Nút flash sale',
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
          title: 'Kết thúc sau',
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
          title: 'Kết thúc sau',
          name: 'tipsColor2',
          default: [
            {
              item: '#666',
            },
          ],
          color: [
            {
              item: '#666',
            },
          ],
        },
        numberBgColor: {
          title: 'Nền chữ số',
          name: 'numberBgColor',
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
        numberBgColor2: {
          title: 'Nền chữ số',
          name: 'numberBgColor2',
          default: [
            {
              item: '#E93323',
            },
            {
              item: '#E93323',
            },
          ],
          color: [
            {
              item: '#E93323',
            },
            {
              item: '#E93323',
            },
          ],
        },
        numberColor: {
          title: 'Số',
          name: 'numberColor',
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
        numberColor2: {
          title: 'Số',
          name: 'numberColor2',
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
        seckillPriceColor: {
          title: 'Giá flash sale',
          name: 'seckillPriceColor',
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
        seckillPriceColor2: {
          title: 'Giá flash sale',
          name: 'seckillPriceColor2',
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
        progressColor: {
          title: 'Màu thanh tiến độ',
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
        progressTxtColor: {
          title: 'Tiến độ flash sale',
          name: 'progressTxtColor',
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
      numberBgColorLeft: '',
      numberBgColor: '',
      numberBgColorLeft2: '',
      numberBgColor2: '',
      numberColor: '',
      numberColor2: '',
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
      seckillPriceColor: '',
      seckillPriceColor2: '',
      toneConfig: 0,
      goodsBntColorLeft: '',
      goodsBntColorRight: '',
      goodStyleConfig: 0,
      goodsBntTxtColor: '',
      seckillConfig: 0,
      progressColorLeft: '',
      progressColorRight: '',
      numberConfig: 1,
      titleColor: '',
      titleNumber: 0,
      progressTxtColor: '',
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
        let numberBgColorLeft = data.numberBgColor.color[0].item;
        let numberBgColorRight = data.numberBgColor.color[1].item;
        this.numberBgColorLeft = numberBgColorLeft;
        this.numberBgColor = `linear-gradient(90deg,${numberBgColorLeft} 0%,${numberBgColorRight} 100%)`;
        let numberBgColorLeft2 = data.numberBgColor2.color[0].item;
        let numberBgColorRight2 = data.numberBgColor2.color[1].item;
        this.numberBgColorLeft2 = numberBgColorLeft2;
        this.numberBgColor2 = `linear-gradient(90deg,${numberBgColorLeft2} 0%,${numberBgColorRight2} 100%)`;
        this.numberColor = data.numberColor.color[0].item;
        this.numberColor2 = data.numberColor2.color[0].item;
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
        this.seckillPriceColor = data.seckillPriceColor.color[0].item;
        this.seckillPriceColor2 = data.seckillPriceColor2.color[0].item;
        this.goodsBntColorLeft = data.goodsBntColor.color[0].item;
        this.goodsBntColorRight = data.goodsBntColor.color[1].item;
        this.goodStyleConfig = data.goodStyleConfig.tabVal;
        this.goodsBntTxtColor = data.goodsBntTxtColor.color[0].item;
        this.seckillConfig = data.seckillConfig.tabVal;
        this.progressColorLeft = data.progressColor.color[0].item;
        this.progressColorRight = data.progressColor.color[1].item;
        this.numberConfig = data.numberConfig.val;
        this.titleColor = data.titleColor.color[0].item;
        this.titleNumber = data.titleNumber.val;
        this.progressTxtColor = data.progressTxtColor.color[0].item;
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
        // margin-right: 8px;
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
      .time {
        display: flex;
        align-items: center;
        margin-left: 4px;
        color: #ff4444;
        span {
          width: 18px;
          height: 18px;
          font-size: 11px;
          text-align: center;
          line-height: 18px;
          background: #fff;
          border-radius: 2px;
        }
        em {
          font-size: 11px;
          margin: 0 3px;
          font-style: initial;
          font-weight: bold;
          color: #fff;
        }
      }
    }
  }
  .list-wrapper {
    display: flex;
    justify-content: center;
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
      overflow: hidden;
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
      .bnt {
        width: 33px;
        height: 20px;
        line-height: 20px;
        border-radius: 0 11px 11px 0;
        &.on2 {
          bottom: 3px;
        }
        .bntCon {
          position: relative;
          img {
            width: 11px;
            height: 20px;
            display: block;
            position: absolute;
            top: 0;
            left: -4px;
          }
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
        .progressBg {
          width: 120px;
          height: 12px;
          background: #fdf0ed;
          border-radius: 8px;
          position: relative;

          .progressBar {
            border-radius: 8px;
            overflow: hidden;
            background-color: rgba(255, 255, 255, 0.9);
          }

          .progressTxt {
            position: absolute;
            right: -50px;
            color: #e93323;
            font-size: 11px;
            top: -3px;
          }

          img {
            position: absolute;
            width: 17px;
            height: 17px;
            display: block;
            top: 50%;
            margin-top: -8.5px;
            left: 65px;
          }
          .progress {
            width: 75px;
            height: 12px;
            line-height: 12px;
            background: linear-gradient(45deg, #ff7931 0%, #e93323 100%);
            border-radius: 8px;
            font-size: 9px;
            color: #fff;
            padding-left: 7px;
          }
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
          text-decoration: line-through;
          .num {
            margin-left: 4px;
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
        width: 75px;
        height: 22px;
        line-height: 22px;
        position: relative;
        border-radius: 0 4px 4px 0;
        margin-top: 1px;
        font-weight: 500;
        height: 20px;
        font-size: 15px;
        padding-left: 13px;

        span {
          font-size: 12px;
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
