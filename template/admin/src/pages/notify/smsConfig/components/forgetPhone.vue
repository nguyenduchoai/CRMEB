<template>
  <el-row justify="center" align="middle">
    <el-col :span="20" style="margin-top: 70px" class="mb50">
      <steps :stepList="stepList" :isActive="current"></steps>
    </el-col>
    <el-col :span="24">
      <div class="index_from page-account-container">
        <el-form ref="formInline" :model="formInline" :rules="ruleInline" @submit.native.prevent>
          <template v-if="current === 0">
            <el-form-item prop="account" class="maxInpt">
              <el-input
                type="text"
                v-model="formInline.account"
                prefix="ios-contact-outline"
                placeholder="Vui lòng nhập số điện thoại hiện tại"
                size="large"
              />
            </el-form-item>
            <el-form-item prop="password" class="maxInpt">
              <el-input
                type="password"
                v-model="formInline.password"
                prefix="ios-lock-outline"
                placeholder="Vui lòng nhập mật khẩu"
              />
            </el-form-item>
          </template>
          <template v-if="current === 1">
            <el-form-item prop="phone" class="maxInpt">
              <el-input
                type="text"
                v-model="formInline.phone"
                prefix="ios-lock-outline"
                placeholder="Vui lòng nhập số điện thoại mới"
                size="large"
              />
            </el-form-item>
            <el-form-item prop="verify_code" class="maxInpt">
              <div class="code">
                <el-input
                  type="text"
                  v-model="formInline.verify_code"
                  prefix="ios-keypad-outline"
                  placeholder="Vui lòng nhập mã xác thực"
                  size="large"
                />
                <el-button :disabled="!this.canClick" v-db-click @click="cutDown" size="large">{{ cutNUm }}</el-button>
              </div>
            </el-form-item>
          </template>
          <template v-if="current === 2">
            <el-form-item prop="phone" class="maxInpt">
              <el-input
                type="text"
                v-model="formInline.phone"
                prefix="ios-contact-outline"
                placeholder="Vui lòng nhập số điện thoại"
              />
            </el-form-item>
            <el-form-item prop="password" class="maxInpt">
              <el-input
                type="password"
                v-model="formInline.password"
                prefix="ios-lock-outline"
                placeholder="Vui lòng nhập mật khẩu"
              />
            </el-form-item>
          </template>
          <el-form-item class="maxInpt">
            <el-button
              v-if="current === 0"
              type="primary"
              long
              size="large"
              v-db-click
              @click="handleSubmit1('formInline', current)"
              class="mb20"
              >Bước tiếp theo</el-button
            >
            <el-button
              v-if="current === 1"
              type="primary"
              long
              size="large"
              v-db-click
              @click="handleSubmit2('formInline', current)"
              class="mb20"
              >Gửi</el-button
            >
            <el-button
              v-if="current === 2"
              type="primary"
              long
              size="large"
              v-db-click
              @click="handleSubmit('formInline', current)"
              class="mb20"
              >Đăng nhập</el-button
            >
            <el-button long size="large" v-db-click @click="returns('formInline')" class="btn">Quay lại </el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import { captchaApi, configApi, serveModifyApi, updateHoneApi } from '@/api/setting';
import steps from '@/components/steps/index';

export default {
  name: 'forgetPhone',
  components: { steps },
  props: {
    isIndex: {
      type: Boolean,
      default: false,
    },
  },
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
    var validatePass = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('Vui lòng nhập mật khẩu'));
      } else {
        if (this.formInline.checkPass !== '') {
          this.$refs.formInline.validateField('checkPass');
        }
        callback();
      }
    };

    return {
      cutNUm: 'Lấy mã xác thực',
      canClick: true,
      current: 0,
      formInline: {
        account: '',
        phone: '',
        verify_code: '',
        password: '',
      },
      ruleInline: {
        phone: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        verify_code: [{ required: true, message: 'Vui lòng nhập mã xác thực', trigger: 'blur' }],
        password: [{ required: true, message: 'Vui lòng nhập mật khẩu', trigger: 'blur' }],
        account: [{ required: true, validator: validatePhone, trigger: 'blur' }],
      },
      stepList: ['Xác minh thông tin tài khoản', 'Đổi số điện thoại', 'Đăng nhập'],
    };
  },
  methods: {
    // Mã xác thực SMS
    cutDown() {
      if (this.formInline.phone) {
        if (!this.canClick) return;
        this.canClick = false;
        this.cutNUm = 60;
        let data = {
          phone: this.formInline.phone,
        };
        captchaApi(data)
          .then(async (res) => {
            this.$message.success(res.msg);
          })
          .catch((res) => {
            this.$message.error(res.msg);
          });
        let time = setInterval(() => {
          this.cutNUm--;
          if (this.cutNUm === 0) {
            this.cutNUm = 'Lấy mã xác thực';
            this.canClick = true;
            clearInterval(time);
          }
        }, 1000);
      } else {
        this.$message.warning('Vui lòng nhập số điện thoại!');
      }
    },
    handleSubmit1(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.handleSubmit(name, 1);
        } else {
          return false;
        }
      });
    },
    handleSubmit2(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          updateHoneApi(this.formInline)
            .then(async (res) => {
              this.$message.success(res.msg);
              this.current = 2;
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    //Đăng nhập
    handleSubmit(name, num) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          configApi({
            account: this.formInline.account,
            password: this.formInline.password,
          })
            .then(async (res) => {
              num === 1 ? this.$message.success('Số điện thoại cũ và mật khẩu chính xác') : this.$message.success('Đăng nhập thành công');
              num === 1 ? (this.current = 1) : this.$emit('on-Login');
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    returns() {
      this.current === 0 ? this.$emit('gobackPhone') : (this.current = 0);
    },
  },
};
</script>

<style scoped lang="scss">
.maxInpt {
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}
.code {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
