<template>
  <div>
    <el-form
      ref="formData"
      :model="table"
      :label-width="labelWidth"
      label-position="right"
      inline
      @submit.native.prevent
    >
      <el-form-item label="Số thẻ:">
        <el-input v-model="table.card_number" placeholder="Vui lòng nhập số thẻ" class="form_content_width" />
      </el-form-item>
      <el-form-item label="Số điện thoại:">
        <el-input v-model="table.phone" placeholder="Vui lòng nhập số điện thoại" class="form_content_width" />
      </el-form-item>
      <el-form-item label="Trạng thái nhận:">
        <el-select clearable v-model="table.is_use" class="form_content_width">
          <el-option value="1" label="Đã nhận"></el-option>
          <el-option value="0" label="Chưa nhận"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" v-db-click @click="formSubmit">Tra cứu</el-button>
      </el-form-item>
    </el-form>
    <el-table
      :data="data1"
      ref="table"
      v-loading="loading"
      highlight-current-row
      no-userFrom-text="Chưa có dữ liệu"
      no-filtered-userFrom-text="Không có kết quả phù hợp"
    >
      <el-table-column label="Mã số" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Số thẻ" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.card_number }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Mật khẩu" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.card_password }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Tên người nhận" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.username ? scope.row.username : '-' }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Số điện thoại người nhận" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.phone ? scope.row.phone : '-' }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Thời gian nhận" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.use_time }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Kích hoạt" min-width="100">
        <template slot-scope="scope">
          <el-switch
            :active-value="1"
            :inactive-value="0"
            v-model="scope.row.status"
            :value="scope.row.status"
            @change="onchangeIsShow(scope.row)"
            size="large"
          >
          </el-switch>
        </template>
      </el-table-column>
    </el-table>
    <div class="acea-row row-right page">
      <pagination
        v-if="total"
        :total="total"
        :page.sync="table.page"
        :limit.sync="table.limit"
        @pagination="getMemberCard"
      />
    </div>
  </div>
</template>

<script>
import { userMemberCard, memberRecord, memberCardStatus } from '@/api/user';
import { mapState } from 'vuex';

export default {
  name: 'card',
  props: {
    id: {
      default: 0,
    },
  },
  data() {
    return {
      data1: [],
      loading: false,
      total: 0,
      table: {
        page: 1,
        limit: 15,
        card_number: '',
        phone: '',
        is_use: '',
      },
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
    this.getMemberCard();
  },
  methods: {
    onchangeIsShow(row) {
      let data = {
        card_id: row.id,
        status: row.status,
      };
      memberCardStatus(data)
        .then((res) => {
          this.$message.success(res.msg);
          this.getMemberCard();
        })
        .catch((err) => {
          this.$message.error(err.msg);
          this.getMemberCard();
        });
    },
    getMemberCard() {
      this.loading = true;
      userMemberCard(this.id, this.table)
        .then((res) => {
          this.loading = false;
          this.data1 = res.data.list;
          this.total = res.data.count;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    // Tìm kiếm
    formSubmit() {
      this.table.page = 1;
      this.getMemberCard();
    },
  },
};
</script>
