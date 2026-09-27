<template>
  <div>
    <el-card :bordered="false" :body-style="{ padding: '0 20px 20px' }">
      <el-tabs>
        <el-tab-pane label="Danh sách cơ sở dữ liệu">
          <!--          <el-card :bordered="false" shadow="never" class="tableBox">-->
          <div class="mb10">
            <!--              <span class="ivu-pl-8 mr10">Danh sách bảng cơ sở dữ liệu</span>-->
            <el-button v-db-click @click="getBackup">Sao lưu</el-button>
            <el-button v-db-click @click="getOptimize">Tối ưu bảng</el-button>
            <el-button v-db-click @click="getRepair">Sửa chữa bảng</el-button>
            <el-button v-db-click @click="exportData(1)">Xuất tệp</el-button>
          </div>
          <el-table
            ref="selection"
            :data="tabList2"
            v-loading="loading"
            empty-text="Chưa có dữ liệu"
            @select="onSelectTab"
            @select-all="onSelectTab"
            class="mt14"
          >
            <el-table-column type="selection" width="55"> </el-table-column>
            <el-table-column label="Tên bảng" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.name }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Ghi chú" min-width="100">
              <template slot-scope="scope">
                <div class="mark">
                  <div v-if="scope.row.is_edit" class="table-mark" v-db-click @click="isEditMark(scope.row)">
                    {{ scope.row.comment }}
                  </div>
                  <el-input ref="mark" v-else v-model="scope.row.comment" @blur="isEditBlur(scope.row, 0)"></el-input>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="Loại" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.engine }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Kích thước" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.data_length }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thời gian cập nhật" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.update_time }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Số dòng" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.rows }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thao tác" fixed="right" width="70">
              <template slot-scope="scope">
                <a v-db-click @click="Info(scope.row)">Chi tiết</a>
              </template>
            </el-table-column>
          </el-table>
          <!--          </el-card>-->
          <!-- Modal chi tiết-->
          <el-drawer
            :visible.sync="modals"
            :wrapperClosable="false"
            :size="740"
            :title="'[ ' + rows.name + ' ]' + rows.comment"
          >
            <el-table
              ref="selection"
              :data="tabList3"
              v-loading="loading2"
              empty-text="Chưa có dữ liệu"
              max-height="600"
              size="small"
            >
              <el-table-column label="Tên trường" min-width="100">
                <template slot-scope="scope">
                  <span>{{ scope.row.COLUMN_NAME }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Loại dữ liệu" min-width="100">
                <template slot-scope="scope">
                  <span>{{ scope.row.COLUMN_TYPE }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Giá trị mặc định" min-width="100">
                <template slot-scope="scope">
                  <span>{{ scope.row.COLUMN_DEFAULT }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Cho phép NULL" min-width="100">
                <template slot-scope="scope">
                  <span>{{ scope.row.IS_NULLABLE }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Tự động tăng" min-width="100">
                <template slot-scope="scope">
                  <span>{{ scope.row.EXTRA }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Ghi chú" min-width="100">
                <template slot-scope="scope">
                  <div class="mark">
                    <div v-if="scope.row.is_edit" class="table-mark" v-db-click @click="isEditMark(scope.row)">
                      {{ scope.row.COLUMN_COMMENT }}
                    </div>
                    <el-input
                      ref="mark"
                      v-else
                      v-model="scope.row.COLUMN_COMMENT"
                      @blur="isEditBlur(scope.row, 1)"
                    ></el-input>
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </el-drawer>
        </el-tab-pane>
        <el-tab-pane label="Danh sách sao lưu">
          <el-table
            ref="selection"
            :data="tabList"
            v-loading="loading3"
            empty-text="Chưa có dữ liệu"
            highlight-current-row
            size="small"
          >
            <el-table-column label="Tên bản sao lưu" min-width="200">
              <template slot-scope="scope">
                <span>{{ scope.row.filename }}</span>
              </template>
            </el-table-column>
            <el-table-column label="part" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.part }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Kích thước" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.size }}</span>
              </template>
            </el-table-column>
            <el-table-column label="compress" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.compress }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thời gian" min-width="100">
              <template slot-scope="scope">
                <span>{{ scope.row.backtime }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Thao tác" fixed="right" width="140">
              <template slot-scope="scope">
                <a v-db-click @click="ImportFile(scope.row)">Nhập</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="del(scope.row, 'Xóa bản sao lưu này', scope.$index)">Xóa</a>
                <el-divider direction="vertical"></el-divider>
                <a v-db-click @click="download(scope.row)">Tải xuống</a>
              </template>
            </el-table-column>
          </el-table>
        </el-tab-pane>
      </el-tabs>
    </el-card>
    <el-dialog :visible.sync="markModal" width="470px" title="Sửa ghi chú" @closed="cancel">
      <el-input v-model="mark"></el-input>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel">Hủy</el-button>
        <el-button type="primary" v-db-click @click="ok">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import {
  backupListApi,
  backupReadListApi,
  backupBackupApi,
  backupOptimizeApi,
  backupRepairApi,
  filesListApi,
  filesDownloadApi,
  filesImportApi,
  updateMark,
} from '@/api/system';
import Setting from '@/setting';
import { getCookies } from '@/libs/util';

export default {
  name: 'systemDatabackup',
  data() {
    return {
      modals: false,
      loading: false,
      tabList: [],
      tabList2: [],
      selectionList: [],
      tabList3: [],
      rows: {},
      dataList: {},
      loading2: false,
      loading3: false,
      markModal: false,
      mark: '',
      header: {},
      Token: '',
      changeMarkData: {
        table: '',
        mark: '',
        type: '',
        field: '',
      },
    };
  },
  computed: {
    fileUrl() {
      const search = '/adminapi/';
      const start = Setting.apiBaseURL.indexOf(search);
      return Setting.apiBaseURL.substring(0, start); // Cắt chuỗi
    },
  },
  created() {
    this.getToken();
    this.getList();
    this.getfileList();
  },
  methods: {
    editMark(row, type) {
      this.changeMarkData.table = row.name || row.TABLE_NAME;
      this.changeMarkData.field = row.COLUMN_NAME || '';
      this.changeMarkData.type = row.COLUMN_TYPE || '';
      this.changeMarkData.is_field = type;
      this.markModal = true;
    },
    ok() {
      this.changeMarkData.mark = this.mark;
      updateMark(this.changeMarkData).then((res) => {
        this.$message.success(res.msg);
        if (this.changeMarkData.is_field) {
          this.Info({ name: this.changeMarkData.table, comment: this.rows.comment });
        } else {
          this.getList();
        }
      });
    },
    cancel() {
      this.mark = '';
    },
    // Nhập
    ImportFile(row) {
      filesImportApi({
        part: row.part,
        time: row.time,
      })
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getfileList();
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Xóa bảng lịch sử backup
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `system/backup/del_file`,
        method: 'DELETE',
        ids: {
          filename: row.time,
        },
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tabList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Token trong header khi tải lên
    getToken() {
      this.Token = getCookies('token');
    },
    download(row) {
      let data = {
        time: row.time,
      };
      filesDownloadApi(data)
        .then((res) => {
          if (res.data.key) {
            window.open(Setting.apiBaseURL + '/download?key=' + res.data.key);
          }
        })
        .catch((res) => {
          this.$message.error(res);
        });
    },
    // Xuất bảng lịch sử sao lưu
    exportData() {
      const columns = this.columns.slice(1, 7);
      this.$refs.selection.exportCsv({
        filename: 'Xuất',
        columns: columns,
        data: this.tabList2,
      });
    },
    // Chọn tất cả
    onSelectTab(selection) {
      this.selectionList = selection;
      let tables = [];
      this.selectionList.map((item) => {
        tables.push(item.name);
      });
      this.dataList = {
        tables: tables.join(','),
      };
    },
    // Bảng backup
    getBackup() {
      if (this.selectionList.length === 0) {
        return this.$message.warning('Vui lòng chọn bảng');
      }
      backupBackupApi(this.dataList)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getfileList();
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Danh sách bảng lịch sử sao lưu
    getfileList() {
      this.loading3 = true;
      filesListApi()
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
          this.loading3 = false;
        })
        .catch((res) => {
          this.loading3 = false;
          this.$message.error(res.msg);
        });
    },
    // Tối ưu bảng
    getOptimize() {
      if (this.selectionList.length === 0) {
        return this.$message.warning('Vui lòng chọn bảng');
      }
      backupOptimizeApi(this.dataList)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa chữa bảng
    getRepair() {
      if (this.selectionList.length === 0) {
        return this.$message.warning('Vui lòng chọn bảng');
      }
      backupRepairApi(this.dataList)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách cơ sở dữ liệu
    getList() {
      this.loading = true;
      backupListApi()
        .then(async (res) => {
          let data = res.data;
          this.tabList2 = data.list;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Chi tiết
    Info(row) {
      this.rows = row;
      this.modals = true;
      this.loading2 = true;
      let data = {
        tablename: row.name,
      };
      backupReadListApi(data)
        .then(async (res) => {
          let data = res.data;
          this.tabList3 = data.list;
          this.loading2 = false;
        })
        .catch((res) => {
          this.loading2 = false;
          this.$message.error(res.msg);
        });
    },
    isEditMark(row) {
      row.is_edit = true;
      this.$nextTick((e) => {
        this.$refs.mark.focus();
      });
    },
    isEditBlur(row, type) {
      row.is_edit = false;
      this.changeMarkData.table = row.name || row.TABLE_NAME;
      this.changeMarkData.field = row.COLUMN_NAME || '';
      this.changeMarkData.type = row.COLUMN_TYPE || '';
      this.changeMarkData.is_field = type;
      this.changeMarkData.mark = type ? row.COLUMN_COMMENT : row.comment;

      updateMark(this.changeMarkData)
        .then((res) => {
          // this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .el-tabs__item {
  height: 54px !important;
  line-height: 54px !important;
}
.tableBox ::v-deep .ivu-table-header table {
  border: none !important;
}
.table-mark {
  cursor: text;
}
.table-mark:hover {
  border: 1px solid #c2c2c2;
  padding: 3px 5px;
}
.mark ::v-deep .ivu-input {
  background: #fff;
  border-radius: 0.39rem;
}
.mark ::v-deep .ivu-input,
.ivu-input:hover,
.ivu-input:focus {
  border: transparent;
  box-shadow: none;
}
</style>
