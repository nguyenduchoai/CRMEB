<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-row>
        <el-col v-bind="grid">
          <el-button v-auth="['admin-template']" type="primary" v-db-click @click="add">Thêm mẫu</el-button>
        </el-col>
      </el-row>
      <el-table
        :data="list"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID trang" width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên trang" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại trang" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.template_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thêm" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian cập nhật" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.update_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <div style="display: inline-block" v-if="scope.row.status != 1">
              <a v-db-click @click="setStatus(scope.row, index)">Đặt làm trang chủ</a>
            </div>
            <el-divider direction="vertical" v-if="scope.row.status != 1" />
            <div style="display: inline-block" v-if="scope.row.status || scope.row.type">
              <a v-db-click @click="edit(scope.row)">Sửa</a>
            </div>
            <el-divider direction="vertical" v-if="scope.row.status || scope.row.type" />
            <template>
              <el-dropdown size="small" @command="changeMenu(scope.row, index, $event)" :transfer="true">
                <span class="el-dropdown-link">Xem thêm<i class="el-icon-arrow-down el-icon--right"></i> </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item command="1" v-show="!scope.row.type">Đặt dữ liệu mặc định</el-dropdown-item>
                  <el-dropdown-item command="2" v-show="!scope.row.type">Khôi phục dữ liệu mặc định</el-dropdown-item>
                  <el-dropdown-item command="3" v-show="scope.row.id != 1">Xóa mẫu</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
    <el-dialog
      :visible.sync="isTemplate"
      title="Liên kết di động phát triển"
      width="470px"
      :show-close="true"
      :close-on-click-modal="false"
    >
      <div class="article-manager">
        <el-card :bordered="false" shadow="never" class="ivu-mt">
          <el-form
            ref="formItem"
            :model="formItem"
            label-width="120px"
            label-position="right"
            :rules="ruleValidate"
            @submit.native.prevent
          >
            <el-row :gutter="24">
              <el-col :span="24">
                <el-col v-bind="grid">
                  <el-form-item label="Liên kết di động phát triển:" prop="link" label-for="link">
                    <el-input v-model="formItem.link" placeholder="http://localhost:8080" />
                  </el-form-item>
                </el-col>
              </el-col>
            </el-row>
          </el-form>
        </el-card>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel">Hủy</el-button>
        <el-button type="primary" v-db-click @click="handleSubmit('formItem')">Gửi</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { diyList, diyDel, setStatus, recovery, getDiyCreate, getRecovery } from '@/api/diy';
import { getCookies, setCookies } from '@/libs/util';
import { mapState } from 'vuex';
export default {
  name: 'devise_list',
  data() {
    return {
      grid: {
        xl: 18,
        lg: 18,
        md: 18,
        sm: 24,
        xs: 24,
      },
      loading: false,
      list: [],
      isTemplate: false,
      formItem: {
        id: 0,
        link: '',
      },
      ruleValidate: {
        link: [{ required: true, message: 'Vui lòng nhập liên kết di động', trigger: 'blur' }],
      },
    };
  },
  created() {
    this.formItem.link = getCookies('moveLink');
    this.getList();
  },
  methods: {
    cancel() {
      this.$refs['formItem'].resetFields();
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          setCookies('moveLink', this.formItem.link);
          this.$router.push({
            path: this.$routeProStr + '/setting/pages/diy',
            query: { id: this.formItem.id, type: 1 },
          });
        } else {
          return false;
        }
      });
    },
    changeMenu(row, index, name) {
      switch (name) {
        case '1':
          this.setDefault(row);
          break;
        case '2':
          this.recovery(row);
          break;
        case '3':
          this.del(row, 'Xóa mẫu này', index);
          break;
        default:
      }
    },
    //Đặt dữ liệu mặc định
    setDefault(row) {
      getRecovery(row.id)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Thêm
    add() {
      this.$modalForm(getDiyCreate()).then(() => this.getList());
    },
    // Lấy danh sách
    getList() {
      this.loading = true;
      diyList().then((res) => {
        this.loading = false;
        this.list = res.data.list;
      });
    },
    // Sửa
    edit(row) {
      this.formItem.id = row.id;
      if (row.type) {
        this.isTemplate = true;
      } else {
        this.$router.push({ path: this.$routeProStr + '/setting/pages/diy', query: { id: row.id, type: 0 } });
      }
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `diy/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Dùng mẫu
    setStatus(row) {
      setStatus(row.id)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    recovery(row) {
      recovery(row.id).then((res) => {
        this.$message.success(res.msg);
        this.getList();
      });
    },
  },
};
</script>

<style scoped></style>
