<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="tableFrom"
          :model="tableFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="Trạng thái đăng bán:">
            <el-select
              placeholder="Vui lòng chọn"
              v-model="tableFrom.status"
              clearable
              @change="userSearchs"
              class="form_content_width"
            >
              <el-option value="1" label="Đang bán"></el-option>
              <el-option value="0" label="Ngừng bán"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm sản phẩm:" label-for="store_name">
            <el-input
              clearable
              placeholder="Vui lòng nhập tên săn giảm giá, ID"
              v-model="tableFrom.store_name"
              class="form_content_width"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">Tra cứu</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16">
      <el-button v-auth="['marketing-store_bargain-create']" type="primary" v-db-click @click="add"
        >Thêm sản phẩm săn giảm giá</el-button
      >
      <el-button v-auth="['export-storeBargain']" class="export" icon="ios-share-outline" v-db-click @click="exportList"
        >Xuất</el-button
      >
      <el-table
        :data="tableList"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
        class="mt14"
      >
        <el-table-column label="ID" width="80">
          <template slot-scope="scope">
            <div>{{ scope.row.id }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Hình ảnh săn giảm giá" min-width="80">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.image" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Tên săn giảm giá" min-width="150">
          <template slot-scope="scope">
            <el-tooltip placement="top" :open-delay="600">
              <div slot="content">{{ scope.row.title }}</div>
              <span class="line2">{{ scope.row.title }}</span>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column label="Giá săn giảm giá" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.price }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Giá thấp nhất" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.min_price }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số người tham gia" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.count_people_all }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số người giúp giảm giá" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.count_people_help }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số người săn giảm giá thành công" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.count_people_success }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Giới hạn số lượng" min-width="80">
          <template slot-scope="scope">
            <div>{{ scope.row.quota_show }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số lượng giới hạn còn lại" min-width="80">
          <template slot-scope="scope">
            <div>{{ scope.row.quota }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái chương trình" min-width="100">
          <template slot-scope="scope">
            <el-tag size="medium" v-show="scope.row.start_name === 'Đang diễn ra'">Đang diễn ra</el-tag>
            <el-tag size="medium" type="warning" v-show="scope.row.start_name === 'Chưa bắt đầu'">Chưa bắt đầu</el-tag>
            <el-tag size="medium" type="info" v-show="scope.row.start_name === 'Đã kết thúc'">Đã kết thúc</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian chương trình" min-width="180">
          <template slot-scope="scope">
            <p>Bắt đầu: {{ scope.row.start_time }}</p>
            <p>Kết thúc: {{ scope.row.stop_time }}</p>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái đăng bán" min-width="100">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeIsShow(scope.row)"
              size="large"
              active-text="Đang bán"
              inactive-text="Ngừng bán"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-if="scope.row.stop_status === 0" v-db-click @click="edit(scope.row, 0)">Sửa</a>
            <el-divider v-if="scope.row.stop_status === 0" direction="vertical" />
            <a v-db-click @click="edit(scope.row, 1)">Sao chép</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'Xóa sản phẩm săn giảm giá', scope.$index)">Xóa</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="viewInfo(scope.row)">Thống kê</a>
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
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { bargainListApi, bargainSetStatusApi, stroeBargainApi } from '@/api/marketing';
import { formatDate } from '@/utils/validate';
import { exportBargainList } from '@/api/export';
export default {
  name: 'marketing_storeBargain',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm:ss');
      }
    },
  },
  data() {
    return {
      loading: false,
      columns1: [
        {
          title: 'ID',
          key: 'id',
          width: 80,
        },
        {
          title: 'Hình ảnh săn giảm giá',
          slot: 'image',
          minWidth: 90,
        },
        {
          title: 'Tên săn giảm giá',
          key: 'title',
          minWidth: 130,
        },
        {
          title: 'Giá săn giảm giá',
          key: 'price',
          minWidth: 100,
        },
        {
          title: 'Giá thấp nhất',
          key: 'min_price',
          minWidth: 100,
        },
        {
          title: 'Số người tham gia',
          key: 'count_people_all',
          minWidth: 100,
        },
        {
          title: 'Số người giúp giảm giá',
          key: 'count_people_help',
          minWidth: 100,
        },
        {
          title: 'Số người săn giảm giá thành công',
          key: 'count_people_success',
          minWidth: 100,
        },
        {
          title: 'Giới hạn số lượng',
          key: 'quota_show',
          minWidth: 100,
        },
        {
          title: 'Số lượng giới hạn còn lại',
          key: 'quota',
          minWidth: 100,
        },
        {
          title: 'Trạng thái chương trình',
          slot: 'start_name',
          minWidth: 100,
        },
        {
          title: 'Thời gian kết thúc',
          slot: 'stop_time',
          minWidth: 150,
        },
        {
          title: 'Trạng thái đăng bán',
          slot: 'status',
          minWidth: 130,
        },
        {
          title: 'Thao tác',
          slot: 'action',
          fixed: 'right',
          minWidth: 160,
        },
      ],
      tableList: [],
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        status: '',
        store_name: '',
        page: 1,
        limit: 15,
      },
      tableFrom2: {
        status: '',
        store_name: '',
        export: 1,
      },
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
  activated() {
    this.getList();
  },
  methods: {
    // Thêm
    add() {
      this.$router.push({ path: this.$routeProStr + '/marketing/store_bargain/create' });
    },
    // Xuất người dùng
    async exportList() {
      this.tableFrom.status = this.tableFrom.status || '';
      let [th, filekey, data, fileName] = [[], [], [], ''];
      let excelData = JSON.parse(JSON.stringify(this.tableFrom));
      excelData.page = 1;
      excelData.limit = 50;
      for (let i = 0; i < excelData.page + 1; i++) {
        let lebData = await this.getExcelData(excelData);
        if (!fileName) fileName = lebData.filename;
        if (!filekey.length) {
          filekey = lebData.fileKey;
        }
        if (!th.length) th = lebData.header;
        if (lebData.export.length) {
          data = data.concat(lebData.export);
          excelData.page++;
        } else {
          this.$exportExcel(th, filekey, fileName, data);
          return;
        }
      }
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        exportBargainList(excelData).then((res) => {
          resolve(res.data);
        });
      });
    },
    // Sửa / Sao chép  type 0 sửa 1 sao chép
    edit(row, type) {
      this.$router.push({
        path: this.$routeProStr + `/marketing/store_bargain/create/${row.id}/${type}`,
      });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/bargain/${row.id}`,
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
    viewInfo(row) {
      this.$router.push({
        path: this.$routeProStr + '/marketing/store_bargain/statistics/' + row.id,
      });
    },
    // Danh sách
    getList() {
      this.loading = true;
      this.tableFrom.status = this.tableFrom.status || '';
      this.tableFrom.product_id = this.$route.params.product_id || '';
      bargainListApi(this.tableFrom)
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
    // Tìm kiếm bảng
    userSearchs() {
      this.tableFrom.page = 1;
      this.getList();
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      bargainSetStatusApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
          row.status = !row.status;
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
</style>
