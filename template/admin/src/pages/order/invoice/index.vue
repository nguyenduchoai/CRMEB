<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="orderData"
          :model="orderData"
          label-width="80px"
          label-position="right"
          inline
          @submit.native.prevent
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
          <el-form-item label="Tìm kiếm:" prop="real_name" label-for="real_name">
            <el-input clearable v-model="orderData.real_name" placeholder="Vui lòng nhập" class="form_content_width">
              <el-select v-model="orderData.field_key" slot="prepend" style="width: 100px">
                <el-option value="all" label="Tất cả"></el-option>
                <el-option value="order_id" label="Mã đơn hàng"></el-option>
                <el-option value="uid" label="UID"></el-option>
                <el-option value="real_name" label="Họ tên người dùng"></el-option>
                <el-option value="user_phone" label="Số điện thoại người dùng"></el-option>
              </el-select>
            </el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="orderSearch">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" :body-style="{ padding: '0 20px 20px' }">
      <el-tabs v-model="currentTab" @tab-click="onClickTab" v-if="tablists">
        <el-tab-pane :label="'Tất cả hóa đơn (' + tablists.all + '）'" name=" " />
        <el-tab-pane :label="'Chờ xuất hóa đơn (' + tablists.noOpened + '）'" name="1" />
        <el-tab-pane :label="'Đã xuất hóa đơn (' + tablists.opened + '）'" name="2" />
        <el-tab-pane :label="'Hóa đơn hoàn tiền (' + tablists.refund + '）'" name="3" />
      </el-tabs>
      <el-table
        :data="orderList"
        ref="table"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Mã đơn hàng" min-width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.order_id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền đơn hàng" min-width="90">
          <template slot-scope="scope">
            <div>¥ {{ scope.row.pay_price }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Loại hóa đơn" min-width="130">
          <template slot-scope="scope">
            <div v-if="scope.row.type === 1">Hóa đơn điện tử thông thường</div>
            <div v-else>Hóa đơn chuyên dụng (VAT) bản giấy</div>
          </template>
        </el-table-column>
        <el-table-column label="Loại tiêu đề hóa đơn" min-width="130">
          <template slot-scope="scope">
            <div v-if="scope.row.header_type === 1">Cá nhân</div>
            <div v-else>Doanh nghiệp</div>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian đặt hàng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái xuất hóa đơn" min-width="130">
          <template slot-scope="scope">
            <div v-if="scope.row.is_invoice === 1">Đã xuất hóa đơn</div>
            <div v-else>Chưa xuất hóa đơn</div>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái đơn hàng" min-width="130">
          <template slot-scope="scope">
            <div v-if="scope.row.status === 0">Chưa giao hàng</div>
            <div v-else-if="scope.row.status === 1">Chờ nhận hàng</div>
            <div v-else-if="scope.row.status === 2">Chờ đánh giá</div>
            <div v-else-if="scope.row.status === 3">Đã hoàn thành</div>
            <div v-else-if="scope.row.status === -2">Đã hoàn tiền</div>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="300">
          <template slot-scope="scope">
            <template v-if="tablists.elec_invoice && tablists.elec_invoice == 1">
              <a
                v-if="
                  scope.row.is_invoice == 1 &&
                  scope.row.unique_num !== '' &&
                  scope.row.red_invoice_num == '' &&
                  scope.row.refund_status == 0
                "
                v-db-click
                @click="downInvoice(scope.row)"
                >Tải xuống hóa đơn</a
              >
              <el-divider
                v-if="
                  scope.row.is_invoice == 1 &&
                  scope.row.unique_num !== '' &&
                  scope.row.red_invoice_num == '' &&
                  scope.row.refund_status == 0
                "
                direction="vertical"
              />
              <a
                v-if="scope.row.is_invoice == 1 && scope.row.unique_num !== '' && scope.row.red_invoice_num == ''"
                v-db-click
                @click="openNegative(scope.row)"
                >Xuất hóa đơn điều chỉnh giảm</a
              >
              <el-divider
                v-if="scope.row.is_invoice == 1 && scope.row.unique_num !== '' && scope.row.red_invoice_num == ''"
                direction="vertical"
              />
              <a
                v-if="scope.row.is_invoice !== 1 && scope.row.refund_status == 0"
                v-db-click
                @click="getInvoice(scope.row)"
                >Xuất hóa đơn điện tử</a
              >
              <el-divider v-if="scope.row.is_invoice !== 1 && scope.row.refund_status == 0" direction="vertical" />
            </template>
            <a v-if="scope.row.status != -2" v-db-click @click="edit(scope.row)">Thao tác</a>
            <el-divider v-if="scope.row.status != -2" direction="vertical" />
            <a v-db-click @click="orderInfo(scope.row.id)">Thông tin đơn hàng</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="orderData.page"
          :limit.sync="orderData.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <el-dialog :visible.sync="invoiceShow" title="Chi tiết hóa đơn" class="order_box" width="720px" @closed="cancel">
      <el-form ref="formInline" :model="formInline" label-width="80px" @submit.native.prevent>
        <div v-if="invoiceDetails.header_type === 1 && invoiceDetails.type === 1">
          <div class="list">
            <div class="title">Thông tin hóa đơn</div>
            <el-row class="row">
              <el-col :span="12"
                >Tiêu đề hóa đơn: <span class="info">{{ invoiceDetails.name }}</span></el-col
              >
              <el-col :span="12">Loại hóa đơn: <span class="info">Hóa đơn điện tử thông thường</span></el-col>
            </el-row>
            <el-row class="row">
              <el-col :span="12">Loại tiêu đề hóa đơn: Cá nhân</el-col>
              <el-col :span="12">Số tiền đơn hàng: {{ invoiceDetails.pay_price }}</el-col>
            </el-row>
          </div>
          <div class="list">
            <div class="title row">Thông tin liên hệ</div>
            <el-row class="row">
              <el-col :span="12">Họ tên: {{ invoiceDetails.name }}</el-col>
              <el-col :span="12">Số điện thoại liên hệ: {{ invoiceDetails.drawer_phone }}</el-col>
            </el-row>
            <el-row class="row">
              <el-col :span="12">Email liên hệ: {{ invoiceDetails.email }}</el-col>
            </el-row>
          </div>
        </div>
        <div v-if="invoiceDetails.header_type === 2 && invoiceDetails.type === 1">
          <div class="list">
            <div class="title">Thông tin hóa đơn</div>
            <el-row class="row">
              <el-col :span="12"
                >Tiêu đề hóa đơn: <span class="info">{{ invoiceDetails.name }}</span></el-col
              >
              <el-col :span="12"
                >Mã số thuế doanh nghiệp: <span class="info">{{ invoiceDetails.duty_number }}</span></el-col
              >
            </el-row>
            <el-row class="row">
              <el-col :span="12">Loại hóa đơn: Hóa đơn điện tử thông thường</el-col>
              <el-col :span="12">Loại tiêu đề hóa đơn: Doanh nghiệp</el-col>
            </el-row>
          </div>
          <div class="list">
            <div class="title row">Thông tin liên hệ</div>
            <el-row class="row">
              <el-col :span="12">Họ tên: {{ invoiceDetails.name }}</el-col>
              <el-col :span="12">Số điện thoại liên hệ: {{ invoiceDetails.user_phone }}</el-col>
            </el-row>
            <el-row class="row">
              <el-col :span="12">Email liên hệ: {{ invoiceDetails.email }}</el-col>
            </el-row>
          </div>
        </div>
        <div v-if="invoiceDetails.header_type === 2 && invoiceDetails.type === 2">
          <div class="list">
            <div class="title">Thông tin hóa đơn</div>
            <el-row class="row">
              <el-col :span="12"
                >Tiêu đề hóa đơn: <span class="info">{{ invoiceDetails.name }}</span></el-col
              >
              <el-col :span="12"
                >Mã số thuế doanh nghiệp: <span class="info">{{ invoiceDetails.duty_number }}</span></el-col
              >
            </el-row>
            <el-row class="row">
              <el-col :span="12">Loại hóa đơn: Hóa đơn chuyên dụng (VAT) bản giấy</el-col>
              <el-col :span="12">Loại tiêu đề hóa đơn: Doanh nghiệp</el-col>
            </el-row>
            <el-row class="row">
              <el-col :span="12"
                >Ngân hàng mở tài khoản: <span class="info">{{ invoiceDetails.bank }}</span></el-col
              >
              <el-col :span="12"
                >Số tài khoản ngân hàng: <span class="info">{{ invoiceDetails.card_number }}</span></el-col
              >
            </el-row>
            <el-row class="row">
              <el-col :span="12">Địa chỉ doanh nghiệp: {{ invoiceDetails.address }}</el-col>
              <el-col :span="12">Điện thoại doanh nghiệp: {{ invoiceDetails.tell }}</el-col>
            </el-row>
          </div>
          <div class="list">
            <div class="title row">Thông tin liên hệ</div>
            <el-row class="row">
              <el-col :span="12">Họ tên: {{ invoiceDetails.real_name }}</el-col>
              <el-col :span="12">Số điện thoại liên hệ: {{ invoiceDetails.user_phone }}</el-col>
            </el-row>
            <el-row class="row">
              <el-col :span="12">Email liên hệ: {{ invoiceDetails.email }}</el-col>
            </el-row>
          </div>
        </div>
        <el-form-item label="Trạng thái xuất hóa đơn:" style="margin-top: 14px">
          <el-radio-group v-model="formInline.is_invoice" @input="kaiInvoice(formInline.is_invoice)">
            <el-radio :label="1">Đã xuất hóa đơn</el-radio>
            <el-radio :label="0">Chưa xuất hóa đơn</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Số hóa đơn:" v-if="formInline.is_invoice === 1">
          <el-input v-model="formInline.invoice_number" placeholder="Vui lòng nhập số hóa đơn"></el-input>
        </el-form-item>
        <el-form-item label="Ghi chú hóa đơn:" v-if="formInline.is_invoice === 1">
          <el-input
            v-model="formInline.remark"
            value="Ghi chú"
            type="textarea"
            :autosize="{ minRows: 2, maxRows: 5 }"
            placeholder="Vui lòng nhập ghi chú hóa đơn"
          ></el-input>
        </el-form-item>
        <div class="acea-row row-right">
          <el-button type="primary" v-db-click @click="handleSubmit()">Xác nhận</el-button>
        </div>
      </el-form>
    </el-dialog>
    <el-dialog :visible.sync="orderShow" title="Chi tiết đơn hàng" class="order_box" width="720px">
      <orderDetall :orderId="orderId" @detall="detall" v-if="orderShow"></orderDetall>
    </el-dialog>
    <el-dialog
      :visible.sync="invoiceModalShow"
      title="Thông tin hóa đơn"
      append-to-body
      :close-on-click-modal="false"
      width="1320px"
      class="mapBox"
    >
      <iframe id="invoicePage" width="100%" height="600px" frameborder="0" v-bind:src="keyUrl"></iframe>
    </el-dialog>
  </div>
</template>
<script>
import orderDetall from './orderDetall';
import {
  orderInvoiceChart,
  orderInvoiceList,
  orderInvoiceSet,
  invoiceIssuanceUrl,
  downInvoice,
  redInvoiceIssuance,
  saveInvoiceInfo,
} from '@/api/order';
import { mapState } from 'vuex';
export default {
  name: 'invoice',
  components: {
    orderDetall,
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
  data() {
    return {
      orderShow: false,
      invoiceShow: false,
      invoiceModalShow: false,
      invoiceDetails: {},
      formInline: {
        is_invoice: 0,
        invoice_number: '',
        remark: '',
      },
      keyUrl: '',
      loading: false,
      currentTab: '',
      tablists: null,
      timeVal: [],
      pickerOptions: this.$timeOptions,
      orderList: [],
      total: 0, // Tổng số bản ghi
      orderData: {
        page: 1, // Trang hiện tại
        limit: 15, // Số mục hiển thị mỗi trang
        status: '',
        data: '',
        real_name: '',
        field_key: '',
        type: '',
      },
      orderId: 0,
      invoiceId: 0,
    };
  },
  created() {
    this.getTabs();
    this.getList();
  },
  mounted() {},

  methods: {
    openNegative(row) {
      // Xác nhận popup
      this.$confirm('Xác nhận xuất hóa đơn điều chỉnh giảm?', 'Thông báo', {
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        type: 'warning',
      }).then(() => {
        redInvoiceIssuance(row.invoice_id).then((res) => {
          this.$message.success(res.msg);
          this.getList();
        });
      });
    },
    downInvoice(row) {
      downInvoice(row.invoice_id).then((res) => {
        window.open(res.data.downloadBase64.pdfUrl, '_blank');
      });
    },
    getInvoice(row) {
      invoiceIssuanceUrl(row.invoice_id).then((res) => {
        this.invoiceId = row.invoice_id;
        this.keyUrl = res.data.uri;
        this.invoiceModalShow = true;
        window.addEventListener('message', this.handleMessage);
      });
    },
    // Xử lý truyền giá trị iframe
    handleMessage(event) {
      switch (event.data.event) {
        case 'onCancel':
          this.invoiceModalShow = false;
          this.keyUrl = '';
          this.invoiceId = 0;
          window.removeEventListener('message');
          break;
        case 'onSuccess':
          saveInvoiceInfo(this.invoiceId, event.data.data).then((res) => {
            this.$message.success(res.msg);
            this.getList();
            this.keyUrl = '';
            this.invoiceId = 0;
            this.invoiceModalShow = false;
            window.removeEventListener('message');
          });
          break;
      }
    },
    detall(e) {
      this.orderShow = e;
    },
    orderInfo(id) {
      this.orderId = id;
      this.orderShow = true;
    },
    empty() {
      this.formInline = {
        is_invoice: 1,
        invoice_number: '',
        remark: '',
      };
    },
    cancel() {
      this.invoiceShow = false;
      this.empty();
    },
    kaiInvoice(invoice) {
      if (invoice !== 1) {
        this.formInline.invoice_number = '';
        this.formInline.remark = '';
      }
    },
    handleSubmit() {
      if (this.formInline.is_invoice === 1) {
        if (this.formInline.invoice_number.trim() === '') return this.$message.error('Vui lòng nhập số hóa đơn');
      }
      orderInvoiceSet(this.invoiceDetails.invoice_id, this.formInline)
        .then((res) => {
          this.$message.success(res.msg);
          this.invoiceShow = false;
          this.getList();
          this.empty();
          this.getTabs();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    edit(row) {
      this.invoiceShow = true;
      this.invoiceDetails = row;
      this.formInline.invoice_number = row.invoice_number;
      this.formInline.remark = row.invoice_reamrk;
      this.formInline.is_invoice = row.is_invoice;
    },
    // Danh sách đơn hàng
    getList() {
      this.loading = true;
      orderInvoiceList(this.orderData)
        .then(async (res) => {
          this.loading = false;
          let data = res.data;
          this.orderList = data.list;
          this.total = data.count;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    getTabs() {
      orderInvoiceChart(this.orderData)
        .then((res) => {
          this.tablists = res.data;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Tìm kiếm chính xác()
    orderSearch() {
      this.orderData.page = 1;
      this.getTabs();
      this.getList();
    },
    // Tìm kiếm theo ngày cụ thể();
    onchangeTime(e) {
      this.orderData.page = 1;
      this.timeVal = e || [];
      this.orderData.data = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.getList();
      this.getTabs();
    },
    //Tìm kiếm theo trạng thái đơn hàng()
    selectChange() {
      this.orderData.page = 1;
      this.getList();
    },
    //Tìm kiếm đơn hàng()
    onClickTab() {
      this.orderData.page = 1;
      this.orderData.type = this.currentTab;
      this.getList();
    },
  },
};
</script>
<style lang="scss" scoped>
.order_box .list {
  font-size: 12px;
  color: #17233d;
  border-bottom: 1px solid #e7eaec;
  margin: 0 10px;
  padding-bottom: 22px;
}
.ivu-form-item {
  margin-left: 10px;
  margin-right: 10px;
}
::v-deep .el-tabs__item {
  height: 54px !important;
  line-height: 54px !important;
}
::v-deep .ivu-form-item-label {
  text-align: left;
  width: 83px !important;
}
::v-deep .ivu-form-item-content {
  margin-left: 83px !important;
}
.order_box .list .title {
  color: #000000;
  font-weight: bold;
}
.order_box .list .row {
  margin-top: 13px;
}
.order_box .list .info {
  color: #515a6e;
}
.tab_data ::v-deep .ivu-form-item-content {
  margin-left: 0 !important;
}
.table_box ::v-deep .ivu-divider-horizontal {
  margin-top: 0px !important;
}
.table_box ::v-deep .ivu-form-item {
  margin-bottom: 15px !important;
}
.tabform {
  margin-bottom: 10px;
}
.Refresh {
  font-size: 12px;
  color: var(--prev-color-primary);
  cursor: pointer;
}
</style>
