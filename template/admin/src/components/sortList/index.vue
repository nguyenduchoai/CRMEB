<template>
  <div class="goodList">
    <el-form ref="formValidate" :model="formValidate" label-width="120px" label-position="right" class="tabform">
      <el-row :gutter="24">
        <el-col v-bind="grid">
          <el-form-item label="Danh mục sản phẩm:" label-for="pid">
            <el-select v-model="formValidate.pid" style="width: 230px" clearable @change="userSearchs">
              <el-option v-for="item in treeSelect" :value="item.id" :key="item.id" :label="item.cate_name"></el-option>
            </el-select>
          </el-form-item>
        </el-col>
        <el-col v-bind="grid">
          <el-form-item label="Tìm kiếm sản phẩm:" label-for="store_name">
            <el-input
              search
              enter-button
              placeholder="Vui lòng nhập danh mục sản phẩm, id"
              v-model="formValidate.name"
              style="width: 80%"
              @on-search="userSearchs"
            />
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <el-table
      ref="table"
      empty-text="Chưa có dữ liệu"
      max-height="400"
      :data="tableList"
      v-loading="loading"
      @select="selectionGood"
    >
      <el-table-column type="selection" width="55"> </el-table-column>
      <el-table-column label="ID sản phẩm" width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Hình ảnh" min-width="90">
        <template slot-scope="scope">
          <div class="tabBox_img" v-viewer>
            <img v-lazy="scope.row.pic" />
          </div>
        </template>
      </el-table-column>
      <el-table-column label="Danh mục sản phẩm" min-width="130">
        <template slot-scope="scope">
          <span>{{ scope.row.cate_name }}</span>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import { getByCategory } from '@/api/diy';
export default {
  name: 'index',
  props: {},
  data() {
    return {
      formValidate: {
        pid: 0,
        name: '',
      },
      treeSelect: [],
      loading: false,
      grid: {
        xl: 10,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableList: [],
      currentid: 0,
      productRow: {},
      columns: [
        {
          type: 'selection',
          width: 60,
          align: 'center',
        },
        {
          title: 'ID sản phẩm',
          key: 'id',
        },
        {
          title: 'Hình ảnh',
          slot: 'image',
        },
        {
          title: 'Danh mục sản phẩm',
          key: 'cate_name',
          minWidth: 150,
        },
      ],
      images: [],
    };
  },
  computed: {},
  created() {},
  mounted() {
    this.goodsCategory();
    this.getList();
  },
  methods: {
    selectionGood(e) {
      this.$emit('getProductDiy', e);
    },
    // Danh mục sản phẩm cấp 1;
    goodsCategory() {
      getByCategory()
        .then((res) => {
          this.treeSelect = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      getByCategory(this.formValidate)
        .then(async (res) => {
          this.tableList = res.data;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.getList();
    },
    // clear() {
    //     this.productRow.id = '';
    //     this.currentid = '';
    // }
  },
};
</script>

<style lang="scss" scoped>
.footer {
  margin: 15px 0;
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
.tabform {
  ::v-deep .ivu-form-item {
    margin-bottom: 16px !important;
  }
}
.btn {
  margin-top: 20px;
  float: right;
}
.goodList {
  ::v-deeptable {
    width: 100% !important;
  }
}
</style>
