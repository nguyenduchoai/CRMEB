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
          <el-form-item label="Trạng thái hiệu lực:">
            <el-select
              placeholder="Vui lòng chọn"
              clearable
              v-model="tableFrom.status"
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Đã sử dụng"></el-option>
              <el-option value="0" label="Chưa sử dụng"></el-option>
              <el-option value="2" label="Đã hết hạn"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Người nhận:" label-for="nickname">
            <el-input placeholder="Vui lòng nhập người nhận" v-model="tableFrom.nickname" clearable class="form_content_width" />
          </el-form-item>
          <el-form-item label="Tìm kiếm phiếu giảm giá:" label-for="coupon_title">
            <el-input
              clearable
              placeholder="Vui lòng nhập tên phiếu giảm giá"
              v-model="tableFrom.coupon_title"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-table :data="tableList">
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
        <el-table-column label="Người nhận" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.nickname }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mệnh giá" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.coupon_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá trị đơn tối thiểu" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.use_min_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian bắt đầu sử dụng" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.start_time | formatDate }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian kết thúc sử dụng" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.end_time | formatDate }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Cách nhận" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.type }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Khả dụng" min-width="150">
          <template slot-scope="scope">
            <i class="el-icon-check" v-if="scope.row.is_fail === 0" style="color: #0092dc; font-size: 14px" />
            <i class="el-icon-close" v-else style="color: #ed5565; font-size: 14px" />
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.status }}</span>
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
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { userListApi } from '@/api/marketing';
import { formatDate } from '@/utils/validate';
export default {
  name: 'storeCouponUser',
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
      tableList: [],
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        status: '',
        coupon_title: '',
        nickname: '',
        page: 1,
        limit: 15,
      },
      total: 0,
    };
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
  created() {
    this.getList();
  },
  methods: {
    // Danh sách
    getList() {
      this.loading = true;
      this.tableFrom.status = this.tableFrom.status || '';
      userListApi(this.tableFrom)
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

<style scoped></style>
