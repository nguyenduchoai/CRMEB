<template>
  <!-- Sản phẩm - Thông số sản phẩm -->
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <!-- Điều kiện lọc -->
        <el-form
          ref="specsFrom"
          inline
          :model="specsFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <el-form-item label="Tên máy in:">
            <el-input
              v-model="specsFrom.keyword"
              placeholder="Vui lòng nhập tên máy in"
              class="form_content_width"
              clearable
              @clear="specsSearchs"
              @change="specsSearchs"
            ></el-input>
          </el-form-item>
          <el-form-item label="Chọn nền tảng:">
            <el-select
              class="form_content_width mr10"
              v-model="specsFrom.type"
              clearable
              @clear="specsSearchs"
              @change="specsSearchs"
            >
              <el-option v-for="(item, i) in optionsList" :value="item.value" :label="item.label" :key="i"></el-option>
            </el-select>
            <el-button type="primary" @click="specsSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button type="primary" v-db-click @click="add">Thêm máy in</el-button>
      <!-- Bảng thông số sản phẩm -->
      <el-table
        :data="list"
        ref="table"
        class="mt25"
        :loading="loading"
        highlight-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" min-width="50">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên máy in" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.print_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Nền tảng" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.type == 1">Yilianyun</span>
            <span v-if="scope.row.type == 2">Feieyun</span>
          </template>
        </el-table-column>
        <el-table-column label="Tài khoản ứng dụng" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.type == 1">{{ scope.row.yly_app_id }}</span>
            <span v-if="scope.row.type == 2">{{ scope.row.fey_user }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số liên in" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.times }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Bật/tắt in" min-width="100">
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
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a @click="setting(scope.row.id)">Thiết kế</a>
            <el-divider direction="vertical" />
            <a @click="edit(scope.row.id)">Sửa</a>
            <el-divider direction="vertical" />
            <a @click="del(scope.row, 'Xóa máy in', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="specsFrom.page"
          :limit.sync="specsFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { printList, printForm, printSetStatus } from '@/api/setting';

export default {
  name: 'specs',
  data() {
    return {
      grid: {
        xl: 10,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      optionsList: [
        {
          value: '0',
          label: 'Tất cả',
        },
        {
          value: '1',
          label: 'Yilianyun',
        },
        {
          value: '2',
          label: 'Feieyun',
        },
      ],
      columns: [
        {
          title: 'ID',
          key: 'id',
          width: 80,
        },
        {
          title: 'Tên máy in',
          key: 'print_name',
          minWidth: 100,
        },
        {
          title: 'Nền tảng',
          slot: 'type',
          minWidth: 100,
        },
        {
          title: 'Tài khoản ứng dụng',
          slot: 'account',
          minWidth: 100,
        },
        {
          title: 'Số liên in',
          key: 'times',
          width: 200,
        },
        {
          title: 'Thời gian tạo',
          key: 'add_time',
          width: 200,
        },
        {
          title: 'Bật/tắt in',
          slot: 'status',
          width: 200,
        },
        {
          title: 'Thao tác',
          slot: 'action',
          fixed: 'right',
          width: 140,
        },
      ],
      specsFrom: {
        page: 1,
        limit: 15,
        keyword: '',
        type: '0',
      },
      list: [],
      total: 0,
    };
  },
  computed: {
    ...mapState('admin/layout', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '96';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getList();
  },
  methods: {
    specsSearchs() {
      this.specsFrom.page = 1;
      this.list = [];
      this.getList();
    },

    // Danh sách đơn vị
    getList() {
      this.loading = true;
      printList(this.specsFrom)
        .then((res) => {
          let data = res.data;
          this.list = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    pageChange(index) {
      this.specsFrom.page = index;
      this.getList();
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      printSetStatus(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thêm
    add() {
      this.$modalForm(printForm(0)).then(() => {
        this.getList();
      });
    },
    //Sửa
    edit(id) {
      this.$modalForm(printForm(id)).then(() => {
        this.getList();
      });
    },
    setting(id) {
      this.$router.push({
        path: this.$routeProStr + '/setting/ticket/content',
        query: { id: id },
      });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `system/ticket/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.list.splice(num, 1);
          if (!this.list.length) {
            this.specsFrom.page = this.specsFrom.page == 1 ? 1 : this.specsFrom.page - 1;
          }
          this.getList();
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.input-add {
  width: 250px;
  margin-right: 14px;
}
</style>
