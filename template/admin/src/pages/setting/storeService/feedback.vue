<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          class="tabform"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Thời gian gửi lời nhắn:">
            <el-date-picker
              clearable
              v-model="timeVal"
              type="daterange"
              :editable="false"
              @change="onchangeTime"
              format="yyyy/MM/dd"
              value-format="yyyy/MM/dd"
              start-placeholder="Ngày bắt đầu"
              end-placeholder="Ngày kết thúc"
              :picker-options="pickerOptions"
              style="width: 250px"
              class="mr20"
            ></el-date-picker>
          </el-form-item>
          <el-form-item label="Nội dung lời nhắn:">
            <el-input
              clearable
              placeholder="Vui lòng nhập biệt danh/số điện thoại/nội dung lời nhắn để tìm kiếm"
              v-model="formValidate.title"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="selChange">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-table :data="list" v-loading="loading" no-userFrom-text="Chưa có dữ liệu" no-filtered-userFrom-text="Không có kết quả phù hợp">
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Biệt danh" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.rela_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Điện thoại" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.phone }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Nội dung" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.content }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="130">
          <template slot-scope="scope">
            <div>{{ scope.row.status === 1 ? 'Đã xử lý' : 'Chưa xử lý' }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="remarks(scope.row.id)">{{ scope.row.status === 1 ? 'Ghi chú' : 'Xử lý' }}</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa phản hồi', scope.$index)">Xóa</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination v-if="total" :total="total" :page.sync="page" :limit.sync="limit" @pagination="getList" />
      </div>
    </el-card>
  </div>
</template>

<script>
import { kefuFeedBack, kefuFeedBackEdit } from '@/api/setting';
import { mapState } from 'vuex';
export default {
  name: 'feedback',
  data() {
    return {
      loading: false,
      list: [],
      page: 1,
      limit: 15,
      formValidate: {
        time: '',
        title: '',
      },
      pickerOptions: this.$timeOptions,
      timeVal: [],
      total: 0,
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
    this.getList();
  },
  methods: {
    //Ghi chú;
    remarks(id) {
      this.$modalForm(kefuFeedBackEdit(id)).then(() => this.getList());
    },
    // Chọn
    selChange() {
      this.page = 1;
      this.getList();
    },
    // Chọn giờ
    selectChange(tab) {
      this.formValidate.time = tab;
      this.timeVal = [];
      this.page = 1;
      this.getList();
    },
    // Ngày cụ thể
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.time = this.timeVal ? this.timeVal.join('-') : '';
      this.page = 1;
      this.getList();
    },
    getList() {
      kefuFeedBack({
        page: this.page,
        limit: this.limit,
        time: this.formValidate.time,
        title: this.formValidate.title,
      }).then((res) => {
        this.list = res.data.data;
        this.total = res.data.count;
      });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `/app/feedback/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.list.splice(num, 1);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style scoped></style>
