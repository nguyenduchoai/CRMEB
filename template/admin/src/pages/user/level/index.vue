<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="levelFrom"
          :model="levelFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          inline
          @submit.native.prevent
        >
          <el-form-item label="Trạng thái hạng:" label-for="status1">
            <el-select
              v-model="levelFrom.is_show"
              placeholder="Vui lòng chọn"
              clearable
              element-id="status1"
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Hiện"></el-option>
              <el-option value="0" label="Không hiển thị"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tên cấp độ:" label-for="title">
            <el-input clearable v-model="levelFrom.title" placeholder="Vui lòng nhập tên cấp độ" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button v-auth="['admin-user-level_add']" type="primary" v-db-click @click="add">Thêm hạng người dùng</el-button>
      <el-table
        :data="levelLists"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Biểu tượng hạng" min-width="100">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.icon" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh nền hạng" min-width="100">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.image" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tên cấp bậc" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Cấp bậc" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.grade }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Chiết khấu được hưởng" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.discount }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Điểm kinh nghiệm yêu cầu" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.exp_num }}</span>
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
        <el-table-column fixed="right" label="Thao tác" width="100">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa hạng', scope.$index)">Xóa</a>
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
    </el-card>
    <!-- Nhiệm vụ lên hạng-->
    <task-list ref="tasks"></task-list>
  </div>
</template>
<script>
import { mapState, mapMutations } from 'vuex';
import { levelListApi, setShowApi, createApi } from '@/api/user';
import taskList from './handle/task';
export default {
  name: 'user_level',
  components: { taskList },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      levelFrom: {
        is_show: '',
        title: '',
        page: 1,
        limit: 15,
      },
      levelLists: [],
      total: 0,
      FromData: null,
      imgName: '',
      visible: false,
      levelId: 0,
      modalTitleSs: '',
      titleType: 'level',
      modelTask: false,
      num: 0,
    };
  },
  created() {
    this.getList();
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
  methods: {
    ...mapMutations('userLevel', ['getlevelId']),

    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `user/user_level/delete/${row.id}`,
        method: 'put',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.levelLists.splice(num, 1);
          this.total--;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Xóa thành công
    // submitModel () {
    //     this.levelLists.splice(this.delfromData.num, 1)
    // },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        is_show: row.is_show,
      };
      setShowApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách hạng
    getList() {
      this.loading = true;
      this.levelFrom.is_show = this.levelFrom.is_show || '';
      levelListApi(this.levelFrom)
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
    // Thêm
    add() {
      this.levelId = 0;
      this.$modalForm(createApi({ id: this.levelId })).then(() => this.getList());
    },
    // Sửa
    edit(row) {
      this.levelId = row.id;
      this.$modalForm(createApi({ id: this.levelId })).then(() => this.getList());
      this.getlevelId(this.levelId);
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.levelFrom.page = 1;
      this.getList();
    },
    // Sửa thành công
    submitFail() {
      this.getList();
    },
  },
};
</script>

<style lang="scss" scoped>
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
