<template>
  <!-- Banner trình chiếu có nền -->
  <common-wrapper :config="configData">
    <view class="swiperBg">
      <template v-if="dataConfig.swiperConfig.list.length">
        <view class="swiper">
          <swiper
            :style="'height:' + imageH + 'rpx;'"
            :autoplay="true"
            :previous-margin="swiperMargin"
            :next-margin="swiperMargin"
            :circular="circular"
            :interval="interval"
            :duration="duration"
            @change="bannerfun"
            v-if="imageH"
          >
            <swiper-item
              v-for="(item, index) in dataConfig.swiperConfig.list"
              :key="index"
            >
              <view
                @click="goDetail(item)"
                class="swiper-item"
                :style="[itemStyle, active == index ? activeStyle : '']"
              >
                <image
                  :src="item.img"
                  mode="aspectFill"
                  class="image"
                  :style="[imageStyle]"
                ></image>
              </view>
            </swiper-item>
          </swiper>
          <view class="noPic" v-else>{{ $t(`Đang tải ảnh`) }}...</view>
          <view class="dot acea-row" :style="[dotStyle]">
            <view
              class="progress"
              v-if="dataConfig.docConfig.tabVal == 2"
              :style="[progressWidth, dotItemStyle]"
            >
              <view
                class="inner"
                :style="[progressValue, dotItemActiveStyle]"
              ></view>
            </view>
            <view
              class="acea-row"
              :class="{
                small: dataConfig.docConfig.tabVal == 1,
                line: dataConfig.docConfig.tabVal == 3,
              }"
              v-else
            >
              <view
                class="dot-item"
                v-for="(item, index) in dataConfig.swiperConfig.list"
                :key="index"
                :class="{ active: active == index }"
                :style="[active == index ? dotItemActiveStyle : dotItemStyle]"
              ></view>
            </view>
          </view>
        </view>
      </template>
    </view>
  </common-wrapper>
</template>

<script>
import commonWrapper from "./commonWrapper.vue";
export default {
  components: { commonWrapper },
  name: "swiperBg",
  props: {
    dataConfig: {
      type: Object,
      default: () => {},
    },
    isSortType: {
      type: String | Number,
      default: 0,
    },
  },
  data() {
    return {
      circular: true,
      autoplay: true,
      interval: 3000,
      duration: 500,
      imgUrls: [], //Dữ liệu banner trình chiếu ảnh
      bgColor: "", //Màu nền banner trình chiếu
      marginTop: 0, //Margin trên của component
      paddinglr: 0, //Margin trái phải của banner trình chiếu
      docConfig: 0, //Style chấm chỉ báo
      imgConfig: 0, //Có phải góc tròn hay không
      imageH: 0,
      isColor: 0,
      txtStyle: 0,
      dotColor: "",
      current: 1, //Chỉ báo dạng số hiện tại
      active: 0, //Chỉ báo thông thường hiện tại
      swiperMargin: "",
    };
  },
  computed: {
    configData() {
      return {
        ...this.dataConfig,
        paddingConfig: this.dataConfig.paddingConfig || {
          isAll: false,
          valList: [
            {
              val: this.dataConfig.topConfig
                ? this.dataConfig.topConfig.val
                : 0,
            },
            {
              val: this.dataConfig.prConfig ? this.dataConfig.prConfig.val : 0,
            },
            {
              val: this.dataConfig.bottomConfig
                ? this.dataConfig.bottomConfig.val
                : 0,
            },
            {
              val: this.dataConfig.prConfig ? this.dataConfig.prConfig.val : 0,
            },
          ],
        },
        marginConfig: this.dataConfig.marginConfig || {
          isAll: false,
          valList: [
            {
              val: this.dataConfig.mbConfig ? this.dataConfig.mbConfig.val : 0,
            },
            {
              val: 0,
            },
            {
              val: 0,
            },
            {
              val: 0,
            },
          ],
        },
      };
    },
    itemStyle() {
      let val = this.dataConfig.imgConfig.val;
      let num = 1;
      if (this.dataConfig.styleConfig.tabVal == 1) {
        num = !val ? 1 : 0.9;
      }
      return {
        transform: `scale(${num})`,
      };
    },
    activeStyle() {
      let val = this.dataConfig.imgConfig.val;
      let num = 1;
      if (this.dataConfig.styleConfig.tabVal == 1) {
        num = !val ? 1 : 1 - val / 400;
      }
      return {
        transform: `scale(${num})`,
      };
    },
    // colorBgStyle() {
    //   let styleObject = {
    //     background: this.dataConfig.bgColor.color[0].item,
    //   };
    //   if (this.dataConfig.styleConfig.tabVal == 1) {
    //     styleObject["height"] = "50%";
    //   }
    //   return styleObject;
    // },
    imageStyle() {
      let borderRadius = `${this.dataConfig.filletImg.val * 2}rpx`;
      if (this.dataConfig.filletImg.type) {
        borderRadius = `${this.dataConfig.filletImg.valList[0].val * 2}rpx ${
          this.dataConfig.filletImg.valList[1].val * 2
        }rpx ${this.dataConfig.filletImg.valList[3].val * 2}rpx ${
          this.dataConfig.filletImg.valList[2].val * 2
        }rpx`;
      }
      return {
        borderRadius: borderRadius,
      };
    },
    dotStyle() {
      let styleObject = {};
      if (this.dataConfig.docPosition.tabVal) {
        styleObject["justify-content"] =
          this.dataConfig.docPosition.tabVal == 1 ? "center" : "flex-end";
      }
      if (this.dataConfig.styleConfig.tabVal == 1) {
        styleObject["padding"] = "0 100rpx";
        styleObject["bottom"] = "32rpx";
      }
      return styleObject;
    },
    dotItemStyle() {
      let styleObject = {};
      if (this.dataConfig.toneConfig.tabVal) {
        styleObject["background"] = this.dataConfig.dotBgColor.color[0].item;
      }
      return styleObject;
    },
    dotItemActiveStyle() {
      let styleObject = {};
      if (this.dataConfig.toneConfig.tabVal) {
        styleObject["background"] = this.dataConfig.dotColor.color[0].item;
      }
      return styleObject;
    },
    progressWidth() {
      return {
        width: `${this.dataConfig.swiperConfig.list.length * 20}rpx`,
      };
    },
    progressValue() {
      return {
        width: `${
          (this.current / this.dataConfig.swiperConfig.list.length) * 100
        }%`,
      };
    },
  },
  watch: {
    imageH(nVal, oVal) {
      let self = this;
      this.imageH = nVal;
    },
  },
  created() {
    this.imgUrls = this.dataConfig.swiperConfig.list;
    if (this.dataConfig.styleConfig.tabVal == 1) {
      this.swiperMargin = "55rpx";
    }
  },
  mounted() {
    if (this.imgUrls.length) {
      let that = this;
      this.$nextTick((e) => {
        uni.getImageInfo({
          src: that.setDomain(that.imgUrls[0].img),
          success: (res) => {
            if (res && res.height > 0) {
              let p = this.dataConfig.paddingConfig.isAll
                ? this.dataConfig.paddingConfig.val
                : this.dataConfig.paddingConfig.valList[1].val;
              let height = res.height * ((750 - p * 4) / res.width);
              that.$set(that, "imageH", height);
            } else {
              that.$set(that, "imageH", 375);
            }
          },
          fail: function (error) {
            that.$set(that, "imageH", 375);
          },
        });
      });
    }
  },
  methods: {
    bannerfun(e) {
      this.active = e.detail.current;
      this.current = e.detail.current + 1;
    },
    //Thay domain an toàn
    setDomain: function (url) {
      url = url ? url.toString() : "";
      //Mở khi debug local, khi lên production hãy comment lại
      if (url.indexOf("https://") > -1) return url;
      else return url.replace("http://", "https://");
    },
    goDetail(url) {
      let urls = url.info[0].value;
      this.$util.JumpPath(urls);
    },
  },
};
</script>

<style lang="scss">
.noPic {
  border-radius: 10rpx;
  width: 100%;
  height: 300rpx;
  background-color: #f0f0f0;
  color: #ccc;
  text-align: center;
  line-height: 300rpx;
  font-size: 30rpx;
}

.swiperBg {
  position: relative;

  .colorBg {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 100%;
  }

  .swiper {
    z-index: 20;
    position: relative;
    overflow: hidden;

    .dot {
      position: absolute;
      bottom: 20rpx;
      left: 0;
      width: 100%;
      padding: 0 20rpx;

      .dot-item {
        width: 12rpx;
        height: 12rpx;
        border-radius: 6rpx;
        margin-right: 16rpx;
        background: #dddddd;

        &:last-child {
          margin-right: 0;
        }

        &.active {
          background: var(--view-theme);
        }
      }

      .small {
        .dot-item {
          width: 10rpx;
          height: 10rpx;
          border-radius: 5rpx;
          margin-right: 8rpx;

          &.active {
            width: 18rpx;
          }
        }
      }

      .line {
        .dot-item {
          width: 20rpx;
          height: 6rpx;
          border-radius: 3rpx;
          margin-right: 10rpx;
        }
      }

      .progress {
        width: 60rpx;
        height: 6rpx;
        border-radius: 3rpx;
        background: #dddddd;

        .inner {
          width: 33%;
          height: 6rpx;
          border-radius: 3rpx;
          background: var(--view-theme);
          transition: 0.3s;
        }
      }
    }

    .swiper-item {
      width: 100%;
      height: 100%;
      transform: scale(0.9);
      transition: 0.3s;

      // &.active {
      // 	transform: scale(1);
      // }
    }

    .image {
      width: 100%;
      height: 100%;
    }

    // Chấm chỉ báo hình tròn
    &.circular {
      ::v-deep.uni-swiper-dot {
        width: 10rpx !important;
        height: 10rpx !important;
        background: rgba(0, 0, 0, 0.4) !important;
      }

      ::v-deep.uni-swiper-dot-active {
        background: #fff !important;
      }
    }

    // Chấm chỉ báo hình vuông
    &.square {
      ::v-deep.uni-swiper-dot {
        width: 20rpx !important;
        height: 5rpx !important;
        border-radius: 3rpx;
        background: rgba(0, 0, 0, 0.4) !important;
      }

      ::v-deep.uni-swiper-dot-active {
        background: #fff !important;
      }
    }
  }
}
</style>
