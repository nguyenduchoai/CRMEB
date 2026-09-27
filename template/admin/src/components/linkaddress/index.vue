<template>
  <div>
    <el-dialog :visible.sync="modals" title="Chọn liên kết" :close-on-click-modal="false" append-to-body width="1000px">
      <div class="table_box">
        <div class="left_box" v-if="fromType !== 'diyPage'">
          <el-tree
            :data="categoryData"
            node-key="id"
            default-expand-all
            :props="props"
            highlight-current
            @node-click="handleCheckChange"
            :current-node-key="treeId"
          ></el-tree>
        </div>
        <div class="right_box" v-if="currenType == 'link'">
          <div v-if="tableList.length">
            <div class="cont">Vui lòng chọn liên kết</div>
            <div class="Box">
              <div
                class="cont_box"
                :class="currenId == item.id ? 'on' : ''"
                v-for="(item, index) in tableList"
                :key="index"
                v-db-click
                @click="getUrl(item)"
              >
                {{ item.name }}
              </div>
            </div>
          </div>
        </div>
        <div class="right_box" v-if="currenType == 'marketing_link' && coupon.length">
          <div>
            <div class="cont">Phiếu giảm giá</div>
            <div class="Box">
              <div
                class="cont_box"
                :class="currenId == item.id ? 'on' : ''"
                v-for="(item, index) in coupon"
                :key="index"
                v-db-click
                @click="getUrl(item)"
              >
                {{ item.name }}
              </div>
            </div>
          </div>
          <div>
            <div v-permission="'seckill'" v-if="basicsList.length">
              <div class="cont">Flash sale</div>
              <div class="Box">
                <div
                  class="cont_box"
                  :class="currenId == item.id ? 'on' : ''"
                  v-for="(item, index) in basicsList"
                  :key="index"
                  v-db-click
                  @click="getUrl(item)"
                >
                  {{ item.name }}
                </div>
              </div>
            </div>
          </div>
          <div>
            <div v-permission="'bargain'" v-if="distributionList.length">
              <div class="cont">Săn giảm giá</div>
              <div class="Box">
                <div
                  class="cont_box"
                  :class="currenId == item.id ? 'on' : ''"
                  v-for="(item, index) in distributionList"
                  :key="index"
                  v-db-click
                  @click="getUrl(item)"
                >
                  {{ item.name }}
                </div>
              </div>
            </div>
          </div>
          <div>
            <div v-permission="'combination'" v-if="userList.length">
              <div class="cont">Mua chung</div>
              <div class="Box">
                <div
                  class="cont_box"
                  :class="currenId == item.id ? 'on' : ''"
                  v-for="(item, index) in userList"
                  :key="index"
                  v-db-click
                  @click="getUrl(item)"
                >
                  {{ item.name }}
                </div>
              </div>
            </div>
          </div>
          <div v-if="integral.length">
            <div class="cont">Điểm thưởng</div>
            <div class="Box">
              <div
                class="cont_box"
                :class="currenId == item.id ? 'on' : ''"
                v-for="(item, index) in integral"
                :key="index"
                v-db-click
                @click="getUrl(item)"
              >
                {{ item.name }}
              </div>
            </div>
          </div>
          <div v-if="luckDraw.length">
            <div class="cont">Quay thưởng</div>
            <div class="Box">
              <div
                class="cont_box"
                :class="currenId == item.id ? 'on' : ''"
                v-for="(item, index) in luckDraw"
                :key="index"
                v-db-click
                @click="getUrl(item)"
              >
                {{ item.name }}
              </div>
            </div>
          </div>
        </div>
        <div
          class="right_box"
          :class="fromType == 'diyPage' ? 'diy' : ''"
          v-if="
            currenType == 'special' ||
            currenType == 'product_category' ||
            currenType == 'product' ||
            currenType == 'seckill' ||
            currenType == 'bargain' ||
            currenType == 'combination' ||
            currenType == 'news' ||
            currenType == 'advance' ||
            currenType == 'integral' ||
            currenType == 'lottery_list'
          "
        >
          <el-form ref="formValidate" :model="formValidate" class="tabform" v-if="currenType == 'product'">
            <el-row :gutter="24">
              <el-col :span="8">
                <el-form-item label="" label-for="pid">
                  <!-- <el-select v-model="formValidate.cate_id" style="width: 180px" clearable @change="userSearchs">
                    <el-option
                      v-for="item in treeSelect"
                      :value="item.id"
                      :key="item.id"
                      :label="item.html + item.cate_name"
                    >
                    </el-option>
                  </el-select> -->
                  <el-cascader
                    style="width: 180px"
                    v-model="formValidate.cate_id"
                    size="small"
                    :options="treeSelect"
                    :props="{ multiple: true, checkStrictly: true, emitPath: false }"
                    filterable
                    clearable
                    @change="userSearchs"
                  ></el-cascader>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="" label-for="store_name">
                  <el-input
                    search
                    enter-button
                    placeholder="Vui lòng nhập tên sản phẩm, từ khóa, mã số"
                    v-model="formValidate.store_name"
                    style="width: 200px"
                    @change="userSearchs"
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </el-form>
          <el-table
            row-key="id"
            ref="table"
            empty-text="Chưa có dữ liệu"
            :data="tableList"
            v-loading="loading"
            :max-height="
              currenType == 'product_category'
                ? '460'
                : currenType == 'product' ||
                  currenType == 'seckill' ||
                  currenType == 'bargain' ||
                  currenType == 'advance' ||
                  currenType == 'combination' ||
                  currenType == 'news' ||
                  currenType == 'integral'
                ? '428'
                : ''
            "
          >
            <el-table-column
              :width="currenType != 'product_category' ? 50 : 80"
              v-if="
                [
                  'special',
                  'product',
                  'seckill',
                  'product_category',
                  'bargain',
                  'combination',
                  'advance',
                  'integral',
                  'news',
                  'lottery_list',
                  'link',
                ].includes(currenType)
              "
            >
              <template slot-scope="scope">
                <el-radio v-model="templateRadio" :label="scope.row.id" @change.native="getTemplateRow(scope.row)"
                  >&nbsp;</el-radio
                >
              </template>
            </el-table-column>
            <el-table-column
              :label="item.title"
              :width="item.width"
              :min-width="item.minWidth"
              v-for="(item, index) in currenType == 'special'
                ? columns
                : currenType == 'product_category'
                ? columns7
                : currenType == 'bargain' ||
                  currenType == 'combination' ||
                  currenType == 'advance' ||
                  currenType == 'integral'
                ? bargain
                : currenType == 'news'
                ? news
                : currenType == 'lottery_list'
                ? lottery
                : currenType == 'link'
                ? diyLink
                : columns8"
              :key="index"
            >
              <template slot-scope="scope">
                <template v-if="item.key">
                  <div>
                    <span>{{ scope.row[item.key] }}</span>
                  </div>
                </template>
                <template v-else-if="item.slot === 'pic' && scope.row.hasOwnProperty('pic')">
                  <viewer>
                    <div class="tabBox_img">
                      <img v-lazy="scope.row.pic" />
                    </div>
                  </viewer>
                </template>
                <template v-else-if="item.slot === 'image' && scope.row.hasOwnProperty('image')">
                  <viewer>
                    <div class="tabBox_img">
                      <img v-lazy="scope.row.image" />
                    </div>
                  </viewer>
                </template>
                <template v-else-if="item.slot === 'image_input' && scope.row.hasOwnProperty('image_input')">
                  <viewer>
                    <div class="tabBox_img">
                      <img v-lazy="scope.row.image_input[0]" />
                    </div>
                  </viewer>
                </template>
              </template>
            </el-table-column>
          </el-table>
          <div
            class="acea-row row-right page"
            v-if="
              currenType == 'product' ||
              currenType == 'seckill' ||
              currenType == 'bargain' ||
              currenType == 'advance' ||
              currenType == 'combination' ||
              currenType == 'news' ||
              currenType == 'integral' ||
              currenType == 'lottery_list'
            "
          >
            <pagination
              v-if="total"
              :total="total"
              :page.sync="formValidate.page"
              :limit.sync="formValidate.limit"
              @pagination="getList"
            />
          </div>
        </div>
        <div class="right_box" v-if="currenType == 'custom'">
          <!--<div v-if="!tableList.length || customNum==2">-->
          <!--<el-button type="primary" v-db-click @click="customList" v-if="tableList.length">Danh sách tùy chỉnh</el-button>-->
          <div style="width: 340px; margin: 150px 100px 0 120px">
            <el-form ref="customdate" :model="customdate" :rules="ruleValidate" :label-width="100">
              <!--<el-form-item label="Tên liên kết:" prop="name">-->
              <!--<el-input v-model="customdate.name" placeholder="Trang thành viên"></el-input>-->
              <!--</el-form-item>-->
              <!-- <el-form-item label="Đường dẫn chuyển tới:" prop="url">
                <el-input v-model="customdate.url" placeholder="Vui lòng nhập đường dẫn chuyển tới"></el-input>
              </el-form-item> -->
              <div class="mb30 radioGroup">
                <el-radio-group v-model="customdate.status" @input="radioTap('customdate')">
                  <el-radio :label="2">
                    <span>Chuyển đến Mini Program khác</span>
                  </el-radio>
                  <el-radio :label="1">
                    <span>Liên kết thông thường</span>
                  </el-radio>
                </el-radio-group>
              </div>
              <div v-if="customdate.status == 1">
                <el-form-item label="Đường dẫn chuyển hướng:" prop="url" key="url">
                  <el-input v-model="customdate.url" placeholder="Vui lòng nhập đúng đường dẫn chuyển hướng"></el-input>
                </el-form-item>
              </div>
              <div v-if="customdate.status == 2">
                <el-form-item label="APPID：" prop="appid" key="appid">
                  <el-input v-model="customdate.appid" placeholder="Vui lòng nhập đúng APPID"></el-input>
                </el-form-item>
                <el-form-item label="Đường dẫn Mini Program:" prop="mpUrl" key="mpUrl">
                  <el-input v-model="customdate.mpUrl" placeholder="Vui lòng nhập đúng đường dẫn Mini Program"></el-input>
                </el-form-item>
              </div>
            </el-form>
          </div>
        </div>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="cancel">Hủy</el-button>
        <el-button type="primary" v-db-click @click="handleSubmit('customdate')" v-if="currenType == 'custom'"
          >Xác nhận</el-button
        >
        <el-button type="primary" v-db-click @click="ok" v-else>Xác nhận</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { pageCategory, pageLink, saveLink } from '@/api/diy';
import { cascaderListApi, changeListApi } from '@/api/product';
import {
  seckillListApi,
  combinationListApi,
  bargainListApi,
  integralProductListApi,
  presellListApi,
} from '@/api/marketing';
import { lotteryList } from '@/api/lottery';
import { cmsListApi } from '@/api/cms';
import { linkListApi } from '@/api/setting';
export default {
  name: 'linkaddress',
  props: {
    fromType: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      modals: false,
      categoryData: [],
      currenType: 'link',
      props: {
        label: 'name',
        children: 'children',
      },
      templateRadio: 0,
      columns: [
        {
          title: 'ID',
          key: 'id',
          width: 60,
        },
        {
          title: 'Tên trang',
          key: 'name',
          width: 150,
        },
        {
          title: 'Liên kết trang',
          key: 'url',
        },
      ],
      columns7: [
        {
          title: 'ID',
          key: 'id',
          width: 60,
        },
        {
          title: 'Tên danh mục',
          key: 'cate_name',
          tree: true,
        },
        {
          title: 'Biểu tượng danh mục',
          slot: 'pic',
        },
      ],
      columns8: [
        {
          title: 'ID',
          key: 'id',
          width: 60,
        },
        {
          title: 'Hình ảnh sản phẩm',
          slot: 'image',
          width: 90,
        },
        {
          title: 'Tên sản phẩm',
          key: 'store_name',
        },
      ],
      bargain: [
        {
          title: 'ID',
          key: 'id',
          width: 60,
        },
        {
          title: 'Hình ảnh sản phẩm',
          slot: 'image',
          width: 90,
        },
        {
          title: 'Tên sản phẩm',
          key: 'title',
        },
      ],
      news: [
        {
          title: 'ID',
          key: 'id',
          width: 60,
        },
        {
          title: 'Hình ảnh bài viết',
          slot: 'image_input',
          width: 90,
        },
        {
          title: 'Tên bài viết',
          key: 'title',
        },
      ],
      lottery: [
        {
          title: 'ID',
          key: 'id',
          width: 60,
        },
        {
          title: 'Tên',
          key: 'name',
        },
      ],
      formValidate: {
        page: 1,
        limit: 15,
        cate_id: '',
        store_name: '',
      },
      total: 0,
      basicsList: [],
      userList: [],
      distributionList: [],
      coupon: [],
      luckDraw: [],
      integral: [],
      currenId: '',
      currenUrl: '',
      loading: false,
      tableList: [],
      presentId: 0,
      categoryId: '', //ID danh mục bên trái
      treeSelect: [],
      customdate: {
        url: '',
        appid: '',
        mpUrl: '',
        status: 2,
      },
      customNum: 1,
      ruleValidate: {
        name: [{ required: true, message: 'Vui lòng nhập tên liên kết', trigger: 'blur' }],
        url: [{ required: true, message: 'Vui lòng nhập đường dẫn chuyển hướng', trigger: 'blur' }],
        appid: [{ required: true, message: 'Vui lòng nhập APPID', trigger: 'blur' }],
      },
      treeId: 0,
    };
  },
  computed: {},
  created() {
    this.getSort();
    this.goodsCategory();
  },
  methods: {
    getTemplateRow(row) {
      this.presentId = row.id;
      this.currenUrl = row.url;
    },
    // Xóa
    delLink(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `diy/del_link/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.tableList.splice(num, 1);
          if (!this.tableList.length) {
            this.customNum = 2;
          }
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    customLink() {
      this.customNum = 2;
    },
    customList() {
      this.customNum = 1;
    },
    getCustomList() {
      pageLink(this.categoryId)
        .then((res) => {
          if (!res.data.list.length) {
            this.customNum = 2;
          }
          this.tableList = res.data.list;
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    getLotteryList() {},
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          let url = this.customdate.url;
          if (this.customdate.status == 1) {
            url = this.customdate.url;
          } else {
            url = this.customdate.mpUrl + '@APPID=' + this.customdate.appid;
          }
          this.$emit('linkUrl', url);
          this.modals = false;
          this.reset();
          // saveLink(this.customdate,this.categoryId).then(res=>{
          // 	this.getCustomList();
          // 	this.$message.success(res.msg);
          // 	this.$emit("linkUrl",this.customdate.url);
          // 	this.modals = false
          // 	this.reset();
          // }).catch(err=>{
          // 	this.$message.error(err.msg);
          // })
        } else {
          this.$message.error('Vui lòng điền thông tin');
        }
      });
    },
    handleReset(name) {
      this.$refs[name].resetFields();
    },
    // Danh mục sản phẩm;
    goodsCategory() {
      cascaderListApi(1)
        .then((res) => {
          this.treeSelect = res.data;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Tìm kiếm bảng
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    reset() {
      this.currenUrl = '';
      this.presentId = 0;
      this.currenId = '';
      // this.customdate.name="";
      this.customdate.url = '';
    },
    getUrl(item) {
      this.currenId = item.id;
      this.currenUrl = item.url;
    },
    getSort() {
      pageCategory()
        .then((res) => {
          res.data[0].children[0].selected = true;
          this.categoryData = res.data;
          if (this.fromType === 'diyPage') {
            this.handleCheckChange(res.data[0].children[2]);
          } else {
            this.handleCheckChange(res.data[0].children[0].children[0]);
          }
        })
        .catch((err) => {
          this.$message.error(err.msg);
        });
    },
    getList() {
      this.loading = true;
      this.formValidate.limit = 15;
      if (this.currenType == 'product') {
        changeListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/goods_details/index?id=${e.id}`;
            });
            this.tableList = data.list;
            this.total = res.data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      } else if (this.currenType == 'seckill') {
        seckillListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/activity/goods_seckill_details/index?id=${e.id}&status=1`;
            });
            this.tableList = data.list;
            this.total = res.data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      } else if (this.currenType == 'advance') {
        presellListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/activity/presell/index?id=${e.id}&status=1`;
            });
            this.tableList = data.list;
            this.total = res.data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
            advance;
          });
      } else if (this.currenType == 'bargain') {
        bargainListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/activity/goods_bargain_details/index?id=${e.id}`;
            });
            this.tableList = data.list;
            this.total = res.data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      } else if (this.currenType == 'combination') {
        combinationListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/activity/goods_combination_details/index?id=${e.id}`;
            });
            this.tableList = data.list;
            this.total = res.data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      } else if (this.currenType == 'news') {
        cmsListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/extension/news_details/index?id=${e.id}`;
            });
            this.tableList = data.list;
            this.total = data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      } else if (this.currenType == 'lottery_list') {
        this.formValidate = {
          page: 1,
          limit: 15,
          factor: 1,
        };
        lotteryList(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/goods/lottery/grids/index?type=1&lottery_id=${e.id}`;
            });
            this.tableList = data.list;
            this.total = data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      } else if (this.currenType == 'integral') {
        integralProductListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((e) => {
              e.url = `/pages/points_mall/integral_goods_details?id=${e.id}`;
            });
            this.tableList = data.list;
            this.total = res.data.count;
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$message.error(res.msg);
          });
      }
    },
    handleCheckChange(data) {
      this.reset();
      let id = '';
      this.treeId = data.id;
      if (data.pid) {
        id = data.id;
        this.categoryId = data.id;
      } else {
        return false;
      }
      this.loading = true;
      this.currenType = data.type;
      if (
        this.currenType == 'product' ||
        this.currenType == 'seckill' ||
        this.currenType == 'bargain' ||
        this.currenType == 'combination' ||
        this.currenType == 'news' ||
        this.currenType == 'advance' ||
        this.currenType == 'integral' ||
        this.currenType == 'lottery_list'
      ) {
        this.getList();
      } else if (this.currenType == 'custom') {
        this.getCustomList();
      } else {
        this.formValidate = {
          id,
          page: 1,
        };
        linkListApi(this.formValidate)
          .then((res) => {
            this.loading = false;
            let data = res.data.list;
            this.total = res.data.count;
            if (this.currenType == 'special') {
              let list = [];
              data.forEach((e) => {
                e.url = `/pages/annex/special/index?id=${e.id}&name=${e.name}`;
                if (e.is_diy) {
                  list.push(e);
                }
              });
              this.tableList = list;
            } else {
              this.tableList = data;
            }
            // if (this.currenType == 'marketing_link' || this.currenType == 'link') {
            //   let basicsList = [];
            //   let distributionList = [];
            //   let userList = [];
            //   let integral = [];
            //   let luckDraw = [];
            //   let coupon = [];
            //   data.forEach((e) => {
            //     if (e.type == 1) {
            //       basicsList.push(e);
            //     } else if (e.type == 2) {
            //       distributionList.push(e);
            //     } else if (e.type == 3) {
            //       userList.push(e);
            //     } else if (e.type == 4) {
            //       integral.push(e);
            //     } else if (e.type == 5) {
            //       luckDraw.push(e);
            //     } else {
            //       coupon.push(e);
            //     }
            //   });
            //   this.basicsList = basicsList;
            //   this.distributionList = distributionList;
            //   this.userList = userList;
            //   this.coupon = coupon;
            //   this.luckDraw = luckDraw;
            //   this.integral = integral;
            // } else if (this.currenType == 'special') {
            //   let list = [];
            //   data.forEach((e) => {
            //     e.url = `/pages/annex/special/index?id=${e.id}&name=${e.name}`;
            //     if (e.is_diy) {
            //       list.push(e);
            //     }
            //   });
            //   this.tableList = list;
            // } else if (this.currenType == 'product_category') {
            //   data.forEach((e) => {
            //     if (e.hasOwnProperty('children')) {
            //       e.children.forEach((j) => {
            //         j.url = `/pages/goods/goods_list/index?sid=${j.id}&title=${j.cate_name}`;
            //       });
            //     }
            //     e.url = `/pages/goods/goods_list/index?cid=${e.id}&title=${e.cate_name}`;
            //   });
            //   this.tableList = data;
            // }
          })
          .catch((err) => {
            this.loading = false;
            this.$message.error(err.msg);
          });
      }
    },
    ok() {
      if (this.currenUrl == '') {
        return this.$message.warning('Vui lòng chọn liên kết');
      } else {
        this.$emit('linkUrl', this.currenUrl);
        this.modals = false;
        this.reset();
      }
    },
    cancel() {
      this.modals = false;
      this.reset();
    },
  },
};
</script>

<style lang="scss" scoped>
::v-deep .el-dialog__body {
}
::v-deep .el-tree-node__content {
  height: 30px;
}
::v-deep .el-tree--highlight-current .el-tree-node.is-current > .el-tree-node__content {
  background-color: var(--prev-bg-menu-hover-ba-color) !important;
  border-right: 2px solid var(--prev-color-primary);
}
::v-deep .ivu-tree-title-selected,
::v-deep .ivu-tree-title-selected:hover,
::v-deep .ivu-tree-title:hover {
  background-color: unset;
  color: var(--prev-color-primary);
}
::v-deep .ivu-table-cell-tree {
  border: 0;
  font-size: 15px;
  background-color: unset;
}
::v-deep .el-table .cell {
  display: flex;
  align-items: center;
}
::v-deep .ivu-table-cell-tree .ivu-icon-ios-add:before {
  content: '\F11F';
}
::v-deep .ivu-table-cell-tree .ivu-icon-ios-remove:before {
  content: '\F116';
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

/* Định nghĩa thanh trượt: đổ bóng trong + góc tròn */
::-webkit-scrollbar-thumb {
  -webkit-box-shadow: inset 0 0 6px #ddd;
}
::-webkit-scrollbar {
  width: 4px !important; /* Áp dụng cho thanh cuộn dọc */
}
.on {
  background-color: var(--prev-color-primary) !important;
  color: #fff !important;
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
.radioGroup {
  ::v-deep .ivu-radio-wrapper {
    margin-right: 30px;
  }
}
.table_box {
  display: flex;
  position: relative;
  .left_box {
    width: 171px;
    height: 470px;
    border-right: 1px solid #eeeeee;
    overflow-x: hidden;
    overflow-y: auto;
    .left_cont {
      margin-bottom: 12px;
      cursor: pointer;
    }
  }
  .right_box {
    margin-left: 23px;
    font-size: 13px;
    font-family: PingFang SC;
    flex: 1;
    height: 470px;
    overflow-x: hidden;
    overflow-y: auto;
    .cont {
      font-weight: 500;
      color: #000000;
      font-weight: bold;
    }
    .Box {
      margin-top: 14px;
      display: flex;
      flex-wrap: wrap;
      .cont_box {
        font-weight: 400;
        color: rgba(0, 0, 0, 0.85);
        background: #fafafa;
        border-radius: 3px;
        text-align: center;
        padding: 7px 30px;
        margin-right: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        &:hover {
          background-color: var(--prev-bg-menu-hover-ba-color);
          color: #333;
        }
      }
      .item {
        position: relative;
        .iconfont {
          display: none;
        }
        &:hover {
          .iconfont {
            display: block;
          }
        }
      }
      .iconfont {
        position: absolute;
        right: 9px;
        top: -8px;
        font-size: 18px;
        color: #333;
      }
    }
  }
  ::v-deep .el-table .cell {
    padding-right: 0;
  }
  ::v-deep .page {
    margin-top: 10px;
  }
  .Button {
    position: absolute;
    bottom: 15px;
    right: 15px;
    font-family: PingFangSC-Regular;
    text-align: center;
    .cancel {
      width: 70px;
      height: 32px;
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.14901960784313725);
      border-radius: 2px;
      font-size: 14px;
      color: #000000;
      line-height: 32px;
      float: left;
      margin-right: 10px;
      cursor: pointer;
    }
    .ok {
      width: 70px;
      height: 32px;
      background: var(--prev-color-primary);
      border-radius: 2px;
      font-size: 14px;
      color: #ffffff;
      line-height: 32px;
      float: left;
      cursor: pointer;
    }
  }
}
</style>
