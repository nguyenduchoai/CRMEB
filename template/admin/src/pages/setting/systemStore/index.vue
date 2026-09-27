<template>
  <div class="article-manager" v-loading="spinShow">
    <el-card :bordered="false" shadow="never" class="ivu-mt">
      <el-form
        ref="formItem"
        :model="formItem"
        :label-width="labelWidth"
        :label-position="labelPosition"
        :rules="ruleValidate"
        @submit.native.prevent
      >
        <el-row :gutter="24">
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Tên cửa hàng:" prop="name" label-for="name">
                <el-input v-model="formItem.name" placeholder="Vui lòng nhập tên cửa hàng" />
              </el-form-item>
            </el-col>
          </el-col>
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Giới thiệu cửa hàng:" label-for="introduction">
                <el-input v-model="formItem.introduction" placeholder="Vui lòng nhập giới thiệu cửa hàng" />
              </el-form-item>
            </el-col>
          </el-col>
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Số điện thoại cửa hàng:" label-for="phone" prop="phone">
                <el-input v-model="formItem.phone" type="number" placeholder="Vui lòng nhập số điện thoại cửa hàng" />
              </el-form-item>
            </el-col>
          </el-col>
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Địa chỉ cửa hàng:" label-for="address" prop="address">
                <el-cascader
                  :options="addresData"
                  :value="formItem.address"
                  v-model="formItem.address"
                  @change="handleChange"
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
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Thời hạn xác nhận sử dụng:" label-for="valid_time">
                <el-date-picker
                  clearable
                  :editable="false"
                  @change="onchangeDate"
                  v-model="formItem.valid_time"
                  format="yyyy/MM/dd"
                  type="daterange"
                  value-format="yyyy/MM/dd"
                  range-separator="-"
                  start-placeholder="Ngày bắt đầu"
                  end-placeholder="Ngày kết thúc"
                ></el-date-picker>
              </el-form-item>
            </el-col>
          </el-col>
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Giờ mở cửa:" label-for="day_time">
                <el-time-picker
                  @change="onchangeTime"
                  v-model="formItem.day_time"
                  format="HH:mm:ss"
                  value-format="HH:mm:ss"
                  range-separator="-"
                  start-placeholder="Thời gian bắt đầu"
                  end-placeholder="Thời gian kết thúc"
                  placeholder="Chọn khoảng thời gian"
                ></el-time-picker>
              </el-form-item>
            </el-col>
          </el-col>
          <el-col :span="24">
            <el-col v-bind="grid">
              <el-form-item label="Logo cửa hàng:" prop="image">
                <div class="picBox" v-db-click @click="modalPicTap('Chọn một')">
                  <div class="pictrue" v-if="formItem.image"><img v-lazy="formItem.image" /></div>
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
        <el-row>
          <el-col v-bind="grid">
            <el-button type="primary" class="ml20" v-db-click @click="handleSubmit('formItem')">Gửi</el-button>
          </el-col>
        </el-row>
      </el-form>
    </el-card>

    <el-dialog
      :visible.sync="modalPic"
      width="1024px"
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

    <el-dialog
      :visible.sync="modalMap"
      title="Tải lên ảnh sản phẩm"
      :show-close="true"
      :close-on-click-modal="false"
      class="mapBox"
    >
      <iframe id="mapPage" width="100%" height="100%" frameborder="0" v-bind:src="keyUrl"></iframe>
    </el-dialog>
  </div>
</template>

<script>
import { storeApi, keyApi, storeAddApi } from '@/api/setting';
import { mapState } from 'vuex';
// import city from '@/utils/city';
import uploadPictures from '@/components/uploadPictures';
import { cityList } from '@/api/app';

export default {
  name: 'systemStore',
  components: { uploadPictures },
  data() {
    const validatePhone = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('Vui lòng điền số điện thoại'));
      } else if (!/^1[3456789]\d{9}$/.test(value)) {
        callback(new Error('Số điện thoại không đúng định dạng!'));
      } else {
        callback();
      }
    };
    const validateUpload = (rule, value, callback) => {
      if (!this.formItem.image) {
        callback(new Error('Vui lòng tải lên logo cửa hàng'));
      } else {
        callback();
      }
    };
    return {
      spinShow: false,
      modalMap: false,
      addresData: [],
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
        name: [{ required: true, message: 'Vui lòng nhập tên cửa hàng', trigger: 'blur' }],
        mail: [
          { required: true, message: 'Mailbox cannot be empty', trigger: 'blur' },
          { type: 'email', message: 'Incorrect email format', trigger: 'blur' },
        ],
        address: [{ required: true, message: 'Vui lòng chọn địa chỉ cửa hàng', type: 'array', trigger: 'change' }],
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
        day_time: [{ required: true, type: 'array', message: 'Vui lòng chọn giờ mở cửa của cửa hàng', trigger: 'change' }],
        phone: [{ required: true, validator: validatePhone, trigger: 'blur' }],
        detailed_address: [{ required: true, message: 'Vui lòng nhập địa chỉ chi tiết', trigger: 'blur' }],
        image: [{ required: true, validator: validateUpload, trigger: 'change' }],
        latlng: [{ required: true, message: 'Vui lòng chọn kinh độ và vĩ độ', trigger: 'blur' }],
      },
      keyUrl: '',
      grid: {
        xl: 10,
        lg: 16,
        md: 18,
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
    this.getKey();
    this.getFrom();
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
    // Chọn kinh độ vĩ độ
    selectAdderss(data) {
      this.formItem.latlng = data.latlng.lat + ',' + data.latlng.lng;
      this.modalMap = false;
    },
    // Giá trị key
    getKey() {
      keyApi()
        .then(async (res) => {
          let keys = res.data.key;
          this.keyUrl = `https://apis.map.qq.com/tools/locpicker?type=1&key=${keys}&referer=myapp`;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // Chi tiết
    getFrom() {
      this.spinShow = true;
      storeApi()
        .then(async (res) => {
          let info = res.data.info || null;
          this.formItem = info || this.formItem;
          this.formItem.address = info.address2;
          this.spinShow = false;
        })
        .catch((res) => {
          this.spinShow = false;
          this.$message.error(res.msg);
        });
    },
    // Chọn ảnh
    modalPicTap() {
      this.modalPic = true;
    },
    // Ảnh đã chọn
    getPic(pc) {
      this.formItem.image = pc.att_dir;
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
      this.modalMap = true;
    },
    // Gửi
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          storeAddApi(this.formItem)
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
</style>
