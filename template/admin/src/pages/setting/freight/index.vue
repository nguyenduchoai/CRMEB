<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="levelFrom"
          :model="levelFrom"
          :label-width="labelWidth"
          label-position="right"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Hiển thị:">
            <el-select
              v-model="levelFrom.is_show"
              placeholder="Vui lòng chọn"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="" label="Tất cả"></el-option>
              <el-option value="1" label="Hiện"></el-option>
              <el-option value="0" label="Không hiển thị"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm:" label-for="keyword">
            <el-input class="form_content_width" v-model="levelFrom.keyword" placeholder="Vui lòng nhập tên hoặc mã đơn vị vận chuyển" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-button type="primary" v-db-click @click="syncExpress">Đồng bộ đơn vị vận chuyển</el-button>
      <el-table
        :data="levelLists"
        ref="table"
        class="mt14"
        v-loading="loading"
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên đơn vị vận chuyển" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mã" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.code }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thứ tự sắp xếp" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.sort }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Hiển thị" min-width="100">
          <template slot-scope="scope">
            <el-switch
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.is_show"
              :value="scope.row.is_show"
              @change="onchangeIsShow(scope.row)"
              size="large"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="80">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
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
  </div>
</template>
<script>
import { mapState } from 'vuex';
import {
  freightCreateApi,
  freightListApi,
  freightEditApi,
  freightStatusApi,
  freightSyncExpressApi,
} from '@/api/setting';
export default {
  name: 'user_level',
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
      columns1: [
        {
          title: 'ID',
          key: 'id',
          width: 80,
        },
        {
          title: 'Tên đơn vị vận chuyển',
          key: 'name',
          minWidth: 100,
        },
        {
          title: 'Mã',
          key: 'code',
          minWidth: 120,
        },
        {
          title: 'Thứ tự sắp xếp',
          key: 'sort',
          sortable: true,
          minWidth: 100,
        },
        {
          title: 'Hiển thị',
          slot: 'is_shows',
          minWidth: 120,
        },
        {
          title: 'Thao tác',
          slot: 'action',
          fixed: 'right',
          minWidth: 120,
        },
      ],
      levelFrom: {
        keyword: '',
        is_show: '',
        page: 1,
        limit: 20,
      },
      levelLists: [],
      total: 0,
      FromData: null,
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
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `freight/express/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.levelLists.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.is_show,
      };
      freightStatusApi(data)
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
      freightListApi(this.levelFrom)
        .then(async (res) => {
          let data = res.data;
          this.levelLists = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Thêm
    add() {
      this.$modalForm(freightCreateApi()).then(() => this.getList());
      // freightCreateApi().then(async res => {
      //     this.FromData = res.data;
      //     this.$refs.edits.modals = true;
      // }).catch(res => {
      //     this.$message.error(res.msg);
      // })
    },
    // Sửa
    edit(row) {
      this.$modalForm(freightEditApi(row.id)).then(() => this.getList());
      // freightEditApi(row.id).then(async res => {
      //     this.FromData = res.data;
      //     this.$refs.edits.modals = true;
      // }).catch(res => {
      //     this.$message.error(res.msg);
      // })
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.levelFrom.page = 1;
      this.getList();
    },
    syncExpress() {
      freightSyncExpressApi()
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
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
