<template>
  <el-row>
    <el-col :span="24">
      <div class="index_from page-account-container">
        <div class="page-account-top">
          <span class="page-account-top-tit">Đăng ký tài khoản Yihaotong</span>
        </div>
        <el-form ref="formInline" :model="formInline" :rules="ruleInline" @submit.native.prevent>
          <!--<el-form-item prop="account">-->
          <!--<el-input type="text" v-model="formInline.account" prefix="ios-contact-outline"-->
          <!--placeholder="Vui lòng nhập tài khoản nền tảng SMS" />-->
          <!--</el-form-item>-->
          <el-form-item prop="phone" class="maxInpt">
            <el-input
              type="number"
              v-model="formInline.phone"
              prefix="ios-contact-outline"
              placeholder="Vui lòng nhập số điện thoại của bạn"
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
          <!--<el-form-item prop="password">-->
          <!--<el-input type="password" v-model="formInline.password" prefix="ios-lock-outline"-->
          <!--placeholder="Vui lòng xác nhận mật khẩu/token nền tảng SMS" />-->
          <!--</el-form-item>-->
          <!-- <el-form-item prop="url" class="maxInpt">
            <el-input type="text" v-model="formInline.url" prefix="ios-contact-outline" placeholder="Vui lòng nhập tên miền URL" />
          </el-form-item> -->
          <!--<el-form-item prop="sign">-->
          <!--<el-input type="text" v-model="formInline.sign" prefix="ios-contact-outline"-->
          <!--placeholder="Vui lòng nhập chữ ký SMS, ví dụ: CRMEB" />-->
          <!--</el-form-item>-->
          <el-form-item prop="verify_code" class="maxInpt">
            <div class="code">
              <el-input
                type="text"
                v-model="formInline.verify_code"
                prefix="ios-keypad-outline"
                placeholder="Vui lòng nhập mã xác thực"
              />
              <el-button :disabled="!canClick" v-db-click @click="cutDown">{{ cutNUm }}</el-button>
            </div>
          </el-form-item>
          <el-form-item class="maxInpt">
            <el-button type="primary" long size="large" v-db-click @click="handleSubmit('formInline')" class="btn"
              >Đăng ký</el-button
            >
          </el-form-item>
        </el-form>
        <div class="page-account-other">
          <span v-db-click @click="changelogo">Đăng nhập ngay</span>
        </div>
      </div>
    </el-col>
  </el-row>
</template>

<script>
import { captchaApi, registerApi } from '@/api/setting';
export default {
  name: 'register',
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
      cutNUm: 'Lấy mã xác thực',
      canClick: true,
      formInline: {
        url: '',
        password: '',
        verify_code: '',
        phone: '',
      },
      ruleInline: {
        account: [{ required: true, message: 'Vui lòng nhập tài khoản nền tảng SMS', trigger: 'blur' }],
        password: [{ required: true, message: 'Vui lòng nhập mật khẩu', trigger: 'blur' }],
        // url: [{ required: true, message: 'Vui lòng nhập tên miền URL', trigger: 'blur' }],
        phone: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        sign: [{ required: true, message: 'Vui lòng nhập chữ ký SMS', trigger: 'blur' }],
        verify_code: [{ required: true, message: 'Vui lòng nhập mã xác thực', trigger: 'blur' }],
      },
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
    // Đăng ký
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          registerApi(this.formInline)
            .then(async (res) => {
              this.$message.success(res.msg);
              setTimeout(() => {
                this.changelogo();
              }, 1000);
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    // Đăng nhập ngay
    changelogo() {
      this.$emit('on-change');
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
.code {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
