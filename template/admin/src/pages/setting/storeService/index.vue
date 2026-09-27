<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-row class="mb20">
        <el-col :span="24">
          <el-button v-auth="['setting-store_service-add']" type="primary" v-db-click @click="add" class="mr10"
            >Thêm nhân viên CSKH</el-button
          >
        </el-col>
      </el-row>
      <el-table
        :data="tableList"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện CSKH" min-width="90">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tên nhân viên CSKH" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.wx_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái CSKH" min-width="130">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeIsShow(scope.row)"
              size="large"
              active-text="Bật"
              inactive-text="Tắt"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian thêm" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa nhân viên CSKH', scope.$index)">Xóa</a>
            <el-divider direction="vertical" v-if="scope.row.status" />
            <a v-db-click @click="goChat(scope.row)" v-if="scope.row.status">Vào không gian làm việc</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="tableFrom.page"
          :limit.sync="tableFrom.limit"
          @pagination="getList"
        />
      </div>
    </el-card>

    <!--Lịch sử trò chuyện-->
    <el-dialog :visible.sync="modals3" title="Lịch sử trò chuyện" width="720px">
      <div v-if="isChat" class="modelBox">
        <el-table
          v-loading="loading3"
          highlight-current-row
          no-userFrom-text="Chưa có dữ liệu"
          no-filtered-userFrom-text="Không có kết quả phù hợp"
          :data="tableList3"
        >
          <el-table-column label="Tên người dùng" width="200">
            <template slot-scope="scope">
              <span>{{ scope.row.nickname }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Ảnh đại diện CSKH" min-width="90">
            <template slot-scope="scope">
              <div class="tabBox_img" v-viewer>
                <img v-lazy="scope.row.headimgurl" />
              </div>
            </template>
          </el-table-column>
          <el-table-column label="Thao tác" fixed="right" width="170">
            <template slot-scope="scope">
              <a v-db-click @click="look(scope.row)">Xem cuộc trò chuyện</a>
            </template>
          </el-table-column>
        </el-table>
        <div class="acea-row row-right page">
          <pagination
            v-if="total3"
            :total="total3"
            :page.sync="formValidate3.page"
            :limit.sync="formValidate3.limit"
            @pagination="getListRecord"
          />
        </div>
      </div>
      <div v-if="!isChat">
        <el-button type="primary" v-db-click @click="isChat = true">Quay lại lịch sử trò chuyện</el-button>
        <el-table
          v-loading="loading5"
          highlight-current-row
          no-userFrom-text="Chưa có dữ liệu"
          class="mt14"
          no-filtered-userFrom-text="Không có kết quả phù hợp"
          :data="tableList5"
        >
          <el-table-column label="Tên người dùng" min-width="200">
            <template slot-scope="scope">
              <span>{{ scope.row.nickname }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Ảnh đại diện người dùng" min-width="90">
            <template slot-scope="scope">
              <div class="tabBox_img" v-viewer>
                <img v-lazy="scope.row.avatar" />
              </div>
            </template>
          </el-table-column>
          <el-table-column label="Gửi tin nhắn" min-width="200">
            <template slot-scope="scope">
              <span>{{ scope.row.msn }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thời gian gửi" min-width="200">
            <template slot-scope="scope">
              <span>{{ scope.row.add_time }}</span>
            </template>
          </el-table-column>
        </el-table>
        <div class="acea-row row-right page">
          <pagination
            v-if="total5"
            :total="total5"
            :page.sync="formValidate5.page"
            :limit.sync="formValidate5.limit"
            @pagination="getChatlist"
          />
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { setCookies } from '@/libs/util';
import {
  kefuListApi,
  kefucreateApi,
  kefuaddApi,
  kefuAddApi,
  kefusetStatusApi,
  kefuEditApi,
  kefuRecordApi,
  kefuChatlistApi,
  kefuLogin,
} from '@/api/setting';
export default {
  name: 'index',
  filters: {
    typeFilter(status) {
      const statusMap = {
        wechat: 'Người dùng WeChat',
        routine: 'Người dùng Mini Program',
      };
      return statusMap[status];
    },
  },
  computed: {
    ...mapState('media', ['isMobile']),
    ...mapState('userLevel', ['categoryId']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  data() {
    return {
      isChat: true,
      formValidate3: {
        page: 1,
        limit: 15,
      },
      total3: 0,
      loading3: false,
      modals3: false,
      tableList3: [],
      formValidate5: {
        page: 1,
        limit: 15,
        uid: 0,
        to_uid: 0,
        id: 0,
      },
      total5: 0,
      loading5: false,
      tableList5: [],
      FromData: null,
      formValidate: {
        page: 1,
        limit: 15,
        data: '',
        type: '',
        nickname: '',
      },
      tableList2: [],
      modals: false,
      total: 0,
      tableFrom: {
        page: 1,
        limit: 15,
      },
      timeVal: [],
      fromList: {
        title: 'Chọn giờ',
        custom: true,
        fromTxt: [
          { text: 'Tất cả', val: '' },
          { text: 'Hôm nay', val: 'today' },
          { text: 'Hôm qua', val: 'yesterday' },
          { text: '7 ngày qua', val: 'lately7' },
          { text: '30 ngày qua', val: 'lately30' },
          { text: 'Tháng này', val: 'month' },
          { text: 'Năm nay', val: 'year' },
        ],
      },
      loading: false,
      tableList: [],
      loading2: false,
      total2: 0,
      addFrom: {
        uids: [],
      },
      selections: [],
      rows: {},
      rowRecord: {},
      eidtLoading: false,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    // Vào không gian làm việc
    goChat(item) {
      kefuLogin(item.id)
        .then((res) => {
          var url = '';
          if (res.data.token) {
            let expires = this.getExpiresTime(res.data.exp_time);
            setCookies('kefu_token', res.data.token, expires);
            setCookies('kefu_uuid', res.data.kefuInfo.uid, expires);
            setCookies('kefu_expires_time', res.data.exp_time, expires);
            setCookies('kefuInfo', res.data.kefuInfo, expires);
            if (this.$store.state.media.isMobile) {
              url = window.location.protocol + '//' + window.location.host + '/kefu/mobile_list';
            } else {
              url = window.location.protocol + '//' + window.location.host + '/kefu/pc_list';
            }

            window.open(url, '_blank');
          }
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    getExpiresTime(expiresTime) {
      let nowTimeNum = Math.round(new Date() / 1000);
      let expiresTimeNum = expiresTime - nowTimeNum;
      return parseFloat(parseFloat(parseFloat(expiresTimeNum / 60) / 60) / 24);
    },
    cancel() {
      this.formValidate = {
        page: 1,
        limit: 10,
        data: '',
        type: '',
        nickname: '',
      };
    },
    handleReachBottom() {
      return new Promise((resolve) => {
        this.formValidate.page = this.formValidate.page + 1;
        setTimeout(() => {
          // this.loading2 = true;
          kefucreateApi(this.formValidate)
            .then(async (res) => {
              let data = res.data;
              // this.tableList2 = data.list;
              if (data.list.length > 0) {
                for (let i = 0; i < data.list.length; i++) {
                  this.tableList2.push(data.list[i]);
                }
              }
              this.total2 = data.count;
              this.loading2 = false;
            })
            .catch((res) => {
              this.loading2 = false;
              this.$message.error(res.msg);
            });
          resolve();
        }, 2000);
      });
    },
    // Xem cuộc trò chuyện
    look(row) {
      this.isChat = false;
      this.rowRecord = row;
      this.getChatlist();
    },
    // Xem danh sách cuộc trò chuyện
    getChatlist() {
      this.loading5 = true;
      this.formValidate5.uid = this.rows.uid;
      this.formValidate5.to_uid = this.rowRecord.uid;
      this.formValidate5.id = this.rows.id;
      kefuChatlistApi(this.formValidate5)
        .then(async (res) => {
          let data = res.data;
          this.tableList5 = data.list;
          this.total5 = data.count;
          this.loading5 = false;
        })
        .catch((res) => {
          this.loading5 = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa thành công
    submitFail() {
      this.getList();
    },
    // Lịch sử trò chuyện
    record(row) {
      this.rows = row;
      this.modals3 = true;
      this.isChat = true;
      this.getListRecord();
    },
    // Danh sách lịch sử trò chuyện
    getListRecord() {
      this.loading3 = true;
      kefuRecordApi(this.formValidate3, this.rows.id)
        .then(async (res) => {
          let data = res.data;
          this.tableList3 = data.list ? data.list : [];
          this.total3 = data.count;
          this.loading3 = false;
        })
        .catch((res) => {
          this.loading3 = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {
      if (this.eidtLoading) return;
      this.eidtLoading = true;
      this.$modalForm(kefuEditApi(row.id))
        .then(() => {
          this.getList();
          this.eidtLoading = false;
        })
        .catch(() => {
          this.eidtLoading = false;
        });
    },
    // Thêm
    add() {
      this.$modalForm(kefuaddApi()).then(() => this.getList());
    },
    // Chọn tất cả
    onSelectTab(selection) {
      this.selections = selection;
      let data = [];
      this.selections.map((item) => {
        data.push(item.uid);
      });
      this.addFrom.uids = data;
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal ? this.timeVal.join('-') : '';
      this.formValidate.page = 1;
      this.getListService();
    },
    // Chọn giờ
    selectChange(tab) {
      this.formValidate.data = tab;
      this.timeVal = [];
      this.formValidate.page = 1;
      this.getListService();
    },
    // Danh sách nhân viên CSKH
    getListService() {
      this.loading2 = true(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tableList2 = data.list;
          this.total2 = data.count;
          this.tableList2.map((item) => {
            item._isChecked = false;
          });
          this.loading2 = false;
        })
        .catch((res) => {
          tkefucreateApihis.loading2 = false;
          this.$message.error(res.msg);
        });
    },
    pageChange2(pageIndex) {
      this.formValidate.page = pageIndex;
      this.getListService();
      this.addFrom.uids = [];
    },
    // Tìm kiếm
    userSearchs() {
      this.formValidate.page = 1;
      this.getListService();
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `app/wechat/kefu/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tableList.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Danh sách
    getList() {
      this.loading = true;
      kefuListApi(this.tableFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableList = data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      kefusetStatusApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Thêm nhân viên CSKH
    putRemark() {
      if (this.addFrom.uids.length === 0) {
        return this.$message.warning('Vui lòng chọn nhân viên CSKH cần thêm');
      }
      kefuAddApi(this.addFrom)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.modals = false;
          this.getList();
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
.modelBox {
  ::v-deep,
  .ivu-table-header {
    width: 100% !important;
  }
}
.trees-coadd {
  width: 100%;
  height: 385px;
  .scollhide {
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: scroll;
  }
}
.scollhide::-webkit-scrollbar {
  display: none;
}
</style>
