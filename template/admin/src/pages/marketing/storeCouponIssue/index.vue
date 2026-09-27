<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="tableFrom"
          :model="tableFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Tên phiếu giảm giá:" label-for="coupon_title">
            <el-input
              v-model="tableFrom.coupon_title"
              placeholder="Vui lòng nhập tên phiếu giảm giá"
              class="form_content_width"
              maxlength="18"
              show-word-limit
              clearable
            />
          </el-form-item>
          <el-form-item label="Loại phiếu giảm giá:" label-for="coupon_type">
            <el-select
              v-model="tableFrom.coupon_type"
              placeholder="Vui lòng chọn"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="0" label="Phiếu toàn cửa hàng"></el-option>
              <el-option value="1" label="Phiếu theo danh mục"></el-option>
              <el-option value="2" label="Phiếu theo sản phẩm"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Trạng thái hiệu lực:" label-for="status">
            <el-select
              v-model="tableFrom.status"
              placeholder="Vui lòng chọn"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Bình thường"></el-option>
              <el-option value="0" label="Chưa bật"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Hình thức phát hành:" label-for="status">
            <el-select
              v-model="receive_type"
              placeholder="Vui lòng chọn"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="all" label="Tất cả"></el-option>
              <el-option value="1" label="Người dùng tự nhận"></el-option>
              <el-option value="2" label="Hệ thống tặng"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button v-auth="['admin-marketing-store_coupon-add']" type="primary" icon="md-add" v-db-click @click="add"
        >Thêm phiếu giảm giá</el-button
      >
      <el-table
        :data="tableList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên phiếu giảm giá" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.coupon_title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại phiếu giảm giá" min-width="80">
          <template slot-scope="scope">
            <span v-if="scope.row.type === 1">Phiếu theo danh mục</span>
            <span v-else-if="scope.row.type === 2">Phiếu theo sản phẩm</span>
            <span v-else-if="scope.row.type === 3">Phiếu thành viên</span>
            <span v-else>Phiếu toàn cửa hàng</span>
          </template>
        </el-table-column>
        <el-table-column label="Mệnh giá" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.coupon_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hình thức nhận" min-width="150">
          <template slot-scope="scope">
            <span v-if="scope.row.receive_type === 1 || scope.row.receive_type === 4">Người dùng tự nhận</span>
            <span v-else>Hệ thống tặng</span>
          </template>
        </el-table-column>
        <el-table-column label="Ngày nhận" min-width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.start_time">
              {{ scope.row.start_time | formatDate }} - {{ scope.row.end_time | formatDate }}
            </div>
            <span v-else>Không giới hạn thời gian</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian sử dụng" min-width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.start_use_time">
              {{ scope.row.start_use_time | formatDate }} -
              {{ scope.row.end_use_time | formatDate }}
            </div>
            <div v-else>{{ scope.row.coupon_time }} ngày</div>
          </template>
        </el-table-column>
        <el-table-column label="Số lượng phát hành" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.is_permanent">Không giới hạn</span>
            <div v-else>
              <span class="fa">Phát hành: {{ scope.row.total_count }}</span>
              <span class="sheng">Còn lại: {{ scope.row.remain_count }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Bật" min-width="100">
          <template slot-scope="scope">
            <el-switch
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              size="large"
              @change="openChange(scope.row)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="200">
          <template slot-scope="scope">
            <a v-db-click @click="receive(scope.row)">Lịch sử nhận</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="copy(scope.row)">Sao chép</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="couponDel(scope.row, 'Xóa phiếu giảm giá đã phát hành', scope.$index)">Xóa</a>
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
    <!-- Lịch sử nhận -->
    <el-dialog :visible.sync="modals2" title="Lịch sử nhận" :close-on-click-modal="false" width="720px">
      <el-table
        :data="receiveList"
        ref="table"
        v-loading="loading2"
        highlight-current-row
        height="500"
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên người dùng" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện người dùng" min-width="150">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian nhận" min-width="150">
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
import {
  releasedListApi,
  releasedissueLogApi,
  releaseStatusApi,
  delCouponReleased,
  couponStatusApi,
} from '@/api/marketing';
import { formatDate } from '@/utils/validate';
export default {
  name: 'marketing_storeCouponIssue',
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
      modals2: false,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,

      tableFrom: {
        status: '',
        coupon_type: '',
        coupon_title: '',
        receive_type: '',
        page: 1,
        limit: 15,
      },
      receive_type: '',
      tableList: [],
      total: 0,
      FromData: null,
      receiveList: [],
      loading2: false,
      total2: 0,
      receiveFrom: {
        page: 1,
        limit: 15,
      },
      rows: {},
    };
  },
  activated() {
    this.getList();
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '90px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  methods: {
    // Hết hiệu lực
    couponInvalid(row, tit, num) {
      this.delfromData = {
        title: tit,
        num: num,
        url: `marketing/coupon/status/${row.id}`,
        method: 'PUT',
        ids: '',
      };
      this.$refs.modelSure.modals = true;
    },
    // Lịch sử nhận
    receive(row) {
      this.modals2 = true;
      this.rows = row;
      this.getReceivelist(row);
    },
    getReceivelist(row) {
      this.loading2 = true;
      releasedissueLogApi(row.id, this.receiveFrom)
        .then(async (res) => {
          let data = res.data;
          this.receiveList = data.list;
          this.total2 = res.data.count;
          this.loading2 = false;
        })
        .catch((res) => {
          this.loading2 = false;
          this.$message.error(res.msg);
        });
    },
    // Thay đổi phân trang của lịch sử nhận
    receivePageChange(index) {
      this.receiveFrom.page = index;
      this.getReceivelist(this.rows);
    },
    // Xóa
    couponDel(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/coupon/released/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tableList.splice(num, 1);
          this.total--;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.tableFrom.receive_type = this.receive_type === 'all' ? '' : this.receive_type;
      this.tableFrom.status = this.tableFrom.status || '';
      releasedListApi(this.tableFrom)
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
    // Thêm phiếu giảm giá
    add() {
      this.$router.push({ path: this.$routeProStr + '/marketing/store_coupon_issue/create' });
    },
    // Sao chép
    copy(data) {
      this.$router.push({
        path: this.$routeProStr + `/marketing/store_coupon_issue/create/${data.id}`,
      });
    },
    // Sao chép
    edit(data) {
      this.$router.push({
        path: this.$routeProStr + `/marketing/store_coupon_issue/create/${data.id}/1`,
      });
    },
    // Bật
    openChange(data) {
      couponStatusApi(data).then(() => this.getList());
    },
  },
};
</script>

<style lang="scss" scoped>
.fa {
  color: #0a6aa1;
  display: block;
}
.sheng {
  color: #ff0000;
  display: block;
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
