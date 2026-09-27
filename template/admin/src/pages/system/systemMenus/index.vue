<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="roleData"
          :model="roleData"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Trạng thái quy tắc:">
            <el-select
              v-model="roleData.is_show"
              placeholder="Vui lòng chọn"
              clearable
              @change="getData"
              class="form_content_width"
            >
              <el-option value="1" label="Hiện"></el-option>
              <el-option value="0" label="Không hiển thị"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tên nút:" prop="status2" label-for="status2">
            <el-input clearable v-model="roleData.keyword" placeholder="Vui lòng nhập tên nút" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="getData">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-button type="primary" v-db-click @click="menusAdd('Thêm quy tắc')">Thêm quy tắc </el-button>
      <vxe-table
        :border="false"
        class="vxeTable mt14"
        highlight-hover-row
        highlight-current-row
        :loading="loading"
        ref="xTable"
        header-row-class-name="false"
        :tree-config="tabconfig"
        :data="tableData"
        row-id="id"
      >
        <vxe-table-column field="menu_name" tree-node title="Tên nút" min-width="100"></vxe-table-column>
        <vxe-table-column field="unique_auth" title="Quyền frontend" min-width="200"></vxe-table-column>
        <vxe-table-column field="menu_path" title="Đường dẫn" min-width="240" tooltip="true">
          <template v-slot="{ row }">
            <span v-if="row.auth_type == 1">Menu: {{ row.menu_path }}</span>
            <span v-if="row.auth_type == 3">Nút</span>
            <span v-if="row.auth_type == 2">API: [{{ row.methods }}]{{ row.api_url }}</span>
          </template>
        </vxe-table-column>
        <vxe-table-column field="flag" title="Trạng thái quy tắc" min-width="120">
          <template v-slot="{ row }">
            <el-switch
              :active-value="1"
              :inactive-value="0"
              v-model="row.is_show"
              :value="row.is_show"
              @change="onchangeIsShow(row)"
              size="large"
            >
            </el-switch>
          </template>
        </vxe-table-column>
        <vxe-table-column field="mark" title="Ghi chú" min-width="120"></vxe-table-column>
        <vxe-table-column field="date" title="Thao tác" width="230" fixed="right">
          <template v-slot="{ row }">
            <span>
              <a v-db-click @click="addRoute(row)" v-if="row.auth_type === 1 || row.auth_type === 3">Chọn quyền</a>
              <el-divider direction="vertical" v-if="row.auth_type === 1 || row.auth_type === 3" />
              <a v-db-click @click="addE(row, 'Thêm menu con')" v-if="row.auth_type === 1 || row.auth_type === 3"
                >Thêm cấp con</a
              >
              <!-- <a v-db-click @click="addE(row, 'Thêm quy tắc')" v-else>Thêm quy tắc</a> -->
            </span>
            <el-divider direction="vertical" v-if="row.auth_type === 1 || row.auth_type === 3"></el-divider>
            <a v-db-click @click="edit(row, 'Sửa')">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(row, 'Xóa quy tắc')">Xóa</a>
          </template>
        </vxe-table-column>
      </vxe-table>
    </el-card>
    <menus-from
      :formVal="formValidate"
      :titleFrom="titleFrom"
      @getList="getList"
      @changeMenu="getMenusUnique"
      ref="menusFrom"
      @clearFrom="clearFrom"
    ></menus-from>
    <el-dialog :visible.sync="ruleModal" width="1100px" title="Danh sách quyền" @closed="modalchange">
      <div class="search-rule">
        <el-alert>
          <template slot="title">
            1. Có thể chọn nhiều API, có thể thêm lặp lại;<br />2. Thêm route theo đúng quy tắc route, sau đó vào Công cụ phát triển->Quản lý API và nhấn Đồng bộ;<br />3. Sau khi đồng bộ xong, có thể chọn API tương ứng tại đây;
          </template>
        </el-alert>
        <el-input
          class="mr10 mt10 form_content_width"
          v-model="searchRule"
          placeholder="Nhập từ khóa để tìm kiếm"
          clearable
          ref="search"
        />
        <el-button type="primary" v-db-click @click="searchRules">Tìm kiếm</el-button>
        <el-button v-db-click @click="init">Đặt lại</el-button>
      </div>
      <div class="route-list">
        <div class="tree">
          <el-tree
            ref="treeBox"
            :data="ruleCateList"
            :highlight-current="true"
            :props="defaultProps"
            node-key="id"
            :default-expanded-keys="expandedKeys"
            :current-node-key="nodeKey"
            @node-click="handleNodeClick"
          ></el-tree>
        </div>
        <div class="rule">
          <div
            class="rule-list"
            v-show="!arrs.length || arrs.includes(item.id)"
            :class="{ 'select-rule': seletRouteIds.includes(item.id) }"
            v-for="(item, index) in children"
            :key="index"
            v-db-click
            @click="selectRule(item)"
          >
            <div>Tên API: {{ item.name }}</div>
            <div>Phương thức yêu cầu: {{ item.method }}</div>
            <div>Địa chỉ API: {{ item.path }}</div>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="ruleModal = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="addRouters">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import {
  getTable,
  menusDetailsApi,
  isShowApi,
  editMenus,
  getRuleList,
  menusBatch,
  getMenusUnique,
  menusRuleCate,
} from '@/api/systemMenus';
import formCreate from '@form-create/element-ui';
import menusFrom from './components/menusFrom';
import { formatFlatteningRoutes, findFirstNonNullChildren, findFirstNonNullChildrenKeys } from '@/libs/system';

export default {
  name: 'systemMenus',
  data() {
    return {
      children: [],
      expandedKeys: [],
      tabconfig: { children: 'children', reserve: true, accordion: true },
      spinShow: false,
      ruleModal: false,
      searchRule: '',
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      roleData: {
        is_show: '',
        keyword: '',
      },
      defaultProps: {
        children: 'children',
        label: 'name',
      },
      ruleCateList: [], //Cây quyền
      loading: false,
      tableData: [],
      FromData: null,
      icons: '',
      formValidate: {
        auth_type: '',
        menu_name: '',
        path: '',
        api_url: '',
        menu_path: '',
        methods: '',
        unique_auth: '',
        mark: '',
        sort: '',
        is_show: 0,
      },
      titleFrom: '',
      modalTitleSs: '',
      routeType: '0',
      arrs: [],
      foundationList: [], // Danh sách API cơ bản
      openList: [], // Danh sách API công khai
      seletRoute: [], // Route được chọn
      seletRouteIds: [], // Id được chọn
      menusId: 0, // Id danh mục được chọn
      nodeKey: 0, // Nút được chọn
    };
  },
  components: { menusFrom, formCreate: formCreate.$form() },
  computed: {
    ...mapState('admin/layout', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  mounted() {
    this.getData();
  },
  methods: {
    init() {
      this.searchRule = '';
      this.searchRules();
    },
    addRouters() {
      let data = {
        menus: this.seletRoute,
      };
      menusBatch(data)
        .then((res) => {
          this.getData();
          this.ruleModal = false;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    selectRule(data) {
      if (this.seletRouteIds.includes(data.id)) {
        let i = this.seletRouteIds.findIndex((e) => e == data.id);
        this.seletRouteIds.splice(i, 1);
        this.seletRoute.splice(i, 1);
      } else {
        this.seletRouteIds.push(data.id);
        this.seletRoute.push({
          menu_name: data.name,
          unique_auth: '',
          api_url: data.path,
          path: this.menusId,
          method: data.method,
        });
      }
    },
    changTab(name) {
      this.routeType = name;
      let index = parseInt(name);
      this.children = this.foundationList[index] ? this.foundationList[index].children : [];
      this.searchRules();
    },
    // Tìm kiếm quy tắc
    searchRules() {
      if (this.searchRule.trim()) {
        this.arrs = [];
        let arr = this.foundationList;
        for (var i = 0; i < arr.length; i++) {
          if (arr[i].name.indexOf(this.searchRule) !== -1) {
            this.arrs.push(arr[i].id);
          }
        }
      } else {
        this.arrs = [];
      }
    },
    addRoute(row) {
      this.menusId = row.id;
      this.routeType = '0';
      // this.getRuleList();
      menusRuleCate().then((res) => {
        this.ruleCateList = res.data;
        this.ruleModal = true;
        if (res.data.length) {
          this.$nextTick((e) => {
            this.expandedKeys = findFirstNonNullChildrenKeys(res.data[0], []);
            this.nodeKey = findFirstNonNullChildren(res.data).id;
            this.$refs.treeBox.setCurrentKey(this.nodeKey);
            this.getRuleList(this.nodeKey);
          });
        }
      });
    },
    handleNodeClick(data) {
      this.getRuleList(data.id);
    },
    modalchange() {
      this.seletRouteIds = [];
      this.seletRoute = [];
    },
    // Lấy danh sách quyền
    getRuleList(cate_id) {
      getRuleList(cate_id).then((res) => {
        this.foundationList = res.data;
        this.children = res.data;
        this.searchRules();

        // this.openList = [];
        // this.seletRouteIds = [];
        // this.seletRoute = [];
      });
    },
    // Sửa trạng thái quy tắc
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        is_show: row.is_show,
      };
      isShowApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.$store.dispatch('menus/getMenusNavList');
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Yêu cầu danh sách
    getList() {
      this.formValidate = Object.assign({}, this.$options.data().formValidate);
      this.getData();
    },

    // Xóa dữ liệu biểu mẫu
    clearFrom() {
      this.formValidate = Object.assign({}, this.$options.data().formValidate);
    },
    // Thêm menu con
    addE(row, title) {
      this.formValidate = {
        is_show: 0,
      };
      let pid = row.id.toString();
      if (pid) {
        menusDetailsApi(row.id)
          .then(async (res) => {
            this.formValidate.path = res.data.path;
            this.formValidate.path.push(row.id);
            this.formValidate.pid = pid;
            this.$refs.menusFrom.valids = false;
            this.titleFrom = title;
            this.formValidate.auth_type = 1;
            this.formValidate.is_show_path = 0;
            this.$refs.menusFrom.getAddFrom();
            this.$refs.menusFrom.modals = true;
          })
          .catch((res) => {
            this.$message.error(res.msg);
          });
      } else {
        this.formValidate.pid = pid;
        this.$refs.menusFrom.modals = true;
        this.$refs.menusFrom.valids = false;
        this.titleFrom = title;
        this.formValidate.auth_type = 1;
        this.formValidate.is_show_path = 0;
      }
    },
    // Xóa
    del(row, tit) {
      let delfromData = {
        title: tit,
        url: `/setting/menus/${row.id}`,
        method: 'DELETE',
        ids: '',
      };

      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getData();
          this.getMenusUnique();
          // this.$store.dispatch('menus/getMenusNavList');
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Chi tiết quy tắc
    menusDetails(id) {
      menusDetailsApi(id)
        .then(async (res) => {
          this.formValidate = res.data;
          this.$refs.menusFrom.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row, title, index) {
      this.modals = true;

      this.formValidate = {};
      this.menusDetails(row.id);
      this.titleFrom = title;
      this.$refs.menusFrom.valids = false;
      this.$refs.menusFrom.getAddFrom(row.id);
    },
    // Thêm
    menusAdd(title) {
      // this.formValidate = {};
      this.modals = true;
      this.$refs.menusFrom.modals = true;
      this.$refs.menusFrom.valids = false;
      this.$refs.menusFrom.getAddFrom();
      // this.formValidate = Object.assign(this.$data, this.$options.formValidate());
      this.titleFrom = title;
      this.formValidate.auth_type = 1;
      this.formValidate.is_show = 0;
      this.formValidate.is_show_path = 0;
    },
    // Biểu mẫu thêm trang mới
    // getAddFrom () {
    //     this.spinShow = true;
    //     addMenus(this.roleData).then(async res => {
    //         this.FromData = res.data;
    //         this.$refs.menusFrom.modals = true;
    //         this.spinShow = false;
    //     }).catch(res => {
    //         this.spinShow = false;
    //         this.$message.error(res.msg);
    //     })
    // },
    // Danh sách
    getData() {
      this.loading = true;
      this.roleData.is_show = this.roleData.is_show || '';
      getTable(this.roleData)
        .then(async (res) => {
          this.tableData = res.data;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    getMenusUnique() {
      getMenusUnique().then((res) => {
        let data = res.data;
        this.$store.commit('userInfo/uniqueAuth', data.uniqueAuth);
        this.$store.commit('menus/getmenusNav', data.menus);
        this.$store.dispatch('routesList/setRoutesList', data.menus);
        let arr = formatFlatteningRoutes(this.$router.options.routes);
        this.formatTwoStageRoutes(arr);
        let routes = formatFlatteningRoutes(data.menus);
        this.$store.commit('menus/setOneLvRoute', routes);
        this.bus.$emit('routesListChange');
      });
    },
    formatTwoStageRoutes(arr) {
      if (arr.length <= 0) return false;
      const newArr = [];
      const cacheList = [];
      arr.forEach((v) => {
        if (v && v.meta && v.meta.keepAlive) {
          newArr.push({ ...v });
          cacheList.push(v.name);
          this.$store.dispatch('keepAliveNames/setCacheKeepAlive', cacheList);
        }
      });
      return newArr;
    },
    // Nút đóng
    cancel() {
      this.$emit('onCancel');
    },
  },
};
</script>

<style scoped lang="scss">
.vxeTable {
  > .vxe-table--header-wrapper {
    background: #fff !important;
  }
  .icon {
    font-size: 20px;
  }
}

.rule {
  display: flex;
  flex-wrap: wrap;
  overflow-y: scroll;
  height: max-content;
  max-height: 600px;
  flex: 1;
}
.tree::-webkit-scrollbar {
  width: 2px;
  background-color: #f5f5f5;
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
  background-color: #ccc;
}

.rule-list {
  background-color: #f2f2f2;
  width: 48.5%;
  height: max-content;
  margin: 5px;
  border-radius: 3px;
  padding: 10px;
  color: #333;
  cursor: pointer;
  transition: all 0.1s;
}

.rule-list:hover {
  background-color: var(--prev-bg-menu-hover-ba-color);
}

.rule-list div {
  white-space: nowrap;
}

.select-rule {
  background-color: var(--prev-bg-menu-hover-ba-color);
}
.route-list {
  display: flex;
  margin-top: 10px;

  .tree {
    width: 200px;
    overflow-y: scroll;
    max-height: 600px;
    ::v-deep .el-tree-node__children .el-tree-node .el-tree-node__content {
      padding-left: 14px !important;
    }
  }
}
.el-dropdown-link {
  cursor: pointer;
  color: var(--prev-color-primary);
  font-size: 12px;
  margin-left: 6px;
}
.el-icon-arrow-down {
  font-size: 12px;
}
</style>
