<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.meta.title"
      :backUrl="$routeProStr + '/marketing/store_bargain/index'"
    ></pages-header>
    <cards-data :cardLists="cardLists" v-if="cardLists.length" class="ivu-mt-16"></cards-data>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="pagination"
        :model="pagination"
        :label-width="labelWidth"
        label-position="right"
        @submit.native.prevent
        inline
      >
        <el-form-item v-if="type == 1" label="Trạng thái đơn hàng:" label-for="status">
          <el-select
            v-model="pagination.status"
            placeholder="Vui lòng chọn trạng thái đơn hàng"
            clearable
            @change="searchList"
            class="form_content_width"
          >
            <el-option value="1" label="Chờ giao hàng"></el-option>
            <el-option value="2" label="Chờ nhận hàng"></el-option>
            <el-option value="3" label="Chờ đánh giá"></el-option>
            <el-option value="4" label="Giao dịch hoàn tất"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Tìm kiếm đơn hàng:" label-for="title">
          <el-input
            v-model="pagination.real_name"
            :placeholder="type == 1 ? 'Vui lòng nhập họ tên người dùng|mã đơn hàng|UID' : 'Vui lòng nhập UID người dùng'"
            class="form_content_width"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-db-click @click="searchList">Tra cứu</el-button>
        </el-form-item>
      </el-form>
      <el-tabs v-model="type" @tab-click="onClickTab">
        <el-tab-pane v-for="(item, index) in tabs" :label="item.label" :name="item.type" :key="index" />
      </el-tabs>
      <el-table
        :data="tbody"
        ref="table"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column
          :label="item.title"
          :min-width="item.minWidth"
          v-for="(item, index) in type == 1 ? thead2 : thead"
          :key="index"
        >
          <template slot-scope="scope">
            <template v-if="item.key">
              <div>
                <span>{{ scope.row[item.key] }}</span>
              </div>
            </template>
            <template v-else-if="item.slot === 'avatar'">
              <div class="tabBox_img" v-viewer>
                <img v-lazy="scope.row.avatar" />
              </div>
            </template>
            <template v-else-if="item.slot === 'status'">
              <el-tag size="medium" type="info" v-show="scope.row.status === 1">Đang diễn ra</el-tag>
              <el-tag size="medium" type="danger" v-show="scope.row.status === 2">Đã thất bại</el-tag>
              <el-tag size="medium" v-show="scope.row.status === 3">Đã thành công</el-tag>
            </template>
            <template v-else-if="item.slot === 'action'">
              <a v-db-click @click="Info(scope.row)">Xem chi tiết</a>
            </template>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="pagination.page"
          :limit.sync="pagination.limit"
          @pagination="getList"
        />
      </div>
    </el-card>
    <!-- Modal chi tiết-->
    <el-dialog :visible.sync="modals" class="tableBox" title="Xem chi tiết" :close-on-click-modal="false" width="720px">
      <el-table
        ref="selection"
        :data="tabList3"
        v-loading="loading2"
        empty-text="Chưa có dữ liệu"
        highlight-current-row
        max-height="600"
        size="small"
      >
        <el-table-column label="ID người dùng" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện người dùng" min-width="90">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Số tiền săn giảm giá" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian săn giảm giá" min-width="130">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
  </div>
</template>

<script>
import cardsData from '@/components/cards/cards';
import {
  getbargainStatistics,
  getbargainStatisticsPeople,
  getbargainStatisticsOrder,
  bargainUserInfoApi,
} from '@/api/marketing';

export default {
  name: 'index',
  components: { cardsData },
  data() {
    return {
      modals: false,
      tabList3: [],
      loading2: false,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      id: 0,
      tbody: [],
      labelWidth: '80px',
      total: 0,
      tabs: [
        {
          type: '0',
          label: 'Tạo lượt săn giảm giá',
        },
        {
          type: '1',
          label: 'Đơn hàng chương trình',
        },
      ],
      currentTab: 0,
      loading: false,
      thead: [
        {
          title: 'Ảnh đại diện',
          slot: 'avatar',
        },
        {
          title: 'Người khởi tạo',
          key: 'nickname',
        },
        {
          title: 'Thời gian khởi tạo',
          key: 'add_time',
        },
        {
          title: 'Số người giúp săn giảm giá',
          key: 'already_num',
        },
        {
          title: 'Thời gian kết thúc',
          key: 'datatime',
        },
        {
          title: 'Trạng thái săn giảm giá',
          slot: 'status',
        },
        {
          title: 'Thao tác',
          slot: 'action',
        },
      ],
      thead2: [
        {
          title: 'Mã đơn hàng',
          key: 'order_id',
        },
        {
          title: 'Người dùng',
          key: 'real_name',
        },
        {
          title: 'Trạng thái đơn hàng',
          key: 'status',
        },
        {
          title: 'Số tiền thanh toán đơn hàng',
          key: 'pay_price',
        },
        {
          title: 'Số sản phẩm trong đơn hàng',
          key: 'total_num',
        },
        {
          title: 'Thời gian đặt hàng',
          key: 'add_time',
        },
        {
          title: 'Thời gian thanh toán',
          key: 'pay_time',
        },
      ],
      cardLists: [
        {
          col: 4,
          count: 0,
          name: 'Số người tham gia chương trình (người)',
          className: 'iconcanyurenshu',
        },
        {
          col: 4,
          count: 0,
          name: 'Số người săn giảm giá (người)',
          className: 'iconxiadanrenshu',
        },
        {
          col: 4,
          count: 0,
          name: 'Số lượt khởi tạo săn giảm giá',
          className: 'icontuiguangrenshu',
        },
        {
          col: 4,
          count: 0,
          name: 'Số lượt săn giảm giá thành công',
          className: 'iconkanjiachenggong',
        },
        {
          col: 4,
          count: 0,
          name: 'Giá trị đơn đã thanh toán (đ)',
          className: 'iconzhifudingdan',
        },
        {
          col: 4,
          count: 0,
          name: 'Số người thanh toán (người)',
          className: 'iconzhifurenshu',
        },
      ],
      pagination: {
        page: 1,
        limit: 15,
        real_name: '',
        status: '',
      },
      type: 0,
    };
  },
  created() {
    this.id = this.$route.params.id;
    this.getStatistics(this.id);
    this.getList(this.id);
  },
  methods: {
    // Thống kê
    getStatistics(id) {
      getbargainStatistics(id).then((res) => {
        let arr = [
          'people_count',
          'spread_count',
          'start_count',
          'success_count',
          'pay_price',
          'pay_count',
          'pay_rate',
        ];
        this.cardLists.map((i, index) => {
          i.count = res.data[arr[index]];
        });
      });
    },
    // Danh sách
    getList(id) {
      this.loading = true;
      if (this.type == 0) {
        getbargainStatisticsPeople(this.id, this.pagination).then((res) => {
          this.loading = false;
          const { count, list } = res.data;
          this.total = count;
          this.tbody = list;
        });
      } else {
        getbargainStatisticsOrder(this.id, this.pagination).then((res) => {
          this.loading = false;
          const { count, list } = res.data;
          this.total = count;
          this.tbody = list;
        });
      }
    },
    // Chuyển đổi tab
    onClickTab(e) {
      this.type = e.index;
      this.getList(this.id);
    },
    // Tìm kiếm
    searchList() {
      this.pagination.page = 1;
      this.getList(this.id);
    },
    // Xem chi tiết
    Info(row) {
      this.modals = true;
      this.rows = row;
      bargainUserInfoApi(row.id)
        .then(async (res) => {
          let data = res.data;
          this.tabList3 = data.list;
          this.loading = false;
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
.cl {
  margin-right: 20px;
}
.code-row-bg {
  display: flex;
  flex-wrap: nowrap;
}
.code-row-bg .ivu-mt {
  width: 100%;
  margin: 0 5px;
}
.ech-box {
  margin-top: 10px;
}
.change-style {
  border: 1px solid #ccc;
  border-radius: 15px;
  padding: 0px 10px;
  cursor: pointer;
}
.table-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.return {
  margin-bottom: 6px;
}
::v-deep .ivu-tabs-nav-scroll {
  background-color: #fff;
  padding-top: 5px;
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
</style>
