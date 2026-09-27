<template>
  <div>
    <el-drawer
      :visible.sync="modal"
      :title="formValidate.id ? 'Sửa tác vụ định kỳ' : 'Thêm tác vụ định kỳ'"
      size="1000px"
      @closed="initData"
    >
      <el-form v-if="modal" class="pb-20" ref="formValidate" :model="formValidate" label-width="97px" label-colon>
        <el-form-item label="Tên nhiệm vụ:" v-if="currentTab === '1'">
          <el-row :gutter="10">
            <el-col :span="24">
              <el-input v-model="formValidate.name" type="text" placeholder="Vui lòng nhập tên nhiệm vụ"></el-input>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Chu kỳ thực thi:" required>
          <el-row :gutter="14">
            <el-col :span="4">
              <el-select v-model="formValidate.type">
                <el-option
                  v-for="item in typeList"
                  :key="item.value"
                  :value="item.value"
                  :label="item.name"
                ></el-option>
              </el-select>
            </el-col>
            <el-col v-if="formValidate.type == 6" :span="4">
              <el-select v-model="formValidate.week">
                <el-option v-for="item in weekList" :key="item.value" v-bind="item"></el-option>
              </el-select>
            </el-col>
            <el-col v-if="[8].includes(formValidate.type)" :span="4">
              <div class="input-number-wrapper">
                <el-input-number :controls="false" v-model="formValidate.month" :max="12" :min="1"></el-input-number>
                <span class="suffix">Tháng</span>
              </div>
            </el-col>
            <el-col v-if="[4, 7, 8].includes(formValidate.type)" :span="4">
              <div class="input-number-wrapper">
                <el-input-number
                  :controls="false"
                  v-model="formValidate.day"
                  :max="formValidate.type === 4 ? 10000 : 31"
                  :min="1"
                ></el-input-number>
                <span class="suffix">CN</span>
              </div>
            </el-col>
            <el-col v-if="[3, 4, 5, 6, 7, 8].includes(formValidate.type)" :span="4">
              <div class="input-number-wrapper">
                <el-input-number
                  controls-position="right"
                  v-model="formValidate.hour"
                  :max="23"
                  :min="0"
                ></el-input-number>
                <span class="suffix">giờ</span>
              </div>
            </el-col>
            <el-col v-if="[2, 3, 4, 5, 6, 7, 8].includes(formValidate.type)" :span="4">
              <div class="input-number-wrapper">
                <el-input-number
                  controls-position="right"
                  v-model="formValidate.minute"
                  :max="formValidate.type === 2 ? 36000 : 59"
                  :min="0"
                ></el-input-number>
                <span class="suffix">phút</span>
              </div>
            </el-col>
            <el-col v-if="[1, 5, 6, 7].includes(formValidate.type)" :span="4">
              <div class="input-number-wrapper">
                <el-input-number
                  controls-position="right"
                  v-model="formValidate.second"
                  :max="formValidate.type === 1 ? 36000 : 59"
                  :min="0"
                ></el-input-number>
                <span class="suffix">giây</span>
              </div>
            </el-col>
          </el-row>
          <el-row :gutter="12">
            <div class="trip">{{ trip }}</div>
          </el-row>
        </el-form-item>
        <el-form-item label="Mô tả tác vụ:">
          <el-row :gutter="10">
            <el-col :span="24">
              <el-input
                v-model="formValidate.content"
                type="textarea"
                :autosize="{ minRows: 3, maxRows: 5 }"
                placeholder="Vui lòng nhập mô tả tác vụ"
              ></el-input>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Mã thực thi:" v-if="currentTab === '1'">
          <el-row :gutter="10">
            <el-col :span="24">
              <div ref="container" id="container" class="monaco-editor"></div>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Mật khẩu phát triển:" v-if="currentTab === '1'">
          <el-row :gutter="10">
            <el-col :span="24">
              <el-input
                v-model="formValidate.password"
                type="password"
                placeholder="Vui lòng nhập mật khẩu phát triển hệ thống, mật khẩu phát triển được sửa tại password trong crmeb/config/filesystem.php"
              ></el-input>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Bật:">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-switch :active-value="1" :inactive-value="0" v-model="formValidate.is_open" size="large">
                <span slot="open">Bật</span>
                <span slot="close">Tắt</span>
              </el-switch>
            </el-col>
          </el-row>
        </el-form-item>
      </el-form>
      <span class="dialog-footer">
        <el-button v-db-click @click="modal = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="handleSubmit">Gửi</el-button>
      </span>
    </el-drawer>
  </div>
</template>

<script>
import * as monaco from 'monaco-editor';
import { mapMutations } from 'vuex';
import { timerTask, timerInfo, saveTimer, updateTimer } from '@/api/system';
export default {
  props: {
    currentTab: {
      type: String,
      default: '0',
    },
  },
  data() {
    return {
      modal: false,
      typeList: [
        {
          name: 'Mỗi N giây',
          value: 1,
        },
        {
          name: 'Mỗi N phút',
          value: 2,
        },
        {
          name: 'Mỗi N giờ',
          value: 3,
        },
        {
          name: 'Mỗi N ngày',
          value: 4,
        },
        {
          name: 'Hằng ngày',
          value: 5,
        },
        {
          name: 'Hằng tuần',
          value: 6,
        },
        {
          name: 'Hằng tháng',
          value: 7,
        },
        {
          name: 'Hằng năm',
          value: 8,
        },
      ],
      task: {},
      loading: false,
      formValidate: {
        name: '',
        mark: 'customTimer', //Khóa
        content: '',
        is_open: 0,
        type: 1,
        month: 1,
        week: 1,
        day: 1,
        hour: 1,
        minute: 1,
        second: 1,
        customCode: '',
      },
      trip: '',
      weekList: [
        { label: 'Thứ 2', value: 1 },
        { label: 'Thứ 3', value: 2 },
        { label: 'Thứ 4', value: 3 },
        { label: 'Thứ 5', value: 4 },
        { label: 'Thứ 6', value: 5 },
        { label: 'Thứ 7', value: 6 },
        { label: 'Chủ nhật', value: 7 },
      ],
      editor: '', //Đối tượng trình soạn thảo hiện tại
    };
  },
  watch: {
    formValidate: {
      handler(nVal, oVal) {
        switch (nVal.type) {
          case 1:
            this.trip = `Thực thi mỗi ${nVal.second} giây một lần`;
            break;
          case 2:
            this.trip = `Thực thi mỗi ${nVal.minute} phút một lần`;
            break;
          case 3:
            this.trip = `Thực thi mỗi ${nVal.hour} giờ một lần vào phút thứ ${nVal.minute}`;
            break;
          case 4:
            this.trip = `Thực thi mỗi ${nVal.day} ngày một lần vào lúc ${nVal.hour} giờ ${nVal.minute} phút`;
            break;
          case 5:
            this.trip = `Thực thi hằng ngày vào lúc ${nVal.hour} giờ ${nVal.minute} phút ${nVal.second} giây`;
            break;
          case 6:
            this.trip = `Thực thi vào ngày thứ ${nVal.week} hằng tuần lúc ${nVal.hour} giờ ${nVal.minute} phút ${nVal.second} giây`;
            break;
          case 7:
            this.trip = `Thực thi vào ngày ${nVal.day} hằng tháng lúc ${nVal.hour} giờ ${nVal.minute} phút ${nVal.second} giây`;
            break;
          case 8:
            this.trip = `Thực thi vào ngày ${nVal.day} tháng ${nVal.month} hằng năm lúc ${nVal.hour} giờ ${nVal.minute} phút ${nVal.second} giây`;
            break;
        }
      },
      immediate: true,
      deep: true,
    },
  },
  created() {
    this.timerTask();
  },
  methods: {
    ...mapMutations('admin/layout', ['setCopyrightShow']),
    modalOpen() {
      if (this.currentTab === '1') {
        this.initEditor();
      }
    },
    /**
     * Khởi tạo trình soạn thảo
     */
    initEditor(conetnt = '') {
      try {
        let that = this;
        that.$nextTick(() => {
          // Khởi tạo trình soạn thảo, đảm bảo dom đã được render
          that.editor = monaco.editor.create(document.getElementById('container'), {
            value: conetnt, //Văn bản hiển thị ban đầu của trình soạn thảo
            language: 'php', //Hỗ trợ ngôn ngữ, tự tham khảo demo
            automaticLayout: true, //Tự động bố cục
            theme: 'vs-dark', //Chính thức có sẵn 3 chủ đề vs, hc-black, or vs-dark
            foldingStrategy: 'indentation', // Code có thể gập theo từng đoạn nhỏ
            overviewRulerBorder: false, // Không cần viền thanh cuộn
            minimap: { enabled: false },
            scrollbar: {
              vertical: 'hidden',
              horizontal: 'hidden',
            },
            wordWrap: 'on',
            autoIndent: true, // Tự động bố cục
            tabSize: 4, // Độ dài thụt lề tab
            autoClosingOvertype: 'always',
            readOnly: false,
          });
        });
      } catch (error) {
        console.log(error);
      }
    },
    timerTask() {
      timerTask().then((res) => {
        this.task = res.data;
      });
    },
    initData(status) {
      this.formValidate = {
        name: '',
        mark: '',
        content: '',
        is_open: 0,
        type: 1,
        month: 1,
        week: 1,
        day: 1,
        hour: 1,
        minute: 1,
        second: 1,
        customCode: '',
      };
      this.modal = false;
    },
    timerInfo(id) {
      if (id) {
        timerInfo(id).then((res) => {
          this.modal = true;
          this.formValidate = res.data;
          this.initEditor(res.data.customCode || '');
        });
      } else {
        this.modal = true;
        this.initEditor(
          "<?php\n\n//Mã ví dụ\n\n//Ghi trực tiếp vào cơ sở dữ liệu\n\\think\\facade\\Db::name('cache')->insert(['key' => 'custom_timer_' . rand(), 'result' => rand(), 'expire_time' => 0]);\n\n//Gọi phương thức hệ thống\napp()->make(\\app\\services\\other\\CacheServices::class)->setDbCache('custom_timer_' . rand(), rand());",
        );
      }
    },
    // Gửi
    handleSubmit() {
      if (this.currentTab === '1') {
        this.formValidate.customCode = this.editor.getValue();
        this.formValidate.mark = 'customTimer';
      }
      if (!this.formValidate.mark) {
        return this.$message.error({
          message: 'Vui lòng chọn tên tác vụ',
          onClose: () => {
            // this.loading = false;
          },
        });
      }
      this.saveTimer(this.formValidate);
    },
    taskChange(task) {
      // this.formValidate.mark = task.value;
    },
    saveTimer(data) {
      saveTimer(data)
        .then((res) => {
          this.$message.success({
            message: res.msg,
          });
          this.$emit('submitAsk');
          this.modal = false;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.form-card {
  margin-bottom: 74px;

  ::v-deep .ivu-card-body {
    padding: 30px 0;
  }
}
.pb-20 {
  padding-bottom: 20px;
}
.btn-card {
  position: fixed;
  right: 0;
  bottom: 0;
  left: 200px;
  z-index: 2;
  text-align: center;
}

.input-number-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
  vertical-align: middle;
  line-height: normal;

  .ivu-input-number {
    width: 100%;
    padding-right: 35px;
  }

  ::v-deep .ivu-input-number-handler-wrap {
    right: 35px;
  }

  .suffix {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 1;
    width: 35px;
    height: 100%;
    text-align: center;
    font-size: 12px;
    line-height: 33px;
    color: #333333;
  }
}

.trip {
  padding-left: 15px;
  color: #aaa;
}
::v-deep .el-input-number__increase,
::v-deep .el-input-number__decrease {
  display: none;
}
.ml30 {
  margin-left: 30px;
}
.dialog-footer {
  // Cố định ở dưới cùng
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1;
  padding: 10px 20px;
  background-color: #fff;
  border-top: 1px solid #e8e8e8;
  display: flex;
  justify-content: center;
}
.monaco-editor {
  border: 1px solid var(--prev-border-color-base);
  border-radius: 4px;
  height: 400px;
  overflow: hidden;
}
</style>
