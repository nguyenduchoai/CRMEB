<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.params.id ? 'Sửa phiếu giảm giá' : 'Thêm phiếu giảm giá'"
      :backUrl="$routeProStr + '/marketing/store_coupon_issue/index'"
    ></pages-header>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-form :model="formData" label-width="160px">
        <el-form-item label="Tên phiếu giảm giá:">
          <el-input
            v-model="formData.coupon_title"
            maxlength="18"
            show-word-limit
            placeholder="Vui lòng nhập tên phiếu giảm giá"
            class="content_width"
          ></el-input>
        </el-form-item>
        <el-form-item label="Mệnh giá phiếu giảm giá:">
          <el-input-number
            :controls="false"
            :min="1"
            :max="9999999999"
            v-model="formData.coupon_price"
            class="content_width input-number-unit-class"
            class-unit="đ"
            :disabled="isEdit"
          ></el-input-number>
        </el-form-item>
        <el-form-item label="Loại người dùng:">
          <el-radio-group v-model="formData.user_type" :disabled="isEdit" @input="changeUserType">
            <el-radio :label="1">Người dùng thường</el-radio>
            <el-radio :label="2">Thành viên trả phí</el-radio>
          </el-radio-group>
          <div class="tip">
            Người dùng thường: phiếu giảm giá mà tất cả người dùng đều có thể nhận;<br />
            Thành viên trả phí: phiếu giảm giá chỉ thành viên trả phí mới được nhận;
          </div>
        </el-form-item>
        <el-form-item label="Hình thức phát:" v-show="formData.user_type == 1">
          <el-radio-group v-model="formData.receive_type" :disabled="isEdit">
            <el-radio :label="1">Người dùng tự nhận</el-radio>
            <el-radio :label="3">Hệ thống tặng</el-radio>
          </el-radio-group>
          <div class="tip">
            Người dùng tự nhận: người dùng cần nhận phiếu giảm giá thủ công;<br />
            Hệ thống tặng: 1. Phát cho người dùng chỉ định từ trang quản trị. 2. Gắn vào sản phẩm, người dùng mua sản phẩm đó sẽ nhận được. 3. Thiết lập tại trang quà tặng người mới, tặng phiếu giảm giá khi người dùng mới đăng ký;
          </div>
        </el-form-item>
        <el-form-item label="Loại phiếu giảm giá:">
          <el-radio-group v-model="formData.type" :disabled="isEdit">
            <el-radio :label="0">Phiếu toàn cửa hàng</el-radio>
            <el-radio :label="1">Phiếu theo danh mục</el-radio>
            <el-radio :label="2">Phiếu theo sản phẩm</el-radio>
            <!--                        <el-radio :label="3">Phiếu giảm giá thành viên</el-radio>-->
          </el-radio-group>
        </el-form-item>
        <el-form-item v-show="formData.type === 2">
          <template>
            <div class="acea-row">
              <div v-for="(item, index) in productList" :key="index" class="pictrue">
                <img v-lazy="item.image" />
                <i  v-if="formData.type == 2 && !formData.id" class="el-icon-error btndel" v-db-click @click="remove(item.product_id)"></i>
              </div>
              <div v-if="formData.type == 2 && !formData.id" class="upLoad acea-row row-center-wrapper" v-db-click @click="modals = true">
                <i class="el-icon-goods" style="font-size: 24px"></i>
              </div>
            </div>
          </template>
        </el-form-item>
        <el-form-item v-show="formData.type === 1">
          <el-cascader
            v-model="formData.category_id"
            size="small"
            :options="categoryList"
            :props="{ multiple: true, emitPath: false, checkStrictly: true }"
            clearable
            style="width: 320px"
            :disabled="isEdit"
          ></el-cascader>
          <div class="info">Chọn danh mục của sản phẩm</div>
        </el-form-item>
        <el-form-item label="Điều kiện sử dụng:">
          <el-radio-group v-model="isMinPrice" :disabled="isEdit">
            <el-radio :label="0">Không điều kiện</el-radio>
            <el-radio :label="1">Có điều kiện</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-if="isMinPrice">
          <el-input-number
            :controls="false"
            :min="0"
            :max="9999999999"
            v-model="formData.use_min_price"
            class="content_width input-number-unit-class"
            :disabled="isEdit"
            class-unit="đ"
          ></el-input-number>
          <div class="info">Điền giá trị đơn hàng tối thiểu để dùng phiếu giảm giá</div>
        </el-form-item>
        <el-form-item label="Thời hạn hiệu lực:">
          <el-radio-group v-model="isCouponTime" :disabled="isEdit">
            <el-radio :label="1">Số ngày</el-radio>
            <el-radio :label="0">Khoảng thời gian</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-show="isCouponTime" label="">
          <el-input-number
            :controls="false"
            :min="0"
            v-model="formData.coupon_time"
            :precision="0"
            class="content_width input-number-unit-class"
            :disabled="isEdit"
            class-unit="ngày"
          ></el-input-number>
          <div class="info">Số ngày có hiệu lực kể từ khi nhận</div>
        </el-form-item>
        <el-form-item v-show="!isCouponTime" label="">
          <el-date-picker
            v-model="datetime1"
            :disabled="isEdit"
            clearable
            :editable="false"
            type="datetimerange"
            value-format="yyyy-MM-dd HH:mm:ss"
            style="width: 380px"
            range-separator="-"
            start-placeholder="Ngày bắt đầu"
            end-placeholder="Ngày kết thúc"
            @change="dateChange"
          ></el-date-picker>
        </el-form-item>

        <el-form-item label="Thời gian nhận:" v-if="formData.receive_type != 2 && formData.receive_type != 3">
          <el-radio-group v-model="isReceiveTime" :disabled="isEdit">
            <el-radio :label="1">Giới hạn thời gian</el-radio>
            <el-radio :label="0">Không giới hạn thời gian</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-show="isReceiveTime" label="">
          <el-date-picker
            clearable
            v-model="datetime2"
            type="datetimerange"
            value-format="yyyy/MM/dd HH:mm:ss"
            style="width: 380px"
            range-separator="-"
            start-placeholder="Ngày bắt đầu"
            end-placeholder="Ngày kết thúc"
            @change="timeChange"
            :disabled="isEdit"
          ></el-date-picker>
        </el-form-item>
        <el-form-item label="Số lượng phiếu giảm giá phát hành:" v-show="formData.receive_type == 1">
          <el-radio-group v-model="formData.is_permanent" :disabled="isEdit">
            <el-radio :label="0">Giới hạn số lượng</el-radio>
            <el-radio :label="1">Không giới hạn</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-show="!formData.is_permanent" label="">
          <el-input-number
            :controls="false"
            :min="isEdit ? formData.total_count : 1"
            :max="9999999999"
            v-model="formData.total_count"
            :precision="0"
            class="content_width input-number-unit-class"
            class-unit="phiếu"
          ></el-input-number>
          <div class="info">Điền số lượng phiếu giảm giá phát hành</div>
        </el-form-item>
        <el-form-item label="Số lượng mỗi người được nhận:" v-if="formData.receive_type != 2 && formData.receive_type != 3">
          <el-input-number
            :controls="false"
            :min="isEdit ? formData.receive_limit : 1"
            :max="9999999999"
            v-model="formData.receive_limit"
            :precision="0"
            class="content_width input-number-unit-class"
            class-unit="phiếu"
          ></el-input-number>
          <div class="info">Điền số phiếu mỗi người dùng có thể nhận</div>
        </el-form-item>
        <el-form-item label="Trạng thái:">
          <el-radio-group v-model="formData.status">
            <el-radio :label="1">Bật</el-radio>
            <el-radio :label="0">Tắt</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-db-click @click="save" :disabled="disabled">{{
            isEdit ? 'Lưu ngay' : 'Tạo ngay'
          }}</el-button>
        </el-form-item>
      </el-form>
    </el-card>
    <el-dialog :visible.sync="modals" title="Danh sách sản phẩm" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" v-if="modals" :ischeckbox="true" @getProductId="getProductId"></goods-list>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import { couponSaveApi, couponDetailApi } from '@/api/marketing';
import { cascaderListApi } from '@/api/product';
export default {
  name: 'storeCouponCreate',
  components: {
    goodsList,
  },
  data() {
    return {
      disabled: false,
      formData: {
        coupon_title: '',
        coupon_price: 0,
        type: 0,
        use_min_price: 0,
        coupon_time: 0,
        start_use_time: 0,
        end_use_time: 0,
        start_time: 0,
        end_time: 0,
        user_type: 1,
        receive_type: 1,
        is_permanent: 1,
        total_count: 1,
        sort: 0,
        status: 1,
        product_id: '',
        category_id: 0,
        receive_limit: 1,
      },
      categoryList: [],
      productList: [],
      isMinPrice: 0,
      isCouponTime: 1,
      isReceiveTime: 0,
      modals: false,
      datetime1: ['2023-10-18 00:00:00', '2023-11-22 00:00:00'],
      datetime2: [],
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    isEdit() {
      return !!this.$route.params.edit;
    },
  },
  created() {
    this.getCategoryList();
    if (this.$route.params.id) {
      this.formData.id = (this.isEdit && Number(this.$route.params.id)) || 0;
      this.getCouponDetail();
    }
  },
  methods: {
    changeUserType() {
      if (this.formData.user_type == 2) {
        this.formData.receive_type = 1;
      }
    },
    // Danh mục
    getCategoryList() {
      cascaderListApi(1).then(async (res) => {
        this.categoryList = res.data;
      });
    },
    // Phiếu giảm giá
    getCouponDetail() {
      couponDetailApi(this.$route.params.id)
        .then((res) => {
          let data = res.data;
          this.formData.coupon_title = data.coupon_title;
          this.formData.type = data.type;
          this.formData.category_id = data.category_id;
          this.formData.coupon_price = parseFloat(data.coupon_price);
          this.formData.use_min_price = parseFloat(data.use_min_price);
          if (this.formData.use_min_price) {
            this.isMinPrice = 1;
          }
          this.formData.coupon_time = data.coupon_time;
          this.formData.receive_type = data.receive_type;
          this.formData.user_type = data.user_type;
          this.formData.is_permanent = data.is_permanent;
          this.formData.status = data.status;
          this.formData.product_id = data.product_id;
          this.formData.start_time = data.start_time;
          this.formData.end_time = data.end_time;
          this.formData.total_count = data.total_count;
          this.formData.sort = data.sort;
          this.formData.receive_limit = data.receive_limit;
          if ('productInfo' in data) {
            this.productList = data.productInfo;
          }
          if (!data.coupon_time) {
            this.isCouponTime = 0;
            this.datetime1 = [this.makeDate(data.start_use_time * 1000), this.makeDate(data.end_use_time * 1000)];
            this.formData.start_use_time = this.makeDate(data.start_use_time * 1000);
            this.formData.end_use_time = this.makeDate(data.end_use_time * 1000);
          }
          if (data.start_time) {
            this.isReceiveTime = 1;
            this.datetime2 = [data.start_time * 1000, data.end_time * 1000];
            this.formData.start_time = this.makeDate(data.start_time * 1000);
            this.formData.end_time = this.makeDate(data.end_time * 1000);
          }
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    makeDate(data) {
      let date = new Date(data);
      let YY = date.getFullYear() + '-';
      let MM = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
      let DD = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
      let hh = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
      let mm = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
      let ss = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
      return YY + MM + DD + ' ' + hh + mm + ss;
    },
    // Tạo
    save() {
      if (!this.formData.coupon_title) {
        return this.$message.error('Vui lòng nhập tên phiếu giảm giá');
      }
      if (this.formData.type === 2) {
        if (!this.formData.product_id) {
          return this.$message.error('Vui lòng chọn sản phẩm');
        }
      }
      if (this.formData.type === 1) {
        if (!this.formData.category_id) {
          return this.$message.error('Vui lòng chọn danh mục');
        }
      }
      if (this.formData.coupon_price <= 0) {
        return this.$message.error('Mệnh giá phiếu giảm giá không được nhỏ hơn 0');
      }
      if (!this.isMinPrice) {
        this.formData.use_min_price = 0;
      } else {
        if (this.formData.use_min_price < 1) {
          return this.$message.error('Giá trị đơn tối thiểu của phiếu giảm giá không được nhỏ hơn 0');
        }
      }
      if (this.isCouponTime) {
        this.formData.start_use_time = 0;
        this.formData.end_use_time = 0;
        if (this.formData.coupon_time < 1) {
          return this.$message.error('Thời hạn sử dụng không được ít hơn 1 ngày');
        }
      } else {
        this.formData.coupon_time = 0;
        if (!this.formData.start_use_time) {
          return this.$message.error('Vui lòng chọn thời hạn sử dụng');
        }
      }
      if (this.isReceiveTime) {
        if (!this.formData.start_time) {
          return this.$message.error('Vui lòng chọn thời gian nhận');
        }
      } else {
        this.formData.start_time = 0;
        this.formData.end_time = 0;
      }
      // if (this.formData.receive_type == 2 || this.formData.receive_type == 3) {
      //   this.formData.is_permanent = 1;
      // }
      if (this.formData.is_permanent) {
        this.formData.total_count = 0;
      } else {
        if (this.formData.total_count < 1) {
          return this.$message.error('Số lượng phát hành không được nhỏ hơn 1');
        }
      }
      if (this.formData.receive_limit < 1) {
        return this.$message.error('Số lượng mỗi người dùng được nhận không được nhỏ hơn 1');
      }
      if (this.formData.type == 0) {
        this.formData.product_id = '';
        this.formData.category_id = '';
        this.productList = [];
      } else if (this.formData.type == 1) {
        this.formData.product_id = '';
        this.productList = [];
      } else if (this.formData.type == 2) {
        this.formData.category_id = '';
      }
      if (this.disabled) return;
      this.disabled = true;
      couponSaveApi(this.formData)
        .then((res) => {
          this.$message.success(res.msg);
          setTimeout(() => {
            this.disabled = false;
            this.$router.push({
              path: this.$routeProStr + '/marketing/store_coupon_issue/index',
            });
          }, 1000);
        })
        .catch((err) => {
          this.disabled = false;
          this.$message.error(err.msg);
        });
    },
    // Thời hạn sử dụng--khoảng thời gian
    dateChange(time) {
      this.formData.start_use_time = time[0];
      this.formData.end_use_time = time[1];
    },
    // Giới hạn thời gian
    timeChange(time) {
      this.formData.start_time = time[0];
      this.formData.end_time = time[1];
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.product_id) && res.set(arr.product_id, 1));
    },
    // Sản phẩm đã chọn
    getProductId(productList) {
      this.modals = false;
      this.productList = this.unique(this.productList.concat(productList));
      this.formData.product_id = '';
      this.productList.forEach((value) => {
        if (this.formData.product_id) {
          this.formData.product_id += `,${value.product_id}`;
        } else {
          this.formData.product_id += `${value.product_id}`;
        }
      });
    },
    cancel() {
      this.modals = false;
    },
    // Xóa sản phẩm
    remove(productId) {
      for (let index = 0; index < this.productList.length; index++) {
        if (this.productList[index].product_id == productId) {
          this.productList.splice(index, 1);
        }
      }
      this.formData.product_id = '';
      this.productList.forEach((value) => {
        if (this.formData.product_id) {
          this.formData.product_id += `,${value.product_id}`;
        } else {
          this.formData.product_id += `${value.product_id}`;
        }
      });
    },
  },
};
</script>

<style scoped lang="scss">
.content_width {
  width: 414px;
}

.info {
  color: #888;
  font-size: 12px;
}

.pictrue {
  width: 60px;
  height: 60px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 15px;
  display: inline-block;
  position: relative;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }

  .btndel {
    position: absolute;
    z-index: 1;
    width: 20px !important;
    height: 20px !important;
    left: 46px;
    top: -4px;
  }
}

.upLoad {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
  cursor: pointer;
}

.ivu-icon-ios-close-circle {
  position: absolute;
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
}

.tip {
  color: #888;
  font-size: 12px;
  line-height: 16px;
}
</style>
