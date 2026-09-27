<template>
  <el-dialog :visible.sync="modals" title="Nhiệm vụ lên hạng" :close-on-click-modal="false" width="1000px" @closed="handleReset">
    <el-form
      ref="levelFrom"
      :model="levelFrom"
      :label-width="labelWidth"
      :label-position="labelPosition"
      @submit.native.prevent
    >
      <el-row :gutter="24">
        <el-col v-bind="grid">
          <el-form-item label="Tên phiếu giảm giá:" prop="status2" label-for="status2">
            <el-input
              search
              enter-button
              v-model="levelFrom.name"
              placeholder="Vui lòng nhập tên phiếu giảm giá"
              @on-search="userSearchs"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <el-divider direction="vertical" dashed />
    <el-row>
      <el-col v-bind="grid" class="mb15">
        <el-button type="primary" v-db-click @click="add">Thêm nhiệm vụ cấp bậc</el-button>
      </el-col>
      <el-col :span="24" class="userAlert">
        <el-alert type="info" show-icon closable>
          <template slot="title">
            Thêm nhiệm vụ lên hạng, {$num} trong loại nhiệm vụ sẽ tự động được thay bằng số lượng giới hạn + đơn vị mặc định của hệ thống để tạo tên nhiệm vụ
          </template>
        </el-alert>
      </el-col>
    </el-row>
    <el-divider direction="vertical" dashed />
    <el-table
      :data="levelLists"
      ref="table"
      v-loading="loading"
      no-userFrom-text="Chưa có dữ liệu"
      no-filtered-userFrom-text="Không có kết quả phù hợp"
    >
      <el-table-column label="ID" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Tên cấp bậc" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.level_name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Tên nhiệm vụ" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Hiển thị" min-width="100">
        <template slot-scope="scope">
          <el-switch
            :active-value="1"
            :inactive-value="0"
            v-model="scope.row.is_show"
            :value="scope.row.is_show"
            size="large"
            @change="onchangeIsShow(scope.row)"
          >
          </el-switch>
        </template>
      </el-table-column>
      <el-table-column label="Bắt buộc đạt được" min-width="100">
        <template slot-scope="scope">
          <el-switch
            :active-value="1"
            :inactive-value="0"
            v-model="scope.row.is_must"
            :value="scope.row.is_must"
            size="large"
            @change="onchangeIsMust(scope.row)"
            active-text="Tất cả"
            inactive-text="Bất kỳ"
          >
          </el-switch>
        </template>
      </el-table-column>
      <el-table-column label="Mô tả nhiệm vụ" min-width="130">
        <template slot-scope="scope">
          <span>{{ scope.row.illustrate }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Thao tác" fixed="right" width="170">
        <template slot-scope="scope">
          <a v-db-click @click="edit(scope.row)">Sửa | </a>
          <el-divider direction="vertical"></el-divider>
          <a v-db-click @click="del(scope.row, 'Xóa nhiệm vụ cấp độ', index)"> Xóa</a>
        </template>
      </el-table-column>
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
import { mapState, mapMutations } from 'vuex';
import { taskListApi, setTaskShowApi, setTaskMustApi, createTaskApi } from '@/api/user';
import editFrom from '@/components/from/from';
export default {
  name: 'sendOut',
  components: { editFrom },
  data() {
    return {
      // levelIds: this.levelId,
      grid: {
        xl: 10,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      modals: false,
      levelFrom: {
        is_show: '',
        name: '',
        page: 1,
        limit: 20,
      },
      total: 0,
      levelLists: [],
      loading: false,

      FromData: null,
      ids: 0,
      modalTitleSs: '',
      titleType: 'task',
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    ...mapState('userLevel', ['levelId']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  methods: {
    ...mapMutations('userLevel', ['getTaskId', 'getlevelId']),
    // Thêm
    add() {
      this.ids = '';
      this.getFrom();
    },
    // Form tạo mới / sửa
    getFrom() {
      let data = {
        id: this.ids,
        level_id: this.levelId,
      };
      createTaskApi(data)
        .then(async (res) => {
          this.FromData = res.data;
          this.$refs.edits.modals = true;
          this.getTaskId(this.ids);
          // this.getlevelId(this.levelId);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {
      this.ids = row.id;
      this.getFrom();
    },
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
    // Sửa hiện/ẩn
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        is_show: row.is_show,
      };
      setTaskShowApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Đặt nhiệm vụ có hoàn thành hay không
    onchangeIsMust(row) {
      let data = {
        id: row.id,
        is_must: row.is_must,
      };
      setTaskMustApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Tạo mới / sửa gửi thành công
    submitFail() {
      this.getList();
    },
    // Xóa nhiệm vụ
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `user/user_level/delete_task/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.levelLists.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style scoped></style>
