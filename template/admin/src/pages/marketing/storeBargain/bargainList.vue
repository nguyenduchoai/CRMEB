<template>
  <div class="article-manager">
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
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
              class="mr20"
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Trạng thái săn giảm giá:">
            <el-select
              v-model="formValidate.status"
              placeholder="Vui lòng chọn"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option :value="1" label="Đang diễn ra"></el-option>
              <el-option :value="2" label="Đã thất bại"></el-option>
              <el-option :value="3" label="Đã thành công"></el-option>
            </el-select>
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
        <el-table-column label="Ảnh đại diện" width="80">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Người khởi tạo" min-width="100">
          <template slot-scope="scope">
            <span> {{ scope.row.nickname + ' / ' + scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian bắt đầu" min-width="110">
          <template slot-scope="scope">
            <span> {{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Sản phẩm săn giảm giá" min-width="300">
          <template slot-scope="scope">
            <span> {{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá thấp nhất" min-width="60">
          <template slot-scope="scope">
            <span> {{ scope.row.bargain_price_min }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá hiện tại" min-width="60">
          <template slot-scope="scope">
            <span> {{ scope.row.now_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tổng số lượt săn giảm giá" min-width="70">
          <template slot-scope="scope">
            <span> {{ scope.row.people_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số lượt săn giảm giá còn lại" min-width="100">
          <template slot-scope="scope">
            <span> {{ scope.row.num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian kết thúc" min-width="150">
          <template slot-scope="scope">
            <span> {{ scope.row.datatime }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="100">
          <template slot-scope="scope">
            <el-tag size="medium" type="info" v-show="scope.row.status === 1">Đang diễn ra</el-tag>
            <el-tag size="medium" type="danger" v-show="scope.row.status === 2">Đã thất bại</el-tag>
            <el-tag size="medium" v-show="scope.row.status === 3">Đã thành công</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="100">
          <template slot-scope="scope">
            <a v-db-click @click="Info(scope.row)">Xem chi tiết</a>
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

    <!-- Modal chi tiết-->
    <el-dialog :visible.sync="modals" class="tableBox" title="Xem chi tiết" :close-on-click-modal="false" width="720px">
      <el-table
        ref="selection"
        :data="tabList3"
        v-loading="loading2"
        empty-text="Chưa có dữ liệu"
        highlight-current-row
        max-height="600"
        size="small"
      >
        <el-table-column label="ID người dùng" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện người dùng" min-width="100">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tên người dùng" min-width="100">
          <template slot-scope="scope">
            <span> {{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền săn giảm giá" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian săn giảm giá" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { formatDate } from '@/utils/validate';
import { bargainUserListApi, bargainUserInfoApi } from '@/api/marketing';
export default {
  name: 'bargainList',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  // components: { cardsData },
  data() {
    return {
      cardLists: [],
      modals: false,
      pickerOptions: this.$timeOptions,
      loading: false,
      formValidate: {
        status: '',
        data: '',
        page: 1,
        limit: 15,
      },
      tableList: [],
      total: 0,
      timeVal: [],
      loading2: false,
      tabList3: [],
      rows: {},
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
    this.getList();
  },
  methods: {
    // Xem chi tiết
    Info(row) {
      this.modals = true;
      this.rows = row;
      bargainUserInfoApi(row.id)
        .then(async (res) => {
          let data = res.data;
          this.tabList3 = data.list;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e || [];
      this.formValidate.data = this.timeVal[0] ? (this.timeVal ? this.timeVal.join('-') : '') : '';
      this.formValidate.page = 1;
      this.getList();
    },
    // Chọn giờ
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.data = tab;
      this.timeVal = [];
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.formValidate.status = this.formValidate.status || '';
      bargainUserListApi(this.formValidate)
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
      this.formValidate.page = 1;
      this.getList();
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .ivu-tag-cyan .ivu-tag-text {
  color: #19be6b !important;
}
.ivu-tag-cyan {
  background: rgba(25, 190, 170, 0.1);
  border-color: #19be6b !important;
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
</style>
