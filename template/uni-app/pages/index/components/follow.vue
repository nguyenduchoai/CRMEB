<template>
  <!-- Theo dõi OA WeChat -->
  <view>
    <view :style="[followWrapStyle]">
      <view :style="[followStyle]" class="follow acea-row row-between-wrapper">
        <view class="picTxt acea-row row-middle">
          <view class="pictrue">
            <easy-loadimage mode="widthFix" :image-src="dataConfig.imgConfig.url" width="92rpx" height="92rpx" borderRadius="46rpx"></easy-loadimage>
          </view>
          <view class="name line1">
            {{ dataConfig.titleConfig.value }}
          </view>
        </view>
        <view :style="[buttonStyle]" class="notes acea-row row-center-wrapper" @click="followTap">
          {{ $t(`Đã theo dõi`) }}
        </view>
        <view class="iconfont icon-ic_close"></view>
      </view>
    </view>
    <view class="followCode" v-if="followCode">
      <view class="pictrue">
        <view class="title">{{ $t(`Theo dõi OA WeChat`) }}</view>
        <view class="tips">{{ $t(`Cập nhật ưu đãi, khuyến mãi sớm nhất`) }}</view>
        <view class="code-bg">
          <image class="imgs" :src="dataConfig.codeConfig.url" mode=""></image>
        </view>
        <!-- #ifdef MP || APP-PLUS -->
        <view class="btn" @tap="savePic">{{ $t(`Lưu ảnh`) }}</view>
        <!-- #endif -->
        <!-- #ifdef H5 -->
        <view class="btn" v-show="isWeixin" @tap="savePic">{{ $t(`Nhấn giữ để lưu ảnh`) }}</view>
        <view class="btn" v-show="!isWeixin" @tap="savePic">{{ $t(`Lưu ảnh`) }}</view>
        <!-- #endif -->
        <view class="close acea-row row-center-wrapper" @click="closeFollowCode">
          <text class="iconfont icon-ic_close1"></text>
        </view>
      </view>
      <view class="mask"></view>
    </view>
  </view>
</template>

<script>
import {follow} from '@/api/api.js';
import {getSubscribe} from '@/api/public';
// #ifdef H5
import Auth from '@/libs/wechat';
// #endif
export default {
  name: 'follow',
  props: {
    dataConfig: {
      type: Object,
      default: () => {}
    },
    isSortType: {
      type: String | Number,
      default: 0
    }
  },
  data() {
    return {
      followCode: false,
      followUrl: '',
      bgColor: '',
      imgConfig: '',
      mbConfig: 0,
      themeColor: '',
      titleConfig: 0,
      subscribe: false,
      // #ifdef H5
      isWeixin: Auth.isWeixin(),
      //#endif
    };
  },
  computed: {
    buttonStyle() {
      return {
        'border-color': this.dataConfig.themeColor.color[0].item,
        'color': this.dataConfig.themeColor.color[0].item,
      };
    },
    followStyle() {
      let borderRadius = `${this.dataConfig.fillet.val * 2}rpx`;
      if (this.dataConfig.fillet.type) {
        borderRadius = `${this.dataConfig.fillet.valList[0].val * 2}rpx ${this.dataConfig.fillet.valList[1].val * 2}rpx ${this.dataConfig.fillet.valList[3].val * 2}rpx ${this.dataConfig.fillet.valList[2].val * 2}rpx`;
      }
      return {
        'border-radius': borderRadius,
        'background': `linear-gradient(90deg, ${this.dataConfig.bgColor.color[0].item} 0%, ${this.dataConfig.bgColor.color[1].item} 100%)`,
        'color': this.dataConfig.themeColor.color[0].item,
      };
    },
    followWrapStyle() {
      return {
        'padding': `0 ${this.dataConfig.prConfig.val * 2}rpx`,
        'margin-top': `${this.dataConfig.mbConfig.val * 2}rpx`,
      };
    },
  },
  created() {},
  mounted() {
    getSubscribe().then(res => {
      this.subscribe = res.data.subscribe || false;
    }).catch(() => {})
  },
  methods: {
    savePic(){
      // #ifdef H5
      var a = document.createElement('a'); // Tạo một phần tử a
      a.download = 'wechat'; // Đặt tên ảnh
      a.style.display = "none";
      a.href = this.dataConfig.codeConfig.url; // Đặt URL đã tạo vào thuộc tính a.href
      document.body.appendChild(a); // Thêm thẻ a vào document object
      a.click(); // Kích hoạt sự kiện click của a
      a.remove(); // Dùng một lần, dùng xong thì xóa thẻ a
      // #endif
      // #ifdef MP
      let that = this;
      uni.downloadFile({
        url: that.dataConfig.codeConfig.url, //Đường dẫn hình ảnh
        success: function(response){
          uni.getSetting({
            success(res) {
              if (!res.authSetting['scope.writePhotosAlbum']) {
                uni.authorize({
                  scope: 'scope.writePhotosAlbum',
                  success() {
                    uni.saveImageToPhotosAlbum({
                      filePath: response.tempFilePath,
                      success: function(res) {
                        that.closeFollowCode();
                        that.$util.Tips({
                          title: 'Lưu thành công',
                          icon: 'success'
                        });
                      },
                      fail: function(res) {
                        that.$util.Tips({
                          title: 'Lưu thất bại'
                        });
                      }
                    });
                  }
                });
              } else {
                uni.saveImageToPhotosAlbum({
                  filePath: response.tempFilePath,
                  success: function(res) {
                    that.closeFollowCode();
                    that.$util.Tips({
                      title: 'Lưu thành công',
                      icon: 'success'
                    });
                  },
                  fail: function(res) {
                    that.$util.Tips({
                      title: 'Lưu thất bại'
                    });
                  }
                });
              }
            }
          });
        }
      })
      // #endif
      //#ifdef APP-PLUS
      let that = this
      uni.downloadFile({
        url: that.dataConfig.codeConfig.url, //Đường dẫn hình ảnh
        success: function(response){
          uni.saveImageToPhotosAlbum({
            filePath: response.tempFilePath,
            success: function(res) {
              that.posterImageClose();
              that.$util.Tips({
                title: 'Lưu thành công',
                icon: 'success'
              });
            },
            fail: function(res) {
              that.$util.Tips({
                title: 'Lưu thất bại'
              });
            }
          });
        }
      })
      // #endif
    },
    followTap() {
		this.followCode = true;
    },
    closeFollowCode() {
      this.followCode = false
    },
  }
}
</script>

<style lang="scss">
.follow {
  padding: 14rpx 32rpx 14rpx 30rpx;
  background: #FFFFFF;

  .picTxt {
    flex: 1;
	min-width: 0;

    .name {
      flex: 1;
      font-size: 30rpx;
      color: #333333;
      margin-left: 20rpx;
    }
  }

  .notes {
	  position: relative;
    font-weight: 500;
    font-size: 24rpx;
    color: var(--view-theme);
    width: 112rpx;
    height: 56rpx;
    border: 1px solid var(--view-theme);
    border-radius: 28rpx;
	overflow: hidden;
  }

  .iconfont {
    margin-left: 16rpx;
    font-size: 32rpx;
    color: #333333;
  }
}

.followCode {
  .pictrue {
    width: 548rpx;
    height: 758rpx;
    border-radius: 48rpx;
    background: #FFFFFF;
    left: 50%;
    top: 50%;
    position: fixed;
    z-index: 10000;
    transform: translate(-50%, -50%);

    .title {
      padding: 48rpx 0 46rpx;
      border-top-left-radius: 48rpx;
      border-top-right-radius: 48rpx;
      border-bottom-right-radius: 274rpx 40rpx;
      border-bottom-left-radius: 274rpx 40rpx;
      background: linear-gradient(90deg, #FF7931 0%, var(--view-theme) 100%);
      text-align: center;
      font-weight: 500;
      font-size: 40rpx;
      line-height: 56rpx;
      color: #FFFFFF;
    }

    .tips {
      margin-top: 48rpx;
      text-align: center;
      font-size: 28rpx;
      line-height: 40rpx;
      color: #3D3D3D;
    }

    .code-bg {
      width: 312rpx;
      height: 312rpx;
      margin: 24rpx auto 0;
    }

    .imgs {
      width: 100%;
      height: 100%;
    }

    .btn {
      width: 420rpx;
      height: 80rpx;
      border-radius: 40rpx;
      margin: 40rpx auto 60rpx;
      background: linear-gradient(90deg, #FF7931 0%, var(--view-theme) 100%);
      text-align: center;
      font-weight: 500;
      font-size: 28rpx;
      line-height: 80rpx;
      color: #FFFFFF;
    }

    .close {
      position: absolute;
      bottom: -108rpx;
      left: 50%;
      width: 60rpx;
      height: 60rpx;
      border-radius: 50%;
      transform: translateX(-50%);

      .iconfont {
        font-size: 40rpx;
        color: #CCCCCC;
      }
    }
  }

  .mask {
    z-index: 9999;
  }
}
.official-account{
	position: absolute;
	z-index: 99999;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	opacity: 0;
}
</style>
