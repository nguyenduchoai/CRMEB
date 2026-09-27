<template>
  <div class="goods-box" v-if="defaults.goodsList">
    <div class="wrapper">
      <draggable class="dragArea list-group" :list="defaults.goodsList.list" group="peoples">
        <div
          class="item"
          v-for="(goods, index) in defaults.goodsList.list"
          :key="index"
          v-show="defaults.goodsList.list.length"
        >
          <img :src="type == 1 ? goods.pic : goods.image" alt="" />
          <span class="iconfont icondel_1" v-db-click @click.stop="bindDelete(index)"></span>
        </div>
        <div class="add-item item" v-db-click @click="modals = true"><span class="iconfont iconaddto"></span></div>
      </draggable>
    </div>

    <el-dialog
      :visible.sync="modals"
      :title="titles"
      class="paymentFooter"
      :class="type ? '' : 'middleTop'"
      width="900px"
    >
      <sort-list ref="goodslist" @getProductDiy="getProductDiy" v-if="modals && type == 1"></sort-list>
      <goods-list
        ref="goodslist"
        @getProductDiy="getProductDiy"
        :ischeckbox="true"
        :type="type"
        :is_new="is_new"
        :diy="true"
        v-if="modals && type != 1"
      ></goods-list>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel">Hủy</el-button>
        <el-button type="primary" v-db-click @click="ok">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import vuedraggable from 'vuedraggable';
import goodsList from '@/components/goodsList';
import sortList from '@/components/sortList';
export default {
  name: 'c_goods',
  props: {
    name: {
      type: String,
    },
    configData: {
      type: null,
    },
    configNum: {
      type: Number | String,
      default: 'default',
    },
  },
  components: {
    goodsList,
    sortList,
    draggable: vuedraggable,
  },
  watch: {
    configData: {
      handler(nVal, oVal) {
        this.defaults = nVal[this.configNum];
        let goodType = nVal[this.configNum].titleInfo ? nVal[this.configNum].titleInfo.type : 0;
        this.type = nVal[this.configNum].selectConfig.type
          ? nVal[this.configNum].selectConfig.type
          : goodType
          ? goodType
          : 0;
        switch (this.type) {
          case 0:
            this.titles = 'Danh sách sản phẩm';
            break;
          case 1:
            this.titles = 'Danh sách danh mục';
            break;
          case 8:
            this.titles = 'Danh sách săn giảm giá';
            break;
          case 2:
            this.titles = 'Danh sách flash sale';
            break;
          case 3:
            this.titles = 'Danh sách mua chung';
            break;
          default:
        }
      },
      immediate: true,
      deep: true,
    },
  },
  data() {
    return {
      modals: false,
      goodsList: [],
      tempGoods: [],
      defaults: {},
      type: '',
      is_new: '',
      loading: true,
      titles: '',
    };
  },
  created() {
    this.defaults = this.configData[this.configNum];
    this.is_new = this.configData[this.configNum].is_new;
  },
  methods: {
    getProductDiy(data) {
      this.tempGoods = data;
      this.loading = false;
    },
    cancel() {
      this.tempGoods = [];
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1));
    },
    ok() {
      if (!this.tempGoods.length) {
        return this.$message.warning('Vui lòng chọn sản phẩm trước');
      }
      let list = this.defaults.goodsList.list;
      list.push.apply(list, this.tempGoods);
      // list.push(this.tempGoods);
      let picList = this.unique(list);
      this.defaults.goodsList.list = picList;
      // this.defaults.goodsList.list.push(this.tempGoods);
      this.modals = false;
    },
    bindDelete(index) {
      this.defaults.goodsList.list.splice(index, 1);
    },
  },
};
</script>

<style lang="scss" scoped>
.middleTop ::v-deep .ivu-modal-wrap .ivu-modal {
  top: 50% !important;
  margin-top: -350px;
}
.goods-box {
  padding: 16px 0;
  margin-bottom: 16px;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  .wrapper,
  .list-group {
    display: flex;
    flex-wrap: wrap;
  }
  .add-item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    margin-bottom: 10px;
    background: #f7f7f7;
    .iconfont {
      font-size: 18px;
      color: #d8d8d8;
    }
  }
  .item {
    position: relative;
    width: 80px;
    height: 80px;
    margin-bottom: 20px;
    margin-right: 12px;
    &:nth-child(4n) {
      margin-right: 0;
    }
    img {
      width: 100%;
      height: 100%;
    }
    .icondel_1 {
      position: absolute;
      right: -10px;
      top: -16px;
      color: #ccc;
      font-size: 28px;
      cursor: pointer;
    }
  }
}
</style>
