<template>
  <div>
    <el-dialog :visible.sync="modals" :z-index="100" title="Gửi phiếu giảm giá" :close-on-click-modal="false" width="1000px">
      <div class="acea-row">
        <span class="sp">Tên phiếu giảm giá:</span
        ><el-input clearable v-model="page.coupon_title" placeholder="Vui lòng nhập tên phiếu giảm giá" class="form_content_width" />
        <el-button type="primary" v-db-click @click="userSearchs" class="ml15">Tra cứu</el-button>
      </div>
      <el-table
        :data="couponList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Tên phiếu giảm giá" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mệnh giá phiếu giảm giá" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.coupon_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá trị đơn tối thiểu để dùng phiếu" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.use_min_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời hạn hiệu lực phiếu giảm giá" min-width="130">
          <template slot-scope="scope">
            <div v-if="scope.row.coupon_time">{{ scope.row.coupon_time }}</div>
            <div v-else>{{ scope.row.use_time }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="90">
          <template slot-scope="scope">
            <a v-db-click @click="sendGrant(scope.row, 'Gửi phiếu giảm giá', index)">Gửi</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination v-if="total" :total="total" :page.sync="page.page" :limit.sync="page.limit" @pagination="getList" />
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { couponApi } from '@/api/user';
export default {
  name: 'send',
  props: {
    userIds: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      modals: false,
      loading: false,
      couponList: [],
      page: {
        page: 1, // Trang hiện tại
        limit: 15,
        coupon_title: '',
        receive_type: 3,
      },
      total: 0, // Tổng số bản ghi
    };
  },
  methods: {
    // Danh sách phiếu giảm giá
    getList(id) {
      this.loading = true;
      couponApi(this.page)
        .then(async (res) => {
          if (res.status === 200) {
            let data = res.data;
            this.couponList = data.list;
            this.total = data.count;
            this.loading = false;
          } else {
            this.loading = false;
            this.$message.error(res.msg);
          }
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.getList();
    },
    // Gửi
    sendGrant(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/coupon/user/grant`,
        method: 'post',
        ids: {
          id: row.id,
          uid: this.userIds,
        },
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.sp {
  font-size: 12px;
  color: #606266;
  line-height: 32px;
}
</style>
