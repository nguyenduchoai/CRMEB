<!-- Sửa địa chỉ giao hàng -->
<template>
  <div class="order-address">
    <el-dialog
      title="Sửa địa chỉ giao hàng"
      :visible.sync="modals"
      width="50%"
      :close-on-click-modal="false"
      :close-on-press-escape="false"
      :show-close="false"
    >
      <el-form :model="form" :rules="rules" ref="form" label-width="120px" class="demo-ruleForm">
        <el-form-item label="Người nhận hàng" prop="consignee">
          <el-input v-model="form.real_name" autocomplete="off" />
        </el-form-item>
        <el-form-item label="Số điện thoại" prop="mobile">
          <el-input v-model="form.user_phone" autocomplete="off" />
        </el-form-item>
        <el-form-item label="Địa chỉ chi tiết" prop="address">
          <el-input v-model="form.user_address" autocomplete="off" />
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button v-db-click @click="modals = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="submitForm('form')">Xác nhận</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  props: {
    addressData: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      modals: false,
      form: {
        real_name: '',
        user_phone: '',
        user_address: '',
      },
    };
  },
  computed: {
    rules() {
      return {
        real_name: [{ required: true, message: 'Vui lòng nhập tên người nhận', trigger: 'blur' }],
        user_phone: [{ required: true, message: 'Vui lòng nhập số điện thoại', trigger: 'blur' }],
        user_address: [{ required: true, message: 'Vui lòng nhập địa chỉ chi tiết', trigger: 'blur' }],
      };
    },
  },
  watch: {
    addressData: {
      handler(newVal) {
        if (newVal) {
          this.form = newVal;
        }
      },
      deep: true,
    },
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$emit('submitSuccess', this.form);
        } else {
          return false;
        }
      });
    },
  },
};
</script>
