<template>
  <div class="header-style-config">
    <div class="config-title">{{ configData.title }}</div>

    <!-- Cỡ chữ -->
    <div class="config-item">
      <span class="item-label">Cỡ chữ tiêu đề</span>
      <div class="slider-container">
        <el-slider v-model="configData.fontSize" show-input :min="12" :max="24"></el-slider>
      </div>
    </div>

    <!-- Màu chữ bên trái -->
    <div class="config-item">
      <span class="item-label">Màu chữ tiêu đề</span>
      <!-- <el-color-picker v-model="configData.leftColor" @change="handleChange" size="small"></el-color-picker> -->
      <div class="row slider-container">
        <el-color-picker
          v-model="configData.leftColor"
          @change="handleChange"
          show-alpha
          size="small"
        ></el-color-picker>
        <el-input
          v-model="configData.leftColor"
          placeholder="Vui lòng nhập màu"
          @change="handleChange"
          style="margin-left: 10px; flex: 1"
        ></el-input>
        <span
          class="reset-btn"
          @click="
            configData.leftColor = '#fff';
            handleChange();
          "
          >Đặt lại</span
        >
      </div>
    </div>
    <!-- Độ đậm chữ bên trái, tùy chọn 300, 500, bình thường -->
    <div class="config-item">
      <span class="item-label">Độ đậm chữ tiêu đề</span>
      <el-radio-group v-model="configData.leftWeight" size="small">
        <el-radio-button label="300" value="300"></el-radio-button>
        <el-radio-button label="500" value="500"></el-radio-button>
        <el-radio-button label="normal">Bình thường</el-radio-button>
      </el-radio-group>
    </div>
    <!-- Màu chữ bên phải -->
    <div class="config-item">
      <span class="item-label">Cỡ chữ nút</span>
      <div class="slider-container">
        <el-slider v-model="configData.rightFontSize" show-input :min="12" :max="24"></el-slider>
      </div>
    </div>
    <div class="config-item">
      <span class="item-label">Màu chữ nút</span>
      <!-- <el-color-picker v-model="configData.rightColor" @change="handleChange" size="small"></el-color-picker> -->
      <div class="row slider-container">
        <el-color-picker
          v-model="configData.rightColor"
          @change="handleChange"
          show-alpha
          size="small"
        ></el-color-picker>
        <el-input
          v-model="configData.rightColor"
          placeholder="Vui lòng nhập màu"
          @change="handleChange"
          style="margin-left: 10px; flex: 1"
        ></el-input>
        <span
          class="reset-btn"
          @click="
            configData.rightColor = '#fff';
            handleChange();
          "
          >Đặt lại</span
        >
      </div>
    </div>
    <div class="config-item">
      <span class="item-label">Độ đậm chữ nút</span>
      <el-radio-group v-model="configData.rightWeight" size="small">
        <el-radio-button label="300" value="300"></el-radio-button>
        <el-radio-button label="500" value="500"></el-radio-button>
        <el-radio-button label="normal">Bình thường</el-radio-button>
      </el-radio-group>
    </div>
    <!-- Lề trên -->
    <div class="config-item">
      <span class="item-label">Lề trên</span>
      <div class="slider-container">
        <el-slider v-model="configData.topPadding" show-input :min="0"></el-slider>
      </div>
    </div>

    <!-- Lề dưới -->
    <div class="config-item">
      <span class="item-label">Lề dưới</span>
      <div class="slider-container">
        <el-slider v-model="configData.bottomPadding" show-input :min="0"></el-slider>
      </div>
    </div>

    <!-- Lề trái phải -->
    <div class="config-item">
      <span class="item-label">Lề trái phải</span>
      <div class="slider-container">
        <el-slider v-model="configData.leftRightPadding" show-input :min="0"></el-slider>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'c_header_style',
  props: {
    configNme: {
      type: String,
    },
    configObj: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      configData: {
        title: 'Kiểu phần đầu',
        fontSize: 14,
        leftColor: '#333333',
        rightColor: '#333333',
        topPadding: 10,
        bottomPadding: 10,
        leftRightPadding: 12,
      },
    };
  },
  watch: {
    configObj: {
      handler(nVal, oVal) {
        this.configData = nVal[this.configNme] || {
          title: 'Kiểu phần đầu',
          fontSize: 14,
          leftColor: '#333333',
          rightColor: '#333333',
          topPadding: 10,
          bottomPadding: 10,
          leftRightPadding: 12,
        };
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    handleChange() {
      this.$emit('getConfig', this.configData);
    },
  },
};
</script>

<style lang="scss" scoped>
.header-style-config {
  padding: 12px 16px;

  .config-title {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin-bottom: 12px;
  }

  .config-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    .slider-container {
      width: 75%;
    }
    &:last-child {
      margin-bottom: 0;
    }

    .item-label {
      font-size: 13px;
      color: #999;
    }
  }
  .row {
    display: flex;
    align-items: center;
    position: relative;
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
}
</style>
