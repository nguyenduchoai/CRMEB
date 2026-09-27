<template>
  <div>
    <div class="main">
      <el-card :bordered="false" shadow="never" class="ivu-mt mr20 card-tree">
        <div class="tree">
          <div class="main-btn">
            <el-button class="mb10" type="primary" v-db-click @click="clickMenu(4)" long>Thêm danh mục</el-button>
          </div>

          <vue-tree-list
            class="tree-list"
            @change-name="onChangeName"
            @delete-node="onDel"
            :model="treeData"
            default-tree-node-name="Thư mục mặc định"
            default-leaf-node-name="Tên API mặc định"
            v-bind:default-expanded="true"
          >
            <template v-slot:leafNameDisplay="slotProps">
              <div></div>
              <div
                class="tree-node"
                :class="{ node: slotProps.model.method, open: formValidate.id == slotProps.model.id }"
                v-db-click
                @click.stop="onClick(slotProps.model)"
              >
                <span class="" :class="{ open: formValidate.id == slotProps.model.id }">{{
                  slotProps.model.name
                }}</span>
                <el-dropdown
                  size="small"
                  @command="
                    (name) => {
                      clickMenu(name, slotProps.model);
                    }
                  "
                >
                  <span class="el-dropdown-link">
                    <i class="el-icon-arrow-down el-icon--right"></i>
                  </span>
                  <template slot="dropdown">
                    <el-dropdown-menu>
                      <el-dropdown-item command="1" v-if="!slotProps.model.method">Thêm API</el-dropdown-item>
                      <el-dropdown-item command="2" v-if="!slotProps.model.method">Sửa tên danh mục</el-dropdown-item>
                      <el-dropdown-item command="3">Xóa</el-dropdown-item>
                    </el-dropdown-menu>
                  </template>
                </el-dropdown>
              </div>
            </template>
            <!-- Tạo thư mục mới -->
            <span class="icon" slot="addTreeNodeIcon"></span>
            <span class="icon" slot="addLeafNodeIcon"></span>
            <span class="icon" slot="editNodeIcon"> </span>
            <span class="icon" slot="delNodeIcon"></span>
            <template v-slot:treeNodeIcon="slotProps">
              <span
                v-if="slotProps.model.method"
                class="req-method"
                :style="{
                  color: methodsColor(slotProps.model.method),
                  'font-weight': slotProps.model.id == formValidate.id ? '500' : '500',
                }"
                >{{ slotProps.model.method == 'delete' ? 'DEL' : slotProps.model.method || '' }}</span
              >

              <!-- <span v-if="slotProps.model.method"></span> -->
            </template>
          </vue-tree-list>
        </div>
      </el-card>
      <el-card :bordered="false" shadow="never" class="ivu-mt right-card">
        <div class="data">
          <div class="eidt-sub">
            <div class="name">
              {{ formValidate.name }}
            </div>
            <div>
              <!-- <el-button type="primary" class="submission mr20" v-db-click @click="debugging()">Gỡ lỗi</el-button> -->
              <el-button
                v-if="formValidate.id"
                type="primary"
                class="submission mr20"
                v-db-click
                @click="isEdit = !isEdit"
                >{{ isEdit ? 'Quay lại' : 'Sửa' }}</el-button
              >
              <el-button
                v-if="isEdit"
                type="primary"
                class="submission"
                v-db-click
                @click="handleSubmit('formValidate')"
                >Lưu</el-button
              >
            </div>
          </div>
          <el-form
            class="formValidate mt20"
            ref="formValidate"
            :rules="ruleValidate"
            :model="formValidate"
            label-width="100px"
            :label-position="labelPosition"
            @submit.native.prevent
          >
            <el-row :gutter="24">
              <el-col :span="24">
                <div class="title">Thông tin API</div>
                <el-form-item label="Tên API:" prop="name">
                  <el-input
                    v-if="isEdit"
                    class="perW20"
                    type="text"
                    :rows="4"
                    v-model.trim="formValidate.name"
                    placeholder="Vui lòng nhập"
                  />
                  <span v-else>{{ formValidate.name || '' }}</span>
                </el-form-item>
                <el-form-item label="Loại yêu cầu:" prop="name">
                  <el-select v-if="isEdit" v-model="formValidate.method" style="width: 120px">
                    <el-option
                      v-for="(item, index) in requestTypeList"
                      :key="index"
                      :value="item.value"
                      :label="item.label"
                    ></el-option>
                  </el-select>
                  <span v-else class="req-method" :style="'background-color:' + methodColor">{{
                    formValidate.method || ''
                  }}</span>
                </el-form-item>
                <el-form-item label="Mô tả chức năng:" prop="name">
                  <el-input
                    v-if="isEdit"
                    class="perW20"
                    type="textarea"
                    :rows="4"
                    v-model.trim="formValidate.describe"
                    placeholder="Vui lòng nhập"
                  />
                  <span v-else class="text-area">{{ formValidate.describe || '' }}</span>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row :gutter="24">
              <el-col :span="24">
                <div class="title">Cách gọi</div>
                <el-form-item label="Nội dung gọi:" prop="url">
                  <el-input
                    v-if="isEdit"
                    class="perW20"
                    type="text"
                    :rows="4"
                    v-model.trim="formValidate.url"
                    placeholder="Vui lòng nhập"
                  />
                  <span v-else>{{ formValidate.url || '' }}</span>
                </el-form-item>
                <el-form-item label="Tham số yêu cầu:">
                  <vxe-table
                    resizable
                    show-overflow
                    keep-source
                    ref="xTable"
                    row-id="id"
                    :print-config="{}"
                    :export-config="{}"
                    v-loading="loading"
                    :tree-config="{ transform: true, rowField: 'id', parentField: 'parentId' }"
                    :data="formValidate.request_params"
                  >
                    <!-- <vxe-column type="checkbox" width="60"></vxe-column> -->
                    <vxe-column field="attribute" width="300" title="Thuộc tính" tree-node :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.attribute" type="text"></vxe-input>
                        <span v-else>{{ row.attribute || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column field="type" title="Loại" width="200" :edit-render="{}">
                      <template #default="{ row }">
                        <!-- <vxe-select v-if="isEdit" v-model="row.type" type="text" :optionGroups="typeList"></vxe-select> -->
                        <vxe-select v-if="isEdit" v-model="row.type" transfer>
                          <vxe-option
                            v-for="item in typeList"
                            :key="item.value"
                            :value="item.value"
                            :label="item.label"
                          ></vxe-option>
                        </vxe-select>
                        <span v-else>{{ row.type || '' }}</span>

                        <!-- <vxe-select v-model="row.type">
                      <vxe-option v-for="num in 12" :key="num" :value="num" :label="num"></vxe-option>
                    </vxe-select> -->
                      </template>
                    </vxe-column>
                    <vxe-column field="must" title="Bắt buộc" width="100" :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-checkbox
                          v-if="isEdit"
                          v-model="row.must"
                          :unchecked-value="'0'"
                          :checked-value="'1'"
                        ></vxe-checkbox>
                        <span v-else>{{ row.must == '1' ? 'Có' : 'Không' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column field="trip" title="Mô tả" :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.trip" type="text"></vxe-input>
                        <span v-else>{{ row.trip || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column title="Thao tác" width="200" v-if="isEdit">
                      <template #default="{ row }">
                        <vxe-button
                          type="text"
                          v-if="row.type === 'array'"
                          status="primary"
                          v-db-click
                          @click="insertRow(row, 'xTable')"
                          >Chèn</vxe-button
                        >
                        <vxe-button type="text" status="primary" v-db-click @click="removeRow(row, 'xTable')"
                          >Xóa</vxe-button
                        >
                      </template>
                    </vxe-column>
                  </vxe-table>

                  <el-button class="mt10" v-if="isEdit" type="primary" v-db-click @click="insertEvent('xTable')"
                    >Thêm thông số</el-button
                  >
                </el-form-item>
                <el-form-item label="Tham số trả về:">
                  <vxe-table
                    resizable
                    show-overflow
                    keep-source
                    ref="resTable"
                    row-id="id"
                    :print-config="{}"
                    :export-config="{}"
                    :loading="loading"
                    :tree-config="{ transform: true, rowField: 'id', parentField: 'parentId' }"
                    :data="formValidate.return_params"
                  >
                    <!-- <vxe-column type="checkbox" width="60"></vxe-column> -->
                    <vxe-column field="attribute" title="Thuộc tính" width="300" tree-node :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.attribute" type="text"></vxe-input>
                        <span v-else>{{ row.attribute || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column field="type" title="Loại" width="200" :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-select v-if="isEdit" v-model="row.type" transfer>
                          <vxe-option
                            v-for="item in typeList"
                            :key="item.value"
                            :value="item.value"
                            :label="item.label"
                          ></vxe-option>
                        </vxe-select>
                        <span v-else>{{ row.type || '' }}</span>
                      </template>
                    </vxe-column>
                    <!-- <vxe-column field="type" title="Bắt buộc" :edit-render="{}">
                  <template #default="{ row }">
                    <vxe-checkbox v-model="row.must" :unchecked-value="0" :checked-value="1"></vxe-checkbox
                    >{{ row.must }}
                  </template>
                </vxe-column> -->
                    <vxe-column field="trip" title="Mô tả" :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.trip" type="text"></vxe-input>
                        <span v-else>{{ row.trip || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column title="Thao tác" width="200" v-if="isEdit">
                      <template #default="{ row }">
                        <vxe-button
                          type="text"
                          v-if="row.type === 'array'"
                          status="primary"
                          v-db-click
                          @click="insertRow(row, 'resTable')"
                          >Chèn</vxe-button
                        >
                        <vxe-button type="text" status="primary" v-db-click @click="removeRow(row, 'resTable')"
                          >Xóa</vxe-button
                        >
                      </template>
                    </vxe-column>
                  </vxe-table>
                  <el-button class="mt10" v-if="isEdit" type="primary" v-db-click @click="insertEvent('resTable')"
                    >Thêm thông số</el-button
                  >
                </el-form-item>
              </el-col>
            </el-row>
            <el-row :gutter="24">
              <el-col :span="24">
                <div class="title">Ví dụ gọi API</div>
                <el-form-item label="Ví dụ dữ liệu yêu cầu:" prop="request_example">
                  <el-input
                    v-if="isEdit"
                    class="perW20"
                    type="textarea"
                    :rows="4"
                    v-model.trim="formValidate.request_example"
                    placeholder="Vui lòng nhập"
                  />
                  <span v-else class="text-area">{{ formValidate.request_example || '' }}</span>
                </el-form-item>
                <el-form-item label="Ví dụ dữ liệu trả về:" prop="return_example">
                  <el-input
                    v-if="isEdit"
                    class="perW20"
                    type="textarea"
                    :rows="4"
                    v-model.trim="formValidate.return_example"
                    placeholder="Vui lòng nhập"
                  />
                  <span v-else class="text-area">{{ formValidate.return_example || '' }}</span>
                </el-form-item>
                <el-form-item label="Mã lỗi:">
                  <vxe-table
                    resizable
                    show-overflow
                    keep-source
                    ref="codeTable"
                    row-id="id"
                    :print-config="{}"
                    :export-config="{}"
                    :loading="loading"
                    :tree-config="{ transform: true, rowField: 'id', parentField: 'parentId' }"
                    :data="formValidate.error_code"
                  >
                    <!-- <vxe-column type="checkbox" width="60"></vxe-column> -->
                    <vxe-column field="code" title="Mã lỗi" tree-node :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.code" type="text"></vxe-input>
                        <span v-else>{{ row.code || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column field="value" title="Giá trị mã lỗi" :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.value" type="text"></vxe-input>
                        <span v-else>{{ row.value || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column field="solution" title="Cách khắc phục" :edit-render="{}">
                      <template #default="{ row }">
                        <vxe-input v-if="isEdit" v-model="row.solution" type="text"></vxe-input>
                        <span v-else>{{ row.solution || '' }}</span>
                      </template>
                    </vxe-column>
                    <vxe-column title="Thao tác" v-if="isEdit">
                      <template #default="{ row }">
                        <vxe-button type="text" status="primary" v-db-click @click="removeRow(row, 'codeTable')"
                          >Xóa</vxe-button
                        >
                      </template>
                    </vxe-column>
                  </vxe-table>
                  <el-button class="mt10" v-if="isEdit" type="primary" v-db-click @click="insertEvent('codeTable')"
                    >Thêm thông số</el-button
                  >
                </el-form-item>
              </el-col>
            </el-row>
            <!-- <el-row :gutter="24" >
              <el-col :span="24">
                <el-form-item>
                  <el-button type="primary" class="submission" v-db-click @click="handleSubmit('formValidate')">Lưu</el-button>
                </el-form-item>
              </el-col>
            </el-row> -->
          </el-form>
        </div>
        <!-- <div v-else class="nothing">
          <div class="box" v-db-click @click="clickMenu(4)">
            <div class="icon">
              <Icon type="ios-folder" />
            </div>
            <div class="text">Tạo file mới</div>
          </div>
          <div class="box" v-db-click @click="clickMenu(1)">
            <div class="icon">
              <Icon type="logo-linkedin" />
            </div>
            <div class="text">Tạo API mới</div>
          </div>
        </div> -->
      </el-card>
    </div>
    <el-dialog :visible.sync="nameModal" width="470px" title="Tên nhóm" @on-ok="asyncOK">
      <label>Tên nhóm:</label>
      <el-input v-model="value" placeholder="Vui lòng nhập tên nhóm" style="width: 85%" />
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="nameModal = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="asyncOK">Xác nhận</el-button>
      </span>
    </el-dialog>
    <el-dialog :visible.sync="debuggingModal" :title="formValidate.name" width="1000px">
      <debugging
        v-if="debuggingModal"
        :formValidate="formValidate"
        :typeList="typeList"
        :requestTypeList="requestTypeList"
      />
    </el-dialog>
  </div>
</template>

<script>
import { interfaceList, interfaceDet, interfaceSave, interfaceEditName, interfaceDel } from '@/api/systemOutAccount';
import { VueTreeList, Tree, TreeNode } from 'vue-tree-list';
import debugging from './debugging.vue';
import { mapState } from 'vuex';
import { storageStatusApi } from '@api/setting';
export default {
  name: 'systemOutInterface',
  components: {
    VueTreeList,
    debugging,
  },
  data() {
    return {
      value: '',
      isEdit: false,
      nameModal: false,
      debuggingModal: false,
      formValidate: {},
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      ruleValidate: {
        title: [{ message: 'Vui lòng nhập mô tả hợp lệ (không quá 200 ký tự)', trigger: 'blur', max: 200 }],
      },
      loading: false,

      typeList: [
        {
          value: 'string',
          label: 'String',
        },
        {
          value: 'array',
          label: 'Array',
        },
        {
          value: 'number',
          label: 'Number',
        },
        {
          value: 'boolean',
          label: 'Boolean',
        },
        {
          value: 'null',
          label: 'Null',
        },
        {
          value: 'any',
          label: 'Any',
        },
      ],
      requestTypeList: [
        {
          value: 'get',
          label: 'get',
        },
        {
          value: 'post',
          label: 'post',
        },
        {
          value: 'delete',
          label: 'delete',
        },
        {
          value: 'put',
          label: 'put',
        },
        {
          value: 'options',
          label: 'options',
        },
      ],
      contextData: null, //Đối tượng dữ liệu được tạo ra khi nhấp chuột phải vào điều hướng bên trái
      treeData: undefined,
      buttonProps: {
        type: 'default',
        size: 'small',
      },
      methodColor: '#fff',
    };
  },
  watch: {
    ['formValidate.method']: {
      deep: true,
      handler(newVal, oldVal) {
        let method = newVal.toUpperCase();
        if (method == 'GET') {
          this.methodColor = '#61affe';
        } else if (method == 'POST') {
          this.methodColor = '#49cc90';
        } else if (method == 'PUT') {
          this.methodColor = '#fca130';
        } else if (method == 'DELETE') {
          this.methodColor = '#f93e3e';
        }
      },
    },
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '50px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  created() {
    this.getInterfaceList('one');
  },
  methods: {
    debugging() {
      this.debuggingModal = true;
    },
    onClicksss(e) {},
    methodsColor(newVal) {
      let method = newVal.toUpperCase();
      if (method == 'GET') {
        return '#61affe';
      } else if (method == 'POST') {
        return '#49cc90';
      } else if (method == 'PUT') {
        return '#fca130';
      } else if (method == 'DELETE') {
        return '#f93e3e';
      }
    },
    insertBefore(params) {},
    insertAfter(params) {},
    moveInto(params) {},
    async addTableData() {
      const { row: data } = await $table.insertAt(newRow, -1);
      await $table.setActiveCell(data, 'name');
    },
    getInterfaceList(disk_type) {
      interfaceList()
        .then((res) => {
          res.data[0].expand = false;
          this.treeData = new Tree(res.data);

          if (res.data.length) {
            if (res.data[0].children.length) {
              this.onClick(res.data[0].children[0]);
            }
          }
        })
        .catch((err) => {
          this.$message.error(err);
        });
    },
    onClick(params) {
      if (params.method) {
        this.isEdit = false;
        interfaceDet(params.id)
          .then((res) => {
            this.formValidate = res.data;
          })
          .catch((err) => {
            this.$message.error(err);
          });
      }
    },
    async handleSubmit() {
      if (!this.formValidate.name) {
        return this.$message.warning('Vui lòng nhập tên API');
      } else if (!this.formValidate.method) {
        return this.$message.warning('Vui lòng chọn loại yêu cầu');
      } else if (!this.formValidate.url) {
        return this.$message.warning('Vui lòng nhập cách gọi');
      }
      this.formValidate.request_params = await this.$refs.xTable.getTableData().tableData;
      this.formValidate.return_params = await this.$refs.resTable.getTableData().tableData;
      this.formValidate.error_code = await this.$refs.codeTable.getTableData().tableData;
      await interfaceSave(this.formValidate)
        .then((res) => {
          this.isEdit = false;
          this.$message.success(res.msg);
          this.getInterfaceList();
        })
        .catch((err) => {
          this.$message.error(err);
        });
    },
    async insertEvent(type) {
      const $table = this.$refs[type];
      let newRow;
      if (type == 'xTable') {
        newRow = {
          attribute: '',
          type: '',
          must: 0,
          trip: '',
        };
      } else if (type == 'resTable') {
        newRow = {
          attribute: '',
          type: '',
          trip: '',
        };
      } else {
        newRow = {
          code: '',
          value: '',
          solution: '',
        };
      }
      // $table.insert(newRow).then(({ row }) => $table.setEditRow(row, -1));
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
          type: '',
          must: 0,
          trip: '',
          id: Date.now(),
          parentId: currRow.id, // Cần chỉ định nút cha, tự động chèn vào nút đó
        };
      } else if (type == 'resTable') {
        record = {
          attribute: '',
          type: '',
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
    // Sửa tên
    add() {
      this.value = '';
      this.formValidate.id = 0;
      this.nameModal = true;
    },
    // Bấm vào menu
    clickMenu(name, params) {
      if (name == 1) {
        this.formValidate = {};
        this.formValidate.pid = params ? params.id : 0;
        this.formValidate.id = 0;
        this.isEdit = true;
      } else if (name == 2) {
        this.value = params.name || '';
        this.formValidate.id = params ? params.id : 0;
        this.nameModal = true;
      } else if (name == 3) {
        this.onDel(params);
      } else if (name == 4) {
        this.add();
      }
    },

    addFac(params) {
      this.formValidate = {
        id: params ? params.id : 0,
      };
      this.isEdit = true;
    },
    asyncOK() {
      let data = {
        id: this.formValidate.id || 0,
        type: 0,
        name: this.value,
      };
      interfaceSave(data)
        .then((res) => {
          this.$message.success(res.msg);
          this.getInterfaceList();
        })
        .catch((err) => {
          this.$message.error(err);
        });
    },
    //Sự kiện nhấp chuột phải sidebar
    handleContextMenu(data, event, position) {
      position.left = Number(position.left.slice(0, -2)) + 75 + 'px';
      this.contextData = data;
    },
    handleContextCreateFolder() {},
    handleContextCreateFile() {},
    // Hiển thị tùy chỉnh
    renderContent(h, { root, node, data }) {
      let that = this;
      return h(
        'span',
        {
          style: {
            display: 'inline-block',
            width: '100%',
          },
        },
        [
          h('span', [
            h(resolveComponent('Icon'), {
              type: 'ios-paper-outline',
              style: {
                marginRight: '8px',
              },
            }),
            h('span', data.title),
          ]),
          h(
            'span',
            {
              style: {
                display: 'inline-block',
                float: 'right',
                marginRight: '32px',
              },
            },
            [
              h(resolveComponent('Button'), {
                ...this.buttonProps,
                icon: 'ios-add',
                style: {
                  marginRight: '8px',
                },
                onClick: () => {
                  this.append(data);
                },
              }),
              h(resolveComponent('Button'), {
                ...this.buttonProps,
                icon: 'ios-remove',
                onClick: () => {
                  this.remove(root, node, data);
                },
              }),
            ],
          ),
        ],
      );
    },
    /**
     * Sự kiện nhấp sidebar
     * @param {Object} data
     */
    clickDir(data, root, node) {
      let that = this;
      that.navItem = data;
      that.pathname = data.pathname;
    },
    append(data) {
      const children = data.children || [];
      children.push({
        title: 'appended node',
        expand: true,
      });
      this.$set(data, 'children', children);
    },
    remove(root, node, data) {
      const parentKey = root.find((el) => el === node).parent;
      const parent = root.find((el) => el.nodeKey === parentKey).node;
      const index = parent.children.indexOf(data);
      parent.children.splice(index, 1);
    },
    onMouseOver(root, node, data, e, d) {
      console.log(root, node, data);
    },
    //
    onDel(node) {
      this.$msgbox({
        title: 'Thông báo',
        message: 'Sau khi xóa sẽ không thể khôi phục, vui lòng xác nhận trước khi xóa!',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Xác nhận',
        iconClass: 'el-icon-warning',
        confirmButtonClass: 'btn-custom-cancel',
      })
        .then(() => {
          interfaceDel(node.id)
            .then((res) => {
              this.$message.success(res.msg);
              node.remove();
            })
            .catch((err) => {
              this.$message.error(err);
            });
        })
        .catch(() => {});
    },

    onChangeName(params) {
      if (params.eventType == 'blur') {
        let data = {
          name: params.newName,
          id: params.id,
        };
        interfaceEditName(data)
          .then((res) => {
            this.$message.success(res.msg);
          })
          .catch((err) => {
            this.$message.error(err);
          });
      }
    },

    onAddNode(params) {
      // this.$router.push({
      //   path: '/admin/setting/system_out_interface/add',
      //   query: {
      //     pid: params.pid,
      //   },
      // });
    },

    addNode() {
      var node = new TreeNode({ name: 'new node', isLeaf: false });
      if (!this.data.children) this.data.children = [];
      this.data.addChildren(node);
    },

    getNewTree() {
      var vm = this;
      function _dfs(oldNode) {
        var newNode = {};

        for (var k in oldNode) {
          if (k !== 'children' && k !== 'parent') {
            newNode[k] = oldNode[k];
          }
        }

        if (oldNode.children && oldNode.children.length > 0) {
          newNode.children = [];
          for (var i = 0, len = oldNode.children.length; i < len; i++) {
            newNode.children.push(_dfs(oldNode.children[i]));
          }
        }
        return newNode;
      }

      vm.newTree = _dfs(vm.data);
    },
  },
};
</script>

<style lang="scss" scoped>
.reset {
  margin-left: 10px;
}
.card-tree {
  height: 72px;
  box-sizing: border-box;
  overflow-x: scroll; /* Đặt overflow scroll */
  white-space: nowrap;
  overflow-y: hidden;
  /* Ẩn thanh cuộn */
  scrollbar-width: none; /* firefox */
  -ms-overflow-style: none; /* IE 10+ */
}
.card-tree::-webkit-scrollbar {
  display: none; /* Chrome Safari */
}
.main {
  width: 100%;
  display: flex;
  .main-btn {
  }
  .card-tree {
    width: 270px;
    height: calc(100vh - 115px);
    overflow-y: scroll;
  }
  ::v-deep .tree {
    .tree-list {
      margin-left: 10px;
    }
    .vtl-caret {
      padding-right: 2px;
    }
    .req-method {
      display: block;
      padding: 0px 2px;
      font-size: 12px;
      margin-right: 5px;
      border-radius: 4px;

      text-transform: uppercase;
    }
    .tree-node {
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;

      padding: 3px 7px 3px 0;
    }
    .node {
      padding: 7px 2px 7px 0px;
    }
    .open {
      font-weight: 500;
      color: #333;
    }
  }
  ::v-deep .vtl-node-main .vtl-operation {
    position: absolute;
    right: 20px;
  }
  ::v-deep .vtl-node-content {
    width: 100%;
  }
  .pop-menu {
    display: flex;
    justify-content: space-between;
  }
  ::v-deep .vtl-node-content .add {
    display: none;
    margin-right: 10px;
  }
  ::v-deep .vtl-node-content:hover .add {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    width: 20px;
    height: 20px;
  }
  ::v-deep .vtl-node-content:hover .add:hover {
    background-color: #fff;
    .pop-menu {
      font-size: 16px;
    }
  }
  ::v-deep .vtl-node-main {
    padding: 0;
  }
  ::v-deep .line1 {
    display: table-caption;
    white-space: nowrap;
    width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  ::v-deep .ivu-form-item {
    margin-bottom: 10px;
  }
  .right-card {
    flex: 1;
    max-height: calc(100vh - 115px);
    overflow-y: scroll;
  }
  .data {
    flex: 1;
    .req-method {
      text-transform: uppercase;
      border-radius: 4px;
      color: #fff;
      padding: 3px 7px;
    }
    .eidt-sub {
      display: flex;
      justify-content: space-between;
      .name {
        font-size: 20px;
        font-weight: 500;
      }
    }
    .title {
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 15px;
    }
    .perW20 {
      width: 500px;
    }
    .text-area {
      white-space: pre-wrap;
      word-break: break-word;
    }
  }
  ::v-deep .ivu-tree-title {
    width: 100% !important;
  }
  ::v-deep .vtl-tree-margin {
    margin-left: 5px;
  }
  ::v-deep .ivu-btn-icon-only.ivu-btn-small {
    width: 28px;
  }
  .nothing {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 800px;
    .box:hover {
      border: 1px solid pink;
    }
    .box {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      width: 150px;
      height: 200px;
      margin: 0 20px;
      border-radius: 10px;
      cursor: pointer;
      overflow: hidden;
      border: 1px solid #fff;
      .icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 150px;
        font-size: 40px;
        color: #2d8cf0;
        background: #f1f1f1;
      }
      .text {
        width: 100%;
        height: 50px;
        background: #ddd;
        text-align: center;
        line-height: 50px;
        font-size: 14px;
        font-weight: 500;
      }
    }
  }
}
</style>
