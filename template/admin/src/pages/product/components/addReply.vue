<template>
  <el-dialog
    :visible.sync="visibleModal"
    title="Thêm đánh giá tự tạo"
    width="720px"
    :close-on-click-modal="false"
    @close="onCancel"
  >
    <el-form :model="formData" label-width="100px" label-position="right">
      <el-form-item label="Sản phẩm:">
        <div class="upload-box" v-db-click @click="callGoods">
          <img v-if="goods.id" :src="goods.image" class="image" />
          <i v-else class="el-icon-goods"></i>
        </div>
      </el-form-item>
      <el-form-item v-if="goods.id" label="Phân loại sản phẩm:">
        <div class="upload-box" v-db-click @click="callAttr">
          <img v-if="attr.pic" :src="attr.pic" class="image" />
          <i v-else class="el-icon-plus" />
        </div>
        <div>{{ attr.suk }}</div>
      </el-form-item>
      <el-form-item label="Ảnh đại diện người dùng:">
        <div class="upload-box" v-db-click @click="callPicture('Chọn một')">
          <img v-if="avatar.att_dir" :src="avatar.att_dir" class="image" />
          <i v-if="avatar.att_dir" class="el-icon-error btn" v-db-click @click.stop="removeUser"></i>
          <i v-else class="el-icon-user" />
        </div>
      </el-form-item>
      <el-form-item label="Tên người dùng:">
        <el-input
          v-model="formData.nickname"
          placeholder="Vui lòng nhập tên người dùng"
          class="w100"
          maxlength="20"
          show-word-limit
        ></el-input>
      </el-form-item>
      <el-form-item label="Nội dung đánh giá:">
        <el-input
          v-model="formData.comment"
          type="textarea"
          placeholder="Vui lòng nhập nội dung đánh giá"
          class="w100"
          maxlength="200"
          show-word-limit
        ></el-input>
      </el-form-item>
      <el-form-item label="Điểm sản phẩm:">
        <el-rate v-model="product_score" />
      </el-form-item>
      <el-form-item label="Điểm dịch vụ:">
        <el-rate v-model="service_score" />
      </el-form-item>
      <el-form-item label="Hình ảnh đánh giá:">
        <div class="df-aic">
          <div v-for="item in picture" :key="item.att_id" class="upload-box">
            <img :src="item.att_dir" class="image" />
            <i class="el-icon-error btn" v-db-click @click.stop="removePicture(item.att_id)"></i>
          </div>
          <div v-if="picture.length < 8" class="upload-box" v-db-click @click="callPicture('Chọn nhiều')">
            <i class="el-icon-picture-outline"></i>
          </div>
        </div>
      </el-form-item>
      <el-form-item label="Thời gian đánh giá:">
        <el-date-picker
          clearable
          v-model="add_time"
          type="datetime"
          range-separator="-"
          start-placeholder="Ngày bắt đầu"
          end-placeholder="Ngày kết thúc"
          @change="onChange"
          style="width: 414px"
        />
      </el-form-item>
    </el-form>
    <span slot="footer" class="dialog-footer">
      <el-button v-db-click @click="onCancel">Hủy</el-button>
      <el-button type="primary" v-db-click @click="onOk">Xác nhận</el-button>
    </span>
  </el-dialog>
</template>

<script>
import { saveFictitiousReply } from '@/api/product';
export default {
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
    goods: {
      type: Object,
      default() {
        return {};
      },
    },
    attr: {
      type: Object,
      default() {
        return {};
      },
    },
    avatar: {
      type: Object,
      default() {
        return {};
      },
    },
    picture: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      formData: {
        avatar: '',
        nickname: '',
        comment: '',
      },
      product_score: 5,
      service_score: 5,
      pics: [],
      add_time: '',
      visibleModal: false,
    };
  },
  watch: {
    picture(value) {
      this.pics = value.map((item) => {
        return item.att_dir;
      });
    },
    visible(value) {
      this.visibleModal = value;
      if (!value) {
        this.formData.nickname = '';
        this.formData.comment = '';
        this.product_score = 5;
        this.service_score = 5;
        this.add_time = '';
      }
    },
  },
  methods: {
    removeUser() {
      this.avatar.att_dir = '';
    },
    removePicture(att_id) {
      this.$emit('removePicture', att_id);
    },
    onChange(date) {
      this.add_time = date;
    },
    callGoods() {
      this.$emit('callGoods');
    },
    callAttr() {
      this.$emit('callAttr');
    },
    callPicture(type) {
      this.$emit('callPicture', type);
    },
    onOk() {
      if (!this.goods.id) {
        return this.$message.error('Vui lòng chọn sản phẩm');
      }
      if (!this.attr.pic) {
        return this.$message.error('Vui lòng chọn phân loại sản phẩm');
      }
      if (!this.avatar.att_dir) {
        return this.$message.error('Vui lòng chọn ảnh đại diện');
      }
      if (!this.formData.nickname) {
        return this.$message.error('Vui lòng điền biệt danh');
      }
      if (!this.formData.comment) {
        return this.$message.error('Vui lòng điền nội dung đánh giá');
      }
      if (!this.product_score) {
        return this.$message.error('Điểm sản phẩm phải là số nguyên từ 1-5');
      }
      if (!this.service_score) {
        return this.$message.error('Điểm dịch vụ phải là số nguyên từ 1-5');
      }
      let data = {
        image: {
          image: this.goods.image,
          product_id: this.goods.id,
        },
        suk: this.attr.suk,
        avatar: this.avatar.att_dir,
        nickname: this.formData.nickname,
        comment: this.formData.comment,
        product_score: this.product_score,
        service_score: this.service_score,
        pics: this.pics,
        add_time: this.add_time,
      };
      saveFictitiousReply(data)
        .then((res) => {
          this.$message.success(res.msg);
          this.$emit('close', false);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    onCancel() {
      this.$emit('close', false);
    },
  },
};
</script>

<style lang="scss" scoped>
.upload-box {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 58px;
  height: 58px;
  border: 1px dashed #c0ccda;
  border-radius: 4px;

  vertical-align: middle;
  text-align: center;
  line-height: 58px;
  cursor: pointer;

  + .upload-box {
    margin-left: 10px;
  }
  .ivu-icon {
    vertical-align: middle;
    font-size: 20px;
  }
  .image {
    width: 100%;
    height: 100%;
    border-radius: 3px;
  }
  .btn {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 14px;
    transform: translate(50%, -50%);
  }
}
.df-aic {
  display: flex;
  flex-wrap: wrap;
}
.w414 {
  width: 414px;
}
.el-rate {
  line-height: 2;
}
</style>
