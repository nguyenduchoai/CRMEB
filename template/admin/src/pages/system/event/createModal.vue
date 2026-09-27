<template>
  <div>
    <el-drawer
      :visible.sync="modal"
      :title="formValidate.id ? 'Sửa sự kiện' : 'Thêm sự kiện'"
      size="1000px"
      @closed="initData"
    >
      <el-form v-if="modal" class="pb-20" ref="formValidate" :model="formValidate" label-width="97px" label-colon>
        <el-form-item label="Tên sự kiện:" required>
          <el-row :gutter="16">
            <el-col :span="20">
              <el-input v-model="formValidate.name" placeholder="Vui lòng nhập tên sự kiện"></el-input>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Loại sự kiện:" required>
          <el-row :gutter="16">
            <el-col :span="20">
              <el-select v-model="formValidate.mark" @change="taskChange">
                <el-option v-for="(item, name) in task" :key="name" :value="item.value" :label="item.label"></el-option>
              </el-select>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Mô tả sự kiện:">
          <el-row :gutter="10">
            <el-col :span="24">
              <el-input
                v-model="formValidate.content"
                type="textarea"
                :autosize="{ minRows: 3, maxRows: 5 }"
                placeholder="Vui lòng nhập mô tả sự kiện"
              ></el-input>
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Mã thực thi:">
          <el-row :gutter="10">
            <el-col :span="24">
              <div ref="container" id="container" class="monaco-editor"></div>
              <!-- <div class="copy-tag">
                <el-tag
                  class="item"
                  size="small"
                  v-for="(i, k, index) in copyData"
                  :key="index"
                  v-db-click
                  @click="onCopy(k)"
                >
                  {{ i }}
                </el-tag>
              </div> -->
            </el-col>
          </el-row>
        </el-form-item>
        <el-form-item label="Tham số khả dụng:" v-if="copyData">
          <el-row :gutter="10">
            <el-col :span="24">
              <el-input
                class="text-area"
                v-model="copyData"
                type="textarea"
                :autosize="{ minRows: 7, maxRows: 7 }"
                placeholder="Vui lòng nhập mô tả sự kiện"
                readonly
              ></el-input>
              <!-- <span class="text-area">{{ copyData }}</span> -->
            </el-col>
          </el-row>
        </el-form-item>

        <el-form-item label="Mật khẩu phát triển:" required>
          <el-row :gutter="10">
            <el-col :span="24">
              <el-input v-model="formValidate.password" type="password" placeholder="Vui lòng nhập mật khẩu phát triển hệ thống, mật khẩu phát triển được sửa tại password trong crmeb/config/filesystem.php"></el-input>
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
import { eventTask, eventInfo, eventSave } from '@/api/system';
export default {
  data() {
    return {
      modal: false,
      task: [],
      loading: false,
      formValidate: {
        mark: '', //Khóa
        content: '',
        is_open: 0,
        name: '',
        password: '',
        customCode: '',
      },
      copyData: '',
      trip: '',
      editor: '', //Đối tượng trình soạn thảo hiện tại
    };
  },
  created() {
    this.eventTask();
  },
  methods: {
    ...mapMutations('admin/layout', ['setCopyrightShow']),
    taskChange(item) {
      // Lấy giá trị data trong task tương ứng với giá trị được chọn
      let taskData = this.task.find((i) => i.value === item);
      this.copyData = taskData.data;
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
    eventTask() {
      eventTask().then((res) => {
        this.task = res.data;
      });
    },
    // onCopy(copyData) {
    //   let data = `$data['${copyData}']`;
    //   this.$copyText(data)
    //     .then((message) => {
    //       this.$message.success('Sao chép thành công');
    //     })
    //     .catch((err) => {
    //       this.$message.error('Sao chép thất bại');
    //     });
    // },
    initData(status) {
      this.formValidate = {
        name: '',
        mark: '',
        is_open: 0,
        content: '',
        password: '',
        customCode: '',
      };
      this.copyData = '';
      this.modal = false;
    },
    eventInfo(id) {
      if (!id) {
        this.modal = true;
        this.initEditor(
          "<?php\n\n//Mã ví dụ\n//Ví dụ sử dụng tham số  $data['uid']\n\n//Ghi trực tiếp vào cơ sở dữ liệu\n\\think\\facade\\Db::name('cache')->insert(['key' => 'custom_event_' . rand(), 'result' => $data['nickname'] . rand(), 'expire_time' => 0]);\n\n//Gọi phương thức hệ thống\napp()->make(\\app\\services\\other\\CacheServices::class)->setDbCache('custom_event_' . rand(), $data['nickname']);",
        );
        return;
      }
      eventInfo(id).then((res) => {
        this.modal = true;
        this.formValidate = res.data;
        let taskData = this.task.find((i) => i.value === res.data.mark);
        this.copyData = taskData.data;
        this.initEditor(res.data.customCode || '');
      });
    },
    // Gửi
    handleSubmit() {
      this.formValidate.customCode = this.editor.getValue();
      if (!this.formValidate.mark) {
        return this.$message.error({
          message: 'Vui lòng chọn loại sự kiện',
          onClose: () => {
            // this.loading = false;
          },
        });
      }
      this.eventSave(this.formValidate);
    },
    eventSave(data) {
      eventSave(data)
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

.copy-tag {
  display: flex;
  flex-wrap: wrap;

  .item {
    margin: 5px;
    cursor: pointer;
  }
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

.text-area {
  white-space: pre-wrap;
  word-break: break-word;
}
</style>
