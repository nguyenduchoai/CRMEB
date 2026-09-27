<template>
  <div>
    <el-form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-width="90px">
      <el-form-item label="Giải thưởng:" prop="type">
        <el-radio-group v-model="formValidate.type">
          <el-radio :label="1">Không trúng thưởng</el-radio>
          <el-radio :label="5">Phiếu giảm giá</el-radio>
          <el-radio :label="2">Điểm thưởng</el-radio>
          <el-radio :label="6">Sản phẩm</el-radio>
          <el-radio :label="4">Lì xì</el-radio>
          <el-radio :label="3">Số dư</el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="Tặng phiếu giảm giá:" v-if="formValidate.type == 5">
        <div v-if="couponName.length" class="mb20">
          <el-tag closable v-for="(item, index) in couponName" :key="index" @close="handleClose(item)">{{
            item.title
          }}</el-tag>
        </div>
        <el-button type="primary" v-db-click @click="addCoupon" v-if="!couponName.length">Thêm phiếu giảm giá</el-button>
      </el-form-item>
      <el-form-item
        :label="[3, 4].includes(formValidate.type) ? 'Thông tin số tiền' : 'Số điểm thưởng'"
        prop="num"
        v-if="[2, 3, 4].includes(formValidate.type)"
      >
        <el-input-number
          :controls="false"
          v-model="formValidate.num"
          placeholder="Vui lòng nhập số tiền"
          :max="9999999999"
          :min="0.1"
          style="width: 300px"
        ></el-input-number>
        <div class="ml100 grey">
          {{
            formValidate.type == 3
              ? 'Sau khi người dùng nhận, số tiền sẽ tự động được cộng vào tài khoản số dư'
              : formValidate.type == 4
              ? 'Sau khi trúng thưởng, người dùng cần nhận thủ công trong danh sách quay thưởng, cần bật tính năng chuyển khoản của người bán (merchant transfer) trên WeChat Pay, số tiền không được nhỏ hơn 0.1đ'
              : ''
          }}
        </div>
      </el-form-item>
      <el-form-item v-if="formValidate.type == 6" label="Sản phẩm:" prop="goods_image">
        <template v-if="formValidate.goods_image">
          <div class="upload-list">
            <img :src="formValidate.goods_image" />
            <i class="el-icon-error" v-db-click @click="removeGoods()" style="font-size: 16px"></i>
          </div>
        </template>
        <div v-else class="upLoad pictrueTab acea-row row-center-wrapper" v-db-click @click="changeGoods">
          <i class="el-icon-picture-outline" style="font-size: 24px"></i>
        </div>
      </el-form-item>
      <el-form-item label="Tên giải thưởng:" prop="name">
        <el-input
          v-model="formValidate.name"
          :maxlength="10"
          placeholder="Vui lòng nhập tên giải thưởng"
          style="width: 300px"
        ></el-input>
      </el-form-item>
      <el-form-item label="Hình ảnh giải thưởng:" prop="image">
        <template v-if="formValidate.image">
          <div class="upload-list">
            <img :src="formValidate.image" />
            <i class="el-icon-error" v-db-click @click="remove()" style="font-size: 16px"></i>
          </div>
        </template>
        <div v-else class="upLoad pictrueTab acea-row row-center-wrapper">
          <i class="el-icon-picture-outline" style="font-size: 24px" v-db-click @click="modalPic = true"></i>
        </div>
        <!-- <div class="info">Chọn sản phẩm</div> -->
      </el-form-item>
      <el-form-item label="Số lượng giải thưởng:" prop="total">
        <el-input-number
          :controls="false"
          v-model="formValidate.total"
          placeholder="Vui lòng nhập số lượng giải thưởng"
          :max="9999999999"
          :min="0"
          :precision="0"
          style="width: 300px"
        ></el-input-number>
      </el-form-item>
      <el-form-item label="Tỷ lệ trúng giải (%):" prop="percent">
        <el-input-number
          :controls="false"
          v-model="formValidate.percent"
          placeholder="Vui lòng nhập tỷ lệ trúng giải"
          :max="100"
          :min="0"
          :precision="2"
          style="width: 300px"
        ></el-input-number>
      </el-form-item>
      <el-form-item label="Câu thông báo:" prop="prompt">
        <el-input
          v-model="formValidate.prompt"
          :maxlength="15"
          placeholder="Vui lòng nhập câu thông báo"
          style="width: 300px"
        ></el-input>
      </el-form-item>
      <!-- <el-form-item>
        <el-button type="primary" v-db-click @click="handleSubmit('formValidate')">Gửi</el-button>
      </el-form-item> -->
    </el-form>
    <!-- Tải lên ảnh-->
    <el-dialog :visible.sync="modalPic" :modal="false" width="1024px" title="Tải lên ảnh" :close-on-click-modal="false">
      <uploadPictures :isChoice="isChoice" @getPic="getPic" v-if="modalPic"></uploadPictures>
    </el-dialog>
    <el-dialog :visible.sync="modals" :modal="false" title="Danh sách sản phẩm" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" @getProductId="getProductId"></goods-list>
    </el-dialog>
    <coupon-list ref="couponTemplates" :luckDraw="true" @getCouponId="getCouponId"></coupon-list>
    <!--<coupon-list-->
    <!--ref="couponTemplates"-->
    <!--@nameId="nameId"-->
    <!--:updateIds="updateIds"-->
    <!--:updateName="updateName"-->
    <!--&gt;</coupon-list>-->
  </div>
</template>

<script>
import couponList from '@/components/couponList';
import uploadPictures from '@/components/uploadPictures';
import goodsList from '@/components/goodsList/index';
import freightTemplate from '@/components/freightTemplate';
export default {
  components: { uploadPictures, goodsList, freightTemplate, couponList },
  data() {
    return {
      modalPic: false,
      modals: false,
      isChoice: 'Chọn một',
      updateIds: [],
      updateName: [],
      goodsData: {
        pic: '',
        product_id: '',
        img: '',
        coverImg: '',
      },
      formValidate: {
        type: 5, //Loại 1: không trúng thưởng 2: điểm thưởng 3: số dư 4: lì xì 5: phiếu giảm giá 6: sản phẩm trong hệ thống
        name: '', //Tên chương trình
        num: 0, //Số lượng giải thưởng
        image: '', //Ảnh giải thưởng
        chance: 1, //Trọng số trúng thưởng
        product_id: 0, //id sản phẩm
        coupon_id: 0, //ID phiếu giảm giá
        total: 0, //Số lượng giải thưởng
        prompt: '', //Câu thông báo
        goods_image: '', //Ảnh sản phẩm tự dùng
        coupon_title: '', //Tên phiếu giảm giá
      },
      ruleValidate: {
        name: [
          {
            required: true,
            message: 'Tên sản phẩm',
            trigger: 'blur',
          },
        ],
        goods_image: [
          {
            required: true,
            message: 'Vui lòng thêm sản phẩm',
            trigger: 'blur',
          },
        ],
        num: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập số tiền',
            trigger: 'blur',
          },
        ],
        chance: [
          {
            required: true,
            type: 'number',
            message: 'Vui lòng nhập trọng số sản phẩm',
            trigger: 'blur',
          },
        ],
        image: [
          {
            required: true,
            message: 'Vui lòng chọn hình ảnh giải thưởng',
            trigger: 'blur',
          },
        ],
        prompt: [
          {
            required: true,
            message: 'Vui lòng nhập câu thông báo',
            trigger: 'blur',
          },
        ],
      },
      couponName: [],
    };
  },
  props: {
    editData: {
      type: Object,
      default: () => {},
    },
  },
  watch: {
    editData(data) {},
  },
  mounted() {
    let keys = Object.keys(this.editData);
    keys.forEach((item) => {
      this.formValidate[item] = this.editData[item];
      if (item === 'coupon_title' && this.editData[item]) {
        this.couponName.push({
          title: this.editData[item],
          id: this.editData.coupon_id,
        });
      }
    });
  },
  methods: {
    // Chọn sản phẩm
    changeGoods() {
      this.modals = true;
      this.$refs.goodslist.getList();
      this.$refs.goodslist.goodsCategory();
    },
    getCouponId(e) {
      this.formValidate.coupon_id = e.id;
      this.formValidate.coupon_title = e.coupon_title;
      let couponName = [];
      couponName.push(e);
      this.couponName = couponName;
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.$emit('addGoodsData', this.formValidate);
          this.$message.success('Thêm thành công');
        } else {
          this.$message.warning('Vui lòng hoàn thiện dữ liệu');
        }
      });
    },
    // Lấy thông tin một ảnh
    getPic(pc) {
      this.formValidate.image = pc.att_dir;
      this.modalPic = false;
    },
    // Bấm ảnh sản phẩm
    modalPicTap() {
      this.modalPic = true;
    },
    cancel() {
      this.modals = false;
    },
    // Sản phẩm đã chọn
    getProductId(productList) {
      // if (productList.length > 1) {
      //   this.$message.warning("Chỉ được thêm tối đa một sản phẩm");
      //   return;
      // }
      this.formValidate.product_id = productList.id;
      this.formValidate.goods_image = productList.image;
      this.modals = false;
      // productList.forEach((value) => {
      //   this.formValidate.product_id = value.product_id;
      //   this.formValidate.goods_image = value.image;
      // });
    },
    removeGoods() {
      this.formValidate.product_id = '';
      this.formValidate.goods_image = '';
    },
    remove() {
      this.formValidate.image = '';
    },
    // Thêm phiếu giảm giá
    addCoupon() {
      this.$refs.couponTemplates.isTemplate = true;
      this.$refs.couponTemplates.tableList();
    },
    handleClose(name) {
      this.couponName.splice(0, 1);
      this.formValidate.coupon_id = 0;
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1));
    },
  },
};
</script>

<style lang="scss" scoped>
.pictrueBox {
  display: inline-block;
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
.upload-list {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
  cursor: pointer;
  position: relative;
  .el-icon-error {
    position: absolute;
    right: -8px;
    top: -8px;
  }
}
.upload-list img {
  display: block;
  width: 100%;
  height: 100%;
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
.grey {
  color: #999;
}
</style>
