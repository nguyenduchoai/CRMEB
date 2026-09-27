<template>
  <div>
    <el-dialog :visible.sync="isTemplate" title="Danh sách phiếu giảm giá" append-to-body width="1000px">
      <el-table
        :data="couponList"
        ref="couponTable"
        class="mt20"
        v-loading="loading"
        highlight-current-row
        :row-key="getRowKey"
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
        @selection-change="changeCheckbox"
      >
        <el-table-column v-if="!luckDraw" type="selection" width="55" :reserve-selection="true"> </el-table-column>
        <el-table-column v-else width="50">
          <template slot-scope="scope">
            <el-radio v-model="templateRadio" :label="scope.row.id" @change.native="getTemplateRow(scope.row)"
              >&nbsp;</el-radio
            >
          </template>
        </el-table-column>
        <el-table-column label="ID" width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên phiếu giảm giá" min-width="120">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Loại phiếu giảm giá" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.type === 1">Phiếu theo danh mục</span>
            <span v-else-if="scope.row.type === 2">Phiếu theo sản phẩm</span>
            <span v-else-if="scope.row.type === 3">Phiếu thành viên</span>
            <span v-else>Phiếu toàn cửa hàng</span>
          </template>
        </el-table-column>
        <el-table-column label="Mệnh giá" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.coupon_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Giá trị đơn tối thiểu" min-width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.use_min_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Số lượng phát hành" min-width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.is_permanent">Không giới hạn</span>
            <div v-else>
              <span class="fa">Phát hành: {{ scope.row.total_count }}</span>
              <span class="sheng ml10">Còn lại: {{ scope.row.remain_count }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Thời hạn hiệu lực" min-width="100">
          <template slot-scope="scope">
            <div v-if="scope.row.start_time">
              {{ scope.row.start_time | formatDate }} - {{ scope.row.end_time | formatDate }}
            </div>
            <span v-else>Không giới hạn thời gian</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái" min-width="100">
          <template slot-scope="scope">
            <el-tag size="medium" v-show="scope.row.status === 1">Bình thường</el-tag>
            <el-tag size="medium" type="danger" v-show="scope.row.status === 0">Chưa bật</el-tag>
            <el-tag size="medium" type="info" v-show="scope.row.status === -1">Đã hết hiệu lực</el-tag>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="tableFrom.page"
          :limit.sync="tableFrom.limit"
          @pagination="tableList"
        />
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel">Hủy</el-button>
        <el-button type="primary" v-db-click @click="ok">Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { releasedListApi } from '@/api/marketing';
import { formatDate } from '@/utils/validate';

export default {
  name: 'index',
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, 'yyyy-MM-dd hh:mm');
      }
    },
  },
  props: {
    couponids: {
      type: Array,
    },
    updateIds: {
      type: Array,
    },
    updateName: {
      type: Array,
    },
    luckDraw: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      templateRadio: 0,

      currentid: 0,
      productRow: {},
      isTemplate: false,
      loading: false,
      tableFrom: {
        receive_type: 3,
        page: 1,
        limit: 15,
      },
      total: 0,
      ids: [],
      texts: [],
      couponList: [],
      selectedIds: [],
      selectedNames: [],
      multipleSelection: [],
    };
  },
  mounted() {},
  watch: {
    updateIds: function (newVal) {
      this.selectedIds = newVal;
    },
    updateName: function (newVal) {
      this.selectedNames = newVal;
      this.multipleSelection = newVal;
    },
  },
  created() {},
  methods: {
    getRowKey(row) {
      return row.id;
    },
    getTemplateRow(row) {
      this.currentid = row.id;
      this.productRow = row;
    },
    //Loại bỏ trùng lặp trong mảng object;
    unique(arr) {
      const res = new Map();
      return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1));
    },
    changeCheckbox(selection) {
      let uniqueArr = [];
      let cups = [];
      let ids = [];
      let arr = this.unique(selection);
      for (let i = 0; i < arr.length; i++) {
        const item = arr[i];
        if (!ids.includes(item.id)) {
          let obj = {
            id: item.id,
            title: item.title,
            full_reduction: item.full_reduction, // Đơn từ
            use_min_price: item.use_min_price, // Đơn từ
            coupon_price: item.coupon_price, // Trừ
          };
          cups.push(obj);
          ids.push(item.id);
          uniqueArr.push(item);
        }
      }
      this.selectedIds = ids;
      this.selectedNames = cups;
      this.multipleSelection = uniqueArr;
    },
    cancel() {
      this.isTemplate = false;
      if (this.luckDraw) {
        this.currentid = 0;
      }
    },
    tableList() {
      this.loading = true;
      releasedListApi(this.tableFrom).then((res) => {
        let data = res.data;
        this.couponList = data.list;
        this.total = data.count;
        this.$nextTick(() => {
          //Đảm bảo dom đã tải xong
          this.selectedIds.length && this.setChecked();
          this.showSelectData();
        });
        this.loading = false;
      });
    },
    setChecked() {
      //Chuyển new Set() thành mảng
      let ids = [...this.selectedIds];
      this.couponList.forEach((row) => {
        if (ids.includes(row.id)) {
          this.$refs.couponTable.toggleRowSelection(row, true);
        }
      });
    },
    ok() {
      if (this.luckDraw) {
        this.$emit('getCouponId', this.productRow);
        this.currentid = 0;
      } else {
        this.$emit('nameId', this.selectedIds, this.selectedNames);
      }
      this.isTemplate = false;
    },
    pageChange(index) {
      this.tableFrom.page = index;
      this.tableList();
    },
    limitChange(limit) {
      this.tableFrom.limit = limit;
      this.tableList();
    },
    showSelectData() {
      if (this.multipleSelection.length > 0) {
        // Kiểm tra có dữ liệu đã tick chọn không
        this.couponList.forEach((row) => {
          // Dữ liệu lấy được từ API danh sách dữ liệu
          this.multipleSelection.forEach((item) => {
            // Dữ liệu đã tick chọn
            if (row.id === item.id) {
              // this.$refs.table.toggleRowSelection(item, true); // Nếu có trùng khớp thì hiển thị lại dòng dữ liệu này
            }
          });
        });
      }
    },
  },
};
</script>

<style scoped></style>
