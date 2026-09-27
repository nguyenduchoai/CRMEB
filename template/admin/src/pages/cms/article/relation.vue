<template>
  <el-dialog :visible.sync="modals" title="Chọn sản phẩm" :close-on-click-modal="false" width="1000px" @closed="handleReset">
    <el-form
      ref="levelFrom"
      :model="levelFrom"
      :label-width="labelWidth"
      :label-position="labelPosition"
      @submit.native.prevent
    >
      <el-row :gutter="24">
        <el-col v-bind="grid">
          <el-form-item label="Tên sản phẩm:" prop="status2" label-for="status2">
            <el-input
              search
              enter-button
              v-model="levelFrom.name"
              placeholder="Vui lòng nhập tên sản phẩm"
              @on-search="userSearchs"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <el-divider direction="vertical" dashed />
    <el-table
      :data="levelLists"
      ref="table"
      v-loading="loading"
      no-userFrom-text="Chưa có dữ liệu"
      no-filtered-userFrom-text="Không có kết quả phù hợp"
    >
      <template slot-scope="{ row, index }" slot="is_shows">
        <el-switch
          :active-value="1"
          :inactive-value="0"
          v-model="row.is_show"
          :value="row.is_show"
          size="large"
          @change="onchangeIsShow(row)"
        >
        </el-switch>
      </template>
      <template slot-scope="{ row, index }" slot="is_musts">
        <el-switch
          :active-value="1"
          :inactive-value="0"
          v-model="row.is_must"
          :value="row.is_must"
          size="large"
          @change="onchangeIsMust(row)"
          active-text="Hoàn thành tất cả"
          inactive-text="Đạt một trong số đó"
        >
        </el-switch>
      </template>
      <template slot-scope="{ row, index }" slot="action">
        <a v-db-click @click="edit(row)">Sửa | </a>
        <a v-db-click @click="del(row, 'Xóa nhiệm vụ')"> Xóa</a>
      </template>
    </el-table>
    <div class="acea-row row-right page">
      <pagination
        v-if="total"
        :total="total"
        :page.sync="levelFrom.page"
        :limit.sync="levelFrom.limit"
        @pagination="getList"
      />
    </div>
    <!-- Form tạo mới / sửa-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="submitFail" :titleType="titleType"></edit-from>
  </el-dialog>
</template>

<script>
export default {
  name: 'relation',
  data() {
    return {
      modals: false,
      grid: {
        xl: 12,
        lg: 12,
        md: 12,
        sm: 24,
        xs: 24,
      },
    };
  },
  methods: {
    // Đóng modal
    handleReset() {
      this.modals = false;
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.getList();
    },
    // Danh sách tác vụ
    getList() {
      this.loading = true;
      taskListApi(this.levelId, this.levelFrom)
        .then(async (res) => {
          let data = res.data;
          this.levelLists = data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    pageChange(index) {
      this.levelFrom.page = index;
      this.getList();
    },
  },
};
</script>

<style scoped></style>
