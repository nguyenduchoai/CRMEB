<template>
  <view
    class="aleart"
    v-if="aleartStatus"
    :style="'background-image: url(' + giftbag + ');'"
  >
    <template v-if="!posterImageStatus">
      <text class="iconfont icon-cha2 close" @click="posterImageClose"></text>
      <view class="from">Gửi tặng bạn bè một món quà</view>
      <view class="message">{{ giftData.message }}</view>
      <view class="aleart-body">
        <image class="goods-img" :src="giftData.image" mode=""></image>
      </view>
      <view class="title line1">
        {{ $t(giftData.title) }}
      </view>
      <!-- #ifdef H5 -->
      <view class="btn" @click="copyLink()">
        {{ $t("Sao chép liên kết quà tặng") }}
      </view>
      <!-- #endif -->
      <!-- #ifndef H5 -->
      <button class="btn" open-type="share" hover-class="none">
        {{ $t(`Tặng bạn bè`) }}
      </button>
      <!-- #endif -->
      <view class="btn-clear" @click="getPoster()">
        {{ $t("Lưu poster") }}
      </view>
    </template>
    <template v-if="posterImageStatus">
      <text class="iconfont icon-cha2 close" @click="posterImageClose"></text>
      <image class="poster-img" :src="posterImage"></image>
      <!-- #ifdef H5 -->
      <view class="keep">{{ $t(`Nhấn giữ ảnh để lưu vào điện thoại`) }}</view>
      <!-- #endif -->
    </template>
    <!-- #ifdef H5 || APP-PLUS -->
    <zb-code
      ref="qrcode"
      :show="codeShow"
      :cid="cid"
      :val="codeVal"
      :size="size"
      :unit="unit"
      :background="background"
      :foreground="foreground"
      :pdground="pdground"
      :icon="codeIcon"
      :iconSize="iconsize"
      :onval="onval"
      :loadMake="loadMake"
      @result="qrR"
    />
    <!-- #endif -->
  </view>
</template>

<script>
import { HTTP_REQUEST_URL } from "@/config/app";
export default {
  data() {
    return {
      aleartData: {},
      bag: HTTP_REQUEST_URL + "/statics/images/canvas-bag.png",
      giftBorder: HTTP_REQUEST_URL + "/statics/images/gift-border.png",
      giftbag: HTTP_REQUEST_URL + "/statics/images/gift-bag.png",
      //Tham số mã QR
      codeShow: false,
      cid: "1",
      codeVal: "", // Giá trị mã QR cần tạo
      size: 200, // Kích thước mã QR
      unit: "upx", // Đơn vị
      background: "#FFF", // Màu nền
      foreground: "#000", // Màu nền trước (foreground)
      pdground: "#000", // Màu badge góc
      codeIcon: "", // Icon mã QR
      iconsize: 40, // Kích thước icon mã QR
      lv: 3, // Mức chịu lỗi mã QR, thường không cần đặt, để mặc định là được
      onval: true, // Khi giá trị val thay đổi thì tự tạo lại mã QR
      loadMake: true, // Sau khi component tải xong thì tự tạo mã QR
      PromotionCode: "",
      posterImageStatus: false,
      posterImage: "",
    };
  },
  props: {
    giftData: {
      type: Object,
    },
    aleartStatus: {
      type: Boolean,
      default: false,
    },
  },
  watch: {
    aleartStatus(status) {
      if (!status) {
        this.aleartData = {};
      } else {
        // #ifdef H5
        this.codeVal =
          window.location.origin +
          "/pages/goods/receive_gift/index?id=" +
          this.giftData.id +
          "&spid=" +
          this.$store.state.app.uid;
        // #endif
        // #ifdef APP-PLUS
        this.codeVal =
          HTTP_REQUEST_URL +
          "/pages/goods/receive_gift/index?id=" +
          this.giftData.id +
          "&spid=" +
          this.$store.state.app.uid;
        // #endif
        // #ifdef MP
        this.PromotionCode = this.giftData.code;
        // #endif
      }
    },
  },
  methods: {
    copyLink() {
      uni.setClipboardData({
        data: this.codeVal,
      });
    },
    qrR(res) {
      // #ifdef H5
      if (!this.$wechat.isWeixin() || this.shareQrcode != "1") {
        this.PromotionCode = res;
      }
      // #endif
      // #ifdef APP-PLUS
      this.PromotionCode = res;
      // #endif
    },
    //Ẩn popup
    posterImageClose() {
      this.posterImageStatus = false;
      this.$emit("close", false);
    },

    drawPoster(loadedImages, name, store_name) {
      // Hàm cắt ngắn tiêu đề
      function truncateTitle(title, maxLength) {
        if (title.length > maxLength) {
          return title.substring(0, maxLength) + "...";
        }
        return title;
      }
      // Lấy context của canvas
      const ctx = uni.createCanvasContext("posterCanvas");
      return new Promise(async (resolve, reject) => {
        uni.getImageInfo({
          src: loadedImages[0],
          success: (res) => {
            // Kích thước poster
            const posterWidth = 375;
            const posterHeight = 579;
            // const posterWidth = res.width / 2;
            // const posterHeight = res.height / 2;
            // Vẽ ảnh nền
            ctx.drawImage(loadedImages[0], 0, 0, posterWidth, posterHeight);
            ctx.save();
            // Bố cục ảnh đại diện và tiêu đề

            const avatarSize = 22; // Kích thước ảnh đại diện
            const nickname = name; // Biệt danh
            const title = "Gửi tặng bạn bè một món quà"; // Chữ tiêu đề
            const titleFontSize = 14; // Cỡ chữ tiêu đề
            const nicknameFontSize = 14; // Cỡ chữ biệt danh
            const padding = 10; // Khoảng cách giữa các phần tử
            // Tính chiều rộng tiêu đề
            ctx.setFontSize(titleFontSize);
            const titleWidth = ctx.measureText(title).width;
            const nicknameWidth = ctx.measureText(nickname).width;
            // Tính tổng chiều rộng ảnh đại diện và tiêu đề
            const totalWidth =
              avatarSize + padding + nicknameWidth + padding + titleWidth;

            // Tính vị trí vẽ bắt đầu (căn giữa theo chiều ngang)
            const startX = (posterWidth - totalWidth) / 2;
            const startY = 77; // Khoảng cách tới đỉnh

            // Vẽ ảnh đại diện
            // ctx.drawImage(loadedImages[3], startX, startY, avatarSize, avatarSize);
            const avatarX = startX + avatarSize / 2; // Điểm tâm X của ảnh đại diện
            const avatarY = startY + avatarSize / 2; // Điểm tâm Y của ảnh đại diện
            ctx.save(); // Lưu trạng thái canvas
            ctx.beginPath();
            ctx.arc(avatarX, avatarY, avatarSize / 2, 0, Math.PI * 2); // Vẽ đường viền hình tròn (path)
            ctx.clip(); // Cắt vùng hình tròn
            ctx.drawImage(
              loadedImages[3],
              startX,
              startY,
              avatarSize,
              avatarSize
            ); // Vẽ ảnh đại diện
            ctx.restore(); // Khôi phục trạng thái canvas

            // Vẽ biệt danh
            ctx.setFontSize(nicknameFontSize);
            ctx.setTextAlign("left");
            ctx.fillText(
              nickname,
              startX + avatarSize + padding,
              startY + avatarSize - 5
            ); // Điều chỉnh chữ căn giữa theo chiều dọc
            // Vẽ tiêu đề
            ctx.setFontSize(titleFontSize);
            ctx.fillText(
              title,
              startX + avatarSize + padding + nicknameWidth + padding,
              startY + avatarSize - 5
            );

            // Kích thước ảnh sản phẩm
            const productImageSize = 225; // Kích thước ảnh sản phẩm là 225px x 225px

            // Vẽ viền ảnh sản phẩm
            const productBorderX = (posterWidth - productImageSize) / 2; // Căn giữa theo chiều ngang
            const productBorderY = startY + avatarSize + 31; // Khoảng cách với ảnh đại diện và tiêu đề
            ctx.drawImage(
              loadedImages[1],
              productBorderX,
              productBorderY,
              productImageSize,
              productImageSize
            );

            // Vẽ ảnh sản phẩm
            const productImagePadding = 10; // Padding giữa ảnh sản phẩm và viền
            const productImageX = productBorderX + productImagePadding;
            const productImageY = productBorderY + productImagePadding + 11;
            const productImageInnerSize =
              productImageSize - 2 * productImagePadding; // Kích thước vẽ thực tế của ảnh sản phẩm
            ctx.drawImage(
              loadedImages[2],
              productImageX,
              productImageY,
              productImageInnerSize,
              productImageInnerSize - 10
            );

            // Vẽ tiêu đề sản phẩm
            const productTitle = store_name;
            const maxTitleLength = 20; // Độ dài tối đa của tiêu đề
            const truncatedTitle = truncateTitle(productTitle, maxTitleLength); // Cắt ngắn tiêu đề
            ctx.setFontSize(14);
            ctx.setTextAlign("center");
            ctx.fillText(
              truncatedTitle,
              posterWidth / 2,
              productBorderY + productImageSize + 26
            );
            // Vẽ mã QR chia sẻ
            const qrCodeSize = 100;
            const qrCodeX = (posterWidth - qrCodeSize) / 2;
            const qrCodeY = productBorderY + productImageSize + 63; // Khoảng cách với tiêu đề sản phẩm
            ctx.drawImage(
              loadedImages[4],
              qrCodeX,
              qrCodeY,
              qrCodeSize,
              qrCodeSize
            );

            // Vẽ xong
            ctx.draw(false, () => {
              // Tạo ảnh poster
              uni.canvasToTempFilePath({
                canvasId: "posterCanvas",
                width: posterWidth,
                height: posterHeight,
                success: (res) => {
                  resolve(res.tempFilePath);
                },

                fail: (err) => {
                  reject(err);
                },
              });
            });
          },
        });
      });
    },
    loadImage(src) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = "anonymous"; // Cho phép cross-domain (CORS)
        img.src = src;
        img.onload = () => {
          resolve(img);
        };
        img.onerror = (err) => reject(err);
      });
    },
    share() {
      this.$emit("shareH5");
    },
    getPoster() {
      let images = [
        this.bag,
        this.giftBorder,
        this.giftData.image,
        this.giftData.avatar,
        this.PromotionCode,
      ];
      let postImg = ["", "", "", "", ""];
      // #ifdef MP
      for (let i = 0; i < images.length; i++) {
        uni.downloadFile({
          url: images[i],
          success: (res) => {
            if (res.statusCode == 200) {
              postImg[i] = res.tempFilePath;
            }
            const allNotEmpty = postImg.every((item) => item !== "");
            if (allNotEmpty) this.goPoster(postImg);
          },
          fail: () => {
            this.$set(this, "PromotionCode", "");
          },
        });
      }
      // #endif
      // #ifndef MP
      this.goPoster(images);
      // #endif
    },
    goPoster(postImg) {
      this.drawPoster(postImg, this.giftData.nickname, this.giftData.title)
        .then((posterPath) => {
          // #ifdef APP-PLUS || MP
          this.savePosterPathMp(posterPath);
          // #endif
          // #ifdef H5
          this.posterImage = posterPath;
          this.posterImageStatus = true;
          // #endif
        })
        .catch((err) => {
          console.error("Tạo poster thất bại:", err);
        });
    },
    // #ifdef APP-PLUS || MP
    savePosterPathMp(url) {
      let that = this;
      uni.saveImageToPhotosAlbum({
        filePath: url,
        success: function (res) {
          that.$util.Tips({
            title: that.$t(`Lưu thành công`),
            icon: "success",
          });
        },
        fail: function (res) {
          that.$util.Tips({
            title: that.$t(`Lưu thất bại`),
          });
        },
      });
    },
    // #endif
    savePic(url) {
      var a = document.createElement("a"); // Tạo một phần tử a
      a.download = "Gift"; // Đặt tên ảnh
      a.style.display = "none";
      a.href = url; // Đặt URL đã tạo vào thuộc tính a.href
      document.body.appendChild(a); // Thêm thẻ a vào document object
      a.click(); // Kích hoạt sự kiện click của a
      a.remove(); // Dùng một lần, dùng xong thì xóa thẻ a
    },
  },
};
</script>

<style lang="scss" scoped>
.aleart {
  width: 600rpx;
  height: 980rpx;
  position: fixed;
  left: 50%;
  transform: translateX(-50%);
  z-index: 999;
  top: 50%;
  margin-top: -490rpx;
  background-color: #fff;
  border-radius: 32rpx;
  background-size: 100% 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  .poster-img {
    width: 100%;
    height: 100%;
    border-radius: 32rpx;
  }
  .from {
    font-size: 28rpx;
    font-weight: 500;
    color: #333333;
    margin-bottom: 16rpx;
  }
  .message {
    font-weight: 400;
    font-size: 26rpx;
    color: #999999;
    margin-bottom: 42rpx;
  }
  .aleart-body {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 396rpx;
    height: 419rpx;
    background-image: url("../../static/gift-border.png");
    background-size: 100% 100%;
    margin-bottom: 32rpx;
    .goods-img {
      width: 360rpx;
      height: 360rpx;
      margin-top: 24rpx;
    }
  }
  .title {
    max-width: 80%;
    font-weight: 400;
    font-size: 28rpx;
    color: #333333;
    margin-bottom: 52rpx;
    text-align: center;
  }
  .btn,
  .btn-clear {
    width: 396rpx;
    height: 80rpx;
    line-height: 80rpx;
    border-radius: 20px;
    text-align: center;
    font-size: 28rpx;
  }
  .btn {
    color: #fff;
    background: linear-gradient(90deg, #ff7931 0%, #e93323 100%);
  }
  .btn-clear {
    margin-top: 20rpx;
    color: #e93323;
    border: 1px solid #e93323;
  }
  .keep {
    font-size: 24rpx;
    font-weight: bold;
    color: rgba(255, 255, 255, 0.7);
    position: fixed;
    right: calc(50% - 130rpx);
    bottom: -45rpx;
    display: block;
  }
  .close {
    font-size: 50rpx;
    font-weight: bold;
    color: #fff;
    position: fixed;
    right: calc(50% - 23rpx);
    bottom: -110rpx;
    display: block;
  }
}
</style>
