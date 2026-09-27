<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          label-position="right"
          inline
          class="tabform"
          @submit.native.prevent
        >
          <el-form-item label="Trạng thái duyệt:">
            <el-select v-model="formValidate.status" clearable @change="selChange" class="form_content_width">
              <el-option
                v-for="(item, index) in treeData.withdrawal"
                :value="item.value"
                :key="index"
                :label="item.title"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm:">
            <el-input
              clearable
              placeholder="Vui lòng nhập tên sản phẩm/ID"
              v-model="formValidate.kerword"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="selChange">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button v-auth="['setting-system_menus-add']" type="primary" v-db-click @click="menusAdd('Thêm phòng livestream')"
        >Thêm sản phẩm
      </el-button>
      <!-- <el-button
        v-auth="['setting-system_menus-add']"
        type="success"
        v-db-click @click="syncGoods"
        style="margin-left: 20px"
        >Đồng bộ sản phẩm
      </el-button> -->
      <el-table
        :data="tabList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID sản phẩm" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.product_id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên sản phẩm" min-width="120">
          <template slot-scope="scope">
            <div class="product_box">
              <div v-viewer>
                <img :src="scope.row.product.image" alt="" />
              </div>
              <div class="txt">{{ scope.row.name }}</div>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Giá livestream" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá gốc" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.cost_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tồn kho" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.product.stock }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái duyệt" min-width="80">
          <template slot-scope="scope">
            <div>{{ scope.row.audit_status | liveStatusFilter }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Hiển thị" min-width="80">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.is_show"
              :value="scope.row.is_show"
              @change="onchangeIsShow(scope.row)"
              size="large"
              :disabled="scope.row.audit_status != 2"
              active-text="Hiện"
              inactive-text="Ẩn"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row, 'Sửa')">Chi tiết</a>
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
    <!--Chi tiết-->
    <el-dialog :visible.sync="modals" title="Chi tiết sản phẩm" class="paymentFooter" scrollable width="720px">
      <goodsFrom ref="goodsDetail" :FormData="FormData" />
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { liveGoods, liveSyncGoods, liveGoodsDetail, liveGoodsShow } from '@/api/live';
import goodsFrom from './components/goods_detail';
export default {
  name: 'live',
  components: {
    goodsFrom,
  },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      formValidate: {
        status: '',
        kerword: '',
        page: 1,
        limit: 20,
      },
      treeData: {
        withdrawal: [
          {
            title: 'Tất cả',
            value: '',
          },
          {
            title: 'Chờ duyệt',
            value: 0,
          },
          {
            title: 'Đã duyệt',
            value: 1,
          },
          {
            title: 'Không được duyệt',
            value: -1,
          },
        ],
      },
      columns1: [
        { key: 'product_id', title: 'ID sản phẩm', minWidth: 35 },
        { slot: 'name', minWidth: 35, title: 'Tên sản phẩm' },
        { key: 'price', minWidth: 35, title: 'Giá livestream' },
        { slot: 'cost_price', minWidth: 35, title: 'Giá gốc' },
        { slot: 'stock', minWidth: 35, title: 'Tồn kho' },
        { slot: 'status', minWidth: 35, title: 'Trạng thái duyệt' },
        { slot: 'is_mer_show', title: 'Hiển thị', minWidth: 80 },
        // {"key": "sort", "title": "Sắp xếp", "minWidth": 35},
        { slot: 'action', fixed: 'right', title: 'Thao tác', minWidth: 120 },
      ],
      tabList: [],
      loading: false,
      modals: false,
      total: 0,
      FormData: {},
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
  mounted() {
    this.getList();
  },
  methods: {
    // Hiện/ẩn phòng livestream
    onchangeIsShow({ id, is_show }) {
      liveGoodsShow(id, is_show)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    // Dữ liệu danh sách
    getList() {
      this.loading = true;
      liveGoods(this.formValidate)
        .then((res) => {
          this.total = res.data.count;
          this.tabList = res.data.list;
          this.loading = false;
        })
        .catch((error) => {
          this.$message.error(error.msg);
          this.loading = false;
        });
    },
    // Chọn
    selChange() {
      this.formValidate.page = 1;
      this.getList();
    },
    // Thêm sản phẩm
    menusAdd() {
      this.$router.push({
        path: this.$routeProStr + '/marketing/live/add_live_goods',
      });
    },
    // Đồng bộ sản phẩm
    syncGoods() {
      liveSyncGoods()
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    edit(row) {
      liveGoodsDetail(row.id)
        .then((res) => {
          this.FormData = res.data;
          this.modals = true;
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `live/goods/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tabList.splice(num, 1);

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
.product_box {
  display: flex;
  align-items: center;
  img {
    width: 36px;
    height: 36px;
  }
  .txt {
    margin-left: 10px;
    color: #000;
    font-size: 12px;
  }
}
</style>
