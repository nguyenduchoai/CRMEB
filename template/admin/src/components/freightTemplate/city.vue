<template>
  <div>
    <el-dialog :visible.sync="addressModal" title="Chọn khu vực giao hàng" width="50%" class="modal">
      <el-row :gutter="24">
        <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24" class="item">
          <div class="acea-row row-right row-middle">
            <el-checkbox v-model="iSselect" @change="allCheckbox">Chọn tất cả</el-checkbox>
            <div class="empty" v-db-click @click="empty">Xóa hết</div>
          </div>
        </el-col>
      </el-row>
      <el-row :gutter="24" v-loading="loading">
        <el-col
          :xl="6"
          :lg="6"
          :md="6"
          :sm="8"
          :xs="6"
          class="item"
          v-for="(item, index) in cityList"
          :key="index"
          v-if="item.isShow"
        >
          <el-popover placement="top-start" width="600" trigger="hover" :open-delay="600">
            <div>
              <div class="city">
                <div class="checkBox">
                  <div class="arrow"></div>
                  <div>
                    <el-checkbox
                      v-model="city.checked"
                      :label="city.name"
                      @change="primary(index, indexn)"
                      class="itemn"
                      v-for="(city, indexn) in item.children"
                      :key="indexn"
                      v-show="city.isShow"
                      >{{ city.name }}</el-checkbox
                    >
                  </div>
                </div>
              </div>
            </div>
            <el-checkbox slot="reference" v-model="item.checked" :label="item.name" @change="checkedClick(index)">{{
              item.name
            }}</el-checkbox
            ><span class="red">({{ (item.count || 0) + '/' + item.childNum }})</span>
          </el-popover>
        </el-col>
      </el-row>
      <div slot="footer">
        <el-button v-db-click @click="close">Hủy</el-button>
        <el-button type="primary" v-db-click @click="confirm">Xác nhận</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { templatesCityListApi } from '@/api/setting';
export default {
  name: 'city',
  props: {
    type: {
      type: Number,
      default: 0,
    },
    selectArr: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      iSselect: false,
      addressModal: false,
      cityList: [],
      activeCity: -1,
      loading: false,
    };
  },
  computed: {},
  methods: {
    enter(index) {
      this.activeCity = index;
    },
    leave() {
      this.activeCity = null;
    },
    getCityList() {
      this.loading = true;
      templatesCityListApi().then((res) => {
        this.loading = false;
        this.selectArr = [];
        res.data.forEach((el, index, arr) => {
          el.isShow = true;
          el.children.forEach((child, j) => {
            child.isShow = true;
            if (this.selectArr.length > 0) {
              this.selectArr.forEach((sel, sindex) => {
                sel.children.forEach((sitem, sj) => {
                  if (child.city_id == sitem.city_id) {
                    child.isShow = false;
                  }
                });
              });
            }
          });
        });
        res.data.forEach((el, index, arr) => {
          let num = 0;
          let oldNum = 0;
          el.children.forEach((child, j) => {
            if (!child.isShow) {
              num++;
            } else {
              oldNum++;
            }
          });
          if (num == el.children.length) {
            el.isShow = false;
          }
          el.childNum = oldNum;
        });
        this.cityList = res.data;
        this.iSselect = false;
      });
    },
    /**
     * Chọn tất cả hoặc bỏ chọn tất cả
     * @param checked
     */
    allCheckbox: function () {
      let that = this,
        checked = this.iSselect;
      that.cityList.forEach(function (item, key) {
        that.$set(that.cityList[key], 'checked', checked);
        if (checked) {
          that.$set(that.cityList[key], 'count', that.cityList[key].children.length);
        } else {
          that.$set(that.cityList[key], 'count', 0);
        }
        that.cityList[key].children.forEach(function (val, k) {
          that.$set(that.cityList[key].children[k], 'checked', checked);
        });
      });
      // this.render();
    },
    // Xóa hết;
    empty() {
      let that = this;
      that.cityList.forEach(function (item, key) {
        that.$set(that.cityList[key], 'checked', false);
        that.cityList[key].children.forEach(function (val, k) {
          that.$set(that.cityList[key].children[k], 'checked', false);
        });
        that.$set(that.cityList[key], 'count', 0);
      });
      this.iSselect = false;
    },
    /**
     * Click chọn tỉnh
     * @param index
     */
    checkedClick: function (index) {
      let that = this;
      if (that.cityList[index].checked) {
        that.$set(that.cityList[index], 'count', that.cityList[index].childNum);
        that.cityList[index].children.forEach(function (item, key) {
          that.$set(that.cityList[index].children[key], 'checked', true);
        });
      } else {
        that.$set(that.cityList[index], 'count', 0);
        that.$set(that.cityList[index], 'checked', false);
        that.cityList[index].children.forEach(function (item, key) {
          that.$set(that.cityList[index].children[key], 'checked', false);
        });
        that.iSselect = false;
      }
      // this.render();
    },
    /**
     * Click chọn quận/huyện
     * @param index
     * @param ind
     */
    primary: function (index, ind) {
      let checked = false,
        count = 0;
      this.cityList[index].children.forEach(function (item, key) {
        if (item.checked) {
          checked = true;
          count++;
        }
      });
      this.$set(this.cityList[index], 'count', count);
      this.$set(this.cityList[index], 'checked', checked);
      // this.render();
    },
    // Xác nhận;
    confirm() {
      let that = this;
      // Tỉnh/thành phố đã chọn;
      let selectList = [];
      that.cityList.forEach(function (item, key) {
        let data = {};
        if (item.checked) {
          data = {
            name: item.name,
            city_id: item.city_id,
            children: [],
          };
        }
        that.cityList[key].children.forEach(function (i, k) {
          if (i.checked) {
            data.children.push({
              city_id: i.city_id,
            });
          }
        });
        if (data.city_id !== undefined) {
          selectList.push(data);
        }
      });
      if (selectList.length === 0) {
        return that.$message.error('Vui lòng chọn ít nhất một tỉnh hoặc thành phố');
      } else {
        this.$emit('selectCity', selectList, this.type);
        that.addressModal = false;
        this.cityList = [];
      }
      // parent.selectCity(selectList,type);
      // var index = parent.layer.getFrameIndex(window.name);
      // parent.layer.close(index);
    },
    close() {
      this.addressModal = false;
      this.cityList = [];
    },
  },
  mounted() {
    // this.getCityList();
  },
  beforeDestroy() {},
};
</script>

<style lang="scss" scoped>
.modal .item {
  margin-bottom: 20px;
}
.modal .item .city {
  position: absolute;
  z-index: 9;
  top: 17px;
  width: 100%;
  padding-top: 18px;
}
.modal .item .city .checkBox {
  width: 97%;
  padding: 10px;
  border: 1px solid #eee;
  background-color: #fff;
  max-height: 100px;
  overflow-x: hidden;
  overflow-y: auto;
}
.modal .item .city .checkBox .arrow {
  position: absolute;
  top: 3px;
  width: 0;
  height: 0;
  border: 8px solid transparent;
  border-bottom-color: #ddd;
}
.modal .item .city .checkBox .arrow:before {
  position: absolute;
  bottom: -8px;
  right: -7px;
  content: '';
  width: 0;
  height: 0;
  border: 7px solid transparent;
  border-bottom-color: #fff;
}
.modal .item .city .checkBox .itemn {
  margin-bottom: 14px;
}
.radio {
  padding: 5px 0;
  font-size: 14px !important;
}
.red {
  color: #ff0000;
}
.empty {
  cursor: pointer;
  font-size: 12px;
  margin-left: 10px;
  line-height: 19px;
  color: var(--prev-color-text-primary);
  font-weight: 500;
}
</style>
