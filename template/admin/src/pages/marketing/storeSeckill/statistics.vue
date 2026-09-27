<template>
  <div>
    <pages-header
      ref="pageHeader"
      :title="$route.meta.title"
      :backUrl="$routeProStr + '/marketing/store_seckill/index'"
    ></pages-header>
    <cards-data :cardLists="cardLists" v-if="cardLists.length >= 0" class="ivu-mt-16"></cards-data>
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="pagination"
        :model="pagination"
        label-width="80px"
        label-position="right"
        @submit.native.prevent
        inline
        ><el-form-item v-if="type == 1" label="Trạng thái đơn hàng:" label-for="status">
          <el-select
            v-model="pagination.status"
            clearable
            placeholder="Vui lòng chọn trạng thái đơn hàng"
            @change="changeStatus"
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
            clearable
            v-model="pagination.real_name"
            placeholder="Vui lòng nhập họ tên người dùng|số điện thoại|UID"
            class="form_content_width"
          />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" v-db-click @click="changeStatus">Tra cứu</el-button>
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
          :min-width="item.minWidth || 100"
          v-for="(item, index) in type == 1 ? thead2 : thead"
          :key="index"
        >
          <template slot-scope="scope">
            <template v-if="item.key">
              <span>{{ scope.row[item.key] }}</span>
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
  </div>
</template>

<script>
import cardsData from '@/components/cards/cards';
import { getseckillStatistics, getseckillStatisticsPeople, getseckillStatisticsOrder } from '@/api/marketing';

export default {
  name: 'index',
  components: { cardsData },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      id: 0,
      tbody: [],
      labelWidth: 75,
      total: 0,
      tabs: [
        {
          type: '0',
          label: 'Người tham gia hoạt động',
        },
        {
          type: '1',
          label: 'Đơn hàng chương trình',
        },
      ],
      type: 0,
      loading: false,
      thead: [
        {
          title: 'Họ tên người dùng',
          key: 'real_name',
        },
        {
          title: 'Số sản phẩm đã mua',
          key: 'goods_num',
        },
        {
          title: 'Số đơn hàng đã thanh toán',
          key: 'order_num',
        },
        {
          title: 'Số tiền thanh toán',
          key: 'total_price',
        },
        {
          title: 'Thời gian tham gia gần nhất',
          key: 'add_time',
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
          col: 6,
          count: 0,
          name: 'Số người đặt hàng (người)',
          className: 'iconxiadanrenshu',
        },
        {
          col: 6,
          count: 0,
          name: 'Giá trị đơn đã thanh toán (đ)',
          className: 'iconzhifudingdan',
        },
        {
          col: 6,
          count: 0,
          name: 'Số người thanh toán (người)',
          className: 'iconzhifurenshu',
        },
        {
          col: 6,
          count: 0,
          name: 'Tồn kho còn lại/Tổng tồn kho',
          className: 'iconshengyukucun',
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
    this.getList();
  },
  methods: {
    changeStatus() {
      this.pagination.page = 1;
      this.getList();
    },
    // Thống kê
    getStatistics(id) {
      getseckillStatistics(id).then((res) => {
        let arr = ['order_count', 'all_price', 'pay_count', 'pay_rate'];
        this.cardLists.map((i, index) => {
          i.count = res.data[arr[index]];
        });
      });
    },
    // Danh sách
    getList() {
      this.loading = true;
      if (this.type == 0) {
        getseckillStatisticsPeople(this.id, this.pagination).then((res) => {
          this.loading = false;
          const { count, list } = res.data;
          this.total = count;
          this.tbody = list;
        });
      } else {
        getseckillStatisticsOrder(this.id, this.pagination).then((res) => {
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
      this.getList();
    },
    // Tìm kiếm
    searchList() {
      this.pagination.page = 1;
      this.getList();
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
::v-deep .ivu-tabs-nav-scroll {
  background-color: #fff;
  padding-top: 5px;
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
</style>
