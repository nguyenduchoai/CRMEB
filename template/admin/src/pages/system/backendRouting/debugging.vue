<template>
  <div class="content" v-if="interfaceData">
    <div class="head">
      <el-input v-model="interfaceData.path">
        <template #prepend>
          <el-select v-model="interfaceData.method" style="width: 120px">
            <el-option
              v-for="(item, index) in requestTypeList"
              :key="index"
              :value="item.value"
              :label="item.label"
            ></el-option>
          </el-select>
        </template>
      </el-input>
      <el-button class="ml20" type="primary" v-db-click @click="requestData">Gửi yêu cầu</el-button>
      <el-button v-if="codes" class="ml10 copy-btn" type="success" v-db-click @click="insertCopy()">Sao chép kết quả</el-button>
    </div>
    <div class="params">
      <el-tabs class="mt10" v-model="paramsType" @tab-click="changeTab">
        <el-tab-pane label="Params" name="Params"> </el-tab-pane>
        <el-tab-pane label="Body" name="Body"> </el-tab-pane>
        <el-tab-pane label="Header" name="Header"> </el-tab-pane>
      </el-tabs>
      <div v-show="paramsType === 'Params'">
        <vxe-table
          class="mt10"
          resizable
          show-overflow
          keep-source
          ref="xTable"
          row-id="id"
          :print-config="{}"
          :export-config="{}"
          :tree-config="{ transform: true, rowField: 'id', parentField: 'parentId' }"
          :data="interfaceData.query"
        >
          <vxe-column field="attribute" width="150" title="Thuộc tính" tree-node :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.attribute" type="text"></vxe-input>
            </template>
          </vxe-column>
          <vxe-column field="value" title="Giá trị thông số" :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.value" type="text"></vxe-input>
            </template>
          </vxe-column>
          <vxe-column field="type" title="Loại" width="120" :edit-render="{}">
            <template #default="{ row }">
              <vxe-select
                v-model="row.type"
                transfer
                @change="
                  (val) => {
                    handleChange(val, row, 'xTable');
                  }
                "
              >
                <vxe-option
                  v-for="item in typeList"
                  :key="item.value"
                  :value="item.value"
                  :label="item.label"
                ></vxe-option>
              </vxe-select>
            </template>
          </vxe-column>
          <!-- <vxe-column field="must" title="Bắt buộc" width="50" :edit-render="{}">
            <template #default="{ row }">
              <span>{{ row.must == '1' ? 'Có' : 'Không' }}</span>
            </template>
          </vxe-column>
          <vxe-column field="trip" width="150" title="Mô tả" :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.trip" type="text"></vxe-input>
            </template>
          </vxe-column> -->
          <vxe-column title="Thao tác" width="120">
            <template #default="{ row }">
              <vxe-button
                type="text"
                v-if="['array', 'object'].includes(row.type)"
                status="primary"
                v-db-click
                @click="insertRow(row, 'xTable')"
                >Chèn</vxe-button
              >
              <vxe-button type="text" status="primary" v-db-click @click="removeRow(row, 'xTable')">Xóa</vxe-button>
            </template>
          </vxe-column>
        </vxe-table>
        <el-button class="mt10" type="primary" v-db-click @click="insertEvent('xTable')">Thêm thông số</el-button>
      </div>
      <div v-show="paramsType === 'Body'">
        <el-radio-group v-model="bodyType" class="mt10">
          <el-radio label="form-data"></el-radio>
          <el-radio label="json"></el-radio>
        </el-radio-group>
        <vxe-table
          v-if="bodyType == 'form-data'"
          class="mt10"
          resizable
          show-overflow
          keep-source
          ref="yTable"
          row-id="id"
          :print-config="{}"
          :export-config="{}"
          :tree-config="{ transform: true, rowField: 'id', parentField: 'parentId' }"
          :data="interfaceData.request_body"
        >
          <vxe-column field="attribute" width="150" title="Thuộc tính" tree-node :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.attribute" type="text"></vxe-input>
            </template>
          </vxe-column>
          <vxe-column field="value" title="Giá trị thông số" :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.value" type="text"></vxe-input>
            </template>
          </vxe-column>
          <vxe-column field="type" title="Loại" width="120" :edit-render="{}">
            <template #default="{ row }">
              <vxe-select
                v-model="row.type"
                transfer
                @change="
                  (val) => {
                    handleChange(val, row, 'yTable');
                  }
                "
              >
                <vxe-option
                  v-for="item in typeList"
                  :key="item.value"
                  :value="item.value"
                  :label="item.label"
                ></vxe-option>
              </vxe-select>
            </template>
          </vxe-column>
          <!-- <vxe-column field="must" title="Bắt buộc" width="50" :edit-render="{}">
            <template #default="{ row }">
              <span>{{ row.must == '1' ? 'Có' : 'Không' }}</span>
            </template>
          </vxe-column>
          <vxe-column field="trip" title="Mô tả" width="150" :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.trip" type="text"></vxe-input>
            </template>
          </vxe-column> -->
          <vxe-column title="Thao tác" width="120">
            <template #default="{ row }">
              <vxe-button
                type="text"
                v-if="['array', 'object'].includes(row.type)"
                status="primary"
                v-db-click
                @click="insertRow(row, 'yTable')"
                >Chèn</vxe-button
              >
              <vxe-button type="text" status="primary" v-db-click @click="removeRow(row, 'yTable')">Xóa</vxe-button>
            </template>
          </vxe-column>
        </vxe-table>
        <div v-else>
          <el-input v-model="jsonBody" type="textarea" :rows="8" placeholder="Dữ liệu yêu cầu" />
        </div>
        <el-button v-if="bodyType == 'form-data'" class="mt10" type="primary" v-db-click @click="insertEvent('yTable')"
          >Thêm thông số</el-button
        >
      </div>

      <div v-show="paramsType === 'Header'">
        <vxe-table
          class="mt10"
          resizable
          show-overflow
          keep-source
          ref="zTable"
          row-id="id"
          :print-config="{}"
          :export-config="{}"
          :tree-config="{ transform: true, rowField: 'id', parentField: 'parentId' }"
          :data="interfaceData.headerData"
        >
          <vxe-column field="attribute" width="300" title="Thuộc tính" tree-node :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.attribute" type="text"></vxe-input>
            </template>
          </vxe-column>
          <vxe-column field="value" title="Giá trị thông số" :edit-render="{}">
            <template #default="{ row }">
              <vxe-input v-model="row.value" type="text"></vxe-input>
            </template>
          </vxe-column>
          <vxe-column title="Thao tác" width="100">
            <template #default="{ row }">
              <vxe-button
                type="text"
                v-if="['array', 'object'].includes(row.type)"
                status="primary"
                v-db-click
                @click="insertRow(row, 'zTable')"
                >Chèn</vxe-button
              >
              <vxe-button type="text" status="primary" v-db-click @click="removeRow(row, 'zTable')">Xóa</vxe-button>
            </template>
          </vxe-column>
        </vxe-table>
        <el-button class="mt10" type="primary" v-db-click @click="insertEvent('zTable')">Thêm thông số</el-button>
      </div>
    </div>
    <div class="res mt10 mb10" v-if="codes">
      <MonacoEditor :codes="codes" :readOnly="true" />
    </div>
  </div>
</template>

<script>
import request from './request';
import MonacoEditor from './components/MonacoEditor.vue';
import vuedraggable from 'vuedraggable';
import { getCookies } from '@/libs/util';

function requestMethod(url, method, params, data, headerItem) {
  return request({
    url,
    method,
    params,
    data,
    headerItem,
  });
}
export default {
  components: { MonacoEditor },
  props: {
    formValidate: {
      type: Object,
      default: () => {
        return {};
      },
    },
    requestTypeList: {
      type: Array,
      default: () => {
        return [];
      },
    },
    typeList: {
      type: Array,
      default: () => {
        return [];
      },
    },
    apiType: {
      type: String,
      default: 'adminapi',
    },
  },
  data() {
    return {
      bodyType: 'form-data',
      interfaceData: undefined,
      paramsType: 'Params',
      editor: '', //Đối tượng trình soạn thảo hiện tại
      codes: '',
      jsonBody: '',
    };
  },
  created() {
    this.interfaceData = this.formValidate;
    this.interfaceData.request_body = JSON.parse(JSON.stringify(this.interfaceData.request));
  },
  mounted() {
    if (!this.$refs.zTable.getTableData().tableData.length && this.apiType == 'adminapi') {
      this.insertEvent('zTable', {
        attribute: 'Authori-Zation',
        value: 'Bearer ' + getCookies('token'),
      });
      // this.insertEvent('zaTable');
    } else {
      if (this.interfaceData.header) {
        this.interfaceData.header.forEach((item, index) => {
          this.insertEvent('zTable', {
            attribute: item.attribute || '',
            value: item.value || '',
          });
        });
      }
    }
  },
  methods: {
    async handleChange(e, row, type) {
      if (e.value !== 'array' && e.value !== 'object') {
        if (row.children.length) {
          let arr = this.$refs[type].getTableData().tableData;
          let id = row.children[0].parentId;
          const $table = this.$refs[type];
          for (let i = 0; i < arr.length; i++) {
            if (arr[i].parentId == id) {
              await $table.remove(arr[i]);
            }
          }
        }
      }
    },
    insertCopy() {
      this.$copyText(this.codes)
        .then((message) => {
          this.$message.success('Sao chép thành công');
        })
        .catch((err) => {
          this.$message.error('Sao chép thất bại');
        });
    },
    async requestData() {
      let url, method, params, body, headers;
      url = this.apiType + '/' + this.interfaceData.path;
      method = this.interfaceData.method;
      params = this.filtersData((await this.$refs.xTable.getTableData().tableData) || []);
      body =
        this.bodyType === 'json'
          ? this.jsonBody
          : this.filtersData((await this.$refs.yTable.getTableData().tableData) || []);
      let h = this.filtersData((await this.$refs.zTable.getTableData().tableData) || []);
      headers = h;
      this.codes = '';
      requestMethod(url, method, params, body, headers)
        .then((res) => {
          if (!res) return this.$message.error('Lỗi API');
          this.codes = JSON.stringify(res);
        })
        .catch((err) => {
          if (!err) return this.$message.error('Lỗi API');
          this.codes = JSON.stringify(err);
        });
    },
    filtersData(arr) {
      try {
        let x = {};
        arr.map((e) => {
          if (!e.parentId) {
            for (let i in e) {
              if (i == 'attribute') {
                if (e.type === 'object') {
                  let obj = {};

                  e.children.map((item, index) => {
                    obj = this.filtersObj(item, 1);
                  });
                  x[e[i]] = obj;
                } else if (e.type !== 'array') {
                  x[e[i]] = e.value || '';
                } else {
                  let arr = [];
                  e.children.map((item, index) => {
                    arr[index] = this.filtersObj(item);
                  });
                  x[e[i]] = arr;
                }
              }
            }
          }
        });
        return x;
      } catch (error) {
        console.log(error);
      }
    },
    // type 1 là thuộc tính obj
    filtersObj(obj, type) {
      let x = {};
      for (let i in obj) {
        if (i == 'attribute') {
          if (obj.type === 'object') {
            let oj = {};
            obj.children.map((item, index) => {
              oj[obj.attribute] = this.filtersObj(item);
            });
            x = oj;
          } else if (obj.type !== 'array') {
            if (type) {
              x[obj.attribute] = obj.value || '';
            } else {
              x[obj[i]] = obj.value || '';
            }
          } else {
            let arr = [];
            obj.children.map((item, index) => {
              arr[index] = this.filtersObj(item);
            });
            x[obj[i]] = arr;
          }
        }
      }
      return x;
    },
    changeTab(name) {
      // if (name === 'Header') {
      //   if (!this.$refs.zTable.getTableData().tableData.length) {
      //     this.insertEvent('zTable', {
      //       attribute: 'Authori-Zation',
      //       value: 'Bearer ' + getCookies('token'),
      //     });
      //     this.insertEvent('zaTable');
      //   }
      // }
    },
    async insertEvent(type, d) {
      const $table = this.$refs[type];
      let newRow;
      if (type == 'xTable') {
        newRow = {
          attribute: '',
          type: 'string',
          must: 0,
          value: '',
          trip: '',
        };
      } else if (type == 'yTable') {
        newRow = {
          attribute: '',
          type: 'string',
          value: '',
          must: 0,
          trip: '',
        };
      } else if (type == 'zTable') {
        newRow = {
          attribute: '',
          type: '',
          value: '',
          trip: '',
        };
        newRow = { ...newRow, ...d };
      } else if (type == 'zaTable') {
        newRow = {
          attribute: 'token',
          type: 'string',
          value: '',
          must: 0,
          trip: '',
        };
      } else {
        newRow = {
          code: '',
          value: '',
          solution: '',
        };
      }
      const { row: data } = await $table.insertAt(newRow, -1);
      await $table.setActiveCell(data, 'name');
    },
    async insertRow(currRow, type) {
      const $table = this.$refs[type];
      // Nếu là null thì chèn vào đỉnh của nút đích
      // Nếu là -1 thì chèn vào đáy của nút đích
      // Nếu là row thì chèn vào vị trí dòng đó của nút đích
      let record;
      if (type == 'xTable') {
        record = {
          attribute: '',
          type: 'string',
          must: 0,
          value: '',
          trip: '',
          id: Date.now(),
          parentId: currRow.id, // Cần chỉ định nút cha, tự động chèn vào nút đó
        };
      } else {
        record = {
          code: '',
          value: '',
          solution: '',
          id: Date.now(),
          parentId: currRow.id, // Cần chỉ định nút cha, tự động chèn vào nút đó
        };
      }
      const { row: newRow } = await $table.insertAt(record, -1);
      await $table.setTreeExpand(currRow, true); // Mở rộng nút cha
      await $table.setActiveRow(newRow); // Chèn nút con
    },
    async removeRow(row, type) {
      const $table = this.$refs[type];
      await $table.remove(row);
    },
  },
};
</script>
<style>
.vxe-select--panel.is--transfer {
  z-index: 99999 !important;
}
</style>
<style lang="scss" scoped>
.content {
  padding: 12px;
  .head {
    display: flex;
    align-items: center;
    .item {
      display: flex;
      align-items: center;
      margin-bottom: 12px;
      font-size: 14px;
      .title {
        margin-right: 14px;
      }
    }
  }
}
.copy-btn {
  display: flex;
  justify-content: right;
}
::v-deep .monaco-editor {
  min-height: 700px;
}
</style>
