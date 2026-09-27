<template>
  <div>
    <el-dialog :visible.sync="modals" width="720px" :title="titleFrom" :close-on-click-modal="false">
      <el-form
        ref="formValidate"
        :model="formValidate"
        label-width="100px"
        :rules="ruleValidate"
        @submit.native.prevent
      >
        <el-row :gutter="24">
          <el-col :span="24">
            <el-form-item label="Tên nhóm dữ liệu:" prop="name">
              <el-input v-model="formValidate.name" placeholder="Vui lòng nhập tên nhóm dữ liệu" style="width: 90%"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Trường dữ liệu:" prop="config_name">
              <el-input v-model="formValidate.config_name" placeholder="Vui lòng nhập trường dữ liệu" style="width: 90%"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Mô tả dữ liệu:" prop="info">
              <el-input v-model="formValidate.info" placeholder="Vui lòng nhập mô tả dữ liệu" style="width: 90%"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Loại dữ liệu:" prop="cate_id">
              <el-radio-group v-model="formValidate.cate_id">
                <el-radio :label="0">Mặc định</el-radio>
                <el-radio :label="1">Dữ liệu</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-col>
          <el-col :span="24" v-for="(item, index) in formValidate.typelist" :key="index">
            <el-col v-bind="grid">
              <el-form-item
                :label="'Trường' + (index + 1) + '：'"
                label-width="90px"
                :prop="'typelist.' + index + '.name.value'"
                :rules="{ required: true, message: 'Vui lòng nhập tên trường: Họ tên', trigger: 'blur' }"
              >
                <el-input v-model="item.name.value" placeholder="Tên trường: Họ tên"></el-input>
              </el-form-item>
            </el-col>
            <el-col v-bind="grid" class="goupBox">
              <el-form-item
                label-width="0"
                :prop="'typelist.' + index + '.title.value'"
                :rules="{ required: true, message: 'Vui lòng nhập tên cấu hình trường', trigger: 'blur' }"
              >
                <el-input v-model="item.title.value" placeholder="Tên cấu hình trường: name"></el-input>
              </el-form-item>
            </el-col>
            <el-col v-bind="grid" prop="type" class="goupBox">
              <el-form-item
                :prop="'typelist.' + index + '.type.value'"
                :rules="{ required: true, message: 'Vui lòng chọn loại trường', trigger: 'change' }"
                label-width="0"
              >
                <el-select placeholder="Loại trường" v-model="item.type.value">
                  <el-option value="input">Ô văn bản</el-option>
                  <el-option value="textarea">Ô văn bản nhiều dòng</el-option>
                  <el-option value="radio">Ô chọn một</el-option>
                  <el-option value="checkbox">Ô chọn nhiều</el-option>
                  <el-option value="select">Danh sách thả xuống</el-option>
                  <el-option value="upload">Một ảnh</el-option>
                  <el-option value="uploads">Nhiều ảnh</el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-button icon="el-icon-delete" v-db-click @click="delGroup(index)"></el-button>
            <!-- <el-col span="1"> </el-col> -->
            <el-col
              :span="24"
              v-if="item.type.value === 'radio' || item.type.value === 'checkbox' || item.type.value === 'select'"
            >
              <el-form-item
                :prop="'typelist.' + index + '.param.value'"
                :rules="{ required: true, message: 'Vui lòng nhập định dạng tham số', trigger: 'blur' }"
              >
                <el-input
                  type="textarea"
                  :rows="4"
                  :placeholder="item.param.placeholder"
                  v-model="item.param.value"
                  style="width: 90%"
                ></el-input>
              </el-form-item>
            </el-col>
          </el-col>
          <el-col>
            <el-form-item>
              <el-button type="primary" v-db-click @click="addType">Thêm trường</el-button>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="handleReset">Hủy</el-button>
        <el-button type="primary" v-db-click @click="handleSubmit('formValidate')" :disabled="valids">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { groupAddApi, groupInfoApi } from '@/api/system';
export default {
  name: 'menusFrom',
  props: {
    groupId: {
      type: Number,
      default: 0,
    },
    titleFrom: {
      type: String,
      default: '',
    },
    addId: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      iconVal: '',
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      modals: false,
      modal12: false,
      ruleValidate: {
        name: [{ required: true, message: 'Vui lòng nhập tên nhóm dữ liệu', trigger: 'blur' }],
        config_name: [{ required: true, message: 'Vui lòng nhập trường dữ liệu', trigger: 'blur' }],
        info: [{ required: true, message: 'Vui lòng nhập mô tả dữ liệu', trigger: 'blur' }],
        names: [{ required: true, message: 'Vui lòng nhập tên trường', trigger: 'blur' }],
      },
      FromData: [],
      valids: false,
      list2: [],
      formValidate: {
        name: '',
        config_name: '',
        info: '',
        typelist: [],
        cate_id: 0,
      },
    };
  },
  watch: {
    addId(n) {
      if (n === 'addId') {
        this.formValidate.typelist = [];
      }
    },
  },
  methods: {
    // Nhấp để thêm trường
    addType() {
      this.formValidate.typelist.push({
        name: {
          value: '',
        },
        title: {
          value: '',
        },
        type: {
          value: '',
        },
        param: {
          placeholder: 'Định dạng tham số, ví dụ:\n1=>Trắng\n2=>Đỏ\n3=>Đen',
          value: '',
        },
      });
    },
    // Xóa trường
    delGroup(index) {
      this.formValidate.typelist.splice(index, 1);
    },
    // Chi tiết
    fromData(id) {
      groupInfoApi(id)
        .then(async (res) => {
          this.formValidate = res.data.info;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Gửi
    handleSubmit(name) {
      let data = {
        url: this.groupId ? `/setting/group/${this.groupId}` : 'setting/group',
        method: this.groupId ? 'put' : 'post',
        datas: this.formValidate,
      };
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (this.formValidate.typelist.length === 0) return this.$message.error('Vui lòng thêm tên trường: Họ tên!');
          groupAddApi(data)
            .then(async (res) => {
              this.$message.success(res.msg);
              this.modals = false;
              this.$refs[name].resetFields();
              this.formValidate.typelist = [];
              this.$emit('getList');
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          if (!this.formValidate.name) return this.$message.error('Vui lòng thêm tên nhóm dữ liệu!');
          if (!this.formValidate.config_name) return this.$message.error('Vui lòng thêm trường dữ liệu!');
          if (!this.formValidate.info) return this.$message.error('Vui lòng thêm mô tả dữ liệu!');
        }
      });
    },
    handleReset() {
      this.modals = false;
      this.$refs['formValidate'].resetFields();
      this.$emit('clearFrom');
    },
  },
  created() {},
  mounted() {},
};
</script>

<style lang="scss" scoped>
.cur {
  cursor: pointer;
}
.goupBox ::v-deep .ivu-form-item-content {
  margin-left: 43px !important;
}
</style>
