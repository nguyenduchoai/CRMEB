<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          label-position="right"
          inline
          @submit.native.prevent
        >
          <el-form-item label="Thời gian đơn hàng:">
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
          <el-form-item label="Loại giao dịch:">
            <el-select clearable v-model="formValidate.trading_type" @change="selChange" class="form_content_width">
              <el-option
                :label="item"
                :value="Object.keys(withdrawal)[index]"
                v-for="(item, index) in Object.values(withdrawal)"
                :key="index"
              ></el-option>
            </el-select>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never">
      <el-table ref="table" :data="tabList" v-loading="loading" empty-text="Chưa có dữ liệu">
        <el-table-column label="ID" width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Đơn hàng liên quan" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.relation }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian giao dịch" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá trị giao dịch" min-width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.pm" class="z-price">+ {{ scope.row.number }}</div>
            <div v-else class="f-price">- {{ scope.row.number }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Người dùng" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại giao dịch" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.type_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ghi chú" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.mark }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="70">
          <template slot-scope="scope">
            <a v-db-click @click="setMark(scope.row)">Ghi chú</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="formValidate.page"
          :limit.sync="formValidate.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <!-- Từ chối duyệt-->
    <el-dialog :visible.sync="modals" title="Ghi chú" :close-on-click-modal="false" width="540px">
      <el-input v-model="mark_msg.mark" type="textarea" :rows="4" placeholder="Vui lòng nhập ghi chú" />
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" v-db-click @click="oks">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import { getBalanceList, setBalanceMark } from '@/api/finance';
import { formatDate } from '@/utils/validate';
import dateRadio from '@/components/dateRadio';
export default {
  name: 'balance',
  components: { dateRadio },
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  data() {
    return {
      images: ['1.jpg', '2.jpg'],
      modal_loading: false,
      pickerOptions: this.$timeOptions,
      mark_msg: {
        mark: '',
      },
      modals: false,
      total: 0,
      loading: false,
      tabList: [],
      withdrawal: [],
      selectIndexTime: '',
      payment: [
        {
          title: 'Tất cả',
          value: '',
        },
        {
          title: 'WeChat',
          value: 'weixin',
        },
        {
          title: 'Alipay',
          value: 'alipay',
        },
        {
          title: 'Thẻ ngân hàng',
          value: 'bank',
        },
        {
          title: 'Thanh toán ngoại tuyến',
          value: 'offline',
        },
      ],
      formValidate: {
        trading_type: '',
        time: '',
        keywords: '',
        page: 1,
        limit: 20,
      },
      timeVal: [],
      FromData: null,
      extractId: 0,
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
  mounted() {
    this.getList();
  },
  methods: {
    // Xác nhận
    oks() {
      this.modal_loading = true;
      this.mark_msg.mark = this.mark_msg.mark.trim();
      setBalanceMark(this.extractId, this.mark_msg)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.modal_loading = false;
          this.modals = false;
          this.getList();
        })
        .catch((res) => {
          this.modal_loading = false;
          this.$message.error(res.msg);
        });
    },
    // Ghi chú
    setMark(row) {
      this.modals = true;
      this.extractId = row.id;
      this.mark_msg.mark = row.mark;
    },
    onSelectDate(e) {
      this.formValidate.time = e;
      this.formValidate.page = 1;
      this.getList();
    },
    dateToMs(date) {
      let result = new Date(date).getTime();
      return result;
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.time = this.timeVal ? this.timeVal.join('-') : '';
      this.formValidate.page = 1;
      this.getList();
    },
    // Chọn
    selChange(e) {
      this.formValidate.page = 1;
      this.formValidate.trading_type = e;
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      getBalanceList(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
          this.total = data.count;
          this.withdrawal = data.status;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa gửi thành công
    submitFail() {
      this.getList();
    },
  },
};
</script>
<style lang="scss" scoped>
.ivu-mt .type .item {
  margin: 3px 0;
}
.Refresh {
  font-size: 12px;
  color: var(--prev-color-primary);
  cursor: pointer;
}
.ivu-form-item {
  margin-bottom: 10px;
}
.status ::v-deep .item ~ .item {
  margin-left: 6px;
}
.status ::v-deep .statusVal {
  margin-bottom: 7px;
}

/* .ivu-mt ::v-deep .ivu-table-header */
/* border-top:1px dashed #ddd!important */
.type {
  padding: 3px 0;
  box-sizing: border-box;
}
.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
.z-price {
  color: red;
}
.f-price {
  color: green;
}
</style>
