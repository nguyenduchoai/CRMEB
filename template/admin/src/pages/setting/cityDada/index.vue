<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <div class="acea-row row-between-wrapper mb20">
        <el-row>
          <el-col v-bind="grid">
            <div class="button acea-row row-middle">
              <el-button type="primary" v-db-click @click="add(0)">Thêm tỉnh</el-button>
              <el-button v-db-click @click="cleanCache">Xóa bộ nhớ đệm</el-button>
            </div>
          </el-col>
        </el-row>
      </div>
      <el-table
        row-key="id"
        :load="handleLoadData"
        :tree-props="{ children: 'children', hasChildren: 'hasChildren' }"
        :data="cityLists"
        lazy
      >
        <el-table-column label="Mã số" width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên khu vực" min-width="300">
          <template slot-scope="scope">
            <span>{{ scope.row.label }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên cấp trên" min-width="300">
          <template slot-scope="scope">
            <span>{{ scope.row.parent_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-if="scope.row.hasOwnProperty('children')" v-db-click @click="add(scope.row.city_id)">Thêm</a>
            <el-divider direction="vertical" v-if="scope.row.hasOwnProperty('children')" />
            <a v-db-click @click="edit(scope.row.id)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa thành phố', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { cityListApi, cityAddApi, cityApi, cityCleanCacheApi } from '@/api/setting';
export default {
  name: 'setting_dada',
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
          title: 'Mã số',
          key: 'id',
          width: 80,
        },
        {
          title: 'Tên khu vực',
          key: 'label',
          minWidth: 300,
          tree: true,
        },
        {
          title: 'Tên cấp trên',
          key: 'parent_name',
          minWidth: 300,
        },
        {
          title: 'Thao tác',
          slot: 'action',
          fixed: 'right',
          minWidth: 120,
        },
      ],
      cityLists: [],
      cityId: 0, // Id thành phố
    };
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
  created() {
    this.getList(0);
  },
  methods: {
    // Xóa bộ nhớ đệm;
    cleanCache() {
      cityCleanCacheApi()
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.success(res.msg);
        });
    },
    // Thêm
    add(cityId) {
      this.$modalForm(cityAddApi(cityId)).then(() => this.getList(0));
    },
    // Thêm cấp dưới;
    lower(cityId) {
      this.cityId = cityId;
      this.getList(cityId);
    },
    // Danh sách thành phố
    getList(parentId) {
      let that = this;
      that.loading = true;
      cityListApi(parentId)
        .then(async (res) => {
          that.cityLists = res.data;
          that.loading = false;
        })
        .catch((res) => {
          that.loading = false;
          that.$message.error(res.msg);
        });
    },
    // Quay lại
    goBack() {
      this.cityId = 0;
      this.getList(0);
    },
    // Sửa
    edit(id) {
      this.$modalForm(cityApi(id)).then(() => this.getList(this.cityId));
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `setting/city/del/${row.city_id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.cityLists.splice(num, 1);
          this.getList(this.cityId);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    handleLoadData(item, node, callback) {
      cityListApi(item.city_id).then((res) => {
        callback(res.data);
      });
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .ivu-table-cell-tree {
  border: 0;
  font-size: 15px;
  background-color: unset;
}
::v-deep .ivu-table-cell-tree .ivu-icon-ios-add:before {
  content: '\F11F';
}
::v-deep .ivu-table-cell-tree .ivu-icon-ios-remove:before {
  content: '\F116';
}
.button {
  width: 300px;
}
</style>
