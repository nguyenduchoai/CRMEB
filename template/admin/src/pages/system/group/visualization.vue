<template>
  <div :style="bgcolors">
    <div class="i-layout-page-header header-title">
      <span class="ivu-page-header-title mr20">{{ $route.meta.title }}</span>
      <div>
        <div style="float: right">
          <el-button class="bnt" type="primary" v-db-click @click="save">Lưu</el-button>
        </div>
      </div>
    </div>
    <div class="box-wrapper h100">
      <div class="left-wrapper" v-if="!$route.params.id && groupAll.length">
        <div class="tree-vis">
          <div
            class="tab-item"
            :class="{ active: item.id == pageId }"
            v-for="(item, index) in groupAll"
            :key="index"
            v-db-click
            @click="edits(item)"
          >
            {{ item.name }}
          </div>
          <div class="tab-item" :class="{ active: pageId == 1617 }" v-db-click @click="edits(2)">Quảng cáo màn hình khởi động</div>
        </div>
      </div>
      <div v-if="name == 'user_recharge_quota'" class="iframe">
        <div class="iframe-boxs">
          <div class="moneyBox">
            <div class="box1">
              <div class="font1">Số dư của tôi</div>
              <div>￥ <i class="font2">0.00</i></div>
            </div>
            <div class="moneyBox_content">
              <div class="box2">
                <div>Nạp tiền vào tài khoản</div>
                <div>Chuyển hoa hồng vào số dư</div>
              </div>
              <div class="box3">
                <div v-show="item.status != 0" class="box3_box" v-for="(item, index) in sginList.list" :key="index">
                  <div>{{ item.price }}<i class="font">đ</i></div>
                  <div class="font">Tặng: {{ item.give_money }}đ</div>
                </div>
                <div class="box3_box">
                  <div class="other">Khác</div>
                </div>
              </div>
              <div class="box4">
                <div class="tips">Lưu ý:</div>
                <div class="tips-samll">
                  <p>Số tiền đã nạp vào tài khoản không thể rút, có thể dùng để mua sắm tại cửa hàng.</p>
                  <p>Hoa hồng sau khi chuyển vào tài khoản không thể chuyển ra lại, không thể rút tiền.</p>
                  <p>Nếu gặp sự cố khi nạp tiền, vui lòng liên hệ CSKH của cửa hàng hoặc gọi đường dây nóng CSKH: 40088888889.</p>
                </div>
              </div>
              <div class="box5">Nạp tiền ngay</div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="name == 'admin_login_slide'" class="pciframe" :bordered="false" shadow="never">
        <img src="../../../assets/images/pclogin.png" class="pciframe-box" />
        <div class="pcmoddile_goods">
          <div class="nofont" v-if="tabList.list == ''">Chưa có ảnh, vui lòng thêm~</div>
          <swiper v-else :options="swiperOption" class="pcswiperimg_goods">
            <swiper-slide class="spcwiperimg_goods" v-for="(item, index) in tabList.list" :key="index">
              <img :src="item.slide" />
            </swiper-slide>
          </swiper>
        </div>
      </div>
      <div v-if="name == 'integral_shop_banner'" class="iframe" :bordered="false">
        <div class="iframe-box">
          <img src="../../../assets/images/integral.png" style="width: 100%" />
          <div class="moddile_goods">
            <div class="nofonts" v-if="tabList.list == '' || !tabList.list.length || !tabList.list[0].img">
              Chưa có ảnh, vui lòng thêm~
            </div>
            <swiper v-else :options="swiperOption" class="pcswiperimg_goods">
              <swiper-slide class="swiperimg_goods" v-for="(item, index) in tabList.list" :key="index">
                <img :src="item.img" />
              </swiper-slide>
            </swiper>
          </div>
        </div>
      </div>
      <div
        v-if="
          name != 'sign_day_num' &&
          name != 'admin_login_slide' &&
          name != 'user_recharge_quota' &&
          name != 'integral_shop_banner' &&
          a != 1 &&
          guide != 2
        "
        class="iframe"
        :bordered="false"
        shadow="never"
      >
        <iframe :src="url" class="iframe-box" frameborder="0"></iframe>
        <div class="moddile"></div>
        <div
          v-if="
            name == 'routine_home_bast_banner' ||
            name == 'routine_home_hot_banner' ||
            name == 'routine_home_new_banner' ||
            name == 'routine_home_benefit_banner'
          "
          class="moddile_box"
        >
          <div class="nofonts" v-if="!tabList.list || !tabList.list.length || !tabList.list[0].img">
            Chưa có ảnh, vui lòng thêm~
          </div>
          <swiper v-else :options="swiperOption" class="swiperimg">
            <swiper-slide class="swiperimg" v-for="(item, index) in tabList.list" :key="index">
              <img :src="item.img" />
            </swiper-slide>
          </swiper>
        </div>
        <div v-if="name == 'combination_banner'" class="moddile_goods">
          <div class="nofonts" v-if="tabList.list == ''">Chưa có ảnh, vui lòng thêm~</div>
          <swiper v-else :options="swiperOption" class="swiperimg_goods">
            <swiper-slide class="swiperimg_goods" v-for="(item, index) in tabList.list" :key="index">
              <img :src="item.img" />
            </swiper-slide>
          </swiper>
        </div>
      </div>
      <div v-if="name == 'sign_day_num'" class="iframe" :bordered="false">
        <div class="iframe-box">
          <img v-if="bgimg == 1" src="../../../assets/images/bluesgin.png" />
          <img v-if="bgimg == 2" src="../../../assets/images/greesgin.png" />
          <img v-if="bgimg == 3" src="../../../assets/images/redsgin.png" />
          <img v-if="bgimg == 4" src="../../../assets/images/pinksgin.png" />
          <img v-if="bgimg == 5" src="../../../assets/images/oragesgin.png" />
        </div>
      </div>
      <div v-if="a == 1" class="iframe" :bordered="false">
        <div class="agreement-box">
          <div class="template"></div>
          <div class="htmls_box">
            <div class="htmls_top">Thỏa thuận dịch vụ và Chính sách bảo mật</div>
            <div class="htmls_font">
              <div class="ok">Tôi đồng ý</div>
              <div>Không đồng ý</div>
            </div>
            <div class="htmls" v-html="formValidate.content"></div>
          </div>
        </div>
      </div>
      <div v-if="a == 1" style="margin-left: 40px">
        <div class="table_box">
          <div>
            <div v-bind="grid">
              <div class="title">Hiển thị trang quyền riêng tư:</div>
            </div>
          </div>
          <div>
            <el-form
              class="form"
              ref="formValidate"
              :model="formValidate"
              :rules="ruleValidate"
              :label-width="labelWidth"
              :label-position="labelPosition"
              @submit.native.prevent
            >
              <div class="goodsTitle acea-row"></div>
              <el-form-item label="" prop="content" style="margin: 0px">
                <WangEditor
                  style="width: 90%"
                  :content="formValidate.content"
                  @editorContent="getEditorContent"
                ></WangEditor>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
      <div v-if="guide == 2" class="iframe" :bordered="false">
        <div class="nofonts" v-if="tabList.list == '' || !tabList.list.length || !tabList.list[0].img">
          Chưa có ảnh, vui lòng thêm~
        </div>
        <swiper :options="swiperOption" class="swiperimgs" v-else>
          <swiper-slide class="swiperimgs" v-for="(item, index) in tabList.list" :key="index">
            <img :src="item.img" />
          </swiper-slide>
        </swiper>
      </div>
      <div v-if="name == 'sign_day_num'" style="margin-left: 20px">
        <div class="table_box">
          <div>
            <div v-bind="grid">
              <div class="title">Cài đặt số ngày điểm danh</div>
              <el-button
                type="primary"
                v-db-click
                @click="groupAdd('Thêm dữ liệu')"
                style="margin-left: 14px; margin-top: 30px"
                >Thêm dữ liệu</el-button
              >
            </div>
          </div>
          <div class="table">
            <el-table
              :data="cmsList"
              ref="table"
              class="mt14"
              v-loading="loading"
              highlight-current-row
              no-userFrom-text="Chưa có dữ liệu"
              no-filtered-userFrom-text="Không có kết quả phù hợp"
            >
              <el-table-column label="Mã số" width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.id }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Ngày thứ" min-width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.day }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Điểm thưởng nhận được" min-width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.sign_num }}</span>
                </template>
              </el-table-column>

              <el-table-column label="Khả dụng" min-width="80">
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
              <el-table-column label="Thứ tự sắp xếp" min-width="80">
                <template slot-scope="scope">
                  <span>{{ scope.row.sort }}</span>
                </template>
              </el-table-column>
              <el-table-column label="Thao tác" fixed="right" width="150">
                <template slot-scope="scope">
                  <a v-db-click @click="edit(scope.row, 'Sửa')">Sửa</a>
                  <el-divider direction="vertical"></el-divider>
                  <a v-db-click @click="del(scope.row, 'xóa thông tin này', scope.$index)">Xóa</a>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </div>
      <div v-if="name == 'user_recharge_quota'" style="margin-left: 20px">
        <div class="table_box">
          <div>
            <div v-bind="grid">
              <div class="title">Cài đặt mệnh giá nạp tiền</div>
              <el-button
                type="primary"
                v-db-click
                @click="groupAdd('Thêm dữ liệu')"
                style="margin-left: 14px; margin-top: 30px"
                >Thêm dữ liệu</el-button
              >
            </div>
          </div>
          <div class="table">
            <el-table
              :data="sginList.list"
              ref="table"
              class="mt14"
              v-loading="loading"
              highlight-current-row
              no-userFrom-text="Chưa có dữ liệu"
              no-filtered-userFrom-text="Không có kết quả phù hợp"
            >
              <el-table-column
                :label="item.title"
                :min-width="item.minWidth"
                v-for="(item, index) in columns1"
                :key="index"
              >
                <template slot-scope="scope">
                  <template v-if="item.key">
                    <div>
                      <span>{{ scope.row[item.key] }}</span>
                    </div>
                  </template>
                  <template v-else-if="item.slot === 'status'">
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
                  <template v-else-if="item.slot === 'action'">
                    <a v-db-click @click="edit(scope.row, 'Sửa')">Sửa</a>
                    <el-divider direction="vertical"></el-divider>
                    <a v-db-click @click="del(scope.row, 'xóa thông tin này', scope.$index)">Xóa</a>
                  </template>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </div>
      <div v-else>
        <div
          v-if="name != 'sign_day_num' && a != 1 && guide != 2"
          :class="name != 'admin_login_slide' ? 'content' : 'contents'"
        >
          <div class="right-box">
            <div class="hot_imgs">
              <div class="title" v-if="name == 'admin_login_slide'">Cài đặt trình chiếu</div>
              <div class="title" v-else>Cài đặt ảnh trình chiếu (banner)</div>
              <div class="title-text">Kích thước đề xuất: 690 * 240px, kéo thả ảnh để thay đổi thứ tự, tối đa 5 ảnh</div>
              <div class="list-box">
                <draggable
                  v-if="name == 'admin_login_slide'"
                  class="dragArea list-group Bbox"
                  :list="tabList.list"
                  group="peoples"
                  handle=".move-icon"
                >
                  <div class="items" v-for="(item, index) in tabList.list" :key="index">
                    <div class="move-icon">
                      <span class="iconfont icondrag2"></span>
                    </div>
                    <div class="img-box" v-db-click @click="modalPicTap('Chọn một', index)">
                      <img :src="item.slide" alt="" v-if="item.slide" />
                      <div class="upload-box" v-else>
                        <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                      </div>
                      <div class="delect-btn" v-db-click @click.stop="bindDelete(item, index)">
                        <i class="el-icon-circle-close" style="font-size: 24px" />
                      </div>
                    </div>
                    <div class="info"></div>
                  </div>
                </draggable>
                <draggable v-else class="dragArea list-group" :list="tabList.list" group="peoples" handle=".move-icon">
                  <div class="item" v-for="(item, index) in tabList.list" :key="index">
                    <div class="move-icon">
                      <span class="iconfont icondrag2"></span>
                    </div>
                    <div class="img-box" v-db-click @click="modalPicTap('Chọn một', index)">
                      <img :src="item.img" alt="" v-if="item.img" />
                      <div class="upload-box" v-else>
                        <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                      </div>
                      <div class="delect-btn" v-db-click @click.stop="bindDelete(item, index)">
                        <i class="el-icon-circle-close" style="font-size: 24px" />
                      </div>
                    </div>
                    <div class="info">
                      <div class="info-item">
                        <span>Tên hình ảnh:</span>
                        <div class="input-box">
                          <el-input v-model="item.comment" placeholder="Vui lòng nhập tên" />
                        </div>
                      </div>
                      <div class="info-item">
                        <span>Địa chỉ liên kết:</span>
                        <div class="input-box" v-db-click>
                          <el-input v-model="item.link" placeholder="Chọn liên kết">
                            <i class="el-icon-link" slot="suffix" @click="getLink(index)" />
                          </el-input>
                        </div>
                      </div>
                    </div>
                  </div>
                </draggable>
                <div>
                  <el-dialog
                    :visible.sync="modalPic"
                    width="950px"
                    title="Tải lên ảnh sản phẩm"
                    :close-on-click-modal="false"
                    :show-close="true"
                  >
                    <uploadPictures
                      :isChoice="isChoice"
                      @getPic="getPic"
                      :gridBtn="gridBtn"
                      :gridPic="gridPic"
                      v-if="modalPic"
                    ></uploadPictures>
                  </el-dialog>
                </div>
              </div>
              <template>
                <div class="add-btn">
                  <el-button
                    type="primary"
                    ghost
                    style="width: 100px; height: 35px; background-color: var(--prev-color-primary); color: #ffffff"
                    v-db-click
                    @click="addBox"
                    >Thêm ảnh
                  </el-button>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
      <div v-if="guide === 2" :class="name != 'admin_login_slide' ? 'content' : 'contents'">
        <div class="right-box">
          <div class="hot_imgs">
            <div class="title">Cài đặt trang hướng dẫn</div>
            <div class="title-text">Kích thước đề xuất: 750 * 1334px, kéo thả ảnh để thay đổi thứ tự, tối đa 5 ảnh</div>
            <div class="list-box">
              <div>
                <el-form :model="formItem" label-width="85px">
                  <el-form-item label="Quảng cáo màn hình khởi động:">
                    <el-switch active-value="1" inactive-value="0" v-model="formItem.status" size="large"> </el-switch>
                  </el-form-item>
                  <el-form-item label="Thời gian quảng cáo:">
                    <el-input
                      v-model.number="formItem.time"
                      type="number"
                      placeholder="Vui lòng nhập thời gian quảng cáo màn hình khởi động"
                      style="width: 150px"
                    ></el-input>
                    Đơn vị (giây)
                  </el-form-item>
                </el-form>
              </div>
              <draggable class="dragArea list-group" :list="tabList.list" group="peoples" handle=".move-icon">
                <div class="item" v-for="(item, index) in tabList.list" :key="index">
                  <div class="move-icon">
                    <span class="iconfont icondrag2"></span>
                  </div>
                  <div class="img-box" v-db-click @click="modalPicTap('Chọn một', index)">
                    <img :src="item.img" alt="" v-if="item.img" />
                    <div class="upload-box" v-else>
                      <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                    </div>
                    <div class="delect-btn" v-db-click @click.stop="bindDelete(item, index)">
                      <i class="el-icon-circle-close" style="font-size: 24px" />
                    </div>
                  </div>
                  <div class="info">
                    <div class="info-item">
                      <span>Tên hình ảnh:</span>
                      <div class="input-box">
                        <el-input v-model="item.comment" placeholder="Vui lòng nhập tên" />
                      </div>
                    </div>
                    <div class="info-item">
                      <span>Địa chỉ liên kết:</span>
                      <div class="input-box" v-db-click>
                        <el-input v-model="item.link" placeholder="Chọn liên kết">
                          <i class="el-icon-link" slot="suffix" @click="getLink(index)" />
                        </el-input>
                      </div>
                    </div>
                  </div>
                </div>
              </draggable>
              <div>
                <el-dialog :visible.sync="modalPic" width="950px" title="Tải lên ảnh sản phẩm" :close-on-click-modal="false">
                  <uploadPictures
                    :isChoice="isChoice"
                    @getPic="getPic"
                    :gridBtn="gridBtn"
                    :gridPic="gridPic"
                    v-if="modalPic"
                  ></uploadPictures>
                </el-dialog>
              </div>
            </div>
            <template v-if="tabList.list.length < 5">
              <div class="add-btn">
                <el-button
                  type="primary"
                  ghost
                  style="width: 100px; height: 35px; background-color: var(--prev-color-primary); color: #ffffff"
                  v-db-click
                  @click="addBox"
                  >Thêm ảnh
                </el-button>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
    <linkaddress ref="linkaddres" @linkUrl="linkUrl"></linkaddress>
  </div>
</template>

<script>
import WangEditor from '@/components/wangEditor/index.vue';
import Setting from '@/setting';
import { diyGetInfo, diySave, getColorChange } from '@/api/diy';
import { mapState } from 'vuex';
import editFrom from '@/components/from/from';
import { productGetTempKeysApi, uploadType } from '@/api/product';
import {
  groupAllApi,
  groupDataListApi,
  groupSaveApi,
  openAdvSave,
  groupDataAddApi,
  groupDataHeaderApi,
  groupDataEditApi,
  groupDataSetApi,
  getAgreement,
  setAgreement,
  getOpenAdv,
} from '@/api/system';
import draggable from 'vuedraggable';
import uploadPictures from '@/components/uploadPictures';
import linkaddress from '@/components/linkaddress';
import { getCookies } from '@/libs/util';

export default {
  name: 'list',
  components: {
    editFrom,
    draggable,
    uploadPictures,
    linkaddress,
    WangEditor,
  },
  computed: {
    bgcolors() {
      return {
        '--color-theme': this.bgCol,
      };
    },
    labelWidth() {
      return this.isMobile ? undefined : '120px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
    ...mapState('admin/layout', ['menuCollapse']),
  },
  data() {
    return {
      formValidate: {
        content: '',
      },
      ruleValidate: {},
      myConfig: {
        autoHeightEnabled: false, // Trình soạn thảo không tự động giãn cao theo nội dung
        initialFrameHeight: 500, // Chiều cao container ban đầu
        initialFrameWidth: '100%', // Chiều rộng container ban đầu
        UEDITOR_HOME_URL: '/UEditor/',
        serverUrl: '',
      },
      a: 0, //Kiểm tra thỏa thuận bảo mật
      guide: 0,
      bgimg: 0,
      columns1: [],
      bgCol: '',
      name: 'routine_home_bast_banner',
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      sginList: [],
      progress: 0, // Thanh tiến trình mặc định 0
      swiperOption: {
        //Hiển thị phân trang
        pagination: {
          el: '.swiper-pagination',
        },
        //Đặt bấm mũi tên
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        //Tự động trình chiếu
        autoplay: false,
        //Bật chế độ lặp
        loop: false,
      },
      url: '',
      BaseURL: Setting.apiBaseURL.replace(/adminapi/, ''),
      pageId: 0,
      theme3: 'light',
      tabList: [],
      upload_type: '', //Loại tải video lên 1 tải lên local 2 3 4 tải lên OSS
      uploadData: {}, // Tham số tải lên
      lastObj: {
        add_time: '',
        comment: '',
        gid: '',
        id: '',
        img: '',
        link: '',
        sort: '',
        status: 1,
      },
      isChoice: 'Chọn một',
      modalPic: false,
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      groupAll: [],
      activeIndex: 0,
      sortName: null,
      activeIndexs: 0,
      cmsList: [],
      loadingExist: false,
      formItem: {
        time: '',
        type: 'pic',
        status: 1,
        value: [],
        video_link: '',
      },
      fileUrl: Setting.apiBaseURL + '/file/upload',
      cardUrl: Setting.apiBaseURL + '/file/upload/1',
      header: {},
      type: 0,
      upload: {
        videoIng: false, // Có hiển thị thanh tiến trình không;
      },
    };
  },
  created() {
    this.color();
    this.uploadType();
    this.getToken();
  },
  mounted() {
    this.getGroupAll();
    this.info();
    this.url = this.BaseURL + 'pages/columnGoods/HotNewGoods/index?type=1';
  },
  methods: {
    getEditorContent(data) {
      this.formValidate.content = data;
    },
    // Xóa video;
    delVideo() {
      let that = this;
      that.$set(that.formItem, 'video_link', '');
    },
    //Lấy loại tải lên video
    uploadType() {
      uploadType().then((res) => {
        this.upload_type = res.data.upload_type;
      });
    },
    // Tải lên thành công
    handleSuccess(res, file, fileList) {
      if (res.status === 200) {
        this.$set(this.formItem, 'video_link', res.data.src);
        this.$message.success(res.msg);
      } else {
        this.$message.error(res.msg);
      }
    },
    zh_uploadFile() {
      if (this.video_link) {
        this.formValidate.video_link = this.video_link;
      } else {
        this.$refs.refid.click();
      }
    },
    zh_uploadFile_change(evfile) {
      let that = this;
      let suffix = evfile.target.files[0].name.substr(evfile.target.files[0].name.indexOf('.'));
      if (suffix.indexOf('.mp4') === -1) {
        return that.$message.error('Chỉ có thể tải lên tệp MP4');
      }
      let types = {
        key: evfile.target.files[0].name,
        contentType: evfile.target.files[0].type,
      };
      productGetTempKeysApi(types)
        .then((res) => {
          that.$videoCloud
            .videoUpload({
              type: res.data.type,
              evfile: evfile,
              res: res,
              uploading(status, progress) {
                that.upload.videoIng = status;
              },
            })
            .then((res) => {
              that.formValidate.video_link = res.url;
              that.$message.success('Tải lên video thành công');
            })
            .catch((res) => {
              that.$message.error(res);
            });
        })
        .catch((res) => {
          that.$message.error(res.msg);
        });
    },
    // Token trong header khi tải lên
    getToken() {
      this.header['Authori-zation'] = 'Bearer ' + getCookies('token');
    },
    beforeUpload() {
      this.uploadData = {};
      let promise = new Promise((resolve) => {
        this.$nextTick(function () {
          resolve(true);
        });
      });
      return promise;
    },
    linkUrl(e) {
      this.tabList.list[this.activeIndexs].link = e;
    },
    color() {
      getColorChange('color_change').then((res) => {
        switch (res.data.status) {
          case 1:
            this.bgCol = '#3875EA';
            this.bgimg = 1;
            break;
          case 2:
            this.bgCol = '#00C050';
            this.bgimg = 2;
            break;
          case 3:
            this.bgCol = '#E93323';
            this.bgimg = 3;
            break;
          case 4:
            this.bgCol = '#FF448F';
            this.bgimg = 4;
            break;
          case 5:
            this.bgCol = '#FE5C2D';
            this.bgimg = 5;
            break;
        }
      });
    },
    // Biểu mẫu thêm
    groupAdd() {
      this.$modalForm(groupDataAddApi({ gid: this.pageId, config_name: this.name }, 'setting/group_data/create')).then(
        () => {
          this.url = this.BaseURL + 'pages/users/user_sgin/index';
          this.info();
        },
      );
    },
    info() {
      groupDataListApi({ config_name: this.name }, 'setting/group_data')
        .then(async (res) => {
          this.tabList = res.data;
          if (this.name == 'admin_login_slide') {
            this.tabList.list.forEach((item, index, array) => {
              if (typeof item.slide != 'string' && item.slide != 'undefined') {
                item.slide = item.slide[0];
              }
            });
          } else if (this.name == 'sign_day_num') {
            this.cmsList = res.data.list;
          } else if (this.name == 'user_recharge_quota') {
            this.sginList = res.data;
          } else {
            this.tabList.list.forEach((item, index, array) => {
              if (typeof item.img != 'string' && item.img != 'undefined') {
                item.img = item.img[0];
              }
            });
          }
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    edits(row) {
      this.pageId = row.id || 0;
      this.name = row.config_name || '';
      if (row == 1) {
        this.a = 1;
        this.guide = 0;
        this.getAgreement();
      } else if (row == 2) {
        this.a = 0;
        this.guide = 2;
        this.pageId = 1617;
        getOpenAdv().then((res) => {
          if (res.data) {
            this.formItem = res.data;
            this.tabList.list = res.data.value;
          } else {
            this.formItem = {
              time: '',
              type: 'pic',
              status: 1,
              value: [],
              video_link: '',
            };
            this.tabList.list = [];
          }
        });
      } else {
        this.info();
        this.guide = 0;
        this.a = 0;
        switch (row.config_name) {
          case 'routine_home_bast_banner':
            this.url = this.BaseURL + 'pages/columnGoods/HotNewGoods/index?type=1&name=Sản phẩm tuyển chọn';
            break;
          case 'sign_day_num':
            this.url = '';
            this.getListHeader();
            break;
          case 'combination_banner':
            this.url = this.BaseURL + 'pages/activity/goods_combination/index';
            break;
          case 'routine_home_hot_banner':
            this.url = this.BaseURL + 'pages/columnGoods/HotNewGoods/index?type=2&name=Bảng xếp hạng hot';
            break;
          case 'routine_home_new_banner':
            this.url = this.BaseURL + 'pages/columnGoods/HotNewGoods/index?type=3&name=Hàng mới ra mắt';
            break;
          case 'routine_home_benefit_banner':
            this.url = this.BaseURL + 'pages/columnGoods/HotNewGoods/index?type=4&name=Sản phẩm khuyến mãi';
            break;
          case 'user_recharge_quota':
            this.url = '';
            this.getListHeader();
            break;
          case 'admin_login_slide':
            this.url = '';
            break;
          case 'integral_shop_banner':
            this.url = '';
            break;
        }
      }
    },
    addBox() {
      if (this.tabList.list.length == 0) {
        this.tabList.list.push(this.lastObj);
        this.lastObj = {
          add_time: '',
          comment: '',
          gid: '',
          id: '',
          img: '',
          link: '',
          sort: '',
          status: 1,
        };
      } else {
        if (this.tabList.list.length == 5) {
          this.$message.warning('Chỉ được thêm tối đa 5 ảnh nhé');
        } else {
          let obj = JSON.parse(JSON.stringify(this.lastObj));
          this.tabList.list.push(obj);
        }
      }
    },
    // Xóa
    bindDelete(item, index) {
      this.tabList.list.splice(index, 1);
    },
    // Click vào ảnh bìa bài viết ảnh-văn
    modalPicTap(title, index) {
      this.activeIndex = index;
      this.modalPic = true;
    },
    // Lấy thông tin ảnh
    getPic(pc) {
      this.$nextTick(() => {
        if (this.name == 'admin_login_slide') {
          this.tabList.list[this.activeIndex].slide = pc.att_dir;
        } else {
          this.tabList.list[this.activeIndex].img = pc.att_dir;
        }
        this.modalPic = false;
      });
    },
    save() {
      if (this.a == 1) {
        this.onsubmit('formValidate');
      } else if (this.guide == 2) {
        this.formItem.value = this.tabList.list;
        openAdvSave(this.formItem).then((res) => {
          this.$message.success(res.msg);
        });
      } else {
        this.loadingExist = true;
        groupSaveApi({
          gid: this.pageId,
          config_name: this.name,
          data: this.tabList.list,
        })
          .then((res) => {
            this.loadingExist = false;
            this.$message.success(res.msg);
          })
          .catch((err) => {
            this.loadingExist = false;
            this.$message.error(err.msg);
          });
      }
    },
    getLink(index) {
      this.activeIndexs = index;
      this.$refs.linkaddres.modals = true;
    },
    getListHeader() {
      this.loading = true;
      groupDataHeaderApi({ config_name: this.name }, 'setting/sign_data/header')
        .then((res) => {
          let data = res.data;
          let header = data.header;
          let index = [];
          this.columns1 = header;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // Sửa
    edit(row) {
      this.$modalForm(
        groupDataEditApi({ gid: this.pageId, config_name: this.name }, 'setting/group_data/' + row.id + '/edit'),
      ).then(() => {
        this.info();
        this.url = this.BaseURL + 'pages/users/user_sgin/index';
      });
    },
    // Xóa
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: 'setting/group_data/' + row.id,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.info();
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Sửa có hiển thị hay không
    onchangeIsShow(row) {
      groupDataSetApi('setting/group_data/set_status/' + row.id + '/' + row.status)
        .then(async (res) => {
          this.url = this.BaseURL + '/pages/users/user_sgin/index';
          this.$message.success(res.msg);
          this.info();
        })
        .catch((res) => {
          this.url = this.BaseURL + '/pages/users/user_sgin/index';
          this.$message.error(res.msg);
        });
    },
    getGroupAll() {
      groupAllApi()
        .then(async (res) => {
          this.groupAll = res.data;
          this.sortName = res.data[0].config_name;
          this.pageId = res.data[0].id;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    getContent(val) {
      this.formValidate.content = val;
    },
    // Gửi dữ liệu
    onsubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          setAgreement(this.formValidate)
            .then(async (res) => {
              this.$message.success(res.msg);
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
    //Chi tiết
    getAgreement() {
      getAgreement()
        .then(async (res) => {
          let data = res.data;
          this.formValidate = {
            content: data.content,
          };
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
  },
};
</script>

<style scoped lang="scss">
::v-deep .ivu-menu-vertical .ivu-menu-item-group-title {
  display: none;
}

::v-deep .ivu-menu-vertical.ivu-menu-light:after {
  display: none;
}

::v-deep .ivu-form-item-content {
  margin-left: 0px !important;
}

.nofont {
  text-align: center;
  line-height: 123px;
}

.nofonts {
  text-align: center;
  line-height: 105px;
}

.save {
  width: 100%;
  margin: 0 auto;
  text-align: center;
  background-color: #fff;
  bottom: 0;
  padding: 16px;
  border-top: 3px solid #f5f7f9;
}

.form {
  .goodsTitle {
    margin-bottom: 25px;
  }

  .goodsTitle ~ .goodsTitle {
    margin-top: 20px;
  }

  .goodsTitle .title {
    border-bottom: 2px solid var(--prev-color-primary);
    padding: 0 8px 12px 5px;
    color: #000;
    font-size: 14px;
  }

  .goodsTitle .icons {
    font-size: 15px;
    margin-right: 8px;
    color: #999;
  }

  .add {
    font-size: 12px;
    color: var(--prev-color-primary);
    padding: 0 12px;
    cursor: pointer;
  }

  .radio {
    margin-right: 20px;
  }

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
  }

  .iconfont {
    color: #898989;
  }

  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;
  }

  .pictrue img {
    width: 100%;
    height: 100%;
  }
}

.agreement-box {
  width: 310px;
  height: 550px;
  border-radius: 10px;
  background: rgba(0, 0, 0, 0);
  border: 1px solid #eeeeee;
  opacity: 1;
  position: relative;

  .template {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    border-radius: 10px;
    background-color: #817e81;
  }

  .htmls_box {
    font-size: 12px;
    width: 259px;
    height: 430px;
    border-radius: 10px;
    background-color: #fff;
    position: absolute;
    top: 58px;
    left: 26px;

    .htmls_top {
      position: absolute;
      top: 8px;
      left: 0;
      height: 34px;
      text-align: center;
      width: 100%;
      line-height: 35px;
      font-weight: 600;
      font-size: 20px;
    }

    .htmls_font {
      position: absolute;
      bottom: 0;
      left: 0;
      padding: 15px 15px;
      text-align: center;
      width: 100%;

      div {
        height: 35px;
        line-height: 35px;
        border-radius: 20px;
      }

      .ok {
        background-color: #f33316;
        color: #ffffff;
      }
    }

    .htmls {
      position: absolute;
      background-color: #fff;
      top: 50px;
      left: 0;
      width: 259px;
      height: 281px;
      border-radius: 4px;
      overflow: auto;
      padding: 5px 15px;
      word-break: break-word;
    }

    .htmls::-webkit-scrollbar {
      display: none;
    }
  }
}

.Bbox {
  width: 495px;
  display: flex;
  flex-wrap: wrap;
}

.item {
  margin-right: 15px;
  border: 1px dashed #dbdbdb;
  padding-bottom: 10px;
  padding-right: 15px;
  padding-top: 20px;
}

.items {
  margin-right: 15px;
  border: 1px dashed #dbdbdb;
  padding-bottom: 10px;
  padding-top: 15px;
  position: relative;
  display: flex;
  margin-top: 20px;

  .move-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 80px;
    cursor: move;
    color: #d8d8d8;
  }

  .img-box {
    position: relative;
    width: 80px;
    height: 80px;

    img {
      width: 100%;
      height: 100%;
    }
  }

  .info {
    flex: 1;
    margin-left: 22px;

    .info-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;

      span {
        // width 40px
        font-size: 13px;

        .input-box {
          flex: 1;
        }
      }
    }
  }

  .delect-btn {
    position: absolute;
    right: -12px;
    top: -12px;
    color: #999999;

    .iconfont {
      font-size: 28px;
      color: #999;
    }
  }
}

.table {
  width: 700px;
  color: #515a6e;
  font-size: 14px;
  background-color: #fff;
  margin-left: 20px;
}

.contents {
  width: 150px;

  .right-box {
    margin-left: 40px;
  }

  .title-text {
    width: 500px;
  }
}

.pciframe {
  margin-left: 20px;
  width: 430px;
  height: 280px;
  background: #ffffff;
  border: 1px solid #eeeeee;
  border-radius: 13px;
  position: relative;

  img {
    width: 100%;
    height: 100%;
  }

  .pciframe-box {
    background: rgba(0, 0, 0, 0);
    // border: 1px solid #EEEEEE;
    border-radius: 4px;
  }

  .pcmoddile_goods {
    position: absolute;
    top: 69px;
    width: 171px;
    height: 140px;
    border-top-left-radius: 2px;
    border-bottom-left-radius: 2px;
    left: 65px;
    background-color: #fff;
  }

  .pcswiperimg_goods {
    height: 140px;
    background-color: #f5f5f5;

    img {
      width: 100%;
      height: 100%;
    }
  }
}

.link {
  display: inline-block;
  width: 100%;
  height: 32px;
  line-height: 1.5;
  padding: 4px 7px;
  border: 1px solid #dcdee2;
  border-radius: 4px;
  background-color: #fff;
  position: relative;
  cursor: text;
  transition: border 0.2s ease-in-out, background 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  font-size: 13px;
  font-family: PingFangSC-Regular;
  line-height: 22px;
  color: rgba(0, 0, 0, 0.25);
  opacity: 1;
  cursor: pointer;

  .you {
    color: #999999;
    float: right;
    margin-right: 11px;
  }
}

.swiperimg {
  width: 310px;
  max-height: 126px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;

  img {
    width: 100%;
    height: 100%;
  }
}

.swiperimgs {
  width: 310px;
  height: 100%;
  border-radius: 10px;

  img {
    width: 100%;
    height: 100%;
  }
}

.swiperimg_goods {
  width: 284px;
  height: 124px;
  border-radius: 4px;
  line-height: 99px;
  text-align: center;
  background-color: #f5f5f5;

  img {
    width: 100%;
    height: 100%;
  }
}

.title {
  padding: 0 0 13px 0;
  font-weight: bold;
  font-size: 15px;
  border-left: 2px solid var(--prev-color-primary);
  height: 23px;
  padding-left: 10px;
}

.title-text {
  padding: 0 0 0px 16px;
  color: #999;
  font-size: 12px;
  margin-top: 10px;
}

.content {
  // width 510px;
  .right-box {
    margin-left: 40px;
  }
}

.box {
  border-top: 3px solid #f5f7f9;
  padding: 10px;
  padding-top: 25px;
  width: 100%;

  .save {
    background-color: var(--prev-color-primary);
    color: #ffffff;
    width: 71px;
    height: 30px;
    margin: 0 auto;
    text-align: center;
    line-height: 30px;
    cursor: pointer;
  }
}

.iframe {
  margin-left: 20px;
  position: relative;
  width: 310px;
  height: 550px;
  background: #ffffff;
  border: 1px solid #eeeeee;
  opacity: 1;
  border-radius: 10px;
}

.moddile {
  position: absolute;
  width: 310px;
  height: 550px;
  top: 0px;
  opacity: 0;
  left: 0px;
  border-radius: 4px;
}

.moddile_box {
  position: absolute;
  top: 0px;
  width: 310px;
  height: 115px;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  left: 0px;
  background-color: #f5f5f5;
}

.moddile_goods {
  position: absolute;
  top: 12px;
  width: 284px;
  height: 124px;
  /* border-top-left-radius: 10px; */
  /* border-top-right-radius: 10px; */
  border-radius: 5px;
  left: 13px;
  line-height: 99px;
  text-align: center;
  background-color: #f5f5f5;
}

.iframe-box {
  width: 310px;
  height: 100%;
  border-radius: 10px;
  // margin: 30px 15px 0px 15px
  background: rgba(0, 0, 0, 0);
  border: 1px solid #eeeeee;
  opacity: 1;

  img {
    width: 100%;
    height: 100%;
  }
}

.left-wrapper {
  min-width: 213px;
  background: #fff;
  border-right: 1px solid #f2f2f2;
}

.menu-item {
  position: relative;
  display: flex;
  justify-content: space-between;
  word-break: break-all;

  .icon-box {
    z-index: 3;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    display: none;
  }

  &:hover .icon-box {
    display: block;
  }

  .right-menu {
    z-index: 10;
    position: absolute;
    right: -106px;
    top: -11px;
    width: auto;
    min-width: 121px;
  }
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

.ivu-menu {
  z-index: auto;
}

.icondrag2 {
  font-size: 26px;
  color: #d8d8d8;
}

.hot_imgs {
  margin-bottom: 20px;

  .title {
    font-size: 14px;
  }

  .list-box {
    margin-top: 14px;
    .item {
      position: relative;
      display: flex;
      margin-top: 14px;

      .move-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 80px;
        cursor: move;
        color: #d8d8d8;
      }

      .img-box {
        position: relative;
        width: 80px;
        height: 80px;

        img {
          width: 100%;
          height: 100%;
        }
      }

      .info {
        flex: 1;
        margin-left: 22px;

        .info-item {
          display: flex;
          align-items: center;
          margin-bottom: 10px;

          span {
            // width 40px
            font-size: 13px;
          }

          .input-box {
            flex: 1;
          }
        }
      }

      .delect-btn {
        position: absolute;
        right: -12px;
        top: -12px;
        color: #999999;

        .iconfont {
          font-size: 28px;
          color: #999;
        }
      }
    }
  }

  .add-btn {
    margin-top: 14px;
  }
}

.upload-box {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background: #ccc;
}

.iconfont {
  color: #dddddd;
  font-size: 28px;
}

.iframe-boxs::-webkit-scrollbar {
  display: none;
}

.sgin_iframe::-webkit-scrollbar {
  display: none;
}

.iframe-boxs {
  width: 310px;
  height: 550px;
  border-radius: 10px;
  background: rgba(0, 0, 0, 0);
  border: 1px solid #eeeeee;
  opacity: 1;
  overflow: auto;

  .moneyBox {
    background-color: var(--color-theme);
    height: 414px;
    border-radius: 10px;

    .box1 {
      text-align: center;
      color: #ffffff;
      padding-bottom: 15px;

      .font1 {
        padding-top: 20px;
        // padding-bottom 15px
        font-size: 12px;
        opacity: 0.6;
      }

      .font2 {
        font-size: 30px;
        font-style: normal;
        opacity: 0.9;
      }
    }

    .moneyBox_content {
      background-color: #ffffff;
      height: 317px;
      border-radius: 4px;

      .box2 {
        display: flex;
        justify-content: space-around;
        height: 35px;
        line-height: 35px;
        margin-bottom: 10px;

        div:nth-child(1) {
          font-weight: bold;
          border-bottom: 2px solid var(--color-theme);
        }
      }

      .box3 {
        padding: 0px 10px;
        display: flex;
        justify-content: left;
        flex-wrap: wrap;

        .box3_box {
          width: 90px;
          height: 55px;
          border-radius: 9px;
          background-color: #f4f4f4;
          color: #888;
          margin-bottom: 10px;
          text-align: center;
          padding-top: 3px;
          font-size: 19px;
          margin-right: 3px;
          margin-left: 3px;

          .font {
            font-size: 11px;
            font-style: normal;
          }
        }

        .box3_box:nth-child(1) {
          width: 90px;
          height: 55px;
          border-radius: 9px;
          background-color: var(--color-theme);
          color: #ffffff;
          text-align: center;
          padding-top: 3px;
          margin-right: 3px;
          margin-left: 3px;
        }

        .other {
          line-height: 55px;
        }
      }

      .box4 {
        padding: 0px 10px;

        .tips {
          font-size: 14px;
          color: #333333;
          font-weight: 800;
          margin-bottom: 7px;
          margin-top: 10px;
        }

        .tips-samll {
          font-size: 12px;
          color: #333333;
          margin-bottom: 7px;

          p {
            margin: 2px 0px;
          }
        }
      }

      .box5 {
        font-size: 15px;
        width: 225px;
        height: 40px;
        border-radius: 25px;
        margin: 23px auto 0 auto;
        line-height: 40px;
        text-align: center;
        background-color: var(--color-theme);
        color: #ffffff;
      }
    }
  }
}

.bnt {
  // width 80px!important
}

::v-deep .ivu-page-header {
  border-bottom: unset;
  position: fixed;
  z-index: 9;
  width: 100%;
}

::v-deep .i-layout-page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.box-wrapper {
  display: flex;
  flex-wrap: nowrap;
  padding: 20px;
  background-color: #fff;
  border-radius: 5px;
}

.box-video-style {
  width: 100%;
  height: 180px;
  border-radius: 10px;
  background-color: #707070;
  margin-top: 10px;
  position: relative;
  overflow: hidden;
}

.box-video-style .iconv {
  color: #fff;
  line-height: 180px;
  width: 50px;
  height: 50px;
  display: inherit;
  font-size: 26px;
  position: absolute;
  top: -74px;
  left: 50%;
  margin-left: -25px;
}

.box-video-style .mark {
  position: absolute;
  width: 100%;
  height: 30px;
  top: 0;
  background-color: rgba(0, 0, 0, 0.5);
  text-align: center;
}
.tree-vis {
  display: flex;
  flex-direction: column;
  .tab-item {
    padding: 15px 20px;
    cursor: pointer;
  }
  .active {
    background-color: var(--prev-bg-main-color);
    color: var(--prev-color-primary);
    border-right: 2px solid var(--prev-color-primary);
  }
}
</style>
