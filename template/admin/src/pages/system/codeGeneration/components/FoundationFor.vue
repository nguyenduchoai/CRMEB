<template>
  <div class="main">
    <el-alert class="mb20" closable>
      <template v-slot:title>Hướng dẫn tạo crud</template>
      <template> Không thể tạo từ các bảng có sẵn của hệ thống; các bảng đã tạo trước đó vẫn có thể tiếp tục tạo </template>
    </el-alert>
    <el-form ref="foundation" :model="foundation" :rules="foundationRules" label-width="100px">
      <el-form-item label="Menu:">
        <el-cascader
          class="form-width"
          v-model="foundation.pid"
          size="small"
          :options="menusList"
          :props="{ checkStrictly: true, multiple: false, emitPath: false }"
          clearable
        ></el-cascader>
        <div class="tip">Tùy chọn, sau khi tạo thành công sẽ tự động được ghi vào menu đã chọn</div>
      </el-form-item>
      <el-form-item label="Tên menu:">
        <el-input class="form-width" v-model="foundation.menuName" placeholder="Vui lòng nhập tên menu"></el-input>
        <div class="tip">
          Tạo menu là tùy chọn, nếu không điền, tên menu được tạo mặc định sẽ là tên bảng; sau khi tạo, các quyền được sinh tự động sẽ mặc định được thêm vào menu này
        </div>
      </el-form-item>
      <el-form-item label="Tên module:" prop="modelName">
        <el-input class="form-width" v-model="foundation.modelName" placeholder="Vui lòng nhập tên module"></el-input>
        <div class="tip">Tên module bằng tiếng Trung hoặc tiếng Anh, dùng cho tiền tố tên API, tiêu đề đầu biểu mẫu</div>
      </el-form-item>
      <el-form-item label="Tên bảng:" prop="tableName">
        <el-input class="form-width" v-model="foundation.tableName" placeholder="Vui lòng nhập tên bảng"></el-input>
        <div class="tip">
          Tên bảng được chỉ định để tạo CRUD, không cần kèm tiền tố bảng; bảng đã được tạo sẽ không thể tạo lại; hoặc có thể xóa các tệp tương ứng để tạo lại! Các bảng dữ liệu quan trọng của hệ thống sẽ không được phép tạo!
        </div>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { crudMenus, crudColumnType, crudFilePath } from '@/api/systemCodeGeneration';

export default {
  name: '',
  props: {
    foundation: {
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  data() {
    return {
      foundationRules: {
        // pid: [{ required: true, message: 'Vui lòng nhập menu', trigger: 'blur' }],
        tableName: [{ required: true, message: 'Vui lòng nhập tên bảng', trigger: 'blur' }],
        modelName: [{ required: true, message: 'Vui lòng nhập tên module', trigger: 'blur' }],
      },
      menusList: [],
      columnTypeList: [],
      fromTypeList: [
        {
          value: '0',
          label: 'Không tạo',
        },
        {
          value: 'input',
          label: 'input',
        },
        {
          value: 'textarea',
          label: 'textarea',
        },
        // {
        //   value: 'select',
        //   label: 'select',
        // },
        {
          value: 'radio',
          label: 'radio',
        },
        {
          value: 'number',
          label: 'number',
        },
        {
          value: 'frameImageOne',
          label: 'frameImageOne',
        },
        {
          value: 'frameImages',
          label: 'frameImages',
        },
      ],
      loading: false,
      tableField: [],
    };
  },
  created() {
    this.getCrudMenus();
  },
  mounted() {},
  methods: {
    disabledInput(index) {
      let fieldInfo = this.tableField[index];
      let res = ['addTimestamps', 'addSoftDelete'].includes(this.tableField[index].field_type);
      if (fieldInfo.primaryKey) {
        res = true;
      }
      if (fieldInfo.field === 'delete_time' && fieldInfo.field_type === 'timestamp') {
        res = true;
      }
      return res;
    },
    initfield() {
      this.tableField = [];
    },
    changeItemField(e, i) {
      if (e === 'addSoftDelete') {
        this.$set(this.tableField[i], 'comment', 'Xóa mềm');
      }
      if (e === 'addTimestamps') {
        this.$set(this.tableField[i], 'comment', 'Thời gian thêm và sửa');
      }
    },
    getCrudMenus() {
      crudMenus().then((res) => {
        this.menusList = res.data;
      });
      crudColumnType().then((res) => {
        this.columnTypeList = res.data.types;
      });
    },
    del(index) {
      this.tableField.splice(index, 1);
    },
  },
};
</script>
<style lang="scss" scoped>
.form-width {
  width: 500px;
}
.item {
  display: flex;
  margin-bottom: 10px;
  .row {
    width: 140px;
    margin-right: 10px;
  }
}
</style>
