<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-alert type="warning" :closable="false" class="alert-info">
        <template slot="title">
          API lấy Token truy cập:<br />
          URL yêu cầu: /outapi/access_token Phương thức yêu cầu: POST Tham số yêu cầu: appid và appsecret Dữ liệu trả về: access_token: token truy cập exp_time: thời gian hết hạn token auth_info: thông tin cấp phép<br />
          Dùng Token đã lấy được để truy cập API bên ngoài:<br />
          Thêm trường Authorization vào header yêu cầu HTTP, giá trị trường là Bearer access_token (lưu ý sau Bearer có một dấu cách)
        </template>
      </el-alert>
      <el-form
        ref="formValidate"
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <el-row>
          <el-col v-bind="grid">
            <el-button v-auth="['setting-system_admin-add']" type="primary" v-db-click @click="add">Thêm tài khoản</el-button>
          </el-col>
        </el-row>
      </el-form>
      <el-table
        :data="list"
        class="mt14"
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
        v-loading="loading"
        highlight-current-row
      >
        <el-table-column label="Mã số" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tài khoản" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.appid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mô tả" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thêm" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian đăng nhập cuối" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.last_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="IP đăng nhập gần nhất" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.ip }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="130">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeIsShow(scope.row)"
              size="large"
              active-text="Bật"
              inactive-text="Tắt"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="140">
          <template slot-scope="scope">
            <a v-db-click @click="setUp(scope.row)">Cài đặt</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa tài khoản', scope.$index)">Xóa</a>
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
    <el-dialog
      :visible.sync="modals"
      :title="type == 0 ? 'Thêm tài khoản' : 'Sửa tài khoản'"
      :close-on-click-modal="false"
      :show-close="true"
      width="720px"
    >
      <el-form
        ref="modalsdate"
        :model="modalsdate"
        :rules="type == 0 ? ruleValidate : editValidate"
        label-width="80px"
        label-position="right"
      >
        <el-form-item label="Tài khoản:" prop="appid">
          <div style="display: flex">
            <el-input type="text" v-model="modalsdate.appid" :disabled="type != 0"></el-input>
          </div>
        </el-form-item>
        <el-form-item label="Mật khẩu:" prop="appsecret">
          <div style="display: flex">
            <el-input type="text" v-model="modalsdate.appsecret" class="input"></el-input>
            <el-button type="primary" v-db-click @click="reset" class="reset">Ngẫu nhiên</el-button>
          </div>
        </el-form-item>
        <el-form-item label="Mô tả:" prop="title">
          <div style="display: flex">
            <el-input type="textarea" v-model="modalsdate.title"></el-input>
          </div>
        </el-form-item>
        <el-form-item label="Quyền API:" prop="title">
          <!-- <el-checkbox-group v-model="modalsdate.rules">
            <el-checkbox
              :disabled="[2, 3].includes(item.id)"
              style="width: 30%"
              v-for="item in intList"
              :key="item.id"
              :label="item.id"
              >{{ item.name }}</el-checkbox
            >
          </el-checkbox-group> -->
          <el-tree
            :data="intList"
            :props="props"
            multiple
            show-checkbox
            ref="tree"
            node-key="id"
            :default-checked-keys="selectIds"
            @check-change="selectTree"
          ></el-tree>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel">Hủy</el-button>
        <el-button type="primary" v-db-click @click="ok('modalsdate')">Xác nhận</el-button>
      </span>
    </el-dialog>
    <el-dialog
      :visible.sync="settingModals"
      scrollable
      title="Cài đặt đẩy dữ liệu"
      width="1000px"
      :close-on-click-modal="false"
      :show-close="true"
    >
      <el-form
        class="setting-style"
        ref="settingData"
        :model="settingData"
        :rules="type == 0 ? ruleValidate : editValidate"
        label-width="155px"
        label-position="right"
      >
        <el-form-item label="Bật/tắt đẩy dữ liệu:" prop="switch">
          <el-switch v-model="settingData.push_open" :active-value="1" :inactive-value="0" />
        </el-form-item>
        <el-form-item label="Tài khoản đẩy dữ liệu:" prop="push_account">
          <div class="form-content">
            <el-input type="text" v-model="settingData.push_account" placeholder="Vui lòng nhập tài khoản đẩy dữ liệu"></el-input>
            <span class="tips-info">Tài khoản để bên nhận dữ liệu đẩy lấy token</span>
          </div>
        </el-form-item>
        <el-form-item label="Mật khẩu đẩy dữ liệu:" prop="push_password">
          <div class="form-content">
            <el-input type="text" v-model="settingData.push_password" placeholder="Vui lòng nhập mật khẩu đẩy dữ liệu"></el-input>
            <span class="tips-info">Mật khẩu để bên nhận dữ liệu đẩy lấy token</span>
          </div>
        </el-form-item>
        <el-form-item label="API lấy TOKEN:" prop="push_token_url">
          <div class="form-content">
            <div class="input-button">
              <el-input type="text" v-model="settingData.push_token_url" placeholder="Vui lòng nhập API lấy TOKEN"></el-input>
              <el-button class="ml10" type="primary" v-db-click @click="textOutUrl(settingData.id)">Kiểm tra liên kết</el-button>
            </div>
            <span class="tips-info"
              >Địa chỉ URL để bên nhận dữ liệu đẩy lấy token, phương thức POST, truyền vào push_account và push_password, trả về token và thời gian hiệu lực time (giây)</span
            >
          </div>
        </el-form-item>
        <el-form-item label="API đẩy dữ liệu khi sửa thông tin người dùng:" prop="user_update_push">
          <div class="form-content">
            <el-input
              type="text"
              v-model="settingData.user_update_push"
              placeholder="Vui lòng nhập API đẩy dữ liệu khi sửa thông tin người dùng"
            ></el-input>
            <span class="tips-info">Khi điểm thưởng, số dư, điểm kinh nghiệm... của người dùng thay đổi, thông tin người dùng sẽ được đẩy đến địa chỉ này, phương thức POST</span>
          </div>
        </el-form-item>
        <el-form-item label="API đẩy dữ liệu khi tạo đơn hàng:" prop="order_create_push">
          <div class="form-content">
            <el-input
              type="text"
              v-model="settingData.order_create_push"
              placeholder="Vui lòng nhập API đẩy dữ liệu khi tạo đơn hàng"
            ></el-input>
            <span class="tips-info">Khi tạo đơn hàng, thông tin đơn hàng sẽ được đẩy đến địa chỉ này, phương thức POST</span>
          </div>
        </el-form-item>
        <el-form-item label="API đẩy dữ liệu khi thanh toán đơn hàng:" prop="order_pay_push">
          <div class="form-content">
            <el-input type="text" v-model="settingData.order_pay_push" placeholder="Vui lòng nhập API đẩy dữ liệu khi thanh toán đơn hàng"></el-input>
            <span class="tips-info">Khi đơn hàng thanh toán xong, thông tin đơn hàng đã thanh toán sẽ được đẩy đến địa chỉ này, phương thức POST</span>
          </div>
        </el-form-item>
        <el-form-item label="API đẩy dữ liệu khi tạo đơn đổi trả:" prop="refund_create_push">
          <div class="form-content">
            <el-input
              type="text"
              v-model="settingData.refund_create_push"
              placeholder="Vui lòng nhập API đẩy dữ liệu khi tạo đơn đổi trả"
            ></el-input>
            <span class="tips-info">Khi đơn đổi trả được tạo, thông tin đơn đổi trả sẽ được đẩy đến địa chỉ này, phương thức POST</span>
          </div>
        </el-form-item>
        <el-form-item label="API đẩy dữ liệu khi hủy đơn đổi trả:" prop="refund_cancel_push">
          <div class="form-content">
            <el-input
              type="text"
              v-model="settingData.refund_cancel_push"
              placeholder="Vui lòng nhập API đẩy dữ liệu khi hủy đơn đổi trả"
            ></el-input>
            <span class="tips-info">Khi đơn đổi trả bị hủy, thông tin hủy đơn đổi trả sẽ được đẩy đến địa chỉ này, phương thức POST</span>
          </div>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button type="primary" v-db-click @click="submit('settingData')">Xác nhận</el-button>
        <el-button v-db-click @click="settingModals = false">Hủy</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import {
  accountListApi,
  outSaveApi,
  outSavesApi,
  setShowApi,
  outSetUp,
  interfaceList,
  setUpPush,
  textOutUrl,
} from '@/api/systemOutAccount';
export default {
  name: 'systemOut',
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      total: 0,
      loading: false,
      roleData: {
        status1: '',
      },
      formValidate: {
        roles: '',
        status: '',
        name: '',
        page: 1, // Trang hiện tại
        limit: 20, // Số mục hiển thị mỗi trang
      },
      status: '',
      list: [],
      intList: [],
      FromData: null,
      modalTitleSs: '',
      ids: Number,
      modals: false,
      modalsid: '',
      type: 0,
      modalsdate: {
        appid: '',
        appsecret: '',
        title: '',
        rules: [],
      },
      settingModals: false,
      settingData: {
        switch: 1,
        name: '',
      },
      ruleValidate: {
        appid: [{ required: true, message: 'Vui lòng nhập tài khoản hợp lệ (từ 4 đến 30 ký tự)', trigger: 'blur', min: 4, max: 30 }],
        appsecret: [{ required: true, message: 'Vui lòng nhập mật khẩu hợp lệ (từ 6 đến 32 ký tự)', trigger: 'blur', min: 6, max: 32 }],
        title: [{ message: 'Vui lòng nhập mô tả hợp lệ (không quá 200 ký tự)', trigger: 'blur', max: 200 }],
      },
      editValidate: {
        appsecret: [{ required: false, message: 'Vui lòng nhập mật khẩu hợp lệ (từ 6 đến 32 ký tự)', trigger: 'blur', min: 6, max: 32 }],
      },
      props: {
        label: 'title',
        disabled: 'disableCheckbox',
      },
      selectIds: [],
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '50px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getList();
  },
  methods: {
    // Trạng thái bật
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      setShowApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Yêu cầu danh sách
    submitFail() {
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.formValidate.roles = this.formValidate.roles || '';
      accountListApi(this.formValidate)
        .then(async (res) => {
          this.total = res.data.count;
          this.list = res.data.list;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Thêm
    add() {
      this.modals = true;
      this.type = 0;
      this.modalsdate = {
        appid: '',
        appsecret: '',
        title: '',
        rules: [],
      };
      this.getIntList();
    },
    selectTree(e, i) {},
    getIntList(type, list) {
      let arr = [];
      interfaceList().then((res) => {
        this.intList = res.data;
        if (!type) {
          this.intList.map((item) => {
            if (item.id === 1) {
              item.checked = true;
              item.disableCheckbox = true;
              arr.push(item.id);
              if (item.children.length) {
                item.children.map((v) => {
                  v.checked = true;
                  v.disableCheckbox = true;
                  arr.push(v.id);
                });
              }
            }
          });
          this.$nextTick((e) => {
            this.selectIds = arr;
          });
        } else {
          list.map((item) => {
            this.intList.map((e) => {
              if (e.id === 1) {
                e.checked = true;
                e.disableCheckbox = true;
                if (e.children.length) {
                  e.children.map((v) => {
                    v.checked = true;
                    v.disableCheckbox = true;
                  });
                }
              }
              listData(e.children || [], item);
            });
          });
          this.selectIds = list;
        }
        function listData(list, id) {
          if (list.length) {
            list.map((v) => {
              if (v.id == id) {
                v.checked = true;
              }
              if (v.children) {
                listData(v.children);
              }
            });
          }
        }
      });
    },
    // Sửa
    edit(row) {
      this.modals = true;
      this.modalsdate.appid = row.appid;
      this.modalsdate.title = row.title;
      this.modalsdate.appsecret = row.apppwd;
      this.modalsdate.rules = row.rules.map((e) => {
        return Number(e);
      });
      this.modalsid = row.id;
      this.type = 1;
      this.getIntList('edit', this.modalsdate.rules);
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `setting/system_out_account/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.list.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa
    setUp(row) {
      this.settingModals = true;
      this.settingData = row;
    },
    // Tìm kiếm
    userSearchs() {
      this.formValidate.status = this.status === 'all' ? '' : this.status;
      this.formValidate.page = 1;
      this.list = [];
      this.getList();
    },
    submit(name) {
      setUpPush(this.settingData).then((res) => {
        this.$message.success(res.msg);
        this.settingModals = false;
        this.getList();
      });
    },
    textOutUrl() {
      textOutUrl(this.settingData)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    ok(name) {
      let fuc = this.modalsid ? outSavesApi : outSaveApi;
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.modalsdate.rules = [];
          this.$refs.tree.getCheckedNodes().map((node) => {
            this.modalsdate.rules.push(node.id);
          });
          if (this.modalsid) this.modalsdate.id = this.modalsid;
          fuc(this.modalsdate)
            .then((res) => {
              this.modalsdate = {
                appid: '',
                appsecret: '',
                title: '',
                rules: [],
              };
              (this.modals = false), this.$message.success(res.msg);
              this.modalsid = '';
              this.getList();
            })
            .catch((err) => {
              this.$message.error(err.msg);
            });
        } else {
          this.$message.warning('Vui lòng hoàn thiện dữ liệu');
        }
      });
    },
    cancel() {
      this.modalsid = '';
      this.modalsdate = {
        appid: '',
        appsecret: '',
        title: '',
      };
      this.modals = false;
    },
    reset() {
      let len = 16;
      let chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
      let maxPos = chars.length;
      let pwd = '';
      for (let i = 0; i < len; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * maxPos));
      }
      this.modalsdate.appsecret = pwd;
    },
  },
};
</script>

<style scoped>
.reset {
  margin-left: 10px;
}
.form-content {
  display: flex;
  flex-direction: column;
}
.input-button {
  display: flex;
}
.setting-style ::v-deep .ivu-form-item {
  margin-bottom: 14px;
}
.alert-info {
  margin-bottom: 14px;
}
</style>
