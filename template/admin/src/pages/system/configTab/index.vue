<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Hiển thị:">
            <el-select
              v-model="formValidate.status"
              placeholder="Vui lòng chọn"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Hiện"></el-option>
              <el-option value="0" label="Không hiển thị"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tên danh mục:" label-for="status2">
            <el-input clearable placeholder="Vui lòng nhập tên danh mục" v-model="formValidate.title" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tìm kiếm danh mục</el-button>
          </el-form-item>
          <div>
            <el-form-item label="Tên cấu hình:" label-for="status2">
              <el-input clearable placeholder="Vui lòng nhập tên cấu hình" v-model="config_name" class="form_content_width" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" v-db-click @click="searchConfig">Tra cứu cấu hình</el-button>
            </el-form-item>
          </div>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-button type="primary" v-db-click @click="classAdd" class="mr20">Thêm danh mục cấu hình</el-button>
      <vxe-table
        :border="false"
        class="vxeTable mt14"
        highlight-hover-row
        highlight-current-row
        :loading="loading"
        ref="xTable"
        :tree-config="tabconfig"
        :data="classList"
        row-id="id"
      >
        <vxe-table-column field="id" title="ID" tooltip width="85"></vxe-table-column>
        <vxe-table-column field="title" tree-node title="Tên danh mục" min-width="150"></vxe-table-column>
        <vxe-table-column field="eng_title" title="Trường danh mục" min-width="150"></vxe-table-column>
        <vxe-table-column field="statuss" title="Trạng thái" width="250">
          <template v-slot="{ row }">
            <el-switch
              :active-value="1"
              :inactive-value="0"
              v-model="row.status"
              :value="row.status"
              @change="onchangeIsShow(row)"
              size="large"
            >
            </el-switch>
          </template>
        </vxe-table-column>
        <vxe-table-column field="action" title="Thao tác" width="160" fixed="right">
          <template v-slot="{ row, index }">
            <a v-db-click @click="goList(row)">Danh sách cấu hình</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="edit(row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(row, 'Xóa danh mục', index)">Xóa</a>
          </template>
        </vxe-table-column>
      </vxe-table>
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

    <!-- Tạo mới  Sửa biểu mẫu-->
    <edit-from ref="edits" :update="true" :FromData="FromData" @submitFail="submitFail"></edit-from>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import editFrom from '@/components/from/from';
import { classListApi, classAddApi, classEditApi, setStatusApi } from '@/api/system';
export default {
  name: 'configTab',
  components: { editFrom },
  data() {
    return {
      tabconfig: { children: 'children', reserve: true, accordion: true },
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      formValidate: {
        status: '',
        page: 1,
        limit: 100,
        title: '',
      },
      total: 0,
      FromData: null,
      classId: 0,
      classList: [],
      config_name: '',
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
    // Chuyển đến trang danh sách cấu hình
    goList(row) {
      this.$router.push({
        path: this.$routeProStr + '/system/config/system_config_tab/list/' + row.id,
      });
    },
    // Thêm danh mục cấu hình
    classAdd() {
      classAddApi()
        .then(async (res) => {
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {
      classEditApi(row.id)
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
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `setting/config_class/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          // this.classList.splice(num, 1);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.formValidate.status = this.formValidate.status || '';
      classListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.classList = data.list;
          this.total = data.count;
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
    //Tìm kiếm mục cấu hình
    searchConfig() {
      if (this.config_name == '') {
        return this.$message.error('Vui lòng nhập tên cấu hình cần tìm');
      }
      this.$router.push({
        path: this.$routeProStr + '/system/config/system_config_tab/list/0?config_name=' + this.config_name,
      });
    },
    // Sửa thành công
    submitFail() {
      //   this.getList();
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      setStatusApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style scoped></style>
