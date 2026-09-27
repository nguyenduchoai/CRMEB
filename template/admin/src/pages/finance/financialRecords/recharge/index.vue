<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          inline
          @submit.native.prevent
        >
          <el-form-item label="Chọn thời gian:">
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
          <el-form-item label="Loại thanh toán:">
            <el-select
              clearable
              v-model="formValidate.paid"
              placeholder="Vui lòng chọn"
              @change="selChange"
              class="form_content_width"
            >
              <el-option value="" label="Tất cả"></el-option>
              <el-option value="1" label="Đã thanh toán"></el-option>
              <el-option value="0" label="Chưa thanh toán"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm:">
            <el-input
              clearable
              placeholder="Vui lòng nhập biệt danh người dùng, mã đơn hàng"
              v-model="formValidate.nickname"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="selChange">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <cards-data :cardLists="cardLists" v-if="cardLists.length >= 0"></cards-data>
    <el-card :bordered="false" shadow="never">
      <el-button v-auth="['export-userRecharge']" class="mr" v-db-click @click="exports">Xuất</el-button>
      <el-table ref="table" :data="tabList" class="mt14" v-loading="loading" empty-text="Chưa có dữ liệu"
        ><el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện" min-width="90">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar ? scope.row.avatar : require('../../../../assets/images/moren.jpg')" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Biệt danh người dùng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mã đơn hàng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.order_id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền thanh toán" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Đã thanh toán" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.paid_type }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại nạp tiền" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row._recharge_type }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thanh toán" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row._pay_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" width="100">
          <template slot-scope="scope">
            <a
              href="javascript:void(0);"
              v-if="scope.row.refund_price <= 0 && scope.row.paid && scope.row.recharge_type != 'system'"
              v-db-click
              @click="refund(scope.row)"
              >Hoàn tiền</a
            >
            <!--                    <el-divider direction="vertical"  v-if="scope.row.paid"/>-->
            <a
              href="javascript:void(0);"
              v-if="scope.row.paid === 0"
              v-db-click
              @click="del(scope.row, 'xóa bản ghi nạp tiền này', scope.$index)"
              >Xóa</a
            >
            <span class="refund" v-if="scope.row.refund_price > 0">Đã hoàn tiền</span>
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
    <!-- Form hoàn tiền-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="submitFail"></edit-from>
  </div>
</template>
<script>
import cardsData from '@/components/cards/cards';
import searchFrom from '@/components/publicSearchFrom';
import { mapState } from 'vuex';
import { rechargelistApi, userRechargeApi, refundEditApi, exportUserRechargeApi } from '@/api/finance';
import editFrom from '@/components/from/from';
export default {
  name: 'recharge',
  components: { cardsData, searchFrom, editFrom },
  data() {
    return {
      FromData: null,
      formValidate: {
        data: '',
        paid: '',
        nickname: '',
        excel: 0,
        page: 1,
        limit: 20,
      },
      formValidate2: {
        data: '',
        paid: '',
        nickname: '',
      },
      total: 0,
      cardLists: [],
      loading: false,
      tabList: [],
      pickerOptions: this.$timeOptions,
      timeVal: [],
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
    this.getUserRecharge();
  },
  methods: {
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `finance/recharge/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tabList.splice(delfromData.num, 1);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Hoàn tiền
    refund(row) {
      refundEditApi(row.id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa gửi thành công
    submitFail() {
      this.getList();
      this.getUserRecharge();
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal ? this.timeVal.join('-') : '';
      this.formValidate.page = 1;
      this.getList();
      this.getUserRecharge();
    },
    // Chọn giờ
    selectChange(tab) {
      this.formValidate.data = tab;
      this.timeVal = [];
      this.formValidate.page = 1;
      this.getList();
      this.getUserRecharge();
    },
    // Chọn
    selChange(x) {
      this.formValidate.page = 1;
      this.getList();
      this.getUserRecharge();
    },
    // Danh sách
    getList() {
      this.loading = true;
      rechargelistApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Ô vuông nhỏ
    getUserRecharge() {
      userRechargeApi({
        data: this.formValidate.data,
        paid: this.formValidate.paid,
        nickname: this.formValidate.nickname,
      })
        .then(async (res) => {
          let data = res.data;
          this.cardLists = data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Xuất
    exports() {
      let formValidate = this.formValidate;
      let data = {
        data: formValidate.data,
        paid: formValidate.paid,
        nickname: formValidate.nickname,
      };
      exportUserRechargeApi(data)
        .then((res) => {
          location.href = res.data[0];
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>
<style lang="scss" scoped>
.ivu-mt .type .item {
  margin: 3px 0;
}
.tabform {
  margin-bottom: 10px;
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
/*.ivu-mt ::v-deep .ivu-table-header*/
/*    border-top:1px dashed #ddd!important*/
</style>
