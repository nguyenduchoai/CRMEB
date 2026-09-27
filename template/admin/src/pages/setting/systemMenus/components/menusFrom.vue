<template>
  <div>
    <el-dialog :visible.sync="modals" width="540px" :title="titleFrom" :close-on-click-modal="false" @closed="visible">
      <el-form ref="formValidate" :model="formValidate" label-width="80px" @submit.native.prevent>
        <el-row :gutter="24">
          <el-col v-bind="grid">
            <el-form-item :label="!authType ? 'Tên API:' : 'Tên nút:'" prop="menu_name">
              <div class="add">
                <el-input
                  v-model="formValidate.menu_name"
                  :placeholder="!authType ? 'Vui lòng nhập tên API' : 'Vui lòng nhập tên nút'"
                >
                </el-input>
              </div>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid">
            <el-form-item label="Danh mục cha:">
              <el-cascader
                :options="menuList"
                change-on-select
                v-model="formValidate.path"
                filterable
                style="width: 100%"
              ></el-cascader>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid" v-if="authType">
            <el-form-item label="Biểu tượng:">
              <el-input v-model="formValidate.icon" placeholder="Vui lòng chọn biểu tượng, nhấp vào biểu tượng bên phải" icon="ios-appstore">
                <el-button slot="append" icon="el-icon-picture-outline" v-db-click @click="iconClick"></el-button>
              </el-input>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid" v-if="authType">
            <el-form-item label="Thứ tự sắp xếp:">
              <el-input type="number" v-model="formValidate.sort" placeholder="Vui lòng nhập thứ tự sắp xếp" number></el-input>
            </el-form-item>
          </el-col>
          <el-col v-bind="grid">
            <el-form-item label="Hiển thị:">
              <el-radio-group v-model="formValidate.is_show_path">
                <el-radio :label="item.value" v-for="(item, i) in isShowPathRadio" :key="i">
                  <span>{{ item.label }}</span>
                </el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="handleReset">Hủy</el-button>
        <el-button type="primary" v-db-click @click="handleSubmit('formValidate')" :disabled="valids">Gửi</el-button>
      </span>
    </el-dialog>
    <el-dialog :visible.sync="modal12" width="720px" title="Chọn biểu tượng">
      <el-input
        v-model="iconVal"
        placeholder="Nhập từ khóa để tìm kiếm, lưu ý chỉ dùng tiếng Anh"
        clearable
        style="width: 300px"
        @change="upIcon(iconVal)"
        ref="search"
      />
      <div class="trees-coadd">
        <div class="scollhide">
          <div class="iconlist">
            <ul class="list-inline">
              <li class="icons-item" v-for="(item, i) in iconVal ? searchData : list" :key="i" :title="item">
                <i :class="'el-icon-' + item" class="f-s-24" v-db-click @click="iconChange(item)"></i>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="ruleModal" width="1100px" title="Danh sách quyền" @closed="modalchange">
      <div class="search-rule">
        <el-input
          class="mr10"
          v-model="searchRule"
          placeholder="Nhập từ khóa để tìm kiếm"
          clearable
          style="width: 300px"
          ref="search"
        />
        <el-button type="primary" v-db-click @click="searchRules">Tìm kiếm</el-button>
        <el-button v-db-click @click="init">Đặt lại</el-button>
      </div>
      <div class="rule">
        <div
          class="rule-list"
          v-show="!arrs.length || arrs.includes(index)"
          :class="{ 'select-rule': arrs.includes(index) }"
          v-for="(item, index) in ruleList"
          :key="index"
          v-db-click
          @click="selectRule(item)"
        >
          <div>Tên API: {{ item.real_name }}</div>
          <div>Phương thức yêu cầu: {{ item.method }}</div>
          <div>Địa chỉ API: {{ item.rule }}</div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { addMenusApi, addMenus, getRuleList } from '@/api/systemMenus';
import icon from '@/utils/icon';

export default {
  name: 'menusFrom',
  props: {
    formValidate: {
      type: Object,
      default: null,
    },
    titleFrom: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      arrs: [],
      searchRule: '',
      iconVal: '',
      grid: {
        xl: 24,
        lg: 24,
        md: 24,
        sm: 24,
        xs: 24,
      },
      modals: false,
      modal12: false,
      FromData: [],
      valids: false,
      list2: [],
      list: icon,
      authType: true,
      search: icon,
      ruleModal: false,
      ruleList: [],
      searchData: [],
    };
  },
  watch: {
    'formValidate.header': function (n) {
      this.formValidate.is_header = n ? 1 : 0;
    },
    'formValidate.auth_type': function (n) {
      if (n === undefined) {
        n = 1;
      }
      this.authType = n === 1;
    },
    'formValidate.data': function (n) {},
  },
  computed: {
    /* eslint-disable */
    optionsList() {
      let a = [];
      this.FromData.map((item) => {
        if ('pid' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    headerOptionsList() {
      let a = [];
      this.FromData.map((item) => {
        if ('header' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    optionsListmodule() {
      let a = [];
      this.FromData.map((item) => {
        if ('module' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    optionsRadio() {
      let a = [];
      this.FromData.map((item) => {
        if ('auth_type' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    isheaderRadio() {
      let a = [];
      this.FromData.map((item) => {
        if ('is_header' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    isShowRadio() {
      let a = [];
      this.FromData.map((item) => {
        if ('is_show' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    isShowPathRadio() {
      let a = [];
      this.FromData.map((item) => {
        if ('is_show_path' === item.field) {
          a = item.options;
        }
      });
      return a;
    },
    menuList() {
      let a = [];
      this.FromData.map((item) => {
        if ('menu_list' === item.field) {
          a = item.props.options;
        }
      });
      return a;
    },
  },
  methods: {
    // Lấy danh sách quyền
    getRuleList() {
      getRuleList().then((res) => {
        this.ruleList = res.data;
        this.ruleModal = true;
      });
    },
    modalchange() {
      this.arrs = [];
      this.ruleModal = '';
      this.ruleModal = false;
    },
    changeUnique(val) {
      let value = this.$routeProStr + val.target.value;
      if (value.slice(0, 1) === '/') value = value.replace('/', '');
      this.formValidate.unique_auth = value.replaceAll('/', '-');
    },
    visible() {
      this.authType = true;
    },
    selectRule(data) {
      this.$emit('selectRule', data);
      this.$nextTick((e) => {
        this.ruleModal = false;
      });
    },
    changeRadio(n) {
      this.authType = n === 1 ? true : false;
    },
    // Tìm kiếm
    upIcon(n) {
      this.searchData = this.list.filter((item) => item.indexOf(this.iconVal) > -1);
    },
    // Tìm kiếm quy tắc
    searchRules() {
      if (this.searchRule.trim()) {
        this.arrs = [];
        for (var i = 0; i < this.ruleList.length; i++) {
          if (this.ruleList[i].real_name.indexOf(this.searchRule) !== -1) {
            this.arrs.push(i);
          }
        }
      } else {
        this.arrs = [];
      }
    },
    init() {
      this.searchRule = '';
      this.arrs = [];
    },
    handleCreate1(val) {
      this.headerOptionsList.push({
        value: val,
        label: val,
      });
    },
    // Lấy biểu mẫu thêm mới
    getAddFrom() {
      addMenus()
        .then(async (res) => {
          this.FromData = res.data.rules;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    iconClick() {
      this.modal12 = true;
    },
    iconChange(n) {
      this.formValidate.icon = n;
      this.modal12 = false;
    },
    // Gửi
    handleSubmit(name) {
      //Kiểm tra có chọn danh mục cấp cha hay không
      if (this.formValidate.path) {
        let length = this.formValidate.path.length;
        this.formValidate.pid = this.formValidate.path[length - 1] || 0;
      }
      let data = {
        url: this.formValidate.id ? `/setting/menus/${this.formValidate.id}` : '/setting/menus',
        method: this.formValidate.id ? 'put' : 'post',
        datas: this.formValidate,
      };
      if (this.authType) {
        if (!this.formValidate.menu_name) {
          return this.$message.warning('Vui lòng điền tên nút');
        }
        if (!this.formValidate.menu_path) {
          return this.$message.warning('Vui lòng điền địa chỉ route');
        }
      } else {
        if (!this.formValidate.menu_name) {
          return this.$message.warning('Vui lòng điền tên API');
        }
        if (!this.formValidate.methods) {
          return this.$message.warning('Vui lòng chọn phương thức yêu cầu');
        }
        if (!this.formValidate.api_url) {
          return this.$message.warning('Vui lòng chọn địa chỉ API');
        }
      }
      this.valids = true;
      addMenusApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.modals = false;
          this.$emit('changeMenu', this.formValidate);
          this.getAddFrom();
          // this.$store.dispatch('menus/getMenusNavList');
        })
        .catch((res) => {
          this.valids = false;
          this.$message.error(res.msg);
        });
    },
    handleReset() {
      this.modals = false;
      this.authType = true;
      this.$refs['formValidate'].resetFields();
      this.$emit('clearFrom');
    },
  },
  created() {
    this.list = this.search;
    this.getAddFrom();
  },
};
</script>

<style scoped>
.trees-coadd {
  width: 100%;
  height: 500px;
  border-radius: 4px;
  overflow: hidden;
}

.scollhide {
  width: 100%;
  height: 100%;
  overflow: auto;
  margin-left: 18px;
  padding: 10px 0 10px 0;
  box-sizing: border-box;
}

.content {
  font-size: 12px;
}

.time {
  font-size: 12px;
  color: #2d8cf0;
}

.icons-item {
  float: left;
  margin: 6px 6px 6px 0;
  width: 53px;
  text-align: center;
  list-style: none;
  cursor: pointer;
  height: 50px;
  color: #5c6b77;
  transition: all 0.2s ease;
  position: relative;
  padding-top: 10px;
}

.icons-item .f-s-24 {
  font-size: 24px;
}

.search-rule {
  display: flex;
  align-items: center;
  padding: 10px;
  background-color: #f2f2f2;
}

.rule {
  display: flex;
  flex-wrap: wrap;
  max-height: 700px;
  overflow: scroll;
}

/*Định nghĩa chiều cao, chiều rộng và nền thanh cuộn; chiều cao, chiều rộng tương ứng kích thước thanh cuộn ngang, dọc*/
.rule::-webkit-scrollbar {
  width: 10px;
  height: 10px;
  background-color: #f5f5f5;
}

/*Định nghĩa rãnh thanh cuộn: bóng đổ trong + góc tròn*/
.rule::-webkit-scrollbar-track {
  border-radius: 4px;
  background-color: #f5f5f5;
}

/*Định nghĩa thanh trượt: đổ bóng trong + góc tròn*/
.rule::-webkit-scrollbar-thumb {
  border-radius: 4px;
  background-color: #555;
}

.rule-list {
  background-color: #f8f5f5;
  width: 32%;
  margin: 5px;
  border-radius: 3px;
  padding: 10px;
  color: #333;
  cursor: pointer;
  transition: all 0.1s;
}

.rule-list:hover {
  background-color: #c5d1dd;
}

.rule-list div {
  white-space: nowrap;
}

.select-rule {
  background-color: #c5d1dd;
}

.add {
  display: flex;
  align-items: center;
}

.df {
  display: flex;
  justify-content: center;
}
</style>
