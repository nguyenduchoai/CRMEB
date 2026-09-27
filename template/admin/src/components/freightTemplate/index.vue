<template>
  <div>
    <el-dialog
      :visible.sync="isTemplate"
      title="Mẫu phí vận chuyển"
      width="1000px"
      if="isTemplate"
      @on-cancel="cancel"
      @closed="close"
    >
      <div class="Modals">
        <el-form class="form" ref="formData" label-width="120px" label-position="right">
          <el-row :gutter="24">
            <el-col :xl="18" :lg="18" :md="18" :sm="24" :xs="24">
              <el-form-item label="Tên mẫu:" prop="name">
                <el-input type="text" placeholder="Vui lòng nhập tên mẫu" :maxlength="20" v-model="formData.name" />
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24">
            <el-col :xl="18" :lg="18" :md="18" :sm="24" :xs="24">
              <el-form-item label="Cách tính phí:" props="state" label-for="state">
                <el-radio-group class="radio" v-model="formData.type" @input="changeRadio" element-id="state">
                  <el-radio :label="1">Theo số lượng</el-radio>
                  <el-radio :label="2">Theo trọng lượng</el-radio>
                  <el-radio :label="3">Theo thể tích</el-radio>
                </el-radio-group>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24">
            <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
              <el-form-item class="label" label="Khu vực giao hàng và phí vận chuyển:" props="state" label-for="state">
                <el-table ref="table" :data="templateList" class="ivu-mt" empty-text="Chưa có dữ liệu" border>
                  <el-table-column label="Khu vực giao hàng" minWidth="100">
                    <template slot-scope="scope">
                      <el-input v-model="templateList[scope.$index].regionName" />
                    </template>
                  </el-table-column>
                  <el-table-column
                    :label="formData.type === 2 ? 'Khối lượng đầu (KG)' : formData.type === 3 ? 'Thể tích đầu (m³)' : 'Số lượng đầu'"
                    minWidth="100"
                  >
                    <template slot-scope="scope">
                      <el-input type="number" v-model="templateList[scope.$index].first" />
                    </template>
                  </el-table-column>
                  <el-table-column label="Phí vận chuyển (đ)" minWidth="100">
                    <template slot-scope="scope">
                      <el-input type="number" v-model="templateList[scope.$index].price" />
                    </template>
                  </el-table-column>
                  <el-table-column
                    :label="formData.type === 2 ? 'Khối lượng tiếp theo (KG)' : formData.type === 3 ? 'Thể tích tiếp theo (m³)' : 'Số lượng tiếp theo'"
                    minWidth="100"
                  >
                    <template slot-scope="scope">
                      <el-input type="number" v-model="templateList[scope.$index].continue" />
                    </template>
                  </el-table-column>
                  <el-table-column label="Phí cộng thêm (đ)" minWidth="100">
                    <template slot-scope="scope">
                      <el-input type="number" v-model="templateList[scope.$index].continue_price" />
                    </template>
                  </el-table-column>
                  <el-table-column label="Thao tác" fixed="right" width="100">
                    <template slot-scope="scope">
                      <a
                        v-if="scope.row.regionName !== 'Mặc định toàn quốc'"
                        v-db-click
                        @click="delCity(scope.row, 'Khu vực giao hàng', scope.$index, 1)"
                        >Xóa</a
                      >
                    </template>
                  </el-table-column>
                </el-table>
                <el-row class="addTop">
                  <el-col>
                    <el-button type="primary" icon="md-add" v-db-click @click="addCity(1)">Thêm khu vực giao hàng</el-button>
                  </el-col>
                </el-row>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24">
            <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
              <el-form-item label="Miễn phí vận chuyển theo khu vực:" prop="store_name" label-for="store_name">
                <el-radio-group class="radio" v-model="formData.appoint_check">
                  <el-radio :label="1">Bật</el-radio>
                  <el-radio :label="0">Tắt</el-radio>
                </el-radio-group>
                <el-table
                  ref="table"
                  :data="appointList"
                  class="addTop mt10"
                  empty-text="Chưa có dữ liệu"
                  border
                  v-if="formData.appoint_check === 1"
                >
                  <el-table-column label="Chọn khu vực" minWidth="100">
                    <template slot-scope="scope">
                      <el-input v-model="appointList[scope.$index].placeName" />
                    </template>
                  </el-table-column>
                  <el-table-column
                    :label="formData.type === 2 ? 'Khối lượng miễn phí vận chuyển' : formData.type === 3 ? 'Thể tích miễn phí vận chuyển (m³)' : 'Số lượng miễn phí vận chuyển'"
                    minWidth="100"
                  >
                    <template slot-scope="scope">
                      <el-input type="number" v-model="appointList[scope.$index].a_num" />
                    </template>
                  </el-table-column>
                  <el-table-column label="Giá trị đơn miễn phí vận chuyển (đ)" minWidth="100">
                    <template slot-scope="scope">
                      <el-input type="number" v-model="appointList[scope.$index].a_price" />
                    </template>
                  </el-table-column>
                  <el-table-column label="Thao tác" fixed="right" width="100">
                    <template slot-scope="scope">
                      <a
                        v-if="scope.row.regionName !== 'Mặc định toàn quốc'"
                        v-db-click
                        @click="delCity(scope.row, 'Khu vực giao hàng', scope.$index, 2)"
                        >Xóa</a
                      >
                    </template>
                  </el-table-column>
                </el-table>
                <div v-if="formData.appoint_check === 1" class="free_tips">
                  Khu vực được chỉ định chỉ được miễn phí vận chuyển khi đáp ứng đồng thời điều kiện (số lượng/khối lượng/thể tích) và điều kiện giá trị đơn
                </div>
                <el-row class="addTop mt5" v-if="formData.appoint_check === 1">
                  <el-col>
                    <el-button type="primary" icon="md-add" v-db-click @click="addCity(2)">Thêm khu vực miễn phí vận chuyển</el-button>
                  </el-col>
                </el-row>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24">
            <el-col :xl="24" :lg="24" :md="24" :sm="24" :xs="24">
              <el-form-item label="Khu vực không giao hàng:" prop="store_name" label-for="store_name">
                <el-radio-group class="radio" v-model="formData.no_delivery_check">
                  <el-radio :label="1">Bật</el-radio>
                  <el-radio :label="0">Tắt</el-radio>
                </el-radio-group>
                <el-table
                  ref="table"
                  :data="noDeliveryList"
                  class="addTop mt10"
                  empty-text="Chưa có dữ liệu"
                  border
                  v-if="formData.no_delivery_check === 1"
                >
                  <el-table-column label="Chọn khu vực" minWidth="100">
                    <template slot-scope="scope">
                      <el-input v-model="noDeliveryList[scope.$index].placeName" />
                    </template>
                  </el-table-column>
                  <el-table-column label="Thao tác" fixed="right" width="100">
                    <template slot-scope="scope">
                      <a
                        v-if="scope.row.regionName !== 'Mặc định toàn quốc'"
                        v-db-click
                        @click="delCity(scope.row, 'Khu vực giao hàng', scope.$index, 3)"
                        >Xóa</a
                      >
                    </template>
                  </el-table-column>
                </el-table>
                <el-row class="addTop" v-if="formData.no_delivery_check === 1">
                  <el-col>
                    <el-button type="primary" icon="md-add" v-db-click @click="addCity(3)">Thêm khu vực không giao hàng</el-button>
                  </el-col>
                </el-row>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24">
            <el-col :xl="18" :lg="18" :md="18" :sm="24" :xs="24">
              <el-form-item label="Thứ tự sắp xếp:" prop="store_name" label-for="store_name">
                <el-input-number
                  :controls="false"
                  :min="0"
                  placeholder="Giá trị càng lớn càng hiển thị trước"
                  v-model="formData.sort"
                ></el-input-number>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="24">
            <el-col>
              <el-form-item prop="store_name" label-for="store_name">
                <el-button type="primary" v-db-click @click="handleSubmit">{{
                  id ? 'Lưu chỉnh sửa' : 'Gửi ngay'
                }}</el-button>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
      </div>
      <div slot="footer"></div>
    </el-dialog>
    <city ref="city" @selectCity="selectCity" :type="type" :selectArr="selectArr"></city>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import city from '@/components/freightTemplate/city';
import { templatesSaveApi, shipTemplatesApi } from '@/api/setting';
export default {
  name: 'freightTemplate',
  components: { city },
  props: {},
  data() {
    let that = this;
    return {
      isTemplate: false,
      templateList: [
        {
          region: [
            {
              name: 'Mặc định toàn quốc',
              city_id: 0,
            },
          ],
          regionName: 'Mặc định toàn quốc',
          first: 1,
          price: 0,
          continue: 1,
          continue_price: 0,
        },
      ],
      appointList: [],
      noDeliveryList: [],
      type: 1,
      formData: {
        type: 1,
        sort: 0,
        name: '',
        appoint_check: 0,
        no_delivery_check: 0,
      },
      id: 0,

      addressModal: false,
      indeterminate: true,
      checkAll: false,
      checkAllGroup: [],
      activeCity: -1,
      provinceAllGroup: [],
      index: -1,
      displayData: '',
      currentProvince: '',
      selectArr: [], // Truyền thành phố đã chọn
      noShippingArr: [], // Dữ liệu thành phố chọn không miễn phí vận chuyển
      yesShippingArr: [], // Dữ liệu thành phố chọn miễn phí vận chuyển
      noDeliveryArr: [], // Dữ liệu thành phố chọn không giao đến
    };
  },
  computed: {},
  methods: {
    close() {
      this.$emit('close');
    },
    editFrom(id) {
      this.id = id;
      shipTemplatesApi(id).then((res) => {
        let formData = res.data.formData;
        this.templateList = res.data.templateList;
        this.appointList = res.data.appointList;
        this.noDeliveryList = res.data.noDeliveryList;
        this.formData = {
          type: formData.type,
          sort: formData.sort,
          name: formData.name,
          appoint_check: formData.appoint_check,
          no_delivery_check: formData.no_delivery_check,
        };
        // this.headerType();
      });
    },
    selectCity: function (data, type) {
      let cityName = data
        .map(function (item) {
          return item.name;
        })
        .join(';');
      switch (type) {
        case 1:
          this.templateList.push({
            region: data,
            regionName: cityName,
            first: 1,
            price: 0,
            continue: 1,
            continue_price: 0,
          });
          this.noShippingArr = this.noShippingArr.concat(data);
          break;
        case 2:
          this.appointList.push({
            place: data,
            placeName: cityName,
            a_num: 0,
            a_price: 0,
          });
          this.yesShippingArr = this.yesShippingArr.concat(data);
          break;
        case 3:
          this.noDeliveryList.push({
            place: data,
            placeName: cityName,
          });
          this.noDeliveryArr = this.noDeliveryArr.concat(data);
          break;
      }
    },
    // Thêm riêng khu vực giao hàng
    addCity(type) {
      this.selectArr = type == 1 ? this.noShippingArr : type == 2 ? this.yesShippingArr : this.noDeliveryArr;
      this.type = type;
      this.$refs.city.getCityList();
      this.$refs.city.addressModal = true;
    },
    changeRadio() {},
    // Gửi
    handleSubmit: function () {
      let that = this;
      if (!that.formData.name.trim().length) {
        return that.$message.error('Vui lòng nhập tên mẫu');
      }
      for (let i = 0; i < that.templateList.length; i++) {
        if (that.templateList[i].first <= 0) {
          return that.$message.error('Số lượng đầu/khối lượng/thể tích phải lớn hơn 0');
        }
        if (that.templateList[i].price < 0) {
          return that.$message.error('Phí vận chuyển phải lớn hơn hoặc bằng 0');
        }
        if (that.templateList[i].continue <= 0) {
          return that.$message.error('Số lượng tiếp theo/khối lượng/thể tích phải lớn hơn 0');
        }
        if (that.templateList[i].continue_price < 0) {
          return that.$message.error('Phí cộng thêm phải lớn hơn hoặc bằng 0');
        }
      }
      if (that.formData.appoint_check === 1) {
        for (let i = 0; i < that.appointList.length; i++) {
          if (that.appointList[i].a_num <= 0) {
            return that.$message.error('Số lượng miễn phí vận chuyển phải lớn hơn 0');
          }
          if (that.appointList[i].a_price < 0) {
            return that.$message.error('Giá trị đơn miễn phí vận chuyển phải lớn hơn hoặc bằng 0');
          }
        }
      }
      let data = {
        appoint_info: that.appointList,
        region_info: that.templateList,
        no_delivery_info: that.noDeliveryList,
        sort: that.formData.sort,
        type: that.formData.type,
        name: that.formData.name,
        appoint: that.formData.appoint_check,
        no_delivery: that.formData.no_delivery_check,
      };
      templatesSaveApi(that.id, data).then((res) => {
        this.isTemplate = false;
        // this.$parent.getList();
        this.formData = {
          type: 1,
          sort: 0,
          name: '',
          appoint_check: 0,
          no_delivery_check: 0,
        };
        this.appointList = [];
        this.noDeliveryList = [];
        this.addressModal = false;
        this.templateList = [
          {
            region: [
              {
                name: 'Mặc định toàn quốc',
                city_id: 0,
              },
            ],
            regionName: 'Mặc định toàn quốc',
            first: 1,
            price: 0,
            continue: 1,
            continue_price: 0,
          },
        ];
        this.$emit('addSuccess');
        this.$message.success(res.msg);
      });
    },
    // Xóa
    delCity(row, tit, num, type) {
      if (type === 1) {
        this.templateList.splice(num, 1);
      } else if (type == 2) {
        this.appointList.splice(num, 1);
      } else {
        this.noDeliveryList.splice(num, 1);
      }
      //   let delfromData = {
      //     title: tit,
      //     num: num,
      //     url: `setting/shipping_templates/del/${row.id}`,
      //     method: "DELETE",
      //     ids: "",
      //   };
      //   this.$modalSure(delfromData)
      //     .then((res) => {
      //       this.$message.success(res.msg);
      //     })
      //     .catch((res) => {
      //       this.$message.error(res.msg);
      //     });
    },
    // Tắt
    cancel() {
      this.noShippingArr = [];
      this.noDeliveryArr = [];
      this.yesShippingArr = [];
      this.selectArr = [];
      this.formData = {
        type: 1,
        sort: 0,
        name: '',
        appoint_check: 0,
        no_delivery_check: 0,
      };
      this.appointList = [];
      this.noDeliveryList = [];
      this.addressModal = false;
      this.templateList = [
        {
          region: [
            {
              name: 'Mặc định toàn quốc',
              city_id: 0,
            },
          ],
          regionName: 'Mặc định toàn quốc',
          first: 0,
          price: 0,
          continue: 0,
          continue_price: 0,
        },
      ];
    },

    address() {
      this.addressModal = true;
    },
    enter(index) {
      this.activeCity = index;
    },
    leave() {
      this.activeCity = null;
    },
  },
  mounted() {},
};
</script>
<style lang="scss" scoped>
.ivu-table-wrapper {
  border-left: 1px solid #dcdee2;
  border-top: 1px solid #dcdee2;
}
.ivu-table-border th,
.ivu-table-border td {
  padding: 0 10px !important;
}
.addTop {
  margin-top: 15px;
}
.radio {
  padding: 5px 0;
}
.ivu-input-number {
  width: 100%;
}
.free_tips {
  font-size: 12px;
  color: #ccc;
}
</style>
