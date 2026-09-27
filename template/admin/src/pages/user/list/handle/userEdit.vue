<template>
  <div class="pb-50">
    <el-form ref="formItem" :rules="ruleValidate" :model="formItem" label-width="100px" @submit.native.prevent>
      <el-form-item label="ID người dùng:" v-if="formItem.uid">
        <el-input
          class="form-sty"
          disabled
          v-model="formItem.uid"
          placeholder="Vui lòng nhập mã số"
          style="width: 80%"
        ></el-input>
      </el-form-item>
      <el-form-item label="Họ tên:" prop="real_name">
        <el-input
          class="form-sty"
          v-model.trim="formItem.real_name"
          placeholder="Vui lòng nhập họ tên"
          style="width: 80%"
        ></el-input>
      </el-form-item>
      <el-form-item label="Số điện thoại:" prop="phone">
        <el-input class="form-sty" v-model="formItem.phone" placeholder="Vui lòng nhập số điện thoại" style="width: 80%"></el-input>
      </el-form-item>
      <el-form-item label="Ngày sinh:">
        <el-date-picker
          clearable
          class="form-sty"
          type="date"
          v-model="formItem.birthday"
          placeholder="Vui lòng chọn ngày sinh"
          style="width: 80%"
          format="yyyy-MM-dd"
          value-format="yyyy-MM-dd"
        ></el-date-picker>
      </el-form-item>
      <el-form-item label="Số CCCD/CMND:">
        <el-input
          class="form-sty"
          v-model.trim="formItem.card_id"
          placeholder="Vui lòng nhập số CCCD/CMND"
          style="width: 80%"
        ></el-input>
      </el-form-item>
      <el-form-item label="Địa chỉ người dùng:">
        <el-input class="form-sty" v-model="formItem.addres" placeholder="Vui lòng nhập địa chỉ người dùng" style="width: 80%"></el-input>
      </el-form-item>
      <el-form-item label="Ghi chú người dùng:">
        <el-input class="form-sty" v-model="formItem.mark" placeholder="Vui lòng nhập ghi chú người dùng" style="width: 80%"></el-input>
      </el-form-item>
      <el-form-item label="Mật khẩu đăng nhập:" prop="pwd">
        <el-input
          class="form-sty"
          type="password"
          v-model="formItem.pwd"
          placeholder="Vui lòng nhập mật khẩu đăng nhập (khi sửa người dùng có thể để trống, nếu để trống sẽ không đổi mật khẩu cũ)"
          style="width: 80%"
        ></el-input>
      </el-form-item>
      <el-form-item label="Xác nhận mật khẩu:" prop="true_pwd">
        <el-input
          class="form-sty"
          type="password"
          v-model="formItem.true_pwd"
          placeholder="Vui lòng nhập mật khẩu xác nhận (khi sửa người dùng có thể để trống, nếu để trống sẽ không đổi mật khẩu cũ)"
          style="width: 80%"
        ></el-input>
      </el-form-item>

      <el-form-item label="Hạng người dùng:">
        <el-select v-model="formItem.level" class="form-sty" clearable>
          <el-option
            v-for="(item, index) in infoData.levelInfo"
            :key="index"
            :value="item.id"
            :label="item.name"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Nhóm người dùng:">
        <el-select v-model="formItem.group_id" class="form-sty" clearable>
          <el-option
            v-for="(item, index) in infoData.groupInfo"
            :key="index"
            :value="item.id"
            :label="item.group_name"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Nhãn người dùng:">
        <!-- <el-select v-model="formItem.label_id">
          <el-option
            v-for="(item, index) in infoData.labelInfo"
            :key="index"
            :value="item.value"
            >{{ item.label }}</el-option
          >
        </el-select> -->
        <div style="display: flex">
          <div class="labelInput acea-row row-between-wrapper" v-db-click @click="openLabel">
            <div style="width: 90%">
              <div v-if="dataLabel.length">
                <el-tag
                  closable
                  v-for="(item, index) in dataLabel"
                  :key="index"
                  @close="closeLabel(item)"
                  class="mr10"
                  >{{ item.label_name }}</el-tag
                >
              </div>
              <span class="span" v-else>Chọn nhãn gắn cho người dùng</span>
            </div>
            <div class="ivu-icon ivu-icon-ios-arrow-down"></div>
          </div>
          <span class="addfont" v-db-click @click="addLabel">Thêm nhãn</span>
        </div>
      </el-form-item>
      <el-form-item label="Vô hiệu hóa tiếp thị liên kết:">
        <el-radio-group v-model="formItem.spread_open" class="form-sty">
          <el-radio :label="0">Có</el-radio>
          <el-radio :label="1">Không</el-radio>
        </el-radio-group>
        <div class="tip">Sau khi vô hiệu hóa tư cách cộng tác viên của người dùng, người dùng này sẽ không có quyền tiếp thị liên kết trong bất kỳ chế độ tiếp thị liên kết nào</div>
      </el-form-item>
      <el-form-item label="Quyền tiếp thị liên kết:" v-if="formItem.spread_open == 1">
        <el-radio-group v-model="formItem.is_promoter" class="form-sty">
          <el-radio :label="1">Bật</el-radio>
          <el-radio :label="0">Tắt</el-radio>
        </el-radio-group>
        <div class="tip">Bật hoặc tắt thủ công quyền tiếp thị liên kết của người dùng</div>
      </el-form-item>
      <el-form-item label="Trạng thái người dùng:">
        <el-radio-group v-model="formItem.status" class="form-sty">
          <el-radio :label="1">Bật</el-radio>
          <el-radio :label="0">Khóa</el-radio>
        </el-radio-group>
      </el-form-item>
    </el-form>

    <el-dialog
      :visible.sync="labelShow"
      scrollable
      title="Vui lòng chọn nhãn người dùng"
      :modal="false"
      :show-close="true"
      width="540px"
    >
      <userLabel
        v-if="labelShow"
        :only_get="true"
        :uid="formItem.uid"
        @close="labelClose"
        @activeData="activeData"
      ></userLabel>
    </el-dialog>
  </div>
</template>

<script>
import userLabel from '@/components/userLabel';

import { userLabelAddApi } from '@/api/user';
export default {
  name: 'userEdit',
  components: { userLabel },
  props: {
    // modals: {
    //   default: false,
    //   type: Boolean,
    // },
    userData: {
      type: Object,
      default: () => {},
    },
  },
  watch: {},
  data() {
    return {
      modals: false,
      labelShow: false,
      formItem: {
        uid: 0,
        real_name: '',
        phone: '',
        birthday: '',
        card_id: '',
        addres: '',
        mark: '',
        pwd: '',
        true_pwd: '',
        level: '',
        group_id: '',
        label_id: [],
        spread_open: 1,
        is_promoter: 0,
        status: 1,
      },
      groupInfo: [],
      labelInfo: [],
      levelInfo: [],
      infoData: {
        groupInfo: [],
        labelInfo: [],
        levelInfo: [],
      },
      ruleValidate: {
        real_name: [{ required: true, message: ' ', trigger: 'blur' }],
        phone: [{ required: true, message: ' ', trigger: 'blur' }],
        pwd: [{ required: true, message: ' ', trigger: 'blur' }],
        true_pwd: [{ required: true, message: ' ', trigger: 'blur' }],
      },
      dataLabel: [],
    };
  },
  mounted() {
    this.$set(this.infoData, 'groupInfo', this.userData.groupInfo);
    this.$set(this.infoData, 'levelInfo', this.userData.levelInfo);
    this.$set(this.infoData, 'labelInfo', this.userData.labelInfo);
    let arr = Object.keys(this.formItem);
    if (this.userData.userInfo) {
      arr.map((i) => {
        this.formItem[i] = this.userData.userInfo[i];
      });
      if (!this.formItem.birthday) this.formItem.birthday = '';
      if (this.formItem.label_id.length) {
        this.dataLabel = this.formItem.label_id;
      }
    } else {
      this.reset();
    }

    // this.formItem = this.userData.userInfo;
  },
  methods: {
    addLabel() {
      this.$modalForm(userLabelAddApi(0)).then(() => {});
    },
    changeModal(status) {
      if (!status) {
        this.cancel();
        this.reset();
      }
    },
    openLabel(row) {
      this.labelShow = true;
      this.$refs.userLabel.userLabel(JSON.parse(JSON.stringify(this.infoData.labelInfo)));
    },
    cancel() {},
    activeData(dataLabel) {
      this.labelShow = false;
      this.dataLabel = dataLabel;
    },
    // Đóng popup nhãn
    labelClose() {
      this.labelShow = false;
    },
    closeLabel(label) {
      let index = this.dataLabel.indexOf(this.dataLabel.filter((d) => d.id == label.id)[0]);
      this.dataLabel.splice(index, 1);
    },
    reset() {
      this.formItem = {
        uid: '',
        real_name: '',
        phone: '',
        birthday: '',
        card_id: '',
        addres: '',
        mark: '',
        pwd: '',
        true_pwd: '',
        level: '',
        group_id: '',
        label_id: [],
        spread_open: 1,
        is_promoter: 0,
        status: 1,
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.labelInput {
  border: 1px solid #dcdee2;
  width: 400px;
  padding: 0 15px;
  border-radius: 5px;
  min-height: 30px;
  cursor: pointer;
  font-size: 12px;
  .span {
    color: #c5c8ce;
  }
  .iconxiayi {
    font-size: 12px;
  }
}
.ivu-form-item {
  margin-bottom: 10px;
}
.form-sty {
  width: 400px !important;
}
.addfont {
  display: inline-block;
  font-size: 12px;
  font-weight: 400;
  color: var(--prev-color-primary);
  margin-left: 14px;
  cursor: pointer;
  margin-left: 10px;
}
.ivu-icon-ios-arrow-down {
  font-size: 14px;
}
.tip {
  color: #bbb;
  font-size: 12px;
  line-height: 12px;
}
.pb-50{
  padding-bottom: 50px;
}
</style>
