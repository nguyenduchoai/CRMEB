<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: '0 20px' }">
      <div>
        <el-tabs v-model="currentTab" @tab-click="getList">
          <el-tab-pane
            :label="item.label"
            :name="item.value.toString()"
            v-for="(item, index) in headerList"
            :key="index"
          />
        </el-tabs>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never">
      <el-alert type="warning" :closable="false">
        <template slot="title">
          Hai cách khởi động tác vụ định kỳ:<br />
          1. Khởi động bằng lệnh: php think timer start --d; nếu thay đổi chu kỳ thực thi, sửa trạng thái bật/tắt hoặc xóa tác vụ định kỳ thì cần khởi động lại tác vụ định kỳ để đảm bảo có hiệu lực;<br />
          2. Dùng API để kích hoạt tác vụ định kỳ, khuyến nghị gọi mỗi phút một lần, địa chỉ API {{ apiBaseURL }}api/crontab/run <br />
        </template>
      </el-alert>
      <el-button v-if="currentTab === '1'" type="primary" v-db-click @click="addTask" class="mt14"
        >Thêm tác vụ định kỳ</el-button
      >
      <el-table :data="tableData" v-loading="loading" class="ivu-mt">
        <el-table-column label="Tiêu đề" min-width="150">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Mô tả nhiệm vụ" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.content }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Chu kỳ thực thi" min-width="130">
          <template slot-scope="scope">
            <span>{{ taskTrip(scope.row) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Bật" min-width="130">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.is_open"
              size="large"
              @change="handleChange(scope.row)"
              active-text="Bật"
              inactive-text="Tắt"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" width="100">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row.id)">Sửa</a>
            <el-divider direction="vertical" v-if="currentTab === '1'"></el-divider>
            <a
              v-if="currentTab === '1'"
              v-permission="'seckill'"
              v-db-click
              @click="handleDelete(scope.row, 'Xóa tác vụ định kỳ', scope.$index)"
              >Xóa</a
            >
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination v-if="total" :total="total" :page.sync="page" :limit.sync="limit" @pagination="getList" />
      </div>
      <creatTask ref="addTask" :currentTab="currentTab" @submitAsk="getList"></creatTask>
    </el-card>
  </div>
</template>

<script>
import { timerIndex, showTimer } from '@/api/system';
import creatTask from './createModal.vue';
import setting from '@/setting';
export default {
  name: 'system_crontab',
  components: { creatTask },
  data() {
    return {
      loading: false,
      tableData: [],
      page: 1,
      limit: 15,
      total: 1,
      apiBaseURL: '',
      headerList: [
        { label: 'Tác vụ hệ thống', value: '0' },
        { label: 'Tác vụ tùy chỉnh', value: '1' },
      ],
      currentTab: '0',
    };
  },
  created() {
    this.apiBaseURL = setting.apiBaseURL.replace(/adminapi/, '');
    this.getList();
  },
  methods: {
    taskTrip(row) {
      switch (row.type) {
        case 1:
          return `Thực thi mỗi ${row.second} giây một lần`;
        case 2:
          return `Thực thi mỗi ${row.minute} phút một lần`;
        case 3:
          return `Thực thi mỗi ${row.hour} giờ một lần`;
        case 4:
          return `Thực thi mỗi ${row.day} ngày một lần`;
        case 5:
          return `Thực thi hằng ngày vào lúc ${row.hour} giờ ${row.minute} phút ${row.second} giây`;
        case 6:
          return `Thực thi vào ngày thứ ${row.week} hằng tuần lúc ${row.hour} giờ ${row.minute} phút ${row.second} giây`;
        case 7:
          return `Thực thi vào ngày ${row.day} hằng tháng lúc ${row.hour} giờ ${row.minute} phút ${row.second} giây`;
        case 8:
          return `Thực thi vào ngày ${row.day} tháng ${row.month} hằng năm lúc ${row.hour} giờ ${row.minute} phút ${row.second} giây`;
      }
    },
    // Danh sách
    getList() {
      this.loading = true;
      timerIndex({
        page: this.page,
        limit: this.limit,
        custom: this.currentTab === '1' ? 1 : 0,
      })
        .then((res) => {
          this.loading = false;
          let { count, list } = res.data;
          this.total = count;
          this.tableData = list;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    addTask() {
      this.$refs.addTask.timerInfo(0);
    },
    edit(id) {
      this.$refs.addTask.timerInfo(id);
    },
    // Xóa
    handleDelete(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `system/crontab/del/${row.id}`,
        method: 'delete',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Bật
    handleChange({ id, is_open }) {
      showTimer(id, is_open)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.ivu-mt {
  padding-top: 10px;
}
</style>
