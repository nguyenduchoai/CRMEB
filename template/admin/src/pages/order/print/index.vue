<template>
  <!-- Thanh toán đơn hàng -->
  <div class="order-bgc">
    <div class="putSupplier perpage" v-for="(item, index) in newArrayData" :key="index">
      <div class="header acea-row row-between-wrapper">
        <div class="left acea-row row-middle">
          <!-- <div class="picture" :id="'qrCodeUrl' + index"></div> -->
          <div class="info">
            <div><span class="name">Người nhận:</span>{{ orderData.user_name }}</div>
            <div><span class="name">Địa chỉ nhận hàng:</span>{{ orderData.user_address }}</div>
            <div>
              <span class="name">Số điện thoại:</span><span>{{ orderData.user_phone }}</span>
            </div>
          </div>
        </div>
        <div class="info">
          <div><span class="name">Mã đơn hàng:</span>{{ orderData.order_id }}</div>
          <div><span class="name">Thời gian thanh toán:</span>{{ orderData.pay_time }}</div>
          <div><span class="name">Phương thức thanh toán:</span>{{ orderData.pay_type }}</div>
        </div>
      </div>
      <div class="mt20">
        <el-table border :data="item" :disabled-hover="true">
          <el-table-column label="ID sản phẩm" width="80" align="center">
            <template slot-scope="scope">
              <span class="nickname">{{ scope.row.index }} </span>
            </template>
          </el-table-column>
          <el-table-column label="Tên sản phẩm" width="170">
            <template slot-scope="scope">
              <span class="nickname">{{ scope.row.name }} </span>
            </template>
          </el-table-column>
          <el-table-column label="Quy cách sản phẩm" minWidth="150">
            <template slot-scope="scope">
              <span class="nickname">{{ scope.row.sku }} </span>
            </template>
          </el-table-column>
          <el-table-column label="Đơn giá" width="80">
            <template slot-scope="scope">
              <span class="nickname">{{ scope.row.price }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Số lượng" width="80">
            <template slot-scope="scope">
              <span class="nickname">{{ scope.row.num }} </span>
            </template>
          </el-table-column>
          <el-table-column label="Số tiền" width="100">
            <template slot-scope="scope">
              <span class="nickname">{{ scope.row.sum_price }}</span>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="bottom acea-row row-between-wrapper">
        <div class="acea-row row-middle">
          <div class="item"><span class="name">Phí vận chuyển:</span>{{ orderData.pay_postage }}</div>
          <div class="item"><span class="name">Ưu đãi:</span>{{ orderData.coupon_price }}</div>
          <div class="item"><span class="name">Chiết khấu thành viên:</span>{{ orderData.vip_price }}</div>
          <div class="item"><span class="name">Khấu trừ điểm thưởng:</span>{{ orderData.deduction_price }}</div>
        </div>
        <div class="pricePay">Số tiền thực trả: {{ orderData.pay_price }}</div>
      </div>
      <div class="bottom acea-row">
        <div class="name">
          Ghi chú người dùng:<span class="con">{{ orderData.mark || '-' }}</span>
        </div>
      </div>
    </div>
    <!--  Chú ý: sau này nếu thêm nội dung khiến trang bị giãn to ra, nhớ kiểm tra xem bản in có nằm trên cùng một tờ không, có dư ra một tờ giấy trắng không  -->
  </div>
</template>
<script>
import { distributionOrder } from '@/api/order';
import QRCode from 'qrcodejs2';
import Setting from '@/setting';
export default {
  data() {
    return {
      orderData: {},
      BaseURL: Setting.apiBaseURL.replace(/adminapi/, ''),
      newArrayData: [],
      site_name: '',
      refund_phone: '',
      refund_address: '',
    };
  },
  created() {
    this.getDistribution();
  },
  mounted() {},
  methods: {
    // Tạo mã QR
    creatQrCode() {
      let qrcode;
      let url = window.location.origin + '/pages/goods/order_details/index?order_id=' + this.$route.query.id;
      this.newArrayData.forEach((item, index) => {
        let obj = document.getElementById('qrCodeUrl' + index);
        qrcode = new QRCode(obj, {
          text: url, // Nội dung cần chuyển đổi thành mã QR
          width: 90,
          height: 90,
          colorDark: '#000000',
          colorLight: '#ffffff',
          correctLevel: QRCode.CorrectLevel.H,
        });
      });
    },
    getDistribution() {
      distributionOrder(this.$route.query.id)
        .then((res) => {
          this.orderData = res.data;
          const chunkedArrays = [];
          res.data.product_info.forEach((item, index) => {
            const chunkIndex = Math.floor(index / 6);
            if (!chunkedArrays[chunkIndex]) {
              chunkedArrays[chunkIndex] = [];
            }
            item.index = index + 1;
            chunkedArrays[chunkIndex].push(item);
          });
          this.newArrayData = chunkedArrays;
          setTimeout(() => {
            this.creatQrCode();
          }, 200);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
  },
};
</script>
<style lang="scss" scoped>
.perpage {
  page-break-after: always !important;
}
.order-bgc {
  position: absolute;
  width: 100%;
  background-color: #fff;
  height: max-content;
  min-height: 100vh;
  font-weight: 600;
}
::v-deep .el-table th {
  background-color: #fff !important;
}
::v-deep .el-table-header thead tr th:nth-of-type(1) {
  padding-left: 0 !important;
}
::v-deep .el-table-header thead tr th {
  border-top: 1px solid #333;
}
::v-deep .el-table td:nth-of-type(1) {
  padding-left: 0 !important;
}
::v-deep .el-table-header table {
  //border-top:0!important;
}
::v-deep .el-table-border th,
::v-deep .el-table-border td {
  border-right: 1px solid #333 !important;
}
::v-deep .el-table-border th:nth-of-type(1),
::v-deep .el-table-border td:nth-of-type(1) {
  border-left: 1px solid #333 !important;
}
::v-deep .el-table th,
::v-deep .el-table td {
  border-bottom: 1px solid #333 !important;
  border-right: 1px solid #333 !important;
}
::v-deep .el-table-wrapper-with-border {
  border-color: #333 !important;
  border: unset;
}
::v-deep .el-table-border:after {
  background-color: #333;
  width: 0 !important;
  height: 0 !important;
}
::v-deep .el-table--border {
  border: 1px solid #333 !important;
}
::v-deep .el-table:before {
  background-color: #333;
  width: 0 !important;
  height: 0 !important;
}
::v-deep .el-table .cell,
::v-deep .el-table th.el-table__cell > .cell {
  height: 47px !important;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}
::v-deep .el-table {
  color: #000;
}
.pricePay {
  font-weight: bold;
}
.bottom {
  color: rgba(0, 0, 0, 0.85);
  font-size: 12px;
  font-weight: 400;
  margin-top: 20px;
  .item {
    margin-right: 30px;
    font-weight: 600;
  }
  .name {
    font-weight: 600;
  }
  .con {
    width: 740px;
    font-weight: unset;
  }
}
.putSupplier {
  width: 794px;
  background-color: #fff;
  margin: 0 auto;
  padding-top: 10px;
  /*padding: 20px 20px 450px 20px;*/
  /*box-sizing: border-box;*/
  .header {
    .info {
      font-size: 12px;
      color: rgba(0, 0, 0, 0.85);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      height: 80px;
      font-weight: 600;
    }
    .left {
      width: 500px;
      .picture {
        width: 90px;
        height: 90px;
        margin-right: 20px;
        img {
          width: 100%;
          height: 100%;
        }
      }
      .info {
        flex: 1;
      }
    }
  }
}
.delivery {
  display: flex;
  justify-content: center;
  width: 794px;
  padding-top: 14px;
  border-top: 1px solid #dddddd;
  margin: 11px auto;
  font-size: 10px;
  color: #333333;

  div + div {
    margin-left: 30px;
  }
}
</style>
