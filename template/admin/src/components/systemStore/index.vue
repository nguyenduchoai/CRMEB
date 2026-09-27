<template>
  <div>
    <el-dialog
      :visible.sync="isTemplate"
      :title="formItem.id ? 'Sửa điểm nhận hàng' : 'Thêm điểm nhận hàng'"
      width="720px"
      @closed="cancel"
      append-to-body
    >
      <div class="article-manager" v-loading="spinShow">
        <el-form
          ref="formItem"
          :model="formItem"
          label-width="110px"
          label-position="right"
          :rules="ruleValidate"
          @submit.native.prevent
        >
          <el-row :gutter="24">
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Tên điểm nhận hàng:" prop="name" label-for="name">
                  <el-input v-model="formItem.name" placeholder="Vui lòng nhập tên điểm nhận hàng" />
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Giới thiệu điểm nhận hàng:" label-for="introduction">
                  <el-input v-model="formItem.introduction" placeholder="Vui lòng nhập giới thiệu điểm nhận hàng" />
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Số điện thoại điểm nhận hàng:" label-for="phone" prop="phone">
                  <el-input v-model="formItem.phone" placeholder="Vui lòng nhập số điện thoại điểm nhận hàng:" />
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Địa chỉ điểm nhận hàng:" label-for="address" prop="address">
                  <el-cascader
                    :options="addresData"
                    v-model="formItem.address"
                    @change="handleChange"
                    style="width: 100%"
                  ></el-cascader>
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Địa chỉ chi tiết:" label-for="detailed_address" prop="detailed_address">
                  <el-input v-model="formItem.detailed_address" placeholder="Vui lòng nhập địa chỉ chi tiết" />
                </el-form-item>
              </el-col>
            </el-col>
            <!--<el-col :span="24">-->
            <!--<el-col v-bind="grid">-->
            <!--<el-form-item label="Thời hạn xác nhận sử dụng:" label-for="valid_time">-->
            <!--<DatePicker @change="onchangeDate" :value="formItem.valid_time" v-model="formItem.valid_time" format="yyyy/MM/dd" type="daterange" split-panels placeholder="Vui lòng chọn thời hạn xác nhận sử dụng" ></DatePicker>-->
            <!--</el-form-item>-->
            <!--</el-col>-->
            <!--</el-col>-->
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Giờ hoạt động điểm nhận hàng:" label-for="day_time" prop="day_time">
                  <el-time-picker
                    is-range
                    @change="onchangeTime"
                    v-model="formItem.day_time"
                    format="HH:mm:ss"
                    value-format="HH:mm:ss"
                    range-separator="-"
                    start-placeholder="Thời gian bắt đầu"
                    end-placeholder="Thời gian kết thúc"
                    placeholder="Chọn khoảng thời gian"
                    style="width: 100%"
                  ></el-time-picker>
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Logo điểm nhận hàng:" prop="image">
                  <div class="picBox" v-db-click @click="modalPicTap('Chọn một', 'logo')">
                    <div class="pictrue" v-if="formItem.image">
                      <img v-lazy="formItem.image" />
                    </div>
                    <div class="upLoad acea-row row-center-wrapper" v-else>
                      <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                    </div>
                  </div>
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Ảnh lớn điểm nhận hàng:" prop="oblong_image">
                  <div class="picBox" v-db-click @click="modalPicTap('Chọn một', 'oblong')">
                    <div class="pictrue" v-if="formItem.oblong_image">
                      <img v-lazy="formItem.oblong_image" />
                    </div>
                    <div class="upLoad acea-row row-center-wrapper" v-else>
                      <i class="el-icon-picture-outline" style="font-size: 24px"></i>
                    </div>
                  </div>
                </el-form-item>
              </el-col>
            </el-col>
            <el-col :span="24">
              <el-col v-bind="grid">
                <el-form-item label="Kinh độ, vĩ độ:" label-for="status2" prop="latlng">
                  <el-tooltip>
                    <el-input v-model="formItem.latlng" style="width: 100%" placeholder="Vui lòng tìm vị trí">
                      <el-button type="primary" slot="append" v-db-click @click="onSearch">Tìm vị trí</el-button>
                    </el-input>
                    <div slot="content">Vui lòng nhấn “Tìm vị trí” để chọn vị trí</div>
                  </el-tooltip>
                </el-form-item>
              </el-col>
            </el-col>
          </el-row>
          <!-- <el-row>
              <div class="btn">
                <el-button type="primary" long v-db-click @click="handleSubmit('formItem')">{{
                  formItem.id ? 'Sửa' : 'Gửi'
                }}</el-button>
              </div>
            </el-row> -->
        </el-form>

        <el-dialog
          :visible.sync="modalPic"
          width="1024px"
          :title="modalTitle"
          :close-on-click-modal="false"
          append-to-body
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
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" long v-db-click @click="handleSubmit('formItem')">{{
          formItem.id ? 'Sửa' : 'Gửi'
        }}</el-button>
      </span>
    </el-dialog>
    <el-dialog
      :visible.sync="modalMap"
      title="Vui lòng chọn địa chỉ"
      append-to-body
      :close-on-click-modal="false"
      width="720px"
      class="mapBox"
    >
      <iframe id="mapPage" width="100%" height="600px" frameborder="0" v-bind:src="keyUrl"></iframe>
    </el-dialog>
  </div>
</template>

<script>
import { storeApi, keyApi, storeAddApi, storeGetInfoApi } from '@/api/setting';
import { mapState } from 'vuex';
import uploadPictures from '@/components/uploadPictures';
import { cityList } from '@/api/app';
export default {
  name: 'systemStore',
  components: { uploadPictures },
  props: {},
  data() {
    const validatePhone = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Vui lòng điền số điện thoại'));
      } else {
        callback();
      }
    };
    const validateUpload = (rule, value, callback) => {
      if (!this.formItem.image) {
        callback(new Error('Vui lòng tải lên logo điểm nhận hàng'));
      } else {
        callback();
      }
    };
    const oblongImageUpload = (rule, value, callback) => {
      if (!this.formItem.oblong_image) {
        callback(new Error('Vui lòng tải lên ảnh lớn điểm nhận hàng'));
      } else {
        callback();
      }
    };
    return {
      isTemplate: false,
      spinShow: false,
      modalMap: false,
      addresData: [],
      modalTitle: '',
      formItem: {
        name: '',
        introduction: '',
        phone: '',
        address: [],
        address2: [],
        detailed_address: '',
        valid_time: [],
        day_time: ['', ''],
        latlng: '',
        id: 0,
      },
      ruleValidate: {
        name: [{ required: true, message: 'Vui lòng nhập tên điểm nhận hàng', trigger: 'blur' }],
        mail: [
          {
            required: true,
            message: 'Mailbox cannot be empty',
            trigger: 'blur',
          },
          { type: 'email', message: 'Incorrect email format', trigger: 'blur' },
        ],
        address: [
          {
            required: true,
            message: 'Vui lòng chọn địa chỉ điểm nhận hàng',
            type: 'array',
            trigger: 'change',
          },
        ],
        valid_time: [
          {
            required: true,
            type: 'array',
            message: 'Vui lòng chọn thời hạn xác nhận sử dụng',
            trigger: 'change',
            fields: {
              0: { type: 'date', required: true, message: 'Vui lòng chọn khoảng năm' },
              1: { type: 'date', required: true, message: 'Vui lòng chọn khoảng năm' },
            },
          },
        ],
        day_time: [
          {
            required: true,
            type: 'array',
            message: 'Vui lòng chọn giờ hoạt động của điểm nhận hàng',
            trigger: 'change',
          },
        ],
        phone: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        detailed_address: [{ required: true, message: 'Vui lòng nhập địa chỉ chi tiết', trigger: 'blur' }],
        image: [{ required: true, validator: validateUpload, trigger: 'change' }],
        oblong_image: [{ required: true, validator: oblongImageUpload, trigger: 'change' }],
        latlng: [{ required: true, message: 'Vui lòng chọn kinh độ và vĩ độ', trigger: 'blur' }],
      },
      keyUrl: '',
      grid: {
        xl: 20,
        lg: 24,
        md: 20,
        sm: 24,
        xs: 24,
      },
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
      modalPic: false,
      isChoice: 'Chọn một',
    };
  },
  created() {
    this.getCityList();
  },
  computed: {},
  mounted: function () {
    window.addEventListener(
      'message',
      function (event) {
        // Nhận thông tin vị trí, sau khi người dùng chọn và xác nhận điểm vị trí, thành phần chọn điểm sẽ kích hoạt sự kiện này, trả về thông tin vị trí của người dùng
        var loc = event.data;
        if (loc && loc.module === 'locationPicker') {
          // Để tránh ứng dụng khác cũng post thông tin vào trang này, cần kiểm tra module có phải là 'locationPicker' không
          window.parent.selectAdderss(loc);
        }
      },
      false,
    );
    window.selectAdderss = this.selectAdderss;
  },
  methods: {
    getCityList() {
      cityList().then((res) => {
        res.data.map((item) => {
          item.value = item.label;
          if (item.children && item.children.length) {
            item.children.map((j) => {
              j.value = j.label;
              if (j.children && j.children.length) {
                j.children.map((o) => {
                  o.value = o.label;
                });
              }
            });
          }
        });
        this.addresData = res.data;
      });
    },
    cancel() {
      this.$refs['formItem'].resetFields();
      this.clearFrom();
    },
    clearFrom() {
      this.formItem.introduction = '';
      this.formItem.day_time = ['', ''];
      this.formItem.oblong_image = '';
      this.formItem.id = 0;
    },
    // Chọn kinh độ vĩ độ
    selectAdderss(data) {
      this.formItem.latlng = data.latlng.lat + ',' + data.latlng.lng;
      this.modalMap = false;
    },
    // Giá trị key
    getKey() {},
    // Chi tiết
    getInfo(id) {
      let that = this;
      that.formItem.id = id;
      that.spinShow = true;
      storeGetInfoApi(id)
        .then((res) => {
          let info = res.data.info || null;
          that.formItem = info || that.formItem;
          that.formItem.address = info.address2;
          that.formItem.day_time = info.day_time.split('-');
          that.spinShow = false;
        })
        .catch(function (res) {
          that.spinShow = false;
          that.$message.error(res.msg);
        });
    },
    // Chọn ảnh
    modalPicTap(tit, picTit) {
      this.modalTitle = picTit == 'oblong' ? 'Ảnh lớn điểm nhận hàng' : 'LOGO điểm nhận hàng';
      this.modalPic = true;
      this.picTit = picTit;
    },
    // Ảnh đã chọn
    getPic(pc) {
      switch (this.picTit) {
        case 'logo':
          this.formItem.image = pc.att_dir;
          break;
        case 'oblong':
          this.formItem.oblong_image = pc.att_dir;
          break;
      }
      this.modalPic = false;
    },
    // Chọn địa chỉ
    handleChange(value, selectedData) {
      this.formItem.address = selectedData.map((o) => o.label);
      //  this.formItem.address2 = selectedData.map(o => o.value);
    },
    // Thời hạn xác nhận sử dụng
    onchangeDate(e) {
      this.formItem.valid_time = e;
    },
    // Giờ mở cửa
    onchangeTime(e) {
      this.formItem.day_time = e;
    },
    onSearch() {
      if (!this.keyUrl) {
        keyApi()
          .then(async (res) => {
            let keys = res.data.key;
            this.keyUrl = `https://apis.map.qq.com/tools/locpicker?type=1&key=${keys}&referer=myapp`;
            this.modalMap = true;
          })
          .catch((res) => {
            this.$message.error(res.msg);
          });
      } else {
        this.modalMap = true;
      }
    },
    // Gửi
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          storeAddApi(this.formItem)
            .then(async (res) => {
              this.$message.success(res.msg);
              this.isTemplate = false;
              this.$parent.getList();
              this.$refs[name].resetFields();
              this.clearFrom();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        } else {
          return false;
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
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
  .iconfont {
    color: #898989;
  }
}
.mapBox ::v-deep .ivu-modal-body {
  height: 640px !important;
}
.btn {
  margin: 0 auto;
  width: 40%;
}
</style>
