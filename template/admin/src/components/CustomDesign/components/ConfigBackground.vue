<template>
  <el-form-item label="Màu nền">
    <div style="margin-bottom: 10px">
      <el-radio-group v-model="curComponent.propValue.bgDirection" @change="onChange" size="mini">
        <el-radio label="horizontal">Ngang</el-radio>
        <el-radio label="vertical">Dọc</el-radio>
        <el-radio label="left-diagonal">Chéo trái</el-radio>
        <el-radio label="right-diagonal">Chéo phải</el-radio>
      </el-radio-group>
    </div>
    <div class="row">
      <div class="iconfont iconlianjie"></div>
      <el-color-picker v-model="curComponent.propValue.backgroundColor" @change="onChange" show-alpha></el-color-picker>
      <el-input
        v-model="curComponent.propValue.backgroundColor"
        placeholder="Vui lòng nhập màu"
        @change="onChange"
        style="margin-left: 10px; flex: 1"
      ></el-input>
      <span
        class="reset-btn"
        @click="
          curComponent.propValue.backgroundColor = '';
          onChange();
        "
        >Đặt lại</span
      >
    </div>
    <div class="row" style="margin-top: 10px">
      <el-color-picker v-model="curComponent.propValue.bgColor2" @change="onChange" show-alpha></el-color-picker>
      <el-input
        v-model="curComponent.propValue.bgColor2"
        placeholder="Vui lòng nhập màu"
        @change="onChange"
        style="margin-left: 10px; flex: 1"
      ></el-input>
      <span
        class="reset-btn"
        @click="
          curComponent.propValue.bgColor2 = '';
          onChange();
        "
        >Đặt lại</span
      >
    </div>
  </el-form-item>
</template>

<script>
export default {
  name: 'ConfigBackground',
  props: {
    curComponent: {
      type: Object,
      required: true,
    },
  },
  methods: {
    onChange() {
      this.$emit('change');
    },
  },
};
</script>

<style scoped lang="scss">
.row {
  display: flex;
  align-items: center;
  position: relative;
  .iconlianjie {
    position: absolute;
    left: 11px;
    z-index: 11;
    top: 41px;
    transform: translateY(-50%);
  }
  .reset-btn {
    cursor: pointer;
    font-size: 12px;
    margin-left: 10px;
    color: var(--prev-color-primary);
    &:hover {
      color: var(--prev-color-primary-light-1);
    }
  }
}
</style>
