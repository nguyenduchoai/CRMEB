<template>
  <div>
    <div class="i-layout-page-header header-title">
      <div class="fl_header">
        <el-button
          class="btn-back"
          icon="el-icon-arrow-left"
          size="small"
          type="text"
          v-db-click
          @click="$router.go(-1)"
          >Quay lại</el-button
        >
        <el-divider direction="vertical"></el-divider>
        <span class="ivu-page-header-title">{{ $route.meta.title }}</span>
      </div>
    </div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="tableFrom"
          :model="tableFrom"
          :label-width="labelWidth"
          label-position="right"
          @submit.native.prevent
          inline
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
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Loại giải thưởng:">
            <el-select type="button" v-model="tableFrom.type" @change="selectType" class="form_content_width" clearable>
              <el-option v-for="(item, i) in typeList" :key="i" :label="item.text" :value="item.val"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm người dùng:" label-for="store_name">
            <el-input clearable placeholder="Vui lòng nhập thông tin người dùng" v-model="tableFrom.keyword" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-table
        :data="tableList"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <div>{{ scope.row.id }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin người dùng" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.user.nickname }} </span>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin giải thưởng" min-width="130">
          <template slot-scope="scope">
            <div class="prize">
              <img :src="scope.row.prize.image" alt="" />
              <span>{{ scope.row.prize.name }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian quay thưởng" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.add_time }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thông tin nhận hàng" min-width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.receive_info.name">
              <div>Họ tên: {{ scope.row.receive_info.name }}</div>
              <div>Điện thoại: {{ scope.row.receive_info.phone }}</div>
              <div>Địa chỉ: {{ scope.row.receive_info.address }}</div>
              <div v-if="scope.row.receive_info.mark">Ghi chú: {{ scope.row.receive_info.mark }}</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Ghi chú" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.deliver_info.mark }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="120">
          <template slot-scope="scope">
            <a v-db-click @click="deliver(scope.row, 1)" v-if="scope.row.type == 6 && scope.row.is_deliver === 0"
              >Giao hàng</a
            >
            <a v-else-if="scope.row.type == 6 && scope.row.is_deliver === 1" v-db-click @click="isDeliver(scope.row)"
              >Thông tin giao hàng</a
            >
            <el-divider direction="vertical" v-if="scope.row.type == 6" />
            <a v-db-click @click="deliver(scope.row, 2)">Ghi chú</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="tableFrom.page"
          :limit.sync="tableFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <!-- Giao hàng-->
    <el-dialog
      :visible.sync="shipModel"
      width="540px"
      :title="!modelTitle ? (modelType === 1 ? 'Giao hàng' : 'Ghi chú') : modelTitle"
      :close-on-click-modal="false"
    >
      <el-form
        v-model="shipModel"
        :ref="modelType === 1 ? 'shipForm' : 'markForm'"
        :model="modelType === 1 ? shipForm : markForm"
        :rules="modelType === 1 ? ruleShip : ruleMark"
        label-width="90px"
      >
        <el-form-item v-if="modelType === 1" label="Đơn vị vận chuyển:" prop="deliver_name">
          <el-select v-model="shipForm.deliver_name" class="w100">
            <el-option v-for="item in locationList" :value="item.value" :key="item.id" :label="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item v-if="modelType === 1" label="Mã vận đơn:" prop="deliver_number">
          <el-input v-model="shipForm.deliver_number" placeholder="Vui lòng nhập mã vận đơn" class="w100"></el-input>
          <div class="tips-info" v-if="shipForm.deliver_name == 'SF Express'">
            <p>Với SF Express, vui lòng nhập mã vận đơn: 4 số cuối số điện thoại người nhận hoặc người gửi</p>
            <p>Ví dụ: SF000000000000:3941</p>
          </div>
        </el-form-item>
        <el-form-item v-if="modelType === 2" label="Ghi chú:">
          <el-input v-model="markForm.mark" placeholder="Vui lòng nhập ghi chú" class="w100"></el-input>
        </el-form-item>
        <el-form-item>
          <div class="acea-row row-right">
            <el-button v-db-click @click="cancel('formValidate')">Tắt</el-button>
            <el-button type="primary" v-db-click @click="ok(modelType === 1 ? 'shipForm' : 'markForm')">Gửi</el-button>
          </div>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { lotteryRecordList, lotteryRecordDeliver } from '@/api/lottery';
import { formatDate } from '@/utils/validate';
import { getExpressData } from '@/api/order';
import { ruleShip, ruleMark } from './formRule/ruleShip';
export default {
  name: 'lotteryRecordList',
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
      shipModel: false,
      loading: false,
      locationList: [],
      shipForm: {
        id: '',
        deliver_name: '',
        deliver_number: null,
      },
      markForm: {
        id: '',
        mark: '',
      },
      ruleShip: ruleShip,
      ruleMark: ruleMark,
      pickerOptions: this.$timeOptions,
      typeList: [
        { text: 'Tất cả', val: '' },
        { text: 'Không trúng thưởng', val: '1' },
        { text: 'Điểm thưởng', val: '2' },
        { text: 'Số dư', val: '3' },
        { text: 'Lì xì', val: '4' },
        { text: 'Phiếu giảm giá', val: '5' },
        { text: 'Sản phẩm', val: '6' },
      ],
      tableList: [],
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        keyword: '',
        time: [],
        page: 1,
        limit: 15,
        lottery_id: 0,
      },
      total: 0,
      timeVal: [],
      modelType: 1,
      lottery_id: '',
      modelTitle: '',
    };
  },
  computed: {
    ...mapState('admin/layout', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.tableFrom.lottery_id = this.$route.query.id;
    this.lottery_id = this.$route.query.id;
    this.getList();
    this.getExpressData();
  },
  methods: {
    deliver(row, type) {
      this.markForm.id = row.id;
      this.shipForm.id = row.id;
      this.shipForm.deliver_name = '';
      this.shipForm.deliver_number = '';
      this.markForm.mark = row.deliver_info.mark;
      this.modelType = type;
      this.shipModel = true;
    },
    isDeliver(row) {
      this.markForm.id = row.id;
      this.shipForm.id = row.id;
      this.modelType = 1;
      this.modelTitle = 'Thông tin giao hàng';
      this.shipModel = true;
      this.shipForm.deliver_name = row.deliver_info.deliver_name;
      this.shipForm.deliver_number = row.deliver_info.deliver_number;
    },
    ok(name) {
      this.$refs[name].validate((valid) => {
        lotteryRecordDeliver(this.modelType == 1 ? this.shipForm : this.markForm)
          .then((res) => {
            this.$message.success('Thao tác thành công');
            this.shipModel = false;
            this.getList();
            this.shipForm = {
              id: '',
              deliver_name: '',
              deliver_number: null,
            };
            this.modelTitle = '';
            this.markForm = {
              id: '',
              mark: '',
            };
          })
          .catch((err) => {
            this.$message.error(err.msg);
          });
      });
    },
    cancel() {
      this.modelType = 1;
      this.modelTitle = '';
      this.shipModel = false;
    },
    // Danh sách đơn vị vận chuyển
    getExpressData() {
      getExpressData()
        .then(async (res) => {
          this.locationList = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e || [];
      this.tableFrom.time = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.tableFrom.page = 1;
      this.getList();
    },
    // Chọn giờ
    selectChange(tab) {
      this.tableFrom.page = 1;
      this.tableFrom.time = tab;
      this.timeVal = [];
      this.getList();
    },
    selectType(type) {
      this.tableFrom.page = 1;
      this.timeVal = [];
      this.getList();
    },
    selectChangeFactor() {
      this.tableFrom.page = 1;
      this.timeVal = [];
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      lotteryRecordList(this.tableFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableList = data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.tableFrom.page = 1;
      this.getList();
    },
  },
};
</script>

<style lang="scss" scoped>
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
.prize {
  display: flex;
  align-items: center;
}
.prize img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 5px;
}
.trips {
  color: #ccc;
}
.w414 {
  width: 414px;
}
</style>
