<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="`Danh sách cấu hình${$route.query.config_name ? ` - ` + $route.query.config_name : ''}`"
      :backUrl="$routeProStr + '/system/config/system_config_tab/index'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-row v-if="!$route.query.config_name">
        <el-col v-bind="grid">
          <!-- <el-button type="primary" v-db-click @click="goIndex">Danh mục cấu hình</el-button> -->
          <el-button type="primary" v-db-click @click="configureAdd">Thêm cấu hình</el-button>
        </el-col>
      </el-row>
      <el-table
        :data="classList"
        ref="table"
        v-loading="loading"
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
        class="mt14"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên cấu hình" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.info }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Biến trường" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.menu_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại trường" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.type }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá trị" min-width="130">
          <template slot-scope="scope">
            <span
              v-if="
                scope.row.type === 'text' ||
                scope.row.type === 'textarea' ||
                scope.row.type === 'radio' ||
                scope.row.type === 'checkbox'
              "
              >{{ scope.row.value }}</span
            >
            <div class="valBox acea-row" v-if="scope.row.type === 'upload' && scope.row.upload_type === 3">
              <div v-if="scope.row.value.length">
                <div
                  class="valPicbox acea-scope.row scope.row-column-around"
                  v-for="(item, index) in scope.row.value"
                  :key="index"
                >
                  <div class="valPicbox_pic"><i class="el-icon-document" /></div>
                  <span class="valPicbox_sp">{{ item.filename }}</span>
                </div>
              </div>
            </div>
            <div class="valBox acea-row" v-if="scope.row.type === 'upload' && scope.row.upload_type !== 3">
              <div v-if="scope.row.value.length">
                <div class="valPicbox acea-row row-column-around" v-for="(item, index) in scope.row.value" :key="index">
                  <div class="valPicbox_pic"><img v-lazy="item.filepath" /></div>
                  <span class="valPicbox_sp">{{ item.filename }}</span>
                </div>
              </div>
            </div>
            <span v-if="scope.row.type === 'switch'">{{ scope.row.value == 1 ? 'Bật' : 'Tắt' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Cấu hình/Giá trị liên kết" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.link_data }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Danh mục cấu hình" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.config_tab_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hiển thị" min-width="130">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeIsShow(scope.row)"
              size="large"
              active-text="Hiện"
              inactive-text="Ẩn"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="120">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa danh mục', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- Tạo mới Biểu mẫu-->
    <el-dialog
      :visible.sync="modals2"
      :title="`${rowId ? 'Sửa' : 'Thêm'} trường cấu hình`"
      :close-on-click-modal="false"
      :show-close="true"
      width="720px"
    >
      <el-tabs v-if="!rowId" v-model="typeFrom.type" @tab-click="onhangeTab" class="tabsName">
        <el-tab-pane label="Ô văn bản " name="0"></el-tab-pane>
        <el-tab-pane label="Ô văn bản nhiều dòng" name="1"></el-tab-pane>
        <el-tab-pane label="Ô chọn một" name="2"></el-tab-pane>
        <el-tab-pane label="Tải tệp lên" name="3"></el-tab-pane>
        <el-tab-pane label="Ô chọn nhiều" name="4"></el-tab-pane>
        <el-tab-pane label="Danh sách thả xuống" name="5"></el-tab-pane>
        <el-tab-pane label="Công tắc" name="6"></el-tab-pane>
      </el-tabs>
      <form-create
        v-if="rules.length != 0"
        :rule="rules"
        v-model="fapi"
        :option="config"
        @submit="onSubmit"
        class="formBox"
        ref="fc"
        handleIcon="false"
      ></form-create>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="modals2 = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="submitForm">Xác nhận</el-button>
      </span>
    </el-dialog>
    <!-- Form sửa-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="submitFail"></edit-from>
  </div>
</template>

<script>
import { configTabListApi, configTabAddApi, configTabEditApi, configSetStatusApi } from '@/api/system';
import formCreate from '@form-create/element-ui';
import editFrom from '@/components/from/from';
import request from '@/libs/request';
export default {
  name: 'list',
  components: { formCreate: formCreate.$form(), editFrom },
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
      formValidate: {
        tab_id: 0,
        config_name: '',
        page: 1,
        limit: 20,
      },
      config: {
        form: {
          labelWidth: '100px',
        },
        resetBtn: false,
        submitBtn: false,
        global: {
          upload: {
            props: {
              onSuccess(res, file) {
                if (res.status === 200) {
                  file.url = res.data.src;
                } else {
                  this.$message.error(res.msg);
                }
              },
            },
          },
        },
      },
      total: 0,
      FromData: null,
      FromRequestData: {},
      modalTitleSs: '',
      classList: [],
      num: 0,
      typeFrom: {
        type: 0,
        tab_id: this.$route.params.id,
      },
      rules: [],
      fapi: null,
      rowId: 0,
    };
  },
  watch: {
    $route: {
      handler: function (val, oldVal) {
        this.getList();
      },
      // Theo dõi sâu
      deep: true,
    },
  },
  mounted() {
    this.getList();
  },
  methods: {
    // Nhấp tab
    onhangeTab() {
      this.classAdd();
    },
    submitForm() {
      this.fapi.submit();
    },
    // Form thêm mới
    classAdd() {
      configTabAddApi(this.typeFrom)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          let data = res.data || {};
          this.FromRequestData = { action: data.action, method: data.method };
          this.rules = data.rules;
          this.modals2 = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Form sửa
    edit(row) {
      this.rowId = row.id;
      configTabEditApi(row.id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          let data = res.data || {};
          this.FromRequestData = { action: data.action, method: data.method };
          this.rules = data.rules;
          this.$refs.edits.modals = true;
          this.modals2 = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Gửi biểu mẫu
    onSubmit(formData) {
      request({
        url: this.FromRequestData.action,
        method: this.FromRequestData.method,
        data: formData,
      })
        .then((res) => {
          this.$message.success(res.msg);
          setTimeout(() => {
            this.modals2 = false;
          }, 1000);
          setTimeout(() => {
            this.getList();
          }, 1500);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa thành công
    submitFail() {
      this.getList();
    },
    // Chuyển đến trang danh mục cấu hình
    goIndex() {
      this.$router.push({
        path: this.$routeProStr + '/system/config/system_config_tab/index',
      });
    },
    // Thêm cấu hình
    configureAdd() {
      // this.modals2 = true;
      this.rowId = 0;
      this.typeFrom.type = 0;
      this.classAdd();
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.formValidate.tab_id = this.$route.params.id;
      this.formValidate.config_name = this.$route.query.config_name;
      configTabListApi(this.formValidate)
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
    pageChange(index) {
      this.formValidate.page = index;
      this.getList();
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `/setting/config/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.classList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      configSetStatusApi(row.id, row.status)
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
<style lang="scss" scoped>
.tabsName {
  margin-bottom: 15px;
}
.valBox {
  margin: 10px 0;
}
.valPicbox {
  border: 1px solid #e7eaec;
}
.valPicbox_pic {
  width: 200px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    width: 100%;
    height: 100%;
  }
  ::v-deep .ivu-icon-md-document {
    font-size: 70px;
    color: #dadada;
  }
}
.valPicbox_sp {
  display: block;
  font-size: 12px;
  width: 200px;
  padding: 7px;
  box-sizing: border-box;
  border-top: 1px solid #e7eaec;
}
</style>
