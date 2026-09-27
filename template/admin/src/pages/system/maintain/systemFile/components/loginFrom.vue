<template>
  <el-row>
    <el-col :span="24">
      <div class="index_from page-account-container">
        <div class="page-account-top">
          <span class="page-account-top-tit">Đăng nhập quản lý tệp</span>
        </div>
        <el-form ref="formInline" :model="formInline" :rules="ruleInline" @submit.native.prevent>
          <!-- <el-form-item prop="sms_account" class="maxInpt">
            <el-input type="text" v-model="formInline.account" prefix="ios-contact-outline" placeholder="Vui lòng nhập số điện thoại" />
          </el-form-item> -->
          <el-form-item prop="sms_token" class="maxInpt">
            <el-input
              type="password"
              v-model="formInline.password"
              prefix="ios-lock-outline"
              placeholder="Vui lòng nhập mật khẩu"
            />
          </el-form-item>
          <el-form-item class="maxInpt">
            <el-button type="primary" long size="large" v-db-click @click="handleSubmit('formInline')" class="btn"
              >Đăng nhập</el-button
            >
          </el-form-item>
        </el-form>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import { opendirLoginApi } from '@/api/system';
export default {
  name: 'file_login',
  data() {
    const validatePhone = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Vui lòng điền số điện thoại'));
      } else if (!/^1[3456789]\d{9}$/.test(value)) {
        callback(new Error('Số điện thoại không đúng định dạng!'));
      } else {
        callback();
      }
    };
    return {
      formInline: {
        // account: '',
        password: '',
      },
      ruleInline: {
        // account: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        password: [{ required: true, message: 'Vui lòng nhập mật khẩu', trigger: 'blur' }],
      },
    };
  },
  created() {
    var _this = this;
    document.onkeydown = function (e) {
      let key = window.event.keyCode;
      if (key === 13) {
        _this.handleSubmit('formInline');
      }
    };
  },
  methods: {
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          opendirLoginApi(this.formInline)
            .then(async (res) => {
              this.$message.success('Đăng nhập thành công!');
              this.$emit('on-Login', res.data);
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.maxInpt {
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}
.page-account-container {
  text-align: center;
  padding: 50px 0;
}
.page-account-top {
  margin-bottom: 20px;
}
.page-account-top-tit {
  font-size: 21px;
  color: var(--prev-color-primary);
}
.page-account-other {
  text-align: center;
  color: var(--prev-color-primary);
  font-size: 12px;
  span {
    cursor: pointer;
  }
}
</style>
