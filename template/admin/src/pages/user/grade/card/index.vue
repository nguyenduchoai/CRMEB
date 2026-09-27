<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          :model="gradeFrom"
          inline
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <el-form-item label="Tên lô:" label-for="title">
            <el-input clearable v-model="gradeFrom.title" placeholder="Vui lòng nhập tên lô" class="form_content_width" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="mt16">
      <el-button type="primary" v-db-click @click="addBatch">Thêm lô</el-button>
      <el-button v-db-click @click="getMemberScan">Mã QR trang sử dụng mã thẻ</el-button>
      <el-table
        class="mt14"
        :data="tbody"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="Mã số" width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên lô" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số ngày trải nghiệm" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.use_day }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tổng số thẻ phát hành" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.total_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số lượng sử dụng" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.use_num }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo thẻ" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Kích hoạt" min-width="100">
          <template slot-scope="scope">
            <el-switch
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeIsShow(scope.row)"
              size="large"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Ghi chú" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.remark }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo thẻ" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="120">
          <template slot-scope="scope">
            <el-dropdown size="small" @command="changeMenu(scope.row, $event, scope.$index)" :transfer="true">
              <span class="el-dropdown-link">Xem thêm<i class="el-icon-arrow-down el-icon--right"></i> </span>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="1">Sửa tên lô</el-dropdown-item>
                <el-dropdown-item command="2">Xem danh sách thẻ</el-dropdown-item>
                <el-dropdown-item command="3">Xuất</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="gradeFrom.page"
          :limit.sync="gradeFrom.limit"
          @pagination="getMemberBatch"
        />
      </div>
    </el-card>
    <el-dialog :visible.sync="modal" width="540px" :title="`${formValidate.id ? 'Sửa' : 'Thêm'} lô`">
      <!-- <form-create v-model="fapi" :rule="rule" @submit="onSubmit"></form-create> -->
      <el-form ref="formValidate" :model="formValidate" label-width="80px" @submit.native.prevent>
        <el-form-item label="Tên lô:">
          <el-input placeholder="Vui lòng nhập tên lô" element-id="unit_name" v-model="formValidate.title" class="w100" />
        </el-form-item>
        <el-form-item label="Ghi chú:" v-if="formValidate.id">
          <el-input type="textarea" placeholder="Vui lòng nhập ghi chú" v-model="formValidate.remark" class="w100" />
        </el-form-item>
        <template v-if="!formValidate.id">
          <el-form-item label="Số lượng thẻ cần tạo:">
            <el-input-number
              :controls="false"
              placeholder="Vui lòng nhập số lượng thẻ cần tạo"
              element-id="sort"
              :precision="0"
              :max="100000"
              :min="1"
              v-model="formValidate.total_num"
              class="perW10"
            />
          </el-form-item>
          <el-form-item label="Số ngày trải nghiệm:">
            <el-input-number
              :controls="false"
              placeholder="Vui lòng nhập số ngày trải nghiệm"
              element-id="sort"
              :precision="0"
              :max="100000"
              :min="1"
              v-model="formValidate.use_day"
              class="perW10"
            />
          </el-form-item>
          <el-form-item label="Kích hoạt:">
            <el-radio-group element-id="status" v-model="formValidate.status">
              <el-radio :label="1" class="radio">Kích hoạt</el-radio>
              <el-radio :label="0">Đóng băng</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="Ghi chú:">
            <el-input type="textarea" placeholder="Vui lòng nhập ghi chú" v-model="formValidate.remark" class="w100" />
          </el-form-item>
        </template>
      </el-form>
      <div class="acea-row row-right">
        <el-button v-db-click @click="modal = false">Hủy</el-button>
        <el-button type="primary" v-db-click @click="onSubmit()">Gửi</el-button>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="cardModal" title="Danh sách thẻ" width="1000px">
      <cardList v-if="cardModal" :id="id"></cardList>
    </el-dialog>
    <el-dialog :visible.sync="modal3" title="Mã QR" width="540px">
      <div v-if="qrcode" class="acea-row row-around">
        <div v-if="qrcode && qrcode.wechat_img" class="acea-row row-column-around row-between-wrapper">
          <div v-viewer class="QRpic">
            <img v-lazy="qrcode.wechat_img" />
          </div>
          <span class="mt10">Mã QR OA WeChat</span>
        </div>
        <div v-if="qrcode && qrcode.routine" class="acea-row row-column-around row-between-wrapper">
          <div v-viewer class="QRpic">
            <img v-lazy="qrcode.routine" />
          </div>
          <span class="mt10">Mã QR Mini Program</span>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import cardList from './list.vue';
import { userMemberBatch, memberBatchSave, memberBatchSetValue, exportMemberCard, userMemberScan } from '@/api/user';
import { exportmberCardList } from '@/api/export.js';

export default {
  name: 'index',
  components: { cardList },
  data() {
    return {
      cardModal: false,
      id: 0,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tbody: [],
      total: 0,
      gradeFrom: {
        title: '',
        page: 1,
        limit: 15,
      },
      loading: false,
      modal: false,

      formValidate: {
        id: 0,
        title: '',
        total_num: 1,
        use_day: 1,
        status: 1,
        remark: '',
      },
      modal2: false,
      modal3: false,
      qrcode: null,
      fapi: {},
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
  created() {
    this.getMemberBatch(this.gradeFrom);
  },
  methods: {
    // Danh sách lô
    getMemberBatch() {
      this.loading = true;
      userMemberBatch(this.gradeFrom)
        .then((res) => {
          this.loading = false;
          this.tbody = res.data.list;
          this.total = res.data.count;
        })
        .catch((err) => {
          this.loading = false;
          this.$message.error(err.msg);
        });
    },
    // Tìm kiếm theo tên lô
    userSearchs() {
      this.gradeFrom.page = 1;
      this.getMemberBatch();
    },
    // Kích hoạt | Đóng băng
    onchangeIsShow(row) {
      memberBatchSetValue(row.id, {
        field: 'status',
        value: row.status,
      })
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    // Xuất
    async export(row) {
      let [th, filekey, data, fileName] = [[], [], [], ''];
      let lebData = await this.getExcelData(row.id);
      if (!fileName) fileName = lebData.filename;
      if (!filekey.length) {
        filekey = lebData.fileKey;
      }
      if (!th.length) th = lebData.header;
      data = data.concat(lebData.export);
      this.$exportExcel(th, filekey, fileName, data);
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        exportmberCardList(excelData).then((res) => {
          resolve(res.data);
        });
      });
    },
    // Xem thêm
    changeMenu(row, name) {
      switch (name) {
        case '1':
          this.formValidate.id = row.id;
          this.formValidate.title = row.title;
          this.modal = true;
          break;
        case '2':
          this.id = row.id;
          this.cardModal = true;
          break;
        case '3':
          this.export(row);
          break;
      }
    },
    // Popup thêm lô
    addBatch() {
      // this.fapi.resetFields();
      this.modal = true;
      this.formValidate.id = 0;
      this.formValidate.title = '';
    },
    // Gửi lô
    onSubmit() {
      if (this.formValidate.id) {
        memberBatchSetValue(this.formValidate.id, {
          field: 'title',
          value: this.formValidate.title,
          remark: this.formValidate.remark,
        })
          .then((res) => {
            this.modal = false;
            this.$message.success(res.msg);
            this.getMemberBatch();
          })
          .catch((err) => {
            this.$message.error(err.msg);
          });
      } else {
        memberBatchSave(this.formValidate.id, this.formValidate)
          .then((res) => {
            this.modal = false;
            this.$message.success(res.msg);
            this.getMemberBatch();
          })
          .catch((err) => {
            this.$message.error(err.msg);
          });
      }
    },
    onSubmit2(formData) {},
    // Mã QR thẻ thành viên
    getMemberScan() {
      userMemberScan()
        .then((res) => {
          this.qrcode = res.data;
          this.modal3 = true;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.el-input-number--small {
  width: 100%;
}
.QRpic {
  width: 180px;
  height: 180px;

  img {
    width: 100%;
    height: 100%;
  }
}
.w414 {
  width: 414px;
}
</style>
