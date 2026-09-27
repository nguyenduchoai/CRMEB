<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.meta.title"
      :backUrl="$routeProStr + '/marketing/live/live_goods'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-form
        ref="formValidate"
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        class="tabform"
        @submit.native.prevent
      >
        <el-row :gutter="24">
          <el-col :span="24">
            <el-form-item label="Chọn sản phẩm:">
              <div class="box">
                <div class="box-item" v-for="(item, index) in goodsList" :key="index">
                  <img :src="item.image" alt="" />
                  <i class="el-icon-error" v-db-click @click="bindDelete(index, item)" style="font-size: 16px"></i>
                </div>
                <div class="upload-box acea-row row-center-wrapper" v-db-click @click="selectGoods">
                  <i class="el-icon-goods" style="font-size: 24px"></i>
                </div>
              </div>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div class="active-btn" v-if="goodsList.length > 0">
        <el-button type="primary" v-db-click @click="liveGoods">Tạo sản phẩm livestream</el-button>
      </div>
      <div class="table-box" v-if="isShowBox">
        <el-table
          :data="tabList"
          ref="table"
          class="mt14"
          v-loading="loading"
          no-userFrom-text="Chưa có dữ liệu"
          no-filtered-userFrom-text="Không có kết quả phù hợp"
        >
          <el-table-column label="ID sản phẩm" width="80">
            <template slot-scope="scope">
              <span>{{ scope.row.id }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thông tin sản phẩm" min-width="90">
            <template slot-scope="scope">
              <div class="product_box">
                <img :src="scope.row.image" alt="" />
                <span>{{ scope.row.store_name }}</span>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="Giá bán livestream" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.price }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Tồn kho" min-width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.stock }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thao tác" fixed="right" width="80">
            <template slot-scope="scope">
              <a v-db-click @click="del(scope.row, scope.$index)">Xóa</a>
            </template>
          </el-table-column>
        </el-table>

        <div class="sub_btn">
          <el-button
            type="primary"
            style="width: 8%"
            v-db-click
            @click="bindSub"
            :disabled="disabled"
            :loading="loadings"
            >Gửi</el-button
          >
        </div>
      </div>
    </el-card>
    <el-dialog :visible.sync="modals" title="Danh sách sản phẩm" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" :selectIds="selectIds" @getProductId="getProductId" :ischeckbox="true"></goods-list>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList';
import { liveGoodsCreat, liveGoodsAdd } from '@/api/live';
export default {
  name: 'add_goods',
  components: {
    goodsList,
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '100px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  data() {
    return {
      isShowBox: false,
      loading: false,
      modals: false,
      goodsList: [],
      tempGoods: {},
      formValidate: {},
      tabList: [],
      disabled: false,
      loadings: false,
      selectIds: [],
    };
  },
  methods: {
    selectGoods() {
      this.modals = true;
    },
    // Tạo sản phẩm livestream
    liveGoods() {
      let array = [];
      this.goodsList.map((el) => {
        array.push(el.product_id);
      });
      liveGoodsCreat({
        product_id: array,
      })
        .then((res) => {
          this.tabList = res.data;
          this.isShowBox = true;
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.product_id) && res.set(arr.product_id, 1));
    },
    getProductId(productList) {
      this.modals = false;
      this.goodsList = this.unique(this.goodsList.concat(productList));
    },
    bindDelete(index, item) {
      this.goodsList.splice(index, 1);
      let i = this.tabList.findIndex((e) => e.id == item.product_id);
      this.tabList.splice(i, 1);
      if (!this.goodsList.length) {
        this.isShowBox = false;
      }
    },
    del(row, index) {
      this.tabList.splice(index, 1);
      let i = this.goodsList.findIndex((e) => e.product_id == row.id);
      this.goodsList.splice(i, 1);
      if (!this.tabList.length) {
        this.isShowBox = false;
      }
    },
    // Gửi
    bindSub() {
      this.disabled = true;
      this.loadings = true;
      liveGoodsAdd({
        goods_info: this.tabList,
      })
        .then((res) => {
          this.$message.success('Thêm thành công');
          this.disabled = false;
          setTimeout(() => {
            this.$router.push({ path: this.$routeProStr + '/marketing/live/live_goods' });
          }, 500);
        })
        .catch((error) => {
          this.disabled = false;
          this.$message.error(error.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.upload-box {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
  cursor: pointer;
}
.box {
  display: flex;
  flex-wrap: wrap;
  .box-item {
    position: relative;
    margin-right: 20px;
    .el-icon-error {
      position: absolute;
      right: -10px;
      top: -8px;
      color: #999;
      cursor: pointer;
    }
  }
  .upload-box,
  .box-item {
    width: 60px;
    height: 60px;
    margin-bottom: 10px;

    img {
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
  }
}
.active-btn {
  padding-left: 96px;
}
.table-box {
  margin: 0 107px;
}
.sub_btn {
  margin-top: 10px;
}
.product_box {
  display: flex;

  img {
    width: 36px;
    height: 36px;
    margin-right: 10px;
  }
}
</style>
