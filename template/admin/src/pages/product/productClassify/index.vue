<template>
  <div class="article-manager">
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form ref="artFrom" :model="artFrom" inline label-width="80px" label-position="right" @submit.native.prevent>
          <el-form-item label="Danh mục sản phẩm:" prop="pid" label-for="pid">
            <el-select
              v-model="artFrom.pid"
              placeholder="Vui lòng chọn danh mục sản phẩm"
              @change="userSearchs"
              clearable
              class="form_content_width"
            >
              <el-option v-for="item in treeSelect" :value="item.id" :label="item.cate_name" :key="item.id">{{
                item.cate_name
              }}</el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Trạng thái danh mục:" label-for="is_show">
            <el-select
              v-model="artFrom.is_show"
              placeholder="Vui lòng chọn trạng thái danh mục"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Hiện"></el-option>
              <el-option value="0" label="Ẩn"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tên danh mục:" label-for="status2">
            <el-input clearable placeholder="Vui lòng nhập tên danh mục" v-model="artFrom.cate_name" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button v-auth="['product-save-cate']" type="primary" class="bnt" v-db-click @click="addClass"
        >Thêm danh mục</el-button
      >
      <vxe-table
        class="mt14"
        highlight-hover-row
        :loading="loading"
        header-row-class-name="false"
        :tree-config="{ children: 'children' }"
        :data="tableData"
      >
        <vxe-table-column field="id" title="ID" tooltip width="80"></vxe-table-column>
        <vxe-table-column field="cate_name" tree-node title="Tên danh mục" min-width="250"></vxe-table-column>
        <vxe-table-column field="pic" title="Biểu tượng danh mục" min-width="100">
          <template v-slot="{ row }">
            <div class="tabBox_img" v-viewer v-if="row.pic">
              <img v-lazy="row.pic" />
            </div>
          </template>
        </vxe-table-column>
        <vxe-table-column field="sort" title="Thứ tự sắp xếp" min-width="100" tooltip="true"></vxe-table-column>
        <vxe-table-column field="is_show" title="Trạng thái" min-width="120">
          <template v-slot="{ row }">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="row.is_show"
              :value="row.is_show"
              @change="onchangeIsShow(row)"
              size="large"
              active-text="Bật"
              inactive-text="Tắt"
            >
            </el-switch>
          </template>
        </vxe-table-column>
        <vxe-table-column field="date" title="Thao tác" width="120" fixed="right">
          <template v-slot="{ row, index }">
            <a v-db-click @click="edit(row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(row, 'Xóa danh mục sản phẩm', index)">Xóa</a>
          </template>
        </vxe-table-column>
      </vxe-table>
    </el-card>
    <!-- Thêm Sửa biểu mẫu-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="userSearchs"></edit-from>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { productListApi, productCreateApi, productEditApi, setShowApi, treeListApi } from '@/api/product';
import editFrom from '../../../components/from/from';
export default {
  name: 'product_productClassify',
  components: {
    editFrom,
  },
  data() {
    return {
      treeSelect: [],
      FromData: null,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      artFrom: {
        pid: 0,
        is_show: '',
        page: 1,
        cate_name: '',
        limit: 15,
      },
      total: 0,
      tableData: [],
    };
  },
  computed: {
    ...mapState('admin/userLevel', ['categoryId']),
  },
  mounted() {
    this.goodsCategory();
    this.getList();
  },
  methods: {
    // Danh mục sản phẩm;
    goodsCategory() {
      treeListApi(0)
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
      this.artFrom.is_show = this.artFrom.is_show || '';
      this.artFrom.pid = this.artFrom.pid || '';
      productListApi(this.artFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableData = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    pageChange(index) {
      this.artFrom.page = index;
      this.getList();
    },
    // Thêm
    addClass() {
      this.$modalForm(productCreateApi()).then(() => this.getList());
    },
    // Sửa
    edit(row) {
      this.$modalForm(productEditApi(row.id)).then(() => this.getList());
    },
    // Sửa trạng thái
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
    // Cây thả xuống
    handleCheckChange(data) {
      let value = '';
      let title = '';
      this.list = [];
      this.artFrom.pid = 0;
      data.forEach((item, index) => {
        value += `${item.id},`;
        title += `${item.title},`;
      });
      value = value.substring(0, value.length - 1);
      title = title.substring(0, title.length - 1);
      this.list.push({
        value,
        title,
      });
      this.artFrom.pid = value;
      this.getList();
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `product/category/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.artFrom.page = 1;
      this.getList();
    },
  },
};
</script>
<style lang="scss" scoped>
.treeSel ::v-deep .ivu-select-dropdown-list {
  padding: 0 10px !important;
  box-sizing: border-box;
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
