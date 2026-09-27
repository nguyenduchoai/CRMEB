<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="formValidate"
        :model="formValidate"
        :rules="ruleValidate"
        label-width="160px"
        label-position="right"
      >
        <el-form-item label="Tài khoản:" prop="">
          <el-input type="text" v-model="account" :disabled="true" class="input"></el-input>
        </el-form-item>
        <el-form-item label="Mật khẩu mới quản lý tệp:" prop="file_pwd">
          <el-input type="password" v-model="formValidate.file_pwd" class="input"></el-input>
        </el-form-item>
        <el-form-item label="Xác nhận mật khẩu mới quản lý tệp:" prop="conf_file_pwd">
          <el-input type="password" v-model="formValidate.conf_file_pwd" class="input"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-db-click @click="handleSubmit('formValidate')">Gửi</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { setFilePassword } from '@/api/user';
import { mapState } from 'vuex';
export default {
  name: 'setting_files',
  computed: {
    ...mapState('media', ['isMobile']),
    ...mapState('userLevel', ['categoryId']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  data() {
    return {
      account: '',
      formValidate: {
        file_pwd: '',
        conf_file_pwd: '',
      },
      ruleValidate: {
        file_pwd: [{ required: true, message: 'Vui lòng nhập mật khẩu mới quản lý tệp của bạn', trigger: 'blur' }],
        conf_file_pwd: [{ required: true, message: 'Vui lòng xác nhận mật khẩu mới quản lý tệp của bạn', trigger: 'blur' }],
      },
    };
  },
  mounted() {
    this.account = this.$store.state.userInfo.userInfo.account;
  },
  methods: {
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          setFilePassword(this.formValidate)
            .then((res) => {
              this.$message.success(res.msg);
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        }
      });
    },
  },
};
</script>

<style scoped>
.input {
  width: 400px;
}
</style>
