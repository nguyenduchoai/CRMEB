<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form :model="formValidate" :label-width="labelWidth" label-position="right" @submit.native.prevent inline>
          <el-form-item label="Danh mục ngôn ngữ:">
            <el-select v-model="formValidate.is_admin" clearable @change="selChange" class="form_content_width">
              <el-option
                v-for="(item, index) in langType.isAdmin"
                :key="index"
                :label="item.title"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Loại ngôn ngữ:">
            <el-select v-model="formValidate.type_id" clearable @change="selChange" class="form_content_width">
              <el-option
                v-for="(item, index) in langType.langType"
                :key="index"
                :label="item.title"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm:">
            <el-input
              clearable
              placeholder="Vui lòng nhập ghi chú ngôn ngữ"
              v-model="formValidate.remarks"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" class="userSearch" v-db-click @click="selChange">Tìm kiếm</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-alert type="warning" :closable="false">
      <template slot="title">
        <p class="alert_title">Ngôn ngữ trang</p>
        Thêm ngôn ngữ trang, sau khi thêm xong, mã trạng thái là văn bản tiếng Trung, trang di động dùng $t(`xxxx`), trong tệp js dùng this.t(`xxxx`) hoặc that.t(`xxxx`) để chuyển đổi ngôn ngữ<br />
        <br />
        <p class="alert_title">Ngôn ngữ API</p>
        Thêm ngôn ngữ API, sau khi thêm xong mã trạng thái là 6 chữ số, khi API trả về thông báo, chỉ cần trả về mã lỗi tương ứng là có thể chuyển đổi ngôn ngữ
      </template>
    </el-alert>
    <el-card class="mt14" :bordered="false" shadow="never">
      <el-row class="mb14">
        <el-col>
          <el-button type="primary" v-db-click @click="add">Thêm câu</el-button>
        </el-col>
      </el-row>
      <el-table ref="table" :data="tabList" class="ivu-mt" v-loading="loading" empty-text="Chưa có dữ liệu">
        <el-table-column label="Mã số" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Câu gốc" min-width="230">
          <template slot-scope="scope">
            <span>{{ scope.row.remarks }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Bản dịch tương ứng" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.lang_explain }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mã trạng thái/Văn bản (tham chiếu khi gọi từ API/trang)" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.code }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại ngôn ngữ" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.language_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">Sửa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa ngôn ngữ', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="formValidate.page"
          :limit.sync="formValidate.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <el-dialog :visible.sync="addlangModal" width="720px" title="Thêm câu cần dịch" @closed="modalChange">
      <el-form ref="langFormData" :model="langFormData" :rules="ruleValidate">
        <el-form-item label="Áp dụng cho:" class="mb20" label-width="120px">
          <el-radio-group type="button" v-model="langFormData.is_admin" class="mr15">
            <el-radio :label="item.value" v-for="(item, index) in langType.isAdmin" :key="index">{{
              item.title
            }}</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-input v-model="langFormData.edit" v-show="false"></el-input>
        <el-form-item label="Câu cần dịch:" prop="remarks" class="mb20">
          <el-input
            v-model="langFormData.remarks"
            placeholder="Vui lòng nhập câu cần thêm bản dịch"
            style="width: 330px"
            search
            @on-search="translate"
          >
            <el-button type="primary" slot="append" v-db-click @click="translate">Dịch</el-button>
          </el-input>
        </el-form-item>
        <el-form-item prop="remark" class="mb20">
          <el-table ref="langTable" v-loading="traTabLoading" :data="langFormData.list" empty-text="Chưa có dữ liệu">
            <el-table-column label="Loại ngôn ngữ" width="140">
              <template slot-scope="scope">
                <span> {{ scope.row.language_name }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Bản dịch tương ứng" min-width="250">
              <template slot-scope="scope">
                <el-input v-model="scope.row.lang_explain" class="priceBox"></el-input>
              </template>
            </el-table-column>
          </el-table>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="addlangModal = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="ok">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import { langCodeList, langCodeInfo, langCodeSettingSave, langCodeTranslate } from '@/api/setting';

export default {
  data() {
    return {
      addlangModal: false,
      traTabLoading: false,
      langType: {},
      formValidate: {
        is_admin: 0,
        type_id: 1,
        remarks: '',
        page: 1,
        limit: 20,
      },
      total: 0,
      FormLoading: true,
      loading: false,
      ruleValidate: {
        code: [{ required: true, message: 'Vui lòng nhập mã trạng thái/văn bản', trigger: 'blur' }],
        remarks: [{ required: true, message: 'Vui lòng nhập văn bản', trigger: 'blur' }],
      },
      langColumns: [
        {
          title: 'Loại ngôn ngữ',
          key: 'language_name',
          width: 120,
        },
        {
          title: 'Bản dịch tương ứng',
          slot: 'lang_explain',
          minWidth: 250,
        },
      ],
      langData: [],
      langFormData: {
        is_admin: 0,
        code: '',
        remarks: '',
        edit: 0,
        list: [],
      },
      tabList: [],
      FromData: null,
      extractId: 0,
      code: null,
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '80px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  mounted() {
    this.getList();
  },
  methods: {
    translate() {
      if (!this.langFormData.remarks.trim()) {
        return this.$message.warning('Vui lòng nhập nội dung cần dịch trước');
      }
      this.traTabLoading = true;
      langCodeTranslate({
        text: this.langFormData.remarks,
      })
        .then((res) => {
          this.langFormData.list.map((e) => {
            e.lang_explain = res.data[e.type_id];
          });
          this.traTabLoading = false;
        })
        .catch((err) => {
          this.traTabLoading = false;
          this.$message.error(err.msg);
        });
    },
    add() {
      this.langFormData.list = this.langType.langType.map((e) => {
        return {
          language_name: e.title,
          lang_explain: '',
          remarks: '',
          type_id: e.value,
        };
      });
      this.addlangModal = true;
    },
    ok() {
      if (!this.langFormData.remarks.trim()) {
        this.FormLoading = false;
        this.$nextTick(() => {
          this.FormLoading = true;
        });
        return this.$message.error('Vui lòng nhập mô tả ngôn ngữ trước');
      }
      langCodeSettingSave(this.langFormData)
        .then((res) => {
          this.addlangModal = false;
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((err) => {
          this.FormLoading = false;
          this.$nextTick(() => {
            this.FormLoading = true;
          });
          this.$message.error(err.msg);
        });
    },
    edit(row) {
      this.langFormData.is_admin = this.formValidate.is_admin;
      this.code = row.code;
      langCodeInfo({ code: row.code })
        .then((res) => {
          this.langFormData.list = res.data.list;
          this.langFormData.code = res.data.code;
          this.langFormData.remarks = res.data.remarks;
          this.langFormData.edit = 1;
          this.addlangModal = true;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `setting/lang_code/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tabList.splice(num, 1);
          // this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    modalChange() {
      this.langFormData = {
        is_admin: 0,
        name: '',
        code: '',
        list: [],
      };
      this.code = null;
    },
    // Chọn
    selChange() {
      this.formValidate.page = 1;
      this.getList();
    },
    // Danh sách
    getList() {
      this.loading = true;
      langCodeList(this.formValidate)
        .then(async (res) => {
          this.loading = false;
          this.tabList = res.data.list;
          this.total = res.data.count;
          this.langType = res.data.langType;
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
.ivu-mt .type .item {
  margin: 3px 0;
}
.tabform {
  margin-bottom: 10px;
}
.Refresh {
  font-size: 12px;
  color: var(--prev-color-primary);
  cursor: pointer;
}
.ivu-form-item {
  margin-bottom: 10px;
}
.status ::v-deep .item ~ .item {
  margin-left: 6px;
}
.status ::v-deep .statusVal {
  margin-bottom: 7px;
}

/* .ivu-mt ::v-deep .ivu-table-header */
/* border-top:1px dashed #ddd!important */
.type {
  padding: 3px 0;
  box-sizing: border-box;
}
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
.mb20 ::v-deep .ivu-table-wrapper > .ivu-spin-fix {
  border: none;
}
</style>
