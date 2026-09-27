<template>
  <div v-if="orderDatalist">
    <el-dialog :visible.sync="modals" title="Thông tin đơn hàng" width="720px" class="order_box">
      <el-card :bordered="false" shadow="never" class="i-table-no-border">
        <div class="ivu-description-list-title">Thông tin nhận hàng</div>
        <el-row class="mb10">
          <el-col :span="12">Biệt danh người dùng: {{ orderDatalist.userInfo.nickname }}</el-col>
          <el-col :span="12">Người nhận hàng: {{ orderDatalist.orderInfo.real_name }}</el-col>
        </el-row>
        <el-row class="mb10">
          <el-col :span="12">Số điện thoại liên hệ: {{ orderDatalist.orderInfo.user_phone }}</el-col>
          <el-col :span="12">Địa chỉ nhận hàng: {{ orderDatalist.orderInfo.user_address }}</el-col>
        </el-row>
        <el-divider></el-divider>
        <div class="ivu-description-list-title">Thông tin đơn hàng</div>
        <el-row class="mb10">
          <el-col :span="12">ID đơn hàng: {{ orderDatalist.orderInfo.order_id }}</el-col>
          <el-col :span="12" class="fontColor1">Trạng thái đơn hàng: {{ orderDatalist.orderInfo.status_name }}</el-col>
        </el-row>
        <el-row class="mb10">
          <el-col :span="12"
            >Tên sản phẩm: {{ orderDatalist.orderInfo.store_name + ' | '
            }}{{ orderDatalist.orderInfo.suk ? orderDatalist.orderInfo.suk : '' }}</el-col
          >
        </el-row>
        <el-row class="mb10">
          <el-col :span="12">Tổng số sản phẩm: {{ orderDatalist.orderInfo.total_num }}</el-col>
          <el-col :span="12">Tổng điểm thưởng: {{ orderDatalist.orderInfo.total_price }}</el-col>
        </el-row>
        <el-row class="mb10">
          <el-col :span="12" class="mb10">Thời gian tạo: {{ orderDatalist.orderInfo.add_time }}</el-col>
          <el-col :span="12" class="mb10" v-if="orderDatalist.orderInfo.remark"
            >Ghi chú của cửa hàng: {{ orderDatalist.orderInfo.remark }}</el-col
          >
          <el-col :span="12" class="mb10" v-if="orderDatalist.orderInfo.fictitious_content"
            >Ghi chú giao hàng ảo: {{ orderDatalist.orderInfo.fictitious_content }}</el-col
          >
        </el-row>
        <div v-if="orderDatalist.orderInfo.delivery_type === 'express'">
          <el-divider></el-divider>
          <div class="ivu-description-list-title">Thông tin vận chuyển</div>
          <el-row class="mb10">
            <el-col :span="12">Đơn vị vận chuyển: {{ orderDatalist.orderInfo.delivery_name }}</el-col>
            <el-col :span="12"
              >Mã vận đơn: {{ orderDatalist.orderInfo.delivery_id }}
              <a size="small" v-db-click @click="openLogistics">Tra cứu vận chuyển</a></el-col
            >
          </el-row>
        </div>
        <div v-if="orderDatalist.orderInfo.delivery_type === 'send'">
          <el-divider></el-divider>
          <div class="ivu-description-list-title">Thông tin giao hàng</div>
          <el-row class="mb10">
            <el-col :span="12">Họ tên người giao hàng: {{ orderDatalist.orderInfo.delivery_name }}</el-col>
            <el-col :span="12">Số điện thoại người giao hàng: {{ orderDatalist.orderInfo.delivery_id }}</el-col>
          </el-row>
        </div>
        <div v-if="orderDatalist.orderInfo.mark">
          <el-divider></el-divider>
          <div class="ivu-description-list-title" v-if="orderDatalist.orderInfo.mark">Thông tin ghi chú</div>
          <el-row class="mb10">
            <el-col :span="12" class="fontColor2">{{ orderDatalist.orderInfo.mark }}</el-col>
          </el-row>
        </div>
      </el-card>
    </el-dialog>
    <el-dialog :visible.sync="modal2" title="Tra cứu vận chuyển" width="470px" class="order_box2">
      <div class="logistics acea-row row-top">
        <div class="logistics_img"><img src="../../../../assets/images/expressi.jpg" /></div>
        <div class="logistics_cent">
          <span>Đơn vị vận chuyển: {{ orderDatalist.orderInfo.delivery_name }}</span>
          <span>Mã vận đơn: {{ orderDatalist.orderInfo.delivery_id }}</span>
        </div>
      </div>
      <div class="acea-row row-column-around trees-coadd">
        <div class="scollhide">
          <el-timeline>
            <el-timeline-item v-for="(item, i) in result" :key="i" :timestamp="item.time">
              {{ item.status }}
            </el-timeline-item>
          </el-timeline>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getExpress } from '@/api/marketing';
export default {
  name: 'orderDetails',
  data() {
    return {
      modal2: false,
      modals: false,
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      result: [],
    };
  },
  props: {
    orderDatalist: Object,
    orderId: Number,
  },
  methods: {
    openLogistics() {
      this.getOrderData();
      this.modal2 = true;
    },
    // Lấy thông tin vận chuyển của đơn hàng
    getOrderData() {
      getExpress(this.orderId)
        .then(async (res) => {
          this.result = res.data.result;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
  computed: {},
};
</script>

<style lang="scss" scoped>
.ivu-description-list-title {
  margin-bottom: 16px;
  color: #17233d;
  font-weight: 500;
  font-size: 14px;
}
.logistics {
  align-items: center;
  padding: 10px 0px;
  .logistics_img {
    width: 45px;
    height: 45px;
    margin-right: 12px;
    img {
      width: 100%;
      height: 100%;
    }
  }
  .logistics_cent {
    span {
      display: block;
      font-size: 12px;
    }
  }
}
.trees-coadd {
  width: 100%;
  height: 400px;
  border-radius: 4px;
  overflow: hidden;
  .scollhide {
    width: 100%;
    height: 100%;
    overflow: auto;
    margin-left: 18px;
    padding: 10px 0 10px 0;
    box-sizing: border-box;
    .content {
      font-size: 12px;
    }
    .time {
      font-size: 12px;
      color: #2d8cf0;
    }
  }
}
.order_box2 {
  position: absolute;
  z-index: 999999999;
}
.order_box ::v-deep .ivu-modal-header {
  padding: 30x 16px !important;
}
.order_box ::v-deep .ivu-card {
  font-size: 12px !important;
}
.fontColor1 ::v-deep .ivu-description-term {
  color: red !important;
}
.fontColor1 ::v-deep .ivu-description-detail {
  color: red !important;
  padding-bottom: 14px !important;
}
.fontColor2 ::v-deep .ivu-description-detail {
  color: #733af9 !important;
}
.order_box ::v-deep .ivu-description-term {
  padding-bottom: 10px !important;
}
.order_box ::v-deep .ivu-description-detail {
  padding-bottom: 10px !important;
}
.order_box ::v-deep .ivu-modal-body {
  padding: 0 16px !important;
}
.fontColor3 ::v-deep .ivu-description-term {
  color: #f1a417 !important;
}
.fontColor3 ::v-deep .ivu-description-detail {
  color: #f1a417 !important;
}
.tabBox_img {
  width: 100px;
  height: 70px;
  border-radius: 4px;
  cursor: pointer;
  img {
    width: 100%;
    height: 100%;
    padding: 2px;
  }
}
</style>
