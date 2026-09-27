<template>
  <div class="edit">
    <pages-header
      ref="pageHeader"
      :title="$route.meta.title"
      :backUrl="$routeProStr + '/setting/notification/index'"
    ></pages-header>
    <div class="tabs mt16">
      <el-row :gutter="32">
        <el-col :span="32" class="demo-tabs-style1" style="padding: 16px">
          <el-tabs v-model="tagName" @tab-click="changeTabs">
            <el-tab-pane v-for="(item, index) in tabsList" :key="index" :name="item.slot" :label="item.title">
              <el-form class="form-sty" ref="formData" :model="formData" :rules="ruleValidate" label-width="85px">
                <div v-if="item.slot === 'is_system' && !loading">
                  <el-form-item label="Tiêu đề thông báo:">
                    <el-input
                      v-model="formData.system_title"
                      placeholder="Vui lòng nhập tiêu đề thông báo"
                      style="width: 500px"
                    ></el-input>
                  </el-form-item>
                  <el-form-item label="Nội dung thông báo:">
                    <div class="content">
                      <el-input
                        ref="system_text"
                        id="system_text"
                        v-model="formData.system_text"
                        type="textarea"
                        :autosize="{ minRows: 5, maxRows: 8 }"
                        placeholder="Vui lòng nhập nội dung thông báo"
                        style="width: 500px"
                      >
                      </el-input>
                      <div class="value-list" v-if="formData.type_n == 3">
                        <el-popover placement="right" width="200" trigger="click">
                          <div class="variable">
                            <div
                              class="item"
                              v-db-click
                              @click="changeValue(i.value, 'system_text')"
                              v-for="(i, index) in formData.custom_variable"
                              :key="index"
                            >
                              {{ i.label }}
                            </div>
                          </div>

                          <i class="el-icon-link" slot="reference"></i>
                        </el-popover>
                      </div>
                    </div>
                    <div class="tips-info" v-if="formData.type_n == 3">Có thể nhấp vào biểu tượng ở góc dưới bên phải để chèn biến tùy chỉnh</div>
                  </el-form-item>
                  <el-form-item label="Trạng thái:" prop="is_system">
                    <el-radio-group v-model="formData.is_system">
                      <el-radio :label="1">Bật</el-radio>
                      <el-radio :label="2">Tắt</el-radio>
                    </el-radio-group>
                  </el-form-item>
                </div>
                <div v-if="item.slot === 'is_sms' && !loading">
                  <el-form-item label="ID mẫu SMS:">
                    <el-input v-model="formData.sms_id" placeholder="ID mẫu SMS" style="width: 500px"></el-input>
                  </el-form-item>
                  <el-form-item label="Nội dung thông báo:">
                    <div class="content">
                      <el-input
                        id="sms_text"
                        v-model="formData.sms_text"
                        type="textarea"
                        :disabled="formData.type_n != 3"
                        :autosize="{ minRows: 5, maxRows: 8 }"
                        placeholder="Vui lòng nhập nội dung thông báo"
                        style="width: 500px"
                      ></el-input>
                      <div class="value-list" v-if="formData.type_n == 3">
                        <el-popover placement="right" width="200" trigger="click">
                          <div class="variable">
                            <div
                              class="item"
                              v-db-click
                              @click="changeValue(i.value, 'sms_text')"
                              v-for="(i, index) in formData.custom_variable"
                              :key="index"
                            >
                              {{ i.label }}
                            </div>
                          </div>

                          <i class="el-icon-link" slot="reference"></i>
                        </el-popover>
                      </div>
                    </div>
                    <div class="tips-info" v-if="formData.type_n == 3">Có thể nhấp vào biểu tượng ở góc dưới bên phải để chèn biến tùy chỉnh</div>
                  </el-form-item>
                  <el-form-item label="Trạng thái:" prop="is_sms">
                    <el-radio-group v-model="formData.is_sms">
                      <el-radio :label="1">Bật</el-radio>
                      <el-radio :label="2">Tắt</el-radio>
                    </el-radio-group>
                  </el-form-item>
                </div>
                <div v-else-if="item.slot === 'is_wechat' && !loading">
                  <el-form-item label="Mã mẫu:">
                    <el-input
                      v-model="formData.tempkey"
                      :disabled="formData.type_n !== 3"
                      placeholder="Vui lòng nhập mã mẫu"
                      style="width: 500px"
                    ></el-input>
                  </el-form-item>
                  <el-form-item label="ID mẫu:">
                    <el-input v-model="formData.tempid" placeholder="Vui lòng nhập ID mẫu" style="width: 500px"></el-input>
                  </el-form-item>
                  <el-form-item label="Mẫu:">
                    <div class="content">
                      <el-input
                        :disabled="formData.type_n !== 3"
                        v-model="formData.content"
                        type="textarea"
                        :autosize="{ minRows: 5, maxRows: 8 }"
                        placeholder="Vui lòng nhập mẫu"
                        style="width: 500px"
                        @input="handleContentChange"
                      ></el-input>
                    </div>
                  </el-form-item>
                  <el-form-item label="Trường:" v-if="formData.type_n == 3 && keyList.length">
                    <div class="content">
                      <keys-list
                        :key-list="keyList"
                        :variableList="formData.custom_variable"
                        @add="handleAdd"
                        @remove="handleRemove"
                      />
                    </div>
                  </el-form-item>
                  <el-form-item label="Liên kết chuyển hướng:">
                    <el-input
                      v-model="formData.wechat_link"
                      placeholder="Vui lòng nhập liên kết chuyển hướng của mẫu, có thể kèm tham số"
                      style="width: 500px"
                    ></el-input>
                  </el-form-item>
                  <el-form-item label="Chuyển đến Mini Program:" prop="wechat_to_routine">
                    <el-radio-group v-model="formData.wechat_to_routine">
                      <el-radio :label="1">Bật</el-radio>
                      <el-radio :label="0">Tắt</el-radio>
                    </el-radio-group>
                    <div class="tips-info">
                      Sau khi bật, khi nhấp vào tin nhắn mẫu sẽ chuyển đến trang tương ứng trong Mini Program, chỉ dùng được khi Mini Program đã được duyệt và phát hành
                    </div>
                  </el-form-item>
                  <el-form-item label="Trạng thái:" prop="is_wechat">
                    <el-radio-group v-model="formData.is_wechat">
                      <el-radio :label="1">Bật</el-radio>
                      <el-radio :label="2">Tắt</el-radio>
                    </el-radio-group>
                  </el-form-item>
                </div>
                <div v-else-if="item.slot === 'is_routine' && !loading">
                  <el-form-item label="Mã mẫu:">
                    <el-input
                      v-model="formData.tempkey"
                      :disabled="formData.type_n !== 3"
                      placeholder="Vui lòng nhập mã mẫu"
                      style="width: 500px"
                    ></el-input>
                  </el-form-item>
                  <el-form-item label="ID mẫu:">
                    <el-input v-model="formData.tempid" placeholder="Vui lòng nhập ID mẫu" style="width: 500px"></el-input>
                  </el-form-item>
                  <el-form-item label="Mẫu:">
                    <div class="content">
                      <el-input
                        :disabled="formData.type_n !== 3"
                        v-model="formData.content"
                        type="textarea"
                        :autosize="{ minRows: 5, maxRows: 8 }"
                        placeholder="Vui lòng nhập mẫu"
                        style="width: 500px"
                        @input="handleContentChange"
                      ></el-input>
                    </div>
                  </el-form-item>
                  <el-form-item label="Trường:" v-if="formData.type_n == 3 && keyList.length">
                    <div class="content">
                      <keys-list
                        :key-list="keyList"
                        :variableList="formData.custom_variable"
                        @add="handleAdd"
                        @remove="handleRemove"
                      />
                    </div>
                  </el-form-item>
                  <el-form-item label="Liên kết chuyển hướng:">
                    <el-input
                      v-model="formData.routine_link"
                      placeholder="Vui lòng nhập liên kết chuyển hướng của mẫu, có thể kèm tham số"
                      style="width: 500px"
                    ></el-input>
                  </el-form-item>
                  <el-form-item label="Trạng thái:" prop="is_routine">
                    <el-radio-group v-model="formData.is_routine">
                      <el-radio :label="1">Bật</el-radio>
                      <el-radio :label="2">Tắt</el-radio>
                    </el-radio-group>
                  </el-form-item>
                </div>

                <div v-else-if="item.slot === 'is_ent_wechat' && !loading">
                  <el-form-item label="Nội dung thông báo:">
                    <div class="content">
                      <el-input
                        id="ent_wechat_text"
                        v-model="formData.ent_wechat_text"
                        type="textarea"
                        :autosize="{ minRows: 5, maxRows: 8 }"
                        placeholder="Vui lòng nhập nội dung thông báo"
                        style="width: 500px"
                      ></el-input>
                      <div class="value-list" v-if="formData.type_n == 3">
                        <el-popover placement="right" width="200" trigger="click">
                          <div class="variable">
                            <div
                              class="item"
                              v-db-click
                              @click="changeValue(i.value, 'ent_wechat_text')"
                              v-for="(i, index) in formData.custom_variable"
                              :key="index"
                            >
                              {{ i.label }}
                            </div>
                          </div>

                          <i class="el-icon-link" slot="reference"></i>
                        </el-popover>
                      </div>
                    </div>
                    <div class="tips-info" v-if="formData.type_n == 3">Có thể nhấp vào biểu tượng ở góc dưới bên phải để chèn biến tùy chỉnh</div>
                  </el-form-item>
                  <el-form-item label="Liên kết bot:">
                    <div class="content">
                      <el-input v-model="formData.url" placeholder="Vui lòng nhập liên kết bot" style="width: 500px"></el-input>
                    </div>
                  </el-form-item>
                  <el-form-item label="Trạng thái:" prop="is_ent_wechat">
                    <el-radio-group v-model="formData.is_ent_wechat">
                      <el-radio :label="1">Bật</el-radio>
                      <el-radio :label="2">Tắt</el-radio>
                    </el-radio-group>
                  </el-form-item>
                </div>
                <el-form-item>
                  <el-button type="primary" v-db-click @click="handleSubmit('formData')">Gửi</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import { getNotificationInfo, getNotificationSave } from '@/api/notification.js';
import keysList from './components/keysList.vue';
export default {
  components: { keysList },
  data() {
    return {
      tabs: [
        {
          title: 'Thông báo hệ thống',
          slot: 'is_system',
        },
        {
          title: 'Thông báo SMS',
          slot: 'is_sms',
        },
        {
          title: 'Tin nhắn mẫu WeChat',
          slot: 'is_wechat',
        },
        {
          title: 'Nhắc nhở qua Mini Program WeChat',
          slot: 'is_routine',
        },
        {
          title: 'WeCom',
          slot: 'is_ent_wechat',
        },
      ],
      tabsList: [],
      formData: {},
      id: 0,
      loading: true,
      tagName: 'is_system',
      ruleValidate: {
        name: [
          {
            required: true,
            message: 'Vui lòng nhập tình huống thông báo',
            trigger: 'blur',
          },
        ],
        title: [
          {
            required: true,
            message: 'Vui lòng nhập tình huống thông báo',
            trigger: 'blur',
          },
        ],
        content: [
          {
            required: true,
            message: 'Vui lòng nhập nội dung thông báo',
            trigger: 'blur',
          },
        ],
      },
      keyList: [],
    };
  },
  created() {
    this.id = this.$route.query.id;
    this.getData(this.id, this.tagName, 1);
  },
  methods: {
    handleContentChange(e) {
      if (this.formData.type_n == 3) {
        const regex = /{{(.*?)\./g;
        let match;
        this.keyList = [];
        while ((match = regex.exec(e))) {
          this.keyList.push({
            key: match[1],
            value: '',
          });
        }
      }
    },
    handleRemove(index) {
      this.keyList.splice(index, 1);
    },
    // Thêm mã thẻ
    handleAdd() {
      this.keyList.push({
        key: '',
        value: '',
      });
    },
    changeTabs() {
      this.getData(this.id, this.tagName);
    },
    getData(id, name, init) {
      this.loading = true;
      this.formData = {};
      getNotificationInfo(id, name)
        .then((res) => {
          if (!this.tabsList.length) {
            this.tabs.map((v) => {
              if (res.data[v.slot]) {
                this.tabsList.push(v);
              }
            });
          }
          if (init) this.tagName = this.tabsList[0].slot;
          this.formData = res.data;
          this.formData.type_n = res.data.type; // - -!
          this.formData.type = name; // Tên loại
          this.formData.id = id;
          this.keyList = res.data.key_list || [];
          this.loading = false;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    handleSubmit(name) {
      this.formData.key_list = this.keyList;
      getNotificationSave(this.formData)
        .then((res) => {
          this.$message.success('Cài đặt thành công');
        })
        .catch((err) => {
          this.$message.error(err);
        });
    },
    handleReset(name) {
      this.$emit('close');
    },
    changeValue(e, name) {
      // Lấy phần tử dom
      let textInput = document.getElementById(name);
      // Lấy chỉ số ban đầu của con trỏ
      let index = textInput.selectionStart;
      // Dùng cách nối chuỗi để lấy nội dung cần thiết
      this.formData[name] = this.formData[name].substring(0, index) + e + this.formData[name].substring(index);
      this.$nextTick(() => {
        textInput.selectionStart = index + e.length;
        textInput.selectionEnd = index + e.length;
        textInput.focus();
      });
    },
  },
};
</script>

<style scoped lang="scss">
.edit {
}
.header_top {
  margin-bottom: 10px;
}
.demo-tabs-style1 > .ivu-tabs-card > .ivu-tabs-content {
  height: 120px;
  margin-top: -16px;
}

.demo-tabs-style1 > .ivu-tabs-card > .ivu-tabs-content > .ivu-tabs-tabpane {
  background: #fff;
  padding: 16px;
}

.demo-tabs-style1 > .ivu-tabs.ivu-tabs-card > .ivu-tabs-bar .ivu-tabs-tab {
  border-color: transparent;
}

.demo-tabs-style1 > .ivu-tabs-card > .ivu-tabs-bar .ivu-tabs-tab-active {
  border-color: #fff;
}

.tabs {
  padding: 0 30px;
  background-color: #fff;
}

.trip {
  color: rgb(146, 139, 139);
  background-color: #f2f2f2;
  margin-left: 80px;
  border-radius: 4px;
  padding: 15px;
}

.content {
  display: flex;
  position: relative;
}

.form-sty {
  margin-top: 20px;
}
.value-list {
  position: absolute;
  right: 7px;
  bottom: 7px;
  width: 22px;
  height: 22px;
  line-height: 22px;
  text-align: center;
  background: var(--prev-color-primary);
  color: #ededed;
  cursor: pointer;
  border-radius: 4px;
}
.variable {
  .item {
    cursor: pointer;
    padding: 5px 10px;
    transition: all 0.3s ease;
  }
  .item:hover {
    background: var(--prev-color-primary-light-9);
    color: var(--prev-color-primary);
    border-radius: 4px;
  }
}
// Kiểu thanh cuộn
.variable::-webkit-scrollbar {
  width: 4px;
  height: 4px;
}
.variable::-webkit-scrollbar-thumb {
  background: var(--prev-color-primary-light-9);
  border-radius: 4px;
}
.variable::-webkit-scrollbar-track {
  background: #f2f2f2;
}
</style>
