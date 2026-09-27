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
            <el-select v-model="formValidate.status" clearable @change="search" class="form_content_width">
              <el-option value="" label="Tất cả"></el-option>
              <el-option :value="1" label="Hiện"></el-option>
              <el-option :value="0" label="Không hiển thị"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tên cấp độ:">
            <el-input
              clearable
              placeholder="Vui lòng nhập tên cấp độ"
              v-model="formValidate.keyword"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="search">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button type="primary" v-db-click @click="groupAdd">Thêm hạng</el-button>
      <el-table
        class="mt14"
        :data="tabList"
        ref="table"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="50">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hình ảnh sản phẩm" min-width="90">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.image" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tên" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Cấp bậc" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.grade }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tỷ lệ hoa hồng cấp 1" min-width="150">
          <template slot-scope="scope">
            <span
              >{{
                scope.row.one_brokerage_percent == '0.00'
                  ? scope.row.one_brokerage_ratio
                  : scope.row.one_brokerage_percent
              }}%</span
            >
          </template>
        </el-table-column>
        <el-table-column label="Tỷ lệ hoa hồng cấp 2" min-width="150">
          <template slot-scope="scope">
            <span
              >{{
                scope.row.two_brokerage_percent == '0.00'
                  ? scope.row.two_brokerage_ratio
                  : scope.row.two_brokerage_percent
              }}%</span
            >
          </template>
        </el-table-column>
        <el-table-column label="Tổng số nhiệm vụ" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.task_total_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số lượng cần hoàn thành" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.task_num }}</span>
          </template>
        </el-table-column>
        <!-- <el-table-column label="Tỷ lệ tăng thêm cấp 1" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.one_brokerage }}%</span>
          </template>
        </el-table-column>
        <el-table-column label="Tỷ lệ chia hoa hồng cấp 1 (sau khi tăng)" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.one_brokerage_ratio }}%</span>
          </template>
        </el-table-column>
        <el-table-column label="Tỷ lệ tăng thêm cấp 2" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.two_brokerage }}%</span>
          </template>
        </el-table-column>
        <el-table-column label="Tỷ lệ chia hoa hồng cấp 2 (sau khi tăng)" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.two_brokerage_ratio }}%</span>
          </template>
        </el-table-column> -->
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
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="addTask(scope.row)">Nhiệm vụ lên hạng</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="edit(scope.row, 'Sửa')">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'xóa thông tin này', scope.$index)">Xóa</a>
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
    <div class="task-modal">
      <el-dialog :visible.sync="modal2" title="Thêm nhiệm vụ" width="1000px">
        <el-form :model="taskData" :label-width="labelWidth" :label-position="labelPosition" inline>
          <el-form-item label="Hiển thị:">
            <el-select v-model="taskData.status" class="form_content_width" clearable>
              <el-option :value="1" label="Hiện"></el-option>
              <el-option :value="0" label="Không hiển thị"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tên nhiệm vụ:">
            <el-input v-model="taskData.keyword" placeholder="Vui lòng nhập tên nhiệm vụ" clearable class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="searchTask">Tìm kiếm</el-button>
          </el-form-item>
        </el-form>
        <div>
          <div class="add-task">
            <el-button type="primary" v-db-click @click="taskAdd()">Thêm nhiệm vụ cấp bậc</el-button>
            <el-button type="primary" v-db-click @click="taskEdit()">Thiết lập số lượng cần hoàn thành</el-button>
          </div>
          <div>
            <el-table
              :data="taskTabList"
              ref="table"
              class="mt14"
              v-loading="taskLoading"
              highlight-current-row
              no-userFrom-text="Chưa có dữ liệu"
              no-filtered-userFrom-text="Không có kết quả phù hợp"
            >
              <el-table-column label="ID" width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.id }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Tên" min-width="130">
                <template slot-scope="scope">
                  <span>{{ scope.row.name }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Loại nhiệm vụ" min-width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.type_name }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Số lượng quy định" min-width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.number }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Hiển thị" min-width="80">
                <template slot-scope="scope">
                  <el-switch
                    class="defineSwitch"
                    :active-value="1"
                    :inactive-value="0"
                    v-model="scope.row.status"
                    :value="scope.row.status"
                    @change="onchangeTaskIsShow(scope.row)"
                    active-text="Bật"
                    inactive-text="Tắt"
                  >
                    <span slot="open">Bật</span>
                    <span slot="close">Tắt</span>
                  </el-switch>
                </template>
              </el-table-column>
              <el-table-column label="Thứ tự sắp xếp" min-width="50">
                <template slot-scope="scope">
                  <span>{{ scope.row.sort }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Thao tác" fixed="right" width="170">
                <template slot-scope="scope">
                  <a v-db-click @click="editTask(scope.row, 'Sửa')">Sửa</a>
                  <el-divider direction="vertical"></el-divider>
                  <a v-db-click @click="delTask(scope.row, 'xóa thông tin này', scope.$index)">Xóa</a>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import {
  membershipDataAddApi,
  membershipDataListApi,
  membershipDataEditApi,
  membershipSetApi,
  levelTaskSetApi,
  levelTaskListDataAddApi,
  levelTaskDataEditApi,
  levelTaskDataAddApi,
  getTaskNumFormApi,
} from '@/api/membershipLevel';
export default {
  name: 'list',
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      modal1: false,
      modal2: false,
      formValidate: {
        status: '',
        page: 1,
        limit: 20,
        gid: 0,
      },
      taskData: {
        keyword: '',
        page: 1,
        limit: 20,
        status: '',
      },
      total: 0,
      taskTotal: 0,
      tabList: [],
      taskTabList: [],
      columns1: [
        {
          key: 'id',
          minWidth: 35,
          title: 'ID',
        },
        {
          slot: 'image',
          minWidth: 35,
          title: 'Ảnh nền',
        },
        {
          key: 'name',
          minWidth: 35,
          title: 'Tên',
        },
        {
          key: 'grade',
          minWidth: 35,
          title: 'Cấp bậc',
        },
        {
          slot: 'one_brokerage',
          minWidth: 35,
          title: 'Tỷ lệ tăng thêm cấp 1',
        },
        {
          slot: 'one_brokerage_ratio',
          minWidth: 35,
          title: 'Tỷ lệ hoa hồng cấp 1 (sau khi tăng)',
        },
        {
          slot: 'two_brokerage',
          minWidth: 35,
          title: 'Tỷ lệ tăng thêm cấp 2',
        },
        {
          slot: 'two_brokerage_ratio',
          minWidth: 35,
          title: 'Tỷ lệ hoa hồng cấp 2 (sau khi tăng)',
        },
        {
          slot: 'status',
          minWidth: 35,
          title: 'Hiển thị',
        },
        {
          minWidth: 120,
          slot: 'action',
          title: 'Thao tác',
        },
      ],
      columns2: [
        {
          key: 'id',
          minWidth: 35,
          title: 'ID',
        },
        {
          key: 'name',
          minWidth: 35,
          title: 'Tên',
        },
        {
          key: 'type_name',
          minWidth: 35,
          title: 'Loại nhiệm vụ',
        },
        {
          key: 'number',
          minWidth: 35,
          title: 'Số lượng quy định',
        },
        {
          slot: 'status',
          minWidth: 35,
          title: 'Hiển thị',
        },
        {
          key: 'sort',
          minWidth: 35,
          title: 'Thứ tự sắp xếp',
        },
        {
          fixed: 'right',
          minWidth: 120,
          slot: 'action',
          title: 'Thao tác',
        },
      ],
      FromData: null,
      loading: false,
      taskLoading: false,
      titleType: 'group',
      groupAll: [],
      theme3: 'light',
      labelSort: [],
      sortName: null,
      current: 0,
      model1: '',
      value1: '',
    };
  },
  computed: {
    ...mapState('admin/layout', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  watch: {
    $route(to, from) {
      if (this.$route.params.id) {
      } else {
      }
    },
  },
  mounted() {
    this.getList();
  },
  methods: {
    bindMenuItem(name, index) {
      this.current = index;
      this.formValidate.gid = name.id;
      this.getListHeader();
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      membershipDataListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getTaskList() {
      this.taskLoading = true;
      levelTaskListDataAddApi(this.taskData)
        .then(async (res) => {
          let data = res.data;
          this.taskTabList = data.list;
          this.taskTotal = data.count;
          this.taskLoading = false;
        })
        .catch((res) => {
          this.taskLoading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    search() {
      this.formValidate.page = 1;
      this.getList();
    },
    searchTask() {
      this.taskData.page = 1;
      this.getTaskList();
    },
    taskEdit() {
      this.$modalForm(getTaskNumFormApi(this.id)).then(() => this.getList());
    },
    // Biểu mẫu thêm
    groupAdd() {
      this.$modalForm(membershipDataAddApi({}, '/agent/level/create')).then(() => this.getList());
    },
    taskAdd() {
      this.$modalForm(levelTaskDataAddApi({}, '/agent/level_task/create?level_id=' + this.taskData.id)).then(() =>
        this.getTaskList(),
      );
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      membershipSetApi(`agent/level/set_status/${row.id}/${row.status}`)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa có hiển thị hay không
    onchangeTaskIsShow(row) {
      levelTaskSetApi(`agent/level_task/set_status/${row.id}/${row.status}`)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getTaskList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    //Thêm nhiệm vụ cấp bậc
    addTask(row) {
      this.id = row.id;
      this.modal2 = true;
      this.taskData.id = row.id;
      this.getTaskList();
    },
    // Sửa
    edit(row) {
      let data = {
        gid: row.gid,
      };
      this.$modalForm(membershipDataEditApi(data, `agent/level/${row.id}/edit`)).then(() => this.getList());
    },
    // Sửa
    editTask(row) {
      let data = {
        gid: row.gid,
      };
      this.$modalForm(levelTaskDataEditApi(data, `agent/level_task/${row.id}/edit`)).then(() => this.getTaskList());
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `agent/level/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tabList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Xóa
    delTask(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `agent/level_task/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.taskTabList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .ivu-menu-vertical .ivu-menu-item-group-title {
  display: none;
}
::v-deep .ivu-menu-vertical.ivu-menu-light:after {
  display: none;
}
.left-wrapper {
  height: 904px;
  background: #fff;
  border-right: 1px solid #f2f2f2;
}
.menu-item {
  position: relative;
  display: flex;
  justify-content: space-between;
  word-break: break-all;
  .icon-box {
    z-index: 3;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    display: none;
  }
  &:hover .icon-box {
    display: block;
  }
  .right-menu {
    z-index: 10;
    position: absolute;
    right: -106px;
    top: -11px;
    width: auto;
    min-width: 121px;
  }
}
.tabBox-img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
.ivu-menu {
  z-index: auto;
}
.header,
.headers {
  display: flex;
  flex-direction: column;
  background-color: #f2f2f2;
  padding: 8px;
  .search {
    display: flex;
    align-items: center;
    > div {
      margin-right: 10px;
    }
  }
}
.search ::v-deep .ivu-select-selection {
  border: 1px solid #dcdee2 !important;
}
.headers {
  background-color: #fff;
  margin-bottom: 20px;
}
::v-deep .ivu-modal-mask {
  z-index: 100 !important;
}
::v-deep .ivu-modal-wrap {
  z-index: 100 !important;
}
.add-task {
  margin: 10px 0;
}
</style>
