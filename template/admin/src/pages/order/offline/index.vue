<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="pagination"
          :model="pagination"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Thời gian tạo:">
            <el-date-picker
              clearable
              v-model="timeVal"
              type="daterange"
              :editable="false"
              @change="onchangeTime"
              format="yyyy/MM/dd"
              value-format="yyyy/MM/dd"
              start-placeholder="Ngày bắt đầu"
              end-placeholder="Ngày kết thúc"
              :picker-options="pickerOptions"
              style="width: 250px"
              class="mr20"
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Mã đơn hàng:" label-for="title">
            <el-input clearable v-model="pagination.order_id" placeholder="Vui lòng nhập mã đơn hàng" class="form_content_width" />
          </el-form-item>
          <el-form-item label="Tên người dùng:" label-for="title">
            <el-input clearable v-model="pagination.name" placeholder="Vui lòng nhập tên đăng nhập" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="orderSearch">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button type="primary" v-db-click @click="qrcodeShow">Xem mã QR thanh toán</el-button>
      <el-table
        :data="tbody"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Mã đơn hàng" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.order_id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin người dùng" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.nickname }} | {{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thanh toán thực tế" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.pay_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá ưu đãi" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.true_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thanh toán" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.pay_time }}</span>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="pagination.page"
          :limit.sync="pagination.limit"
          @pagination="getOrderList"
        />
      </div>
    </el-card>
    <el-dialog :visible.sync="modal" title="Mã QR nhận tiền" width="540px">
      <div v-viewer class="acea-row row-around code">
        <div class="acea-row row-column-around row-between-wrapper">
          <div class="QRpic">
            <img v-lazy="qrcode && qrcode.wechat" />
          </div>
          <span class="mt10">{{ animal ? 'Mã QR thanh toán OA WeChat' : 'Mã QR OA WeChat' }}</span>
        </div>
        <div class="acea-row row-column-around row-between-wrapper">
          <div class="QRpic">
            <img v-lazy="qrcode && qrcode.routine" />
          </div>
          <span class="mt10">{{ animal ? 'Mã QR thanh toán Mini Program' : 'Mã QR Mini Program' }}</span>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { orderScanList, orderOfflineScan } from '@/api/order';

export default {
  data() {
    return {
      tbody: [],
      loading: false,
      total: 0,
      animal: 0, // Ẩn viền trang trí
      pagination: {
        page: 1,
        limit: 30,
        order_id: '',
        add_time: '',
      },
      timeVal: [],
      modal: false,
      qrcode: null,
      name: '',
      spin: false,
      pickerOptions: this.$timeOptions,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getOrderList();
  },
  methods: {
    onchangeCode(e) {
      this.animal = e;
      this.qrcodeShow();
    },
    // Tìm kiếm theo ngày cụ thể();
    onchangeTime(e) {
      this.pagination.page = 1;
      this.timeVal = e || [];
      this.pagination.add_time = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.getOrderList();
    },
    // Danh sách đơn hàng
    getOrderList() {
      this.loading = true;
      orderScanList(this.pagination)
        .then((res) => {
          this.loading = false;
          const { count, list } = res.data;
          this.total = count;
          this.tbody = list;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    nameSearch() {
      this.pagination.page = 1;
      this.getOrderList();
    },
    // Tìm kiếm đơn hàng
    orderSearch() {
      this.pagination.page = 1;
      this.getOrderList();
    },
    // Xem mã QR
    qrcodeShow() {
      this.modal = true;

      this.spin = true;
      orderOfflineScan({ type: this.animal })
        .then((res) => {
          this.spin = false;
          this.qrcode = res.data;
        })
        .catch((err) => {
          this.spin = false;
          this.$message.error(err.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.code {
  position: relative;
}
.QRpic {
  width: 180px;
  // height: 259px;

  img {
    width: 100%;
    height: 100%;
  }
}
</style>
