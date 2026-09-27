<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" style="height: calc(100vh - 200px)">
      <iframe
        src="https://api.crmeb.com?token=AF37D4579721672220B08CA872586943"
        style="width: 100%; height: calc(100vh - 200px)"
        frameborder="0"
      ></iframe>
    </el-card>
  </div>
</template>

<script>
import loginFrom from './components/loginFrom';
import forgetPassword from './components/forgetPassword';
import registerFrom from './components/register';
import tableList from './tableList';
import forgetPhone from './components/forgetPhone';
import request from '@/libs/request';
import { isLoginApi, logoutApi, smsNumberApi, serveInfoApi } from '@/api/setting';

export default {
  name: 'smsConfig',
  components: { loginFrom, forgetPassword, registerFrom, tableList, forgetPhone },
  data() {
    return {
      imgUrl: require('@/assets/images/ren.png'),
      spinShow: false,
      isShowLogn: false, // Đăng nhập
      isShow: false, // Đổi mật khẩu
      isShowReg: false, // Đăng ký
      isShowList: false, // Danh sách sau khi đăng nhập
      smsAccount: '',
      accountInfo: {},
      isForgetPhone: false, // Sửa số điện thoại
      isIndex: false, // Kiểm tra đường dẫn trả về của quên mật khẩu
      sms: { open: 0 }, // Thông tin SMS
      query: { open: 0 }, // Tra cứu vận chuyển
      dump: { open: 0 }, // In vận đơn điện tử
      copy: { open: 0 }, // Thu thập sản phẩm
    };
  },
  created() {
    // this.onIsLogin();
    window.addEventListener('message', this.handleConfig);
  },
  beforeDestroy() {
    // Gỡ event listener
    window.removeEventListener('message', this.handleConfig);
  },
  methods: {
    handleConfig(data) {
      let IsSave = false;
      if (data.data.accessKey && data.data.secretKey && IsSave === false) {
        IsSave = true;
        request({
          url: 'setting/config/save_basics',
          method: 'POST',
          data: {
            sms_account: data.data.accessKey,
            sms_token: data.data.secretKey,
            sms_save_type: 'yihaotong',
          },
        }).then((res) => {});
      }
    },
    onChangePhone() {
      this.isForgetPhone = true;
      this.isShowLogn = false;
      this.isShowList = false;
    },
    onOpen(val) {
      this.$refs.tableLists.onOpenIndex(val);
    },
    mealPay(val) {
      this.$router.push({ path: this.$routeProStr + '/setting/sms/sms_pay/index', query: { type: val } });
    },
    // Mở dịch vụ
    openService(val) {
      switch (val) {
        case 'sms':
          this.sms.open = 1;
          break;
        case 'copy':
          this.copy.open = 1;
          break;
        case 'query':
          this.query.open = 1;
          break;
        default:
          this.dump.open = 1;
          break;
      }
    },
    // Thông tin người dùng nền tảng
    getServeInfo() {
      this.spinShow = true;
      serveInfoApi()
        .then(async (res) => {
          let data = res.data;
          this.sms = {
            num: data.sms.num,
            open: data.sms.open,
            surp: data.sms.open,
          };
          this.query = {
            num: data.query.num,
            open: data.query.open,
            surp: data.query.open,
          };
          this.dump = {
            num: data.dump.num,
            open: data.dump.open,
            surp: data.dump.open,
          };
          this.copy = {
            num: data.copy.num,
            open: data.copy.open,
            surp: data.copy.open,
          };
          this.spinShow = false;
          this.smsAccount = data.account;
          this.accountInfo = data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
          this.isShowLogn = true;
          this.isShowList = false;
          this.spinShow = false;
        });
    },
    // Kiểm tra đã đăng nhập chưa
    onIsLogin() {
      this.spinShow = true;
      isLoginApi()
        .then(async (res) => {
          let data = res.data;
          this.isShowLogn = !data.status;
          this.isShowList = data.status;
          this.spinShow = false;
          if (data.status) {
            this.getServeInfo();
          }
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
    // Đăng xuất
    signOut() {
      logoutApi()
        .then(async (res) => {
          this.isShowLogn = true;
          this.isShowList = false;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Đổi mật khẩu
    onChangePassswordIndex() {
      this.isIndex = true;
      this.passsword();
    },
    // Quên mật khẩu
    onChangePasssword() {
      this.isIndex = false;
      this.passsword();
      // this.isShowLogn = false;
      // this.isShow = true;
      // this.isShowList = false;
    },
    passsword() {
      this.isShowLogn = false;
      this.isShow = true;
      this.isShowList = false;
    },

    // Đăng ký ngay
    onChangeReg() {
      this.isShowLogn = false;
      this.isShow = false;
      this.isShowReg = true;
    },
    // Đăng nhập ngay
    logoup() {
      this.isShowLogn = true;
      this.isShow = false;
      this.isShowReg = false;
    },
    // Chuyển hướng đăng nhập
    onLogin() {
      let url = this.$route.query.url;
      if (url) {
        this.$router.replace(url + '?type=' + this.$route.query.type);
      } else {
        this.isShowLogn = false;
        this.isShow = false;
        this.isShowReg = false;
        this.isForgetPhone = false;
        this.isShowList = true;
        this.getServeInfo();
      }
    },
    // Mật khẩu trả về
    goback() {
      if (this.isIndex) {
        this.isShowList = true;
        this.isShow = false;
      } else {
        this.isShowLogn = true;
        this.isShow = false;
      }
    },
    // Số điện thoại trả về
    gobackPhone() {
      this.isShowList = true;
      this.isForgetPhone = false;
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .layout-container .layout-scrollbar {
  padding: 0;
}
::v-deep .ivu-card-body {
  padding: 0;
}
.picTxt {
  padding: 8px 0 12px;
}

.ivu-card .ivu-card-body {
  padding: 0;
}

.dashboard {
  width: auto !important;
  min-width: 300px;
}

.header-extra {
  /*width: 25%;*/
  border-right: 1px solid #e9e9e9;
  text-align: center;
  padding: 0 18px;
}

.page-account-top-tit {
  font-size: 21px;
  color: var(--prev-color-primary);
}

.dashboard-workplace {
  &-header {
    &-avatar {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      margin-right: 16px;
    }

    &-tip {
      display: inline-block;
      vertical-align: middle;

      &-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 12px;
      }

      &-desc {
        color: #808695;
      }
    }

    &-extra {
      width: 100% !important;

      .ivu-col {
        p {
          text-align: right;
        }

        p:first-child {
          span:first-child {
            margin-right: 4px;
          }

          span:last-child {
            color: #808695;
          }
        }

        p:last-child {
          font-size: 22px;
        }
      }
    }
  }
}

.conBox {
  ::v-deep .ivu-page-header-extra {
    width: auto !important;
    min-width: 457px;
  }

  ::v-deep .ivu-page-header {
    padding: 16px 0px 0 32px !important;
  }
}

.samll_font {
  text-align: center;
  padding: 0px 10px;
}

.title-tips {
  font-size: 14px;
  color: #999;
  margin-left: 10px;
}
</style>
