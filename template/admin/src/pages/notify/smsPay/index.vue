<template>
  <div v-loading="spinShow">
    <div class="i-layout-page-header header_top">
      <div class="i-layout-page-header fl_header">
        <router-link :to="{ path: $routeProStr + '/setting/sms/sms_config/index' }"
          ><el-button size="small" type="text">Quay lại</el-button></router-link
        >
        <el-divider direction="vertical"></el-divider>
        <span class="ivu-page-header-title mr20" style="padding: 0">{{ $route.meta.title }}</span>
      </div>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-tabs v-model="isChecked" @tab-click="onChangeType">
        <el-tab-pane label="SMS" name="sms"></el-tab-pane>
        <el-tab-pane label="Thu thập sản phẩm" name="copy"></el-tab-pane>
        <el-tab-pane label="Tra cứu vận chuyển" name="expr_query"></el-tab-pane>
        <el-tab-pane label="In vận đơn điện tử" name="expr_dump"></el-tab-pane>
      </el-tabs>
      <el-row :gutter="16" class="mt50">
        <el-col :span="24" class="ivu-text-left mb20">
          <el-col :xs="12" :sm="6" :md="4" :lg="2" class="mr20">
            <span class="ivu-text-right ivu-block">Số lượt còn lại hiện tại:</span>
          </el-col>
          <el-col :xs="11" :sm="13" :md="19" :lg="20">
            <span>{{ numbers }}</span>
          </el-col>
        </el-col>
        <el-col :span="24" class="ivu-text-left mb20">
          <el-col :xs="12" :sm="6" :md="4" :lg="2" class="mr20">
            <span class="ivu-text-right ivu-block">Chọn gói:</span>
          </el-col>
          <el-col :xs="11" :sm="13" :md="19" :lg="20">
            <el-row :gutter="20">
              <el-col v-for="(item, index) in list" :key="index" :xxl="4" :xl="8" :lg="8" :md="12" :sm="24" :xs="24">
                <div
                  class="list-goods-list-item mb15"
                  :class="{ active: index === current }"
                  v-db-click
                  @click="check(item, index)"
                >
                  <div class="list-goods-list-item-title" :class="{ active: index === current }">
                    ¥ <i>{{ item.price }}</i>
                  </div>
                  <div class="list-goods-list-item-price" :class="{ active: index === current }">
                    <span>Số lượt {{ all[isChecked] }}: {{ item.num }}</span>
                  </div>
                </div>
              </el-col>
            </el-row>
          </el-col>
        </el-col>
        <el-col :span="24" class="ivu-text-left mb20" v-if="checkList">
          <el-col :xs="12" :sm="6" :md="4" :lg="2" class="mr20">
            <span class="ivu-text-right ivu-block">Số lượt nạp:</span>
          </el-col>
          <el-col :xs="11" :sm="13" :md="19" :lg="20">
            <span>{{ checkList.num }}</span>
          </el-col>
        </el-col>
        <el-col :span="24" class="ivu-text-left mb20" v-if="checkList">
          <el-col :xs="12" :sm="6" :md="4" :lg="2" class="mr20">
            <span class="ivu-text-right ivu-block">Số tiền thanh toán:</span>
          </el-col>
          <el-col :xs="11" :sm="13" :md="19" :lg="20">
            <span class="list-goods-list-item-number">￥{{ checkList.price }}</span>
          </el-col>
        </el-col>
        <el-col :span="24" class="ivu-text-left mb20">
          <el-col :xs="12" :sm="6" :md="4" :lg="2" class="mr20">
            <span class="ivu-text-right ivu-block">Phương thức thanh toán:</span>
          </el-col>
          <el-col :xs="11" :sm="13" :md="19" :lg="20">
            <span class="list-goods-list-item-pay"
              >WeChat Pay<i v-if="code.invalid">{{ '  ( Thời gian hết hạn mã thanh toán:' + code.invalid + ' ）' }}</i></span
            >
          </el-col>
        </el-col>
        <el-col :span="24">
          <el-col :xs="12" :sm="6" :md="4" :lg="3" class="mr20">&nbsp;</el-col>
          <el-col :xs="11" :sm="13" :md="19" :lg="20">
            <div class="list-goods-list-item-code mr20"><img v-lazy="code.code_url" v-if="code.code_url" /></div>
          </el-col>
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script>
import { smsPriceApi, payCodeApi, isLoginApi, serveInfoApi } from '@/api/setting';
export default {
  name: 'smsPay',
  data() {
    return {
      all: { sms: 'SMS', copy: 'Thu thập sản phẩm', expr_query: 'Tra cứu vận chuyển', expr_dump: 'In vận đơn điện tử' },
      isChecked: 'sms',
      numbers: '',
      account: '',
      list: [],
      current: 0,
      checkList: {},
      spinShow: false,
      code: {},
    };
  },
  created() {
    this.isChecked = this.$route.query.type;
    this.onIsLogin();
  },
  methods: {
    // Kiểm tra đã đăng nhập chưa
    onIsLogin() {
      this.spinShow = true;
      isLoginApi()
        .then(async (res) => {
          let data = res.data;
          if (!data.status) {
            this.$message.warning('Vui lòng đăng nhập trước');
            this.$router.push({
              path: this.$routeProStr + '/setting/sms/sms_config/index?url=' + this.$route.path,
              query: {
                type: this.$route.query.type,
              },
            });
          } else {
            this.getServeInfo();
            this.getPrice();
          }
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thông tin người dùng nền tảng
    getServeInfo() {
      serveInfoApi()
        .then(async (res) => {
          let data = res.data;
          switch (this.isChecked) {
            case 'sms':
              this.numbers = data.sms.num;
              break;
            case 'copy':
              this.numbers = data.copy.num;
              break;
            case 'expr_dump':
              this.numbers = data.dump.num;
              break;
            default:
              this.numbers = data.query.num;
              break;
          }
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    onChangeType() {
      this.current = 0;
      this.getPrice();
      this.getServeInfo();
    },
    // Gói thanh toán
    getPrice() {
      this.spinShow = true;
      smsPriceApi({ type: this.isChecked })
        .then(async (res) => {
          setTimeout(() => {
            this.spinShow = false;
          }, 800);
          let data = res.data;
          this.list = data.data;
          this.checkList = this.list[0];
          this.getCode(this.checkList);
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
          this.list = [];
        });
    },
    // Đã chọn
    check(item, index) {
      this.spinShow = true;
      this.current = index;
      setTimeout(() => {
        this.getCode(item);
        this.checkList = item;
        this.spinShow = false;
      }, 800);
    },
    // Mã thanh toán
    getCode(item) {
      let data = {
        pay_type: 'weixin',
        meal_id: item.id,
        price: item.price,
        num: item.num,
        type: item.type,
      };
      payCodeApi(data)
        .then(async (res) => {
          this.code = res.data;
        })
        .catch((res) => {
          this.code = '';
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.active {
  background: #0091ff;
  box-shadow: 0px 6px 20px 0px rgba(0, 145, 255, 0.3);
  color: #fff !important;
}
.list-goods-list-item {
  border: 1px solid #dadfe6;
  padding: 20px 10px;
  box-sizing: border-box;
  border-radius: 3px;
}
.list-goods-list {
  &-item {
    text-align: center;
    position: relative;
    cursor: pointer;
    img {
      width: 60%;
    }
    .ivu-tag {
      position: absolute;
      top: 10px;
      right: 10px;
    }
    &-title {
      font-size: 16px;
      font-weight: bold;
      color: #0091ff;
      margin-bottom: 3px;
      i {
        font-size: 30px;
        font-style: normal;
      }
    }
    &-desc {
      font-size: 14px;
      color: #808695;
    }
    &-price {
      font-size: 14px;
      color: #000000;
      s {
        color: #c5c8ce;
      }
    }
    &-number {
      font-size: 14px;
      color: #ed4014;
    }
    &-pay {
      font-size: 14px;
      color: #00c050;
      i {
        font-size: 12px;
        font-style: normal;
        color: #6d7278;
      }
    }
    &-code {
      width: 130px;
      height: 130px;
      img {
        width: 100%;
        height: 100%;
      }
    }
  }
}
</style>
