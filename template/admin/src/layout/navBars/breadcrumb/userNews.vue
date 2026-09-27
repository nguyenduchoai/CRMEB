<template>
  <div class="layout-navbars-breadcrumb-user-news">
    <div class="head-box">
      <div class="head-box-title">Thông báo hệ thống</div>
      <!-- <div class="head-box-btn" v-if="newsList.length > 0" v-db-click @click="onAllReadClick">Đánh dấu tất cả đã đọc</div> -->
    </div>
    <div class="content-box">
      <template v-if="newsList.length > 0">
        <div class="content-box-item" v-for="(v, k) in newsList" :key="k" v-db-click @click="jumpUrl(v.url)">
          <img class="icon" :src="icon(v.type)" alt="" />
          <div class="content-box-right">
            <div class="content-box-type">{{ v.type | msgType }}</div>
            <div class="content-box-msg">
              {{ v.title }}
            </div>
          </div>

          <!-- <div class="content-box-time">{{ v.time }}</div> -->
        </div>
      </template>
      <div class="content-box-empty" v-else>
        <div class="content-box-empty-margin">
          <img class="no-msg" src="@/assets/images/no-msg.png" alt="" />
          <div class="mt15">Chưa có thông báo</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
let newOrderAudioLink = new Audio(require('@/assets/video/newOrderAudioLink.mp3'));
import { jnoticeRequest } from '@/api/common';
import { adminSocket } from '@/libs/socket';
import { getCookies, removeCookies, setCookies } from '@/libs/util';
export default {
  name: 'layoutBreadcrumbUserNews',
  props: {},
  data() {
    return {
      newsList: [],
      newOrderAudioLink: null,
      messageList: [],
    };
  },
  mounted() {
    this.getNotict();
    this.newOrderAudioLink = newOrderAudioLink;
    adminSocket.then((ws) => {
      ws.send({
        type: 'login',
        data: getCookies('token'),
      });
      let that = this;
      ws.$on('ADMIN_NEW_PUSH', function (data) {
        that.getNotict();
      });

      ws.$on('NEW_ORDER', function (data) {
        that.$notify.info({
          title: 'Đơn hàng mới',
          message: 'Bạn có một đơn hàng mới (ID: ' + data.order_id + '), vui lòng kiểm tra',
        });
        if (newOrderAudioLink) newOrderAudioLink.play();
        that.messageList.push({
          title: 'Thông báo đơn hàng mới',
          icon: 'md-bulb',
          iconColor: '#87d068',
          time: 0,
          read: 0,
        });
      });
      ws.$on('NEW_REFUND_ORDER', function (data) {
        that.$notify.info({
          title: 'Thông báo đơn hoàn tiền',
          message: 'Bạn có một đơn hàng yêu cầu hoàn tiền (ID: ' + data.order_id + '), vui lòng kiểm tra',
        });
        if (newOrderAudioLink) newOrderAudioLink.play();
        that.messageList.push({
          title: 'Thông báo đơn hoàn tiền',
          icon: 'md-information',
          iconColor: '#fe5c57',
          time: 0,
          read: 0,
        });
      });
      ws.$on('WITHDRAW', function (data) {
        // that.$Notice.warning({
        //   title: 'Nhắc nhở rút tiền',
        //   duration: 8,
        //   desc: 'Có người dùng yêu cầu rút tiền, mã số là (' + data.id + '), vui lòng chú ý kiểm tra',
        // });
        that.$notify.info({
          title: 'Thông báo rút tiền',
          message: 'Có người dùng yêu cầu rút tiền (mã số: ' + data.id + '), vui lòng kiểm tra',
        });
        that.messageList.push({
          title: 'Thông báo đơn hoàn tiền',
          icon: 'md-people',
          iconColor: '#f06292',
          time: 0,
          read: 0,
        });
      });
      ws.$on('STORE_STOCK', function (data) {
        that.$notify.info({
          title: 'Cảnh báo tồn kho',
          message: 'Sản phẩm (ID: ' + data.id + ') sắp hết hàng, vui lòng kiểm tra~',
        });
        that.messageList.push({
          title: 'Cảnh báo tồn kho',
          icon: 'md-information',
          iconColor: '#fe5c57',
          time: 0,
          read: 0,
        });
      });
      ws.$on('PAY_SMS_SUCCESS', function (data) {
        that.$notify.info({
          title: 'Nạp tiền SMS thành công',
          message: 'Chúc mừng bạn đã nạp ' + data.price + 'đ, nhận được ' + data.number + ' tin nhắn SMS',
        });
        that.messageList.push({
          title: 'Nạp tiền SMS thành công',
          icon: 'md-bulb',
          iconColor: '#87d068',
          time: 0,
          read: 0,
        });
      });
    });
  },
  filters: {
    // 1 chờ giao hàng 2 cảnh báo tồn kho 3 trả lời bình luận 4 yêu cầu rút tiền
    msgType(type) {
      let typeName;
      switch (type) {
        case 1:
          typeName = 'Thông báo đơn hàng chờ giao hàng';
          break;
        case 2:
          typeName = 'Cảnh báo tồn kho';
          break;
        case 3:
          typeName = 'Trả lời đánh giá';
          break;
        case 4:
          typeName = 'Yêu cầu rút tiền';
          break;
        default:
          typeName = 'Khác';
      }
      return typeName;
    },
  },
  methods: {
    // Bấm đánh dấu đã đọc tất cả
    onAllReadClick() {
      this.newsList = [];
      this.$emit('haveNews', !!this.newsList.length);
    },
    // Bấm đi tới trung tâm thông báo
    onGoToGiteeClick() {},
    getNotict() {
      jnoticeRequest()
        .then((res) => {
          this.newsList = res.data || [];
          this.$emit('haveNews', !!this.newsList.length);
        })
        .catch(() => {});
    },
    jumpUrl(path) {
      if (!path) return;
      // Liên kết ngoài thì mở trực tiếp trong cửa sổ mới
      if (/^https?:\/\//.test(path)) {
        window.open(path, '_blank');
        return;
      }
      // Tương thích trường hợp this.$router có thể là undefined trong môi trường render đặc biệt như lớp popup
      const router = this.$router || (this.$root && this.$root.$router);
      if (router && typeof router.push === 'function') {
        router.push({ path });
      } else {
        // Phương án dự phòng: chuyển hướng trực tiếp
        window.location.href = path;
      }
    },
    icon(type) {
      return require(`@/assets/images/news-${type}.png`);
    },
  },
};
</script>

<style scoped lang="scss">
.layout-navbars-breadcrumb-user-news {
  width: 320px;
  padding: 8px 14px 14px;
  .head-box {
    display: flex;
    // border-bottom: 1px solid var(--prev-border-color-lighter);
    box-sizing: border-box;
    color: var(--prev-color-text-primary);
    justify-content: space-between;
    // height: 35px;
    align-items: center;
    .head-box-title {
      font-size: 13px;
      font-weight: 500;
      color: #333333;
      line-height: 13px;
    }
    .head-box-btn {
      color: var(--prev-color-primary);
      font-size: 13px;
      cursor: pointer;
      opacity: 0.8;
      font-weight: 400;
      line-height: 13px;
      &:hover {
        opacity: 1;
      }
    }
  }
  .content-box {
    font-size: 13px;
    .content-box-item {
      padding-top: 24px;
      cursor: pointer;
      display: flex;
      align-items: center;
      &:last-of-type {
        // padding-bottom: 12px;
      }
      .icon {
        width: 26px;
        height: 26px;
        margin-right: 10px;
      }
      .content-box-right {
      }
      .content-box-type {
        font-size: 13px;
        font-weight: 500;
        color: #333333;
        line-height: 13px;
      }
      .content-box-msg {
        margin-top: 6px;
        font-size: 13px;
        font-weight: 400;
        color: #666666;
        line-height: 13px;
      }
      .content-box-time {
        color: var(--prev-color-text-secondary);
      }
    }
    .content-box-empty {
      width: 292px;
      // height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      .content-box-empty-margin {
        text-align: center;
        font-size: 13px;
        color: #999999;
        i {
          color: var(--prev-color-primary);
          font-size: 60px;
        }
        .no-msg {
          width: 180px;
          height: 138px;
        }
      }
    }
  }
  .foot-box {
    height: 35px;
    color: var(--prev-color-primary);
    font-size: 13px;
    cursor: pointer;
    opacity: 0.8;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: 1px solid var(--prev-border-color-lighter);
    &:hover {
      opacity: 1;
    }
  }
  ::v-deep(.el-empty__description p) {
    font-size: 13px;
  }
}
</style>
