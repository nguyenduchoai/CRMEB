<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mt" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="userFrom"
          :model="userFrom"
          label-width="80px"
          label-position="right"
          @submit.native.prevent
          inline
        >
          <div class="acea-row search-form" v-if="!collapse">
            <div>
              <el-form-item label="Tìm người dùng:" label-for="nickname">
                <el-input v-model="userFrom.nickname" placeholder="Vui lòng nhập thông tin người dùng" clearable class="form_content_width">
                  <el-select v-model="field_key" slot="prepend" style="width: 100px">
                    <el-option value="all" label="Tất cả"></el-option>
                    <el-option value="uid" label="UID"></el-option>
                    <el-option value="phone" label="Số điện thoại"></el-option>
                    <el-option value="nickname" label="Biệt danh người dùng"></el-option>
                  </el-select>
                </el-input>
              </el-form-item>
              <el-form-item label="Hạng người dùng:" label-for="level">
                <el-select v-model="level" placeholder="Vui lòng chọn hạng người dùng" clearable class="form_content_width">
                  <el-option value="all" label="Tất cả">Tất cả</el-option>
                  <el-option
                    :value="item.id"
                    v-for="(item, index) in levelList"
                    :key="index"
                    :label="item.name"
                  ></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Nhóm người dùng:">
                <el-select v-model="group_id" placeholder="Vui lòng chọn nhóm người dùng" clearable class="form_content_width">
                  <el-option value="all" label="Tất cả"></el-option>
                  <el-option
                    :value="item.id"
                    v-for="(item, index) in groupList"
                    :key="index"
                    :label="item.group_name"
                  ></el-option>
                </el-select>
              </el-form-item>
            </div>
            <el-form-item class="search-form-sub">
              <el-button type="primary" v-db-click @click="userSearchs">Tìm kiếm</el-button>
              <el-button class="ResetSearch" v-db-click @click="reset('userFrom')">Đặt lại</el-button>
              <a class="ivu-ml-8 font12 ml10" v-db-click @click="collapse = !collapse">
                <template v-if="!collapse"> Mở rộng <i class="el-icon-arrow-down" /> </template>
                <template v-else> Thu gọn <i class="el-icon-arrow-up" /> </template>
              </a>
            </el-form-item>
          </div>
          <div v-if="collapse" class="acea-row search-form">
            <div class="search-form-box">
              <el-form-item label="Tìm người dùng:" label-for="nickname">
                <el-input v-model="userFrom.nickname" placeholder="Vui lòng nhập thông tin người dùng" clearable class="form_content_width">
                  <el-select v-model="field_key" slot="prepend" style="width: 100px">
                    <el-option value="all" label="Tất cả"></el-option>
                    <el-option value="uid" label="UID"></el-option>
                    <el-option value="phone" label="Số điện thoại"></el-option>
                    <el-option value="nickname" label="Biệt danh người dùng"></el-option>
                  </el-select>
                </el-input>
              </el-form-item>
              <el-form-item label="Hạng người dùng:" label-for="level">
                <el-select v-model="level" placeholder="Vui lòng chọn hạng người dùng" clearable class="form_content_width">
                  <el-option value="all" label="Tất cả">Tất cả</el-option>
                  <el-option
                    :value="item.id"
                    v-for="(item, index) in levelList"
                    :key="index"
                    :label="item.name"
                  ></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Nhóm người dùng:">
                <el-select v-model="group_id" placeholder="Vui lòng chọn nhóm người dùng" clearable class="form_content_width">
                  <el-option value="all" label="Tất cả"></el-option>
                  <el-option
                    :value="item.id"
                    v-for="(item, index) in groupList"
                    :key="index"
                    :label="item.group_name"
                  ></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Cấp độ CTV:">
                <el-select v-model="agent_level" placeholder="Vui lòng chọn cấp độ CTV" clearable class="form_content_width">
                  <el-option value="all" label="Tất cả"></el-option>
                  <el-option
                    :value="item.grade"
                    v-for="(item, index) in membershipList"
                    :key="index"
                    :label="item.name"
                  ></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Nhãn người dùng:" label-for="label_id">
                <div class="labelInput acea-row row-between-wrapper" v-db-click @click="openSelectLabel">
                  <div style="width: 222px">
                    <div v-if="selectDataLabel.length">
                      <el-tag :closable="false" v-for="(item, index) in selectDataLabel" :key="index" class="mr10">{{
                        item.label_name
                      }}</el-tag>
                    </div>
                    <span class="span" v-else>Chọn nhãn gắn cho người dùng</span>
                  </div>
                  <div class="ivu-icon ivu-icon-ios-arrow-down"></div>
                </div>
              </el-form-item>
              <el-form-item label="Loại người dùng:">
                <el-select v-model="userFrom.is_promoter" placeholder="Vui lòng chọn" clearable class="form_content_width">
                  <el-option value="" label="Tất cả"></el-option>
                  <el-option value="1" label="Cộng tác viên"></el-option>
                  <el-option value="0" label="Người dùng thường"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Thành viên trả phí:" label-for="isMember">
                <el-select v-model="userFrom.isMember" placeholder="Vui lòng chọn" clearable class="form_content_width">
                  <el-option value="" label="Tất cả"></el-option>
                  <el-option value="1" label="Có"></el-option>
                  <el-option value="0" label="Không"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Số dư tài khoản:" label-for="balance">
                <el-input
                  clearable
                  placeholder="Tối thiểu"
                  v-model="userFrom.balance[0]"
                  class="form_range_content_width"
                />
                ~
                <el-input
                  clearable
                  placeholder="Tối đa"
                  v-model="userFrom.balance[1]"
                  class="form_range_content_width"
                />
              </el-form-item>
              <el-form-item label="Điểm thưởng còn lại:" label-for="integral">
                <el-input
                  clearable
                  placeholder="Tối thiểu"
                  v-model="userFrom.integral[0]"
                  class="form_range_content_width"
                />
                ~
                <el-input
                  clearable
                  placeholder="Tối đa"
                  v-model="userFrom.integral[1]"
                  class="form_range_content_width"
                />
              </el-form-item>
              <el-form-item label="Lần mua gần nhất:" label-for="before_pay_time">
                <el-date-picker
                  clearable
                  v-model="before_pay_time"
                  type="daterange"
                  :editable="false"
                  @change="(e) => onchangeTime(e, 'before_pay_time')"
                  format="yyyy/MM/dd"
                  value-format="yyyy/MM/dd"
                  start-placeholder="Ngày bắt đầu"
                  end-placeholder="Ngày kết thúc"
                  :picker-options="pickerOptions"
                  style="width: 250px"
                ></el-date-picker>
              </el-form-item>
              <el-form-item label="Số lần đặt hàng:" label-for="pay_count">
                <el-input
                  clearable
                  placeholder="Tối thiểu"
                  v-model="userFrom.pay_count_num[0]"
                  class="form_range_content_width"
                />
                ~
                <el-input
                  clearable
                  placeholder="Tối đa"
                  v-model="userFrom.pay_count_num[1]"
                  class="form_range_content_width"
                />
              </el-form-item>
              <el-form-item label="Số tiền chi tiêu:" label-for="store_name">
                <el-input
                  clearable
                  placeholder="Tối thiểu"
                  v-model="userFrom.pay_count_money[0]"
                  class="form_range_content_width"
                />
                ~
                <el-input
                  clearable
                  placeholder="Tối đa"
                  v-model="userFrom.pay_count_money[1]"
                  class="form_range_content_width"
                />
              </el-form-item>
              <el-form-item label="Số lần nạp tiền:" label-for="store_name">
                <el-input
                  clearable
                  placeholder="Tối thiểu"
                  v-model="userFrom.recharge_count[0]"
                  class="form_range_content_width"
                />
                ~
                <el-input
                  clearable
                  placeholder="Tối đa"
                  v-model="userFrom.recharge_count[1]"
                  class="form_range_content_width"
                />
              </el-form-item>
              <el-form-item label="Tình trạng truy cập:" label-for="user_time_type">
                <el-select v-model="user_time_type" placeholder="Vui lòng chọn tình trạng truy cập" clearable class="form_content_width">
                  <el-option value="" label="Tất cả"></el-option>
                  <el-option value="visitno" label="Không truy cập trong khoảng thời gian"></el-option>
                  <el-option value="visit" label="Có truy cập trong khoảng thời gian"></el-option>
                  <el-option value="add_time" label="Truy cập lần đầu"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Thời gian truy cập:" label-for="user_time" v-if="user_time_type">
                <el-date-picker
                  clearable
                  v-model="timeVal"
                  type="daterange"
                  :editable="false"
                  @change="(e) => onchangeTime(e, 'user_time')"
                  format="yyyy/MM/dd"
                  value-format="yyyy/MM/dd"
                  start-placeholder="Ngày bắt đầu"
                  end-placeholder="Ngày kết thúc"
                  :picker-options="pickerOptions"
                  style="width: 250px"
                ></el-date-picker>
              </el-form-item>
              <!-- <el-form-item label="Khu vực:" label-for="country">
                <el-select
                  v-model="userFrom.country"
                  placeholder="Vui lòng chọn quốc gia"
                  clearable
                  @change="changeCountry"
                  class="form_content_width"
                >
                  <el-option value="domestic" label="Trung Quốc"></el-option>
                  <el-option value="abroad" label="Nước ngoài"></el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="Tỉnh/Thành phố:" v-if="userFrom.country === 'domestic'">
                <el-cascader
                  :options="addresData"
                  :value="address"
                  v-model="address"
                  @change="handleChange"
                  clearable
                  style="width: 250px"
                ></el-cascader>
              </el-form-item> -->
            </div>

            <el-form-item class="search-form-sub">
              <el-button type="primary" label="default" v-db-click @click="userSearchs">Tìm kiếm</el-button>
              <el-button class="ResetSearch" v-db-click @click="reset('userFrom')">Đặt lại</el-button>
              <a class="ivu-ml-8 font12 ml10" v-db-click @click="collapse = !collapse">
                <template v-if="!collapse"> Mở rộng <i class="el-icon-arrow-down" /> </template>
                <template v-else> Thu gọn <i class="el-icon-arrow-up" /> </template>
              </a>
            </el-form-item>
          </div>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never" class="ivu-mt mt16" :body-style="{ padding: '0 20px 20px' }">
      <el-tabs v-model="userFrom.user_type" @tab-click="onClickTab">
        <el-tab-pane :label="item.name" :name="item.type" v-for="(item, index) in headeNum" :key="index" />
      </el-tabs>
      <el-row :gutter="24" justify="space-between">
        <el-col :span="24">
          <el-button v-auth="['admin-user-save']" type="primary" v-db-click @click="edit({ uid: 0 })"
            >Thêm người dùng</el-button
          >
          <el-button v-auth="['admin-user-coupon']" v-db-click @click="onSend">Gửi phiếu giảm giá</el-button>
          <el-button
            v-auth="['admin-wechat-news']"
            class="greens mr10"
            v-db-click
            @click="onSendPic"
            v-if="userFrom.user_type === 'wechat'"
          >
            Gửi tin bài
          </el-button>
          <el-button v-auth="['admin-user-group_set']" v-db-click @click="setGroup">Đặt nhóm hàng loạt</el-button>
          <el-button v-auth="['admin-user-set_label']" v-db-click @click="setLabel">Đặt nhãn hàng loạt</el-button>
          <el-button class="mr10" v-db-click @click="exportList">Xuất</el-button>

          <!-- <el-button v-auth="['admin-user-synchro']" class="mr20" v-db-click @click="synchro">Đồng bộ người dùng OA WeChat</el-button> -->
        </el-col>
        <el-col :span="24" class="userAlert" v-if="selectionList.length">
          <el-alert show-icon>
            <template slot="title">
              Đã chọn<i class="userI"> {{ selectionList.length }} </i>mục
            </template>
          </el-alert>
        </el-col>
      </el-row>
      <el-table
        :data="userLists"
        class="mt16"
        ref="table"
        highlight-current-row
        v-loading="loading"
        empty-text="Chưa có dữ liệu"
        no-filtered-userFrom-text="Không có kết quả phù hợp"
        @sort-change="sortChanged"
        @select="handleSelectRow"
        @select-all="handleSelectAll"
      >
        <el-table-column type="expand">
          <template slot-scope="scope">
            <expandRow :row="scope.row"></expandRow>
          </template>
        </el-table-column>
        <el-table-column type="selection" :selectable="isSel" width="55"> </el-table-column>
        <el-table-column label="ID người dùng" min-width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.uid }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Ảnh đại diện" min-width="60">
          <template slot-scope="scope">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="scope.row.avatar" />
            </div>
          </template>
        </el-table-column>
        <el-table-column label="Họ tên" min-width="150">
          <template slot-scope="scope">
            <div class="acea-row">
              <i class="el-icon-male" v-show="scope.row.sex === 'Nam'" style="color: #2db7f5; font-size: 15px"></i>
              <i class="el-icon-female" v-show="scope.row.sex === 'Nữ'" style="color: #ed4014; font-size: 15px"></i>
              <div v-text="scope.row.nickname" class=""></div>
            </div>
            <div v-if="scope.row.is_del == 1" style="color: red">Người dùng đã hủy tài khoản</div>
          </template>
        </el-table-column>
        <el-table-column label="Thành viên trả phí" min-width="90">
          <template slot-scope="scope">
            <div>{{ scope.row.isMember ? 'Có' : 'Không' }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Hạng người dùng" min-width="90">
          <template slot-scope="scope">
            <div>{{ scope.row.level }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Nhóm" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.group_id }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Cấp độ CTV" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.agent_level_name }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số điện thoại" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.phone }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Loại người dùng" min-width="100">
          <template slot-scope="scope">
            <div>{{ scope.row.user_type }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Số dư" prop="now_money" min-width="100" :sortable="true">
          <template slot-scope="scope">
            <div>{{ scope.row.now_money }}</div>
          </template>
        </el-table-column>
        <el-table-column label="Thao tác" fixed="right" width="120">
          <template slot-scope="scope">
            <template v-if="scope.row.is_del != 1">
              <a v-db-click @click="userDetail(scope.row)">Chi tiết</a>

              <el-divider direction="vertical"></el-divider>
              <el-dropdown size="small" @command="changeMenu(scope.row, $event, scope.$index)" :transfer="true">
                <span class="el-dropdown-link">Xem thêm<i class="el-icon-arrow-down el-icon--right"></i> </span>
                <el-dropdown-menu slot="dropdown">
                  <!-- <el-dropdown-item command="1">Sửa</el-dropdown-item> -->
                  <el-dropdown-item command="2">Chỉnh sửa số dư</el-dropdown-item>
                  <el-dropdown-item command="8">Chỉnh sửa điểm thưởng</el-dropdown-item>
                  <el-dropdown-item command="3">Tặng gói thành viên</el-dropdown-item>
                  <!--                                <el-dropdown-item command="4" v-if="row.vip_name">Xóa hạng</el-dropdown-item>-->
                  <el-dropdown-item command="5">Đặt nhóm</el-dropdown-item>
                  <el-dropdown-item command="6">Đặt nhãn</el-dropdown-item>
                  <el-dropdown-item command="7">Sửa người giới thiệu</el-dropdown-item>
                  <el-dropdown-item command="99" v-if="scope.row.spread_uid">Gỡ bỏ người giới thiệu</el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
            <template v-else>
              <a v-db-click @click="userDetail(scope.row)">Chi tiết</a>
            </template>
          </template>
        </el-table-column>
      </el-table>

      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="userFrom.page"
          :limit.sync="userFrom.limit"
          @pagination="pageChange"
        />
      </div>
    </el-card>
    <!-- Form sửa số dư điểm thưởng-->
    <edit-from ref="edits" :FromData="FromData" @submitFail="submitFail"></edit-from>
    <!-- Gửi phiếu giảm giá-->
    <send-from ref="sends" :userIds="ids.toString()"></send-from>
    <!-- Chi tiết thành viên-->
    <user-details ref="userDetails"></user-details>
    <!--Gửi tin bài -->
    <el-dialog :visible.sync="modal13" title="Gửi tin nhắn" width="1200px" class="modelBox">
      <news-category
        v-if="modal13"
        :isShowSend="isShowSend"
        :userIds="ids.toString()"
        :scrollerHeight="scrollerHeight"
        :contentTop="contentTop"
        :contentWidth="contentWidth"
        :maxCols="maxCols"
      ></news-category>
    </el-dialog>
    <!--Sửa người giới thiệu-->
    <el-dialog :visible.sync="promoterShow" title="Sửa người giới thiệu" width="540px" :show-close="true">
      <el-form ref="formInline" :model="formInline" label-width="100px" @submit.native.prevent>
        <el-form-item v-if="formInline" label="Chọn người giới thiệu:" prop="image">
          <div class="picBox" v-db-click @click="customer">
            <div class="pictrue" v-if="formInline.image">
              <img v-lazy="formInline.image" />
            </div>
            <div class="upLoad acea-row row-center-wrapper" v-else>
              <i class="el-icon-user"></i>
            </div>
          </div>
        </el-form-item>
      </el-form>
      <div class="acea-row row-right mt20">
        <el-button v-db-click @click="cancel('formInline')">Hủy</el-button>
        <el-button type="primary" v-db-click @click="putSend('formInline')">Gửi</el-button>
      </div>
    </el-dialog>
    <el-dialog :visible.sync="customerShow" title="Vui lòng chọn người dùng cửa hàng" :show-close="true" width="1000px">
      <customerInfo v-if="customerShow" @imageObject="imageObject"></customerInfo>
    </el-dialog>
    <el-dialog :visible.sync="labelShow" append-to-body title="Vui lòng chọn nhãn người dùng" width="540px" :show-close="true">
      <userLabel
        v-if="labelShow"
        :uid="labelActive.uid"
        :only_get="!labelActive.uid"
        @close="labelClose"
        @activeData="activeData"
        @onceGetList="onceGetList"
      ></userLabel>
    </el-dialog>
    <el-drawer
      custom-class="demo-drawer"
      :visible.sync="modals"
      :wrapperClosable="false"
      size="720"
      title="Điền thông tin người dùng"
    >
      <div class="demo-drawer__content">
        <userEdit ref="userEdit" v-if="modals" :userData="userData"></userEdit>
        <div class="fix_footer acea-row row-center">
          <el-button v-db-click @click="modals = false">Hủy</el-button>
          <el-button type="primary" v-db-click @click="setUser">Gửi</el-button>
        </div>
      </div>
    </el-drawer>
    <!-- Nhãn người dùng -->
    <el-dialog
      :visible.sync="selectLabelShow"
      append-to-body
      title="Vui lòng chọn nhãn người dùng"
      width="540px"
      :show-close="true"
      :close-on-click-modal="false"
    >
      <userLabel
        v-if="selectLabelShow"
        :uid="0"
        ref="userLabel"
        :only_get="true"
        :selectDataLabel="selectDataLabel"
        @activeData="activeSelectData"
        @close="labelClose"
      ></userLabel>
    </el-dialog>
  </div>
</template>

<script>
import userLabel from '@/components/userLabel';
import { mapState } from 'vuex';
import expandRow from './tableExpand.vue';
import userEdit from './handle/userEdit.vue';
import {
  userList,
  getUserData,
  isShowApi,
  editOtherApi,
  giveLevelApi,
  userSetGroup,
  userGroupApi,
  levelListApi,
  userSetLabelApi,
  userLabelApi,
  userSynchro,
  getUserSaveForm,
  giveLevelTimeApi,
  getUserInfo,
  setUser,
  editUser,
  saveSetLabel,
} from '@/api/user';
import { agentSpreadApi } from '@/api/agent';
import { exportUserList } from '@/api/export';
import editFrom from '../../../components/from/from';
import sendFrom from '@/components/sendCoupons/index';
import userDetails from './handle/userDetails';
import newsCategory from '@/components/newsCategory/index';
import customerInfo from '@/components/customerInfo';
import { cityList } from '@/api/app';
import { membershipDataListApi } from '@/api/membershipLevel';

export default {
  name: 'user_list',
  components: {
    expandRow,
    editFrom,
    sendFrom,
    userDetails,
    newsCategory,
    customerInfo,
    userLabel,
    userEdit,
  },
  data() {
    return {
      dataLabel: [],
      selectDataLabel: [],
      userData: {},
      modals: false,
      selectLabelShow: false,
      labelShow: false,
      customerShow: false,
      promoterShow: false,
      labelActive: {
        uid: 0,
      },
      formInline: {
        uid: 0,
        spread_uid: 0,
        image: '',
      },
      pickerOptions: this.$timeOptions,
      collapse: false,
      headeNum: [
        { type: '', name: 'Tất cả' },
        { type: 'wechat', name: 'OA WeChat' },
        { type: 'routine', name: 'WeChat Mini Program' },
        { type: 'h5', name: 'H5' },
        { type: 'pc', name: 'PC' },
        { type: 'app', name: 'APP' },
      ],
      address: [],
      addresData: [],
      isShowSend: true,
      modal13: false,
      maxCols: 4,
      scrollerHeight: '600',
      contentTop: '130',
      contentWidth: '98%',
      grid: {
        xl: 6,
        lg: 6,
        md: 8,
        sm: 12,
        xs: 24,
      },
      grid2: {
        xl: 8,
        lg: 8,
        md: 8,
        sm: 12,
        xs: 24,
      },
      loading: false,
      total: 0,
      userFrom: {
        label_id: '',
        user_type: '',
        status: '',
        sex: '',
        is_promoter: '',
        country: '',
        isMember: '',
        pay_count_num: ['', ''],
        balance: ['', ''],
        integral: ['', ''],
        pay_count_money: ['', ''],
        recharge_count: ['', ''],
        user_time_type: '',
        user_time: '',
        before_pay_time: '',
        nickname: '',
        province: '',
        city: '',
        page: 1,
        limit: 15,
        level: '',
        group_id: '',
        agent_level: '',
        field_key: '',
      },
      before_pay_time: '',
      field_key: '',
      level: '',
      group_id: '',
      agent_level: '',
      label_id: '',
      user_time_type: '',
      pay_count: '',
      userLists: [],
      FromData: null,
      selectionList: [],
      user_ids: '',
      selectedData: [],
      timeVal: [],
      groupList: [],
      levelList: [],
      membershipList: [],
      labelFrom: {
        page: 1,
        limit: '',
      },
      labelLists: [],
      selectedIds: [], //Id của mục đã chọn để gộp
      ids: [],
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
  },
  created() {
    this.getList();
    this.getCityList();
  },
  mounted() {
    this.userGroup();
    this.levelLists();
    this.membershipDataList();
    // this.groupLists();
  },
  methods: {
    getCityList() {
      cityList().then((res) => {
        this.addresData = res.data;
      });
    },
    setUser() {
      let data = this.$refs.userEdit.formItem;
      let ids = [];
      this.$refs.userEdit.dataLabel.map((i) => {
        ids.push(i.id);
      });
      data.label_id = ids;
      // if (!data.real_name) return this.$message.warning("Vui lòng nhập họ tên thật");
      // if (!data.phone) return this.$message.warning("Vui lòng nhập số điện thoại");
      // if (!data.pwd) return this.$message.warning("Vui lòng nhập mật khẩu");
      // if (!data.true_pwd) return this.$message.warning("Vui lòng nhập mật khẩu xác nhận");
      if (data.uid) {
        editUser(data)
          .then((res) => {
            this.modals = false;
            this.$message.success(res.msg);
            this.getList();
          })
          .catch((err) => {
            this.$message.error(err);
          });
      } else {
        setUser(data)
          .then((res) => {
            this.modals = false;
            this.$message.success(res.msg);
            this.getList();
          })
          .catch((err) => {
            this.$message.error(err.msg);
          });
      }
    },
    onceGetList() {
      this.labelActive.uid = 0;
      this.getList();
    },
    // Đóng popup nhãn
    labelClose() {
      this.labelActive.uid = 0;
      this.labelShow = false;
      this.selectLabelShow = false;
    },
    // Gửi
    putSend(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (!this.formInline.spread_uid) {
            return this.$message.error('Vui lòng chọn người dùng');
          }
          agentSpreadApi(this.formInline)
            .then((res) => {
              this.promoterShow = false;
              this.$message.success(res.msg);
              this.getList();
              this.$refs[name].resetFields();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        }
      });
    },

    save() {
      this.modals = true;

      // this.$modalForm(getUserSaveForm())
      //   .then(() => {
      //     this.userFrom.page = 1;
      //     this.getList();
      //   })
      //   .catch((res) => {
      //     this.$message.error(res.msg);
      //   });
    },
    synchro() {
      userSynchro()
        .then((res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    isSel(row) {
      return !!!row.is_del;
    },
    // Danh sách nhóm
    groupLists() {
      this.loading = true;
      userLabelApi(this.labelFrom)
        .then(async (res) => {
          let data = res.data;
          this.labelLists = data.list;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    onClickTab() {
      this.userFrom.page = 1;
      this.getList();
    },
    userGroup() {
      let data = {
        page: 1,
        limit: '',
      };
      userGroupApi(data).then((res) => {
        this.groupList = res.data.list;
      });
    },
    levelLists() {
      let data = {
        page: 1,
        limit: '',
        title: '',
        is_show: 1,
      };
      levelListApi(data).then((res) => {
        this.levelList = res.data.list;
      });
    },
    membershipDataList() {
      let data = {
        page: 1,
        limit: 0,
        staus: 1,
      };
      membershipDataListApi(data).then((res) => {
        this.membershipList = res.data.list;
      });
    },
    // Đặt nhóm theo lô;
    setGroup() {
      if (this.ids.length === 0) {
        this.$message.warning('Vui lòng chọn người dùng cần đặt nhóm');
      } else {
        let uids = { uids: this.ids };
        this.$modalForm(userSetGroup(uids)).then(() => {
          this.ids = [];
          this.selectedIds = [];
          this.getList();
        });
      }
    },
    // Đặt nhãn theo lô;
    setLabel() {
      if (this.ids.length === 0) {
        this.$message.warning('Vui lòng chọn người dùng cần gắn nhãn');
      } else {
        let uids = { uids: this.ids };
        this.labelActive.uid = 0;
        this.labelShow = true;
        // this.$modalForm(userSetLabelApi(uids)).then(() =>
        //   this.$refs.sends.getList()
        // );
      }
    },
    activeSelectData(data) {
      this.selectLabelShow = false;
      this.selectDataLabel = data || [];
      if (this.selectDataLabel.length) {
        let activeIds = [];
        this.selectDataLabel.map((item) => {
          activeIds.push(item.id);
        });
        this.userFrom.label_id = activeIds.join(',');
        this.getList();
      } else {
        this.userFrom.label_id = '';
      }
    },
    handleClose(tag) {
      let i = this.selectDataLabel.findIndex((item) => item.id === tag.id);
      if (i !== -1) {
        this.selectDataLabel.splice(i, 1);
      }
      this.$nextTick(() => {
        if (this.selectDataLabel.length) {
          let activeIds = [];
          this.selectDataLabel.map((item) => {
            activeIds.push(item.id);
          });
          this.userFrom.label_id = activeIds.join(',');
        } else {
          this.userFrom.label_id = '';
        }
      });
      // this.userSearchs();
    },
    // Đặt nhãn hàng loạt
    activeData(data) {
      let labels = [];
      if (!data.length) return;
      data.map((i) => {
        labels.push(i.id);
      });
      saveSetLabel({
        uids: this.ids.join(','),
        label_id: labels,
      }).then((res) => {
        this.labelShow = false;
        this.selectedIds = new Set();
        this.getList();
        this.$message.success(res.msg);
      });
    },
    //Có phải thành viên trả phí hay không;
    changeMember() {
      this.userFrom.page = 1;
      this.getList();
    },
    // Chọn quốc gia
    changeCountry() {
      if (this.userFrom.country === 'abroad' || !this.userFrom.country) {
        this.selectedData = [];
        this.userFrom.province = '';
        this.userFrom.city = '';
        this.address = [];
      }
    },
    // Chọn địa chỉ
    handleChange(selectedData) {
      this.selectedData = selectedData.map((o) => o.label);
      this.userFrom.province = this.selectedData[0];
      this.userFrom.city = this.selectedData[1];
    },
    // Ngày cụ thể
    onchangeTime(e, type) {
      this.userFrom[type] = e ? e.join('-') : '';
    },
    userDetail(row) {
      this.$refs.userDetails.modals = true;
      this.$refs.userDetails.getDetails(row.uid);
    },
    // Thao tác
    changeMenu(row, name, index) {
      let uid = [];
      uid.push(row.uid);
      let uids = { uids: uid };
      switch (name) {
        case '1':
          this.edit(row);
          break;
        case '2':
          this.getOtherFrom(row.uid, 'money');
          break;
        case '3':
          this.giveLevelTime(row.uid);
          break;
        case '4':
          this.del(row, 'xóa hạng thành viên của [ ' + this.tenText(row.nickname) + ' ]', index, 'user');
          break;
        case '5':
          this.$modalForm(userSetGroup(uids)).then(() => this.getList());
          break;
        case '6':
          this.openLabel(row);
          break;
        case '7':
          this.editS(row);
          break;
        case '8':
          this.getOtherFrom(row.uid, 'point');
          break;
        default:
          this.del(row, 'gỡ người giới thiệu của [ ' + this.tenText(row.nickname) + ' ]', index, 'tuiguang');
      }
    },
    tenText(str) {
      if (str.length > 10) {
        //Nếu độ dài ký tự vượt quá 10, các ký tự sau sẽ đổi thành ... có thể tự điều chỉnh độ dài và ký tự thay thế
        str = str.substr(0, 10) + '...'; //Cắt từ ký tự đầu tiên, lấy 10 ký tự tiếp theo, phần còn lại thay bằng ...
      }
      return str;
    },
    openLabel(row) {
      this.labelShow = true;
      this.labelActive.uid = row.uid;
    },
    openSelectLabel() {
      this.selectLabelShow = true;
    },
    editS(row) {
      this.promoterShow = true;
      this.formInline.uid = row.uid;
    },
    customer() {
      this.customerShow = true;
    },
    imageObject(e) {
      this.customerShow = false;
      this.formInline.spread_uid = e.uid;
      this.formInline.image = e.image;
    },
    cancel(name) {
      this.promoterShow = false;
      this.$refs[name].resetFields();
      this.formInline = {
        uid: 0,
        spread_uid: 0,
        image: '',
      };
    },
    // Tặng hạng thành viên
    giveLevel(id) {
      this.$modalForm(giveLevelApi(id)).then(() => this.getList(1));

      // giveLevelApi(id)
      //   .then(async (res) => {
      //     if (res.data.status === false) {
      //       return this.$authLapse(res.data);
      //     }

      //     this.FromData = res.data;
      //     this.$refs.edits.modals = true;
      //   })
      //   .catch((res) => {
      //     this.$message.error(res.msg);
      //   });
    },
    // Tặng hạng thành viên
    giveLevelTime(id) {
      this.$modalForm(giveLevelTimeApi(id)).then(() => this.getList(1));

      // giveLevelTimeApi(id)
      //   .then(async (res) => {
      //     if (res.data.status === false) {
      //       return this.$authLapse(res.data);
      //     }
      //     this.FromData = res.data;
      //     this.$refs.edits.modals = true;
      //   })
      //   .catch((res) => {
      //     this.$message.error(res.msg);
      //   });
    },
    // Xóa
    del(row, tit, num, name) {
      let delfromData = {
        title: tit,
        num: num,
        url: name === 'user' ? `user/del_level/${row.uid}` : `agent/stair/delete_spread/${row.uid}`,
        method: name === 'user' ? 'DELETE' : 'PUT',
        ids: '',
        width: 600,
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
    // Xóa hạng thành viên thành công
    submitModel() {
      this.getList();
    },
    // Danh sách thành viên
    getList() {
      // if (this.selectDataLabel.length) {
      //   let activeIds = [];
      //   this.selectDataLabel.forEach((item) => {
      //     activeIds.push(item.id);
      //   });
      //   this.userFrom.label_id = activeIds.join(',');
      // }
      this.userFrom.user_type = this.userFrom.user_type || '';
      this.userFrom.status = this.userFrom.status || '';
      this.userFrom.sex = this.userFrom.sex || '';
      this.userFrom.is_promoter = this.userFrom.is_promoter || '';
      this.userFrom.country = this.userFrom.country || '';
      this.userFrom.pay_count = this.pay_count === 'all' ? '' : this.pay_count;
      this.userFrom.user_time_type = this.user_time_type === 'all' ? '' : this.user_time_type;
      this.userFrom.field_key = this.field_key === 'all' ? '' : this.field_key;
      this.userFrom.level = this.level === 'all' ? '' : this.level;
      this.userFrom.group_id = this.group_id === 'all' ? '' : this.group_id;
      this.userFrom.agent_level = this.agent_level === 'all' ? '' : this.agent_level;
      this.loading = true;
      userList(this.userFrom)
        .then(async (res) => {
          let data = res.data;
          this.userLists = data.list;

          this.total = data.count;
          this.loading = false;
          this.$nextTick(() => {
            this.setChecked();
          });
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Xuất người dùng
    async exportList() {
      if (this.ids.length) {
        this.userFrom.ids = this.ids;
      }
      this.userFrom.user_type = this.userFrom.user_type || '';
      this.userFrom.status = this.userFrom.status || '';
      this.userFrom.sex = this.userFrom.sex || '';
      this.userFrom.is_promoter = this.userFrom.is_promoter || '';
      this.userFrom.country = this.userFrom.country || '';
      this.userFrom.pay_count = this.pay_count === 'all' ? '' : this.pay_count;
      this.userFrom.user_time_type = this.user_time_type === 'all' ? '' : this.user_time_type;
      this.userFrom.field_key = this.field_key === 'all' ? '' : this.field_key;
      this.userFrom.level = this.level === 'all' ? '' : this.level;
      this.userFrom.group_id = this.group_id === 'all' ? '' : this.group_id;
      this.userFrom.agent_level = this.agent_level === 'all' ? '' : this.agent_level;
      let [th, filekey, data, fileName] = [[], [], [], ''];
      //   let fileName = "";
      let excelData = JSON.parse(JSON.stringify(this.userFrom));
      excelData.page = 1;
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
        exportUserList(excelData).then((res) => {
          resolve(res.data);
        });
      });
    },
    pageChange() {
      this.selectionList = [];
      this.getList();
    },

    // Tìm kiếm
    userSearchs() {
      this.userFrom.page = 1;
      this.getList();
    },
    // Đặt lại
    reset(name) {
      this.userFrom = {
        label_id: '',
        status: '',
        sex: '',
        is_promoter: '',
        country: '',
        isMember: '',
        pay_count_num: ['', ''],
        balance: ['', ''],
        integral: ['', ''],
        pay_count_money: ['', ''],
        recharge_count: ['', ''],
        user_time_type: '',
        user_time: '',
        before_pay_time: '',
        nickname: '',
        province: '',
        city: '',
        page: 1,
        limit: 15,
        level: '',
        group_id: '',
        agent_level: '',
        field_key: '',
        page: 1, // Trang hiện tại
        limit: 20, // Số mục hiển thị mỗi trang
      };
      this.field_key = '';
      this.level = '';
      this.group_id = '';
      this.agent_level = '';
      this.dataLabel = [];
      this.selectDataLabel = [];
      this.user_time_type = '';
      this.pay_count = '';
      this.timeVal = [];
      this.selectedIds = new Set();
      this.getList();
    },
    // Lấy dữ liệu form sửa
    getUserFrom(id) {
      getUserInfo(id)
        .then(async (res) => {
          this.modals = true;
          this.userData = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Lấy form số dư điểm thưởng
    getOtherFrom(id, type) {
      this.$modalForm(editOtherApi(id, type)).then(() => this.getList(1));
    },
    // Sửa trạng thái
    onchangeIsShow(row) {
      let data = {
        id: row.uid,
        status: row.status,
      };
      isShowApi(data)
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Click để gửi phiếu giảm giá
    onSend() {
      if (this.ids.length === 0) {
        this.$message.warning('Vui lòng chọn người dùng cần gửi phiếu giảm giá');
      } else {
        this.$refs.sends.modals = true;
        this.$refs.sends.getList();
      }
    },
    // Gửi tin bài
    onSendPic() {
      if (this.ids.length === 0) {
        this.$message.warning('Vui lòng chọn người dùng cần gửi tin bài');
      } else {
        this.modal13 = true;
      }
    },
    // Sửa
    edit(row) {
      this.getUserFrom(row.uid);
    },
    // Sửa thành công
    submitFail() {
      // this.getList();
    },
    // Thứ tự sắp xếp
    sortChanged(e, props, order) {
      this.userFrom[e.prop] = e.order;
      this.getList();
    },
    //Kích hoạt khi chọn tất cả và bỏ chọn tất cả
    handleSelectAll(selection) {
      let ids = [];
      selection.map((e) => {
        ids.push(e.uid);
      });
      this.selectedIds = ids;
      this.$nextTick(() => {
        //Đảm bảo dom đã tải xong
        this.setChecked();
      });
    },
    //  Chọn một dòng
    handleSelectRow(selection, row) {
      let ids = [];
      selection.map((e) => {
        ids.push(e.uid);
      });
      this.selectedIds = ids;
      this.$nextTick(() => {
        //Đảm bảo dom đã tải xong
        this.setChecked();
      });
    },
    setChecked() {
      //Chuyển new Set() thành mảng
      this.ids = [...this.selectedIds];
      // Tìm dom tương ứng với ref của table đã bind, tìm đối tượng objData của table, objData lưu dữ liệu của trang hiện tại
      let objData = this.$refs.table.objData;
      for (let index in objData) {
        if (this.selectedIds.has(objData[index].uid)) {
          objData[index]._isChecked = true;
        }
      }
    },
  },
};
</script>

<style scoped lang="scss">
::v-deep .el-tabs__item {
  height: 54px !important;
  line-height: 54px !important;
}

.picBox {
  display: inline-block;
  cursor: pointer;

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
    font-size: 24px;
    font-weight: 500;
  }

  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
    }
  }
}
.fix_footer {
  position: fixed;
  bottom: 0;
  width: -webkit-fill-available;
  background: #fff;
  padding: 20px 0px;
  box-sizing: border-box;
  z-index: 100;
}
.userFrom {
  ::v-deep .ivu-form-item-content {
    margin-left: 0px !important;
  }
}

.userAlert {
  margin-top: 20px;
}

.userI {
  color: var(--prev-color-primary);
  font-style: normal;
}

img {
  height: 36px;
  display: block;
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

.tabBox_tit {
  width: 60%;
  font-size: 12px !important;
  margin: 0 2px 0 10px;
  letter-spacing: 1px;
  padding: 5px 0;
  box-sizing: border-box;
}

.modelBox {
  ::v-deep .ivu-modal-body {
    padding: 0 16px 16px 16px !important;
  }
}

.vipName {
  color: #dab176;
}

.listbox {
  ::v-deep .ivu-divider-horizontal {
    margin: 0 !important;
  }
}

.labelInput {
  width: 250px;
  border: 1px solid #dcdee2;
  padding: 0 15px;
  border-radius: 5px;
  min-height: 30px;
  cursor: pointer;
  font-size: 12px;

  .span {
    color: #c5c8ce;
  }

  .ivu-icon-ios-arrow-down {
    font-size: 14px;
    color: #808695;
  }
}

.demo-drawer-footer {
  width: 100%;
  position: absolute;
  bottom: 0;
  left: 0;
  border-top: 1px solid #e8e8e8;
  padding: 10px 16px;
  text-align: right;
  background: #fff;
}

.search-form {
  display: flex;
  justify-content: space-between;

  .search-form-box {
    display: flex;
    flex-wrap: wrap;
    flex: 1;
  }
}

.search-form-sub {
  display: flex;
}
</style>
