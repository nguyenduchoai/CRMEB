<template>
  <div>
    <el-card :bordered="false" shadow="never" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Tìm kiếm:">
            <el-input
              clearable
              placeholder="Vui lòng nhập họ tên, UID"
              v-model="formValidate.keyword"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-row class="ivu-mt box-wrapper">
        <el-col :xs="24" :sm="24" ref="rightBox">
          <el-row>
            <el-col v-bind="grid">
              <el-button type="primary" v-db-click @click="groupAdd('0')" class="mr20">Thêm đại lý</el-button>
            </el-col>
          </el-row>
          <el-table
            :data="userLists"
            ref="table"
            class="mt14"
            v-loading="loading"
            highlight-current-row
            no-formValidate-text="Chưa có dữ liệu"
            no-filtered-formValidate-text="Không có kết quả phù hợp"
          >
            <el-table-column label="UID người dùng" width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.uid }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Ảnh đại diện" min-width="90">
              <template slot-scope="scope">
                <div class="tabBox_img" v-viewer>
                  <img v-lazy="scope.row.avatar" />
                </div>
              </template>
            </el-table-column>
            <el-table-column label="Tên" min-width="130">
              <template slot-scope="scope">
                <div class="acea-row">
                  <div v-text="scope.row.division_name" class="ml10"></div>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="Tỷ lệ hoa hồng" min-width="130">
              <template slot-scope="scope">
                <span> {{ scope.row.division_percent }}%</span>
              </template>
            </el-table-column>
            <el-table-column label="Số lượng nhân viên" min-width="130">
              <template slot-scope="scope">
                <span>{{ scope.row.agent_count }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thời gian hết hạn" min-width="130">
              <template slot-scope="scope">
                <span>{{ scope.row.division_end_time }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Trạng thái" min-width="130">
              <template slot-scope="scope">
                <el-switch
                  :active-value="1"
                  :inactive-value="0"
                  v-model="scope.row.division_status"
                  :value="scope.row.division_status"
                  @change="onchangeIsShow(scope.row)"
                  size="large"
                >
                </el-switch>
              </template>
            </el-table-column>
            <el-table-column label="Thao tác" fixed="right" width="220">
              <template slot-scope="scope">
                <a v-db-click @click="staffAdd(scope.row.uid)">Thêm nhân viên</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="jump(scope.row.uid)">Xem nhân viên</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="groupAdd(scope.row.uid)">Sửa</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="del(scope.row, 'Xóa đại lý', scope.$index, 2)">Xóa</a>
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
        </el-col>
      </el-row>
    </el-card>
    <el-dialog :visible.sync="staffModal" title="Danh sách nhân viên" class="order_box" width="1000px">
      <el-table
        :data="clerkLists"
        ref="table"
        class="mt20"
        v-loading="loading"
        highlight-current-row
        no-formValidate-text="Chưa có dữ liệu"
        no-filtered-formValidate-text="Không có kết quả phù hợp"
      >
        <el-table-column label="UID người dùng" width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện" min-width="120">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Họ tên" min-width="130">
          <template slot-scope="scope">
            <div class="acea-row">
              <i class="el-icon-male mr10" v-show="scope.row.sex === 'Nam'" style="color: #2db7f5; font-size: 15px"></i>
              <i
                class="el-icon-female mr10"
                v-show="scope.row.sex === 'Nữ'"
                style="color: #ed4014; font-size: 15px"
              ></i>
              <div v-text="scope.row.nickname" class=""></div>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tỷ lệ hoa hồng" min-width="130">
          <template slot-scope="scope">
            <span> {{ scope.row.division_percent }}%</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" width="120">
          <template slot-scope="scope">
            <a v-db-click @click="del(scope.row, 'Xóa nhân viên', scope.$index, 3)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total2"
          :total="total2"
          :page.sync="clerkReqData.page"
          :limit.sync="clerkReqData.limit"
          @pagination="getClerkList"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { regionList, agentFrom, isShowApi, clerkList, staffAddFrom } from '@/api/agent';
import { formatDate } from '@/utils/validate';
export default {
  name: 'agent_extra',
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
      total2: 0,
      userLists: [],
      formInline: {
        uid: 0,
        proportion: 0,
        image: '',
      },
      FromData: null,
      loading: false,
      current: 0,
      formValidate: {
        page: 1,
        limit: 15,
        keyword: '',
      },
      staffModal: false,
      clerkReqData: {
        uid: 0,
        page: 1,
        limit: 15,
      },
      clerkLists: [],
    };
  },
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
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
  mounted() {
    this.getList();
  },
  methods: {
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    jump(uid) {
      this.clerkReqData.uid = uid;
      this.getClerkList();
    },
    getClerkList() {
      this.clerkReqData.division_type = 3;
      clerkList(this.clerkReqData).then((res) => {
        this.clerkLists = res.data.list;
        this.total2 = res.data.count;
        this.staffModal = true;
      });
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.formValidate.division_type = 2;
      regionList(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.userLists = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Biểu mẫu thêm
    groupAdd(id) {
      this.$modalForm(agentFrom(id))
        .then((res) => {
          this.getList();
        })
        .catch((err) => {});
    },
    //Form thêm nhân viên
    staffAdd(id) {
      this.$modalForm(staffAddFrom(id))
        .then((res) => {
          this.getList();
        })
        .catch((err) => {});
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.uid,
        status: row.division_status,
      };
      isShowApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {},
    // Xóa
    del(row, tit, num, type) {
      let delfromData = {
        title: tit,
        method: 'DELETE',
        uid: row.uid,
        url: `agent/division/del/${type}/${row.uid}`,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          if (type == 2) {
            this.userLists.splice(num, 1);
          } else {
            this.clerkLists.splice(num, 1);
          }
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.ivu-form-item {
  margin-bottom: 0;
}
.picBox {
  display: inline-block;
  cursor: pointer;
  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
  }
  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
    }
  }
}
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
  z-index: 50;
  position: relative;
  display: flex;
  justify-content: space-between;
  word-break: break-all;
  &:hover .icon-box {
    display: block;
  }
}
.icon-box {
  z-index: 3;
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  display: none;
}
.right-menu {
  z-index: 10;
  position: absolute;
  right: -106px;
  top: -11px;
  width: auto;
  min-width: 121px;
}
.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;
  img {
    width: 100%;
    height: 100%;
  }
}
</style>
