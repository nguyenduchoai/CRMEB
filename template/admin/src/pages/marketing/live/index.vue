<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          label-position="right"
          inline
          @submit.native.prevent
          class="tabform"
        >
          <el-form-item label="Trạng thái livestream:">
            <el-select v-model="formValidate.status" clearable @change="selChange" class="form_content_width">
              <el-option
                v-for="(item, index) in treeData.withdrawal"
                :value="item.value"
                :key="index"
                :label="item.title"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Tìm kiếm:">
            <el-input
              clearable
              placeholder="Vui lòng nhập tên phòng livestream/ID/biệt danh streamer/ID WeChat"
              v-model="formValidate.kerword"
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
      <el-button v-auth="['setting-system_menus-add']" type="primary" v-db-click @click="menusAdd('Thêm phòng livestream')"
        >Thêm phòng livestream</el-button
      >
      <el-button v-auth="['setting-system_menus-add']" v-db-click @click="syncRoom" style="margin-left: 20px"
        >Đồng bộ phòng livestream</el-button
      >
      <el-table
        :data="tabList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
        no-userFrom-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
      >
        <el-table-column label="ID phòng livestream" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Tên phòng livestream" min-width="35">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Biệt danh streamer" min-width="35">
          <template slot-scope="scope">
            <span>{{ scope.row.anchor_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="ID WeChat của streamer" min-width="35">
          <template slot-scope="scope">
            <span>{{ scope.row.anchor_wechat }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian bắt đầu livestream" min-width="35">
          <template slot-scope="scope">
            <span>{{ scope.row.start_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian kết thúc dự kiến" min-width="35">
          <template slot-scope="scope">
            <span>{{ scope.row.end_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Thời gian tạo" min-width="35">
          <template slot-scope="scope">
            <span>{{ scope.row.add_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái hiển thị" min-width="35">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.is_show"
              :value="scope.row.is_show"
              @change="onchangeIsShow(scope.row)"
              size="large"
              active-text="Bật"
              inactive-text="Tắt"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="Trạng thái livestream" min-width="35">
          <template slot-scope="scope">
            <div>{{ scope.row.live_status | liveReviewStatusFilter }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thứ tự sắp xếp" min-width="35">
          <template slot-scope="scope">
            <div>{{ scope.row.sort }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="170">
          <template slot-scope="scope">
            <a v-db-click @click="detail(scope.row, 'Chi tiết')">Chi tiết</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row, 'xóa thông tin này', scope.$index)">Xóa</a>
            <el-divider direction="vertical" v-if="scope.row.live_status == 102" />
            <a v-if="scope.row.live_status == 102" v-db-click @click="addGoods(scope.row)">Thêm sản phẩm</a>
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
    <!--Chi tiết-->
    <el-dialog :visible.sync="modals" title="Chi tiết phòng livestream" class="paymentFooter" width="720px">
      <details-from ref="studioDetail" />
    </el-dialog>
    <!-- Thêm sản phẩm -->
    <el-dialog :visible.sync="isShowBox" title="Thêm sản phẩm" class="paymentFooter" width="720px">
      <!--            <addGoods :datas="activeItem" @getData="getData" ref="liveAdd"></addGoods>-->
      <goods-list
        ref="goodslist"
        @getProductId="getProductId"
        v-if="isShowBox"
        :selectIds="selectIds"
        :ischeckbox="true"
        :liveStatus="true"
      ></goods-list>
    </el-dialog>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { liveList, liveShow, liveRoomGoodsAdd, liveSyncRoom } from '@/api/live';
import detailsFrom from './components/live_detail';
import addGoods from './components/add_goods';
import goodsList from '@/components/goodsList';
export default {
  name: 'live',
  components: {
    detailsFrom,
    addGoods,
    goodsList,
  },
  data() {
    return {
      isShowBox: false,
      modals: false,
      total: 0,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      formValidate: {
        status: '',
        kerword: '',
        page: 1,
        limit: 20,
      },
      treeData: {
        withdrawal: [
          {
            title: 'Tất cả',
            value: '',
          },
          {
            title: 'Đang livestream',
            value: 1,
          },
          {
            title: 'Chưa bắt đầu',
            value: 2,
          },
          {
            title: 'Đã kết thúc',
            value: 3,
          },
        ],
      },
      columns1: [
        { key: 'id', title: 'ID phòng livestream', minWidth: 35 },
        { key: 'name', minWidth: 35, title: 'Tên phòng livestream' },
        { key: 'anchor_name', minWidth: 35, title: 'Biệt danh streamer' },
        { key: 'anchor_wechat', minWidth: 35, title: 'ID WeChat của streamer' },
        { key: 'start_time', minWidth: 35, title: 'Thời gian bắt đầu livestream' },
        { key: 'end_time', minWidth: 35, title: 'Thời gian kết thúc dự kiến' },
        { key: 'add_time', minWidth: 35, title: 'Thời gian tạo' },
        { slot: 'is_mer_show', title: 'Trạng thái hiển thị', minWidth: 80 },
        { slot: 'status', minWidth: 35, title: 'Trạng thái livestream' },
        { key: 'sort', minWidth: 35, title: 'Thứ tự sắp xếp' },
        { slot: 'action', fixed: 'right', title: 'Thao tác', minWidth: 120 },
      ],
      tabList: [],
      loading: false,
      activeItem: {},
      selectIds: [],
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
    // Lấy danh sách livestream
    getList() {
      this.loading = true;
      liveList(this.formValidate).then((res) => {
        this.total = res.data.count;
        this.tabList = res.data.list;
        this.loading = false;
      });
    },
    // Chọn
    selChange() {
      this.formValidate.page = 1;
      this.getList();
    },
    // Thêm phòng livestream
    menusAdd() {
      this.$router.push({
        path: this.$routeProStr + '/marketing/live/add_live_room',
      });
    },
    // Hiện/ẩn phòng livestream
    onchangeIsShow({ id, is_show }) {
      liveShow(id, is_show)
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    //  Chi tiết
    detail(row) {
      this.modals = true;
      this.$refs.studioDetail.getData(row.id);
    },
    // Thêm sản phẩm vào phòng livestream
    addGoods(row) {
      this.selectIds = row.product_ids;
      this.activeItem = row;
      this.isShowBox = true;
    },
    getData(data) {
      liveRoomGoodsAdd({
        room_id: this.activeItem.id,
        goods_ids: data,
      })
        .then((res) => {
          this.$message.success(res.msg);
          this.isShowBox = false;
          this.$refs.liveAdd.goodsList = [];
        })
        .catch((error) => {
          this.$message.error(error.msg);
          this.isShowBox = false;
          this.$refs.liveAdd.goodsList = [];
        });
    },
    // Đồng bộ phòng livestream
    syncRoom() {
      liveSyncRoom()
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((error) => {
          this.$message.error(error.msg);
        });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `live/room/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tabList.splice(num, 1);

          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    getProductId(data) {
      let arr = [];
      data.map((el) => {
        arr.push(el.product_id);
      });
      this.getData(arr);
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .goodList .ivu-input-group {
  width: 200% !important;
}
</style>
