<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="artFrom"
          :model="artFrom"
          :label-width="labelWidth"
          label-position="right"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Danh mục bài viết:" label-for="pid">
            <el-cascader
              v-model="artFrom.pid"
              placeholder="Vui lòng chọn"
              class="treeSel"
              @change="handleCheckChange"
              :options="treeData"
              :props="props"
              style="width: 250px"
            >
            </el-cascader>
          </el-form-item>
          <el-form-item label="Tìm kiếm bài viết:" label-for="title">
            <el-input clearable placeholder="Vui lòng nhập" v-model="artFrom.title" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <router-link :to="$routeProStr + '/cms/article/add_article'" v-auth="['cms-article-creat']"
        ><el-button type="primary" class="bnt">Thêm bài viết</el-button></router-link
      >
      <el-table
        :data="cmsList"
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
        <el-table-column label="Hình ảnh bài viết" min-width="90">
          <template slot-scope="scope">
            <div v-if="scope.row.image_input.length !== 0" v-viewer>
              <div class="tabBox_img" v-for="(item, index) in scope.row.image_input" :key="index">
                <img v-lazy="item" />
              </div>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tên bài viết" min-width="130">
          <template slot-scope="scope">
            <el-tooltip placement="top" :open-delay="600">
              <div slot="content">{{ ' [ ' + scope.row.catename + ' ] ' + scope.row.title }}</div>
              <span class="line2">{{ scope.row.title }}</span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column label="Liên kết sản phẩm" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.store_name || '--' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Lượt xem" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.visit }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time | formatDate }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="300">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="artRelation(scope.row, 'Hủy liên kết', index)">{{
              scope.row.product_id === 0 ? 'Liên kết' : 'Hủy liên kết'
            }}</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa bài viết', scope.$index)">Xóa</a>
            <el-divider direction="vertical"></el-divider>
            <el-dropdown size="small" @command="onCopy(scope.row, $event)" :transfer="true">
              <span class="el-dropdown-link">Sao chép liên kết<i class="el-icon-arrow-down el-icon--right"></i></span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="1">Liên kết di động</el-dropdown-item>
                <el-dropdown-item command="2">Liên kết PC</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="artFrom.page"
          :limit.sync="artFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <!--Liên kết-->
    <el-dialog :visible.sync="modals" title="Danh sách sản phẩm" class="paymentFooter" width="1000px" @closed="cancel">
      <goods-list ref="goodslist" @getProductId="getProductId" v-if="modals"></goods-list>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { cmsListApi, categoryListApi, relationApi } from '@/api/cms';
import relationList from './relation';
import { formatDate } from '@/utils/validate';
import goodsList from '@/components/goodsList/index';
export default {
  name: 'cms_article',
  data() {
    return {
      modalTitleSs: '',
      loading: false,
      artFrom: {
        pid: 0,
        title: '',
        page: 1,
        limit: 20,
      },
      total: 0,
      cmsList: [],
      treeData: [],
      list: [],
      cid: 0, // ID danh mục di chuyển
      cmsId: 0,
      formValidate: {
        type: 1,
      },
      rows: {},
      modal_loading: false,
      modals: false,
      props: {
        value: 'id',
        label: 'title',
        emitPath: false,
      },
    };
  },
  components: {
    relationList,
    goodsList,
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
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  created() {},
  activated() {
    this.artFrom.pid = this.$route.query.id ? this.$route.query.id : 0;
    this.getList();
    this.getClass();
  },
  methods: {
    // Liên kết thành công
    getProductId(row) {
      let data = {
        product_id: row.id,
      };
      relationApi(data, this.rows.id)
        .then(async (res) => {
          this.$message.success(res.msg);
          row.id = 0;
          this.modal_loading = false;
          this.modals = false;
          setTimeout(() => {
            this.getList();
          }, 500);
        })
        .catch((res) => {
          this.modal_loading = false;
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    cancel() {
      this.modals = false;
    },
    // Danh sách hạng
    getList() {
      this.loading = true;
      cmsListApi(this.artFrom)
        .then(async (res) => {
          let data = res.data;
          this.cmsList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Danh mục
    getClass() {
      categoryListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.treeData = data;
          let obj = {
            id: 0,
            title: 'Tất cả',
          };
          this.treeData.unshift(obj);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Cây thả xuống
    handleCheckChange(data) {
      this.artFrom.pid = data ? data : 0;
      this.artFrom.page = 1;
      this.getList();
    },
    // Sửa
    edit(row) {
      this.$router.push({ path: this.$routeProStr + '/cms/article/add_article/' + row.id });
    },
    // Liên kết
    artRelation(row, tit, num) {
      this.rows = row;
      if (row.product_id === 0) {
        this.modals = true;
      } else {
        let delfromData = {
          title: tit,
          num: num,
          url: `/cms/cms/unrelation/${row.id}`,
          method: 'PUT',
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
      }
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `cms/cms/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.cmsList.splice(num, 1);
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
    onCopy(row, type) {
      let copy_url = type == 1 ? row.copy_url : row.copy_url_pc;
      this.$copyText(copy_url)
        .then((message) => {
          this.$message.success('Sao chép thành công');
        })
        .catch((err) => {
          this.$message.error('Sao chép thất bại');
        });
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
