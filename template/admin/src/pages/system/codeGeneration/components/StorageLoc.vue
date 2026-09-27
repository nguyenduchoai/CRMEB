<template>
  <div class="main">
    <el-alert closable class="mb20">
      <template v-slot:title>Hướng dẫn vị trí tệp do crud tạo ra</template>
      <template>
        <p>1. Hạn chế sửa vị trí tệp được tạo, có thể tự di chuyển sau khi tạo</p>
        <p>2. Vị trí tệp được tạo không bao gồm đường dẫn tuyệt đối, khi tạo sẽ tự kèm đường dẫn tuyệt đối đã thiết lập sẵn</p>
        <p>3. Vị trí mặc định của tệp mẫu frontend được cấu hình tại: admin_template_path trong config/app.php</p>
        <p>4. Vị trí tạo mặc định của backend là: thư mục gốc của website</p>
        <p>5. Đảm bảo thư mục frontend /admin/src, thư mục backend app có quyền ghi và tạo tệp</p>
      </template>
    </el-alert>
    <el-form ref="foundation" :model="storage" :rules="storageRules" label-width="140px">
      <el-form-item label="Vị trí tạo controller:">
        <el-input class="form-width" v-model="storage.controller" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu tệp controller backend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo service:">
        <el-input class="form-width" v-model="storage.service" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu tệp service backend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo dao:">
        <el-input class="form-width" v-model="storage.dao" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu tệp dao backend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo model:">
        <el-input class="form-width" v-model="storage.model" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu tệp model backend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo route:">
        <el-input class="form-width" v-model="storage.route" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu route backend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo validate:">
        <el-input class="form-width" v-model="storage.validate" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu validator backend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo pages:">
        <el-input class="form-width" v-model="storage.pages" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu tệp trang frontend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo api:">
        <el-input class="form-width" v-model="storage.api" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu tệp API frontend được tạo</div>
      </el-form-item>
      <el-form-item label="Vị trí tạo router:">
        <el-input class="form-width" v-model="storage.router" disabled placeholder="Vui lòng nhập"></el-input>
        <div class="tip">Vị trí lưu route frontend được tạo</div>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
export default {
  name: '',
  props: {
    storage: {
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  data() {
    return {
      storageRules: {},
    };
  },
  created() {},
  mounted() {},
  methods: {},
};
</script>
<style lang="scss" scoped>
.form-width {
  width: 500px;
}
</style>
