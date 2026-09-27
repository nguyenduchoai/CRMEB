<template>
  <el-dialog :visible.sync="modals" title="Ghi chú" class="order_box" width="470px" :show-close="true">
    <el-form ref="formValidate" :model="formValidate" :rules="ruleValidate" label-width="85px" @submit.native.prevent>
      <el-form-item label="Ghi chú:" prop="remark">
        <el-input
          v-model="formValidate.remark"
          :maxlength="200"
          show-word-limit
          type="textarea"
          placeholder="Vui lòng nhập thông tin ghi chú"
          style="width: 100%"
        />
      </el-form-item>
    </el-form>
    <div slot="footer">
      <el-button type="primary" v-db-click @click="putRemark('formValidate')">Gửi</el-button>
      <el-button v-db-click @click="cancel('formValidate')">Hủy</el-button>
    </div>
  </el-dialog>
</template>

<script>
export default {
  name: 'orderMark',
  data() {
    return {
      formValidate: {
        remark: '',
      },
      modals: false,
      ruleValidate: {
        remark: [
          { required: true, message: 'Vui lòng nhập thông tin ghi chú', trigger: 'blur' },
          // { type: 'string', min: 20, message: 'Introduce no less than 20 words', trigger: 'blur' }
        ],
      },
    };
  },
  props: {
    remark: {
      default: '',
      type: String,
    },
  },
  methods: {
    cancel(name) {
      this.modals = false;
      this.$refs[name].resetFields();
    },
    putRemark(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.$emit('submitFail', this.formValidate.remark);
        } else {
          this.$message.warning('Vui lòng điền thông tin ghi chú');
        }
      });
    },
  },
};
</script>

<style scoped></style>
