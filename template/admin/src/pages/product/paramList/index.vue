<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="artFrom"
          :model="artFrom"
          label-width="80px"
          label-position="right"
          class="tabform"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Tìm kiếm mẫu:">
            <el-input
              clearable
              v-model="artFrom.name"
              placeholder="Vui lòng nhập tên mẫu"
              class="form_content_width"
            ></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button type="primary" v-db-click @click="paramAdd">Thêm thông số sản phẩm</el-button>
      <el-table
        ref="table"
        :data="tableList"
        v-loading="loading"
        highlight-current-row
        :row-key="getRowKey"
        @selection-change="handleSelectRow"
        empty-text="Chưa có dữ liệu"
        class="mt14"
      >
        <!-- <el-table-column type="selection" width="60" :reserve-selection="true"> </el-table-column> -->
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên mẫu" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="120">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa thông số', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="artFrom.page"
          :limit.sync="artFrom.limit"
          @pagination="getDataList"
        />
      </div>
    </el-card>
    <param-add ref="paramAdd" @getList="userSearchs"></param-add>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import paramAdd from './paramAdd';
import { paramListApi } from '@/api/product';
export default {
  name: 'paramList',
  components: { paramAdd },
  data() {
    return {
      loading: false,
      artFrom: {
        page: 1,
        limit: 20,
        name: '',
      },
      tableList: [],
      total: 0,
      selectedIds: new Set(), //Id của mục đã chọn để gộp
      ids: [],
      multipleSelection: [],
    };
  },
  computed: {
    ...mapState('admin/order', ['orderChartType']),
  },
  created() {
    this.getDataList();
  },
  methods: {
    getRowKey(row) {
      return row.id;
    },
    //Kích hoạt khi chọn tất cả và bỏ chọn tất cả
    handleSelectAll(selection) {
      if (selection.length === 0) {
        //Lấy dữ liệu của table;
        let data = this.$refs.table.data;
        data.forEach((item) => {
          if (this.selectedIds.has(item.id)) {
            this.selectedIds.delete(item.id);
          }
        });
      } else {
        selection.forEach((item) => {
          this.selectedIds.add(item.id);
        });
      }
      this.$nextTick(() => {
        //Đảm bảo dom đã tải xong
        this.setChecked();
      });
    },
    //  Chọn một dòng
    handleSelectRow(selection) {
      const uniqueArr = [];
      const ids = [];
      for (let i = 0; i < selection.length; i++) {
        const item = selection[i];
        if (!ids.includes(item.id)) {
          uniqueArr.push(item);
          ids.push(item.id);
        }
      }
      this.selectedIds = ids;
      this.multipleSelection = uniqueArr;
      this.$nextTick((e) => {
        this.setChecked();
      });
    },
    setChecked() {
      //Chuyển new Set() thành mảng
      this.ids = [...this.selectedIds].join(',');
    },
    // Xóa
    del(row, tit) {
      let delfromData = {
        title: tit,
        num: 0,
        url: `product/param/del/${row.id}`,
        method: 'DELETE',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getDataList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    paramAdd() {
      this.$refs.paramAdd.modal = true;
    },
    // Sửa
    edit(row) {
      this.$refs.paramAdd.modal = true;
      this.$refs.paramAdd.getIofo(row);
    },
    // Danh sách;
    getDataList() {
      this.loading = true;
      paramListApi(this.artFrom)
        .then((res) => {
          let data = res.data;
          this.tableList = data.list;
          this.$nextTick(() => {
            //Đảm bảo dom đã tải xong
            this.setChecked();
          });
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.artFrom.page = 1;
      this.getDataList();
    },
  },
};
</script>

<style scoped></style>
