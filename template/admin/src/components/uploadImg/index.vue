<template>
  <div>
    <el-dialog
      title="Tải lên ảnh"
      :visible.sync="uploadModal"
      :append-to-body="true"
      :width="isIframe ? '100%' : '1024px'"
      :fullscreen="isIframe"
      @close="closed"
    >
      <div class="main" v-loading="loading">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
          <el-form-item label="Cách tải lên:" prop="type">
            <el-radio-group v-model="ruleForm.type" @input="radioChange(ruleForm.type)">
              <el-radio :label="0">Tải lên từ máy</el-radio>
              <el-radio :label="1">Tải lên từ URL</el-radio>
              <el-radio :label="2">Quét mã để tải lên</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="Tải lên vào nhóm:" prop="region" v-show="ruleForm.type == 0 || ruleForm.type == 1">
            <el-cascader
              class="form-width"
              v-model="ruleForm.region"
              :props="props"
              :options="categoryList"
              @change="handleChange"
            ></el-cascader>
          </el-form-item>
          <el-form-item label="Ảnh từ URL:" prop="region" v-if="ruleForm.type == 1">
            <el-input class="form-width" v-model="webImgUrl" placeholder="Vui lòng nhập URL ảnh"></el-input>
            <span class="tq-text" v-db-click @click="getImg">Lấy ảnh</span>
          </el-form-item>
          <el-form-item label="Tải lên ảnh:" prop="region" v-if="ruleForm.type == 0">
            <div class="acea-row">
              <div class="uploadCont">
                <el-upload
                  ref="upload"
                  :action="fileUrl"
                  list-type="picture-card"
                  :on-change="fileChange"
                  :file-list="ruleForm.imgList"
                  :auto-upload="false"
                  :data="uploadData"
                  :headers="header"
                  :multiple="true"
                  :limit="limit"
                >
                  <i slot="default" class="el-icon-plus"></i>
                  <div
                    slot="file"
                    slot-scope="{ file }"
                    draggable="false"
                    @dragstart="handleDragStart($event, file)"
                    @dragover="handleDragOver($event, file)"
                    @dragenter="handleDragEnter($event, file)"
                    @dragend="handleDragEnd($event, file)"
                  >
                    <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                    <i class="el-icon-error btndel" v-db-click @click="handleWebRemove(file)" />
                  </div>
                </el-upload>
                <div class="tips">
                  Đề xuất tải lên ảnh có chiều rộng tối đa 750px, không quá 3MB; chỉ hỗ trợ định dạng jpeg, jpg, png, có thể kéo thả để điều chỉnh thứ tự tải lên
                </div>
              </div>
            </div>
          </el-form-item>
          <template v-if="ruleForm.type == 1">
            <div class="img-box pl100">
              <div
                v-for="(item, index) in ruleForm.imgList"
                :key="index"
                class="pictrue"
                draggable="false"
                @dragstart="handleDragStart($event, item)"
                @dragover.prevent="handleDragOver($event, item)"
                @dragenter="handleDragEnter($event, item)"
                @dragend="handleDragEnd($event, item)"
              >
                <img :src="item.url" />
                <i class="el-icon-error btndel" v-db-click @click="handleRemove(index)" />
              </div>
            </div>
          </template>
          <div class="code-image" v-if="ruleForm.type == 2">
            <div class="left">
              <el-form-item label="Tải lên vào nhóm:" prop="region">
                <el-cascader
                  class="form-width"
                  v-model="ruleForm.region"
                  :props="props"
                  :options="categoryList"
                  @change="handleChange"
                ></el-cascader>
              </el-form-item>
              <el-form-item label="Mã QR:" prop="region">
                <div class="code" ref="qrCodeUrl"></div>
                <div class="trip">Quét mã QR để tải nhanh ảnh từ điện thoại</div>
                <div class="trip-small">Nên dùng trình duyệt trên điện thoại</div>
              </el-form-item>
            </div>
            <div class="right">
              <el-button size="small" v-db-click @click="scanUploadGet">Làm mới thư viện ảnh</el-button>
              <div class="tip">Nhấn nút “Làm mới thư viện ảnh” để hiển thị các ảnh đã tải lên thành công từ điện thoại</div>
              <div class="img-box">
                <div
                  v-for="(item, index) in ruleForm.imgList"
                  :key="index"
                  class="pictrue"
                  draggable="false"
                  @dragstart="handleDragStart($event, item)"
                  @dragover.prevent="handleDragOver($event, item)"
                  @dragenter="handleDragEnter($event, item)"
                  @dragend="handleDragEnd($event, item)"
                >
                  <img :src="item.att_dir" />
                  <i class="el-icon-error btndel" v-db-click @click="handleWebRemove(item)" />
                </div>
              </div>
            </div>
          </div>
        </el-form>
      </div>

      <span slot="footer" class="dialog-footer">
        <el-button v-db-click @click="clear">Hủy</el-button>
        <el-button type="primary" :disabled="!ruleForm.imgList.length" v-db-click @click="submitUpload"
          >Xác nhận</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getCategoryListApi, moveApi, onlineUpload, scanUploadCode } from '@/api/uploadPictures';
import Setting from '@/setting';
import { getCookies } from '@/libs/util';
import { fileUpload, scanUploadQrcode, scanUploadGet } from '@/api/setting';
import QRCode from 'qrcodejs2';
import compressImg from '@/utils/compressImg.js';
import { isPicUpload } from '@/utils/index';
export default {
  name: '',
  props: {
    categoryList: {
      default: () => {
        return [];
      },
    },
    categoryId: {
      default: '',
    },
    isPage: {
      default: false,
    },
    isIframe: {
      default: false,
    },
  },
  watch: {
    uploadModal: {
      handler(newVal) {
        if (newVal) this.ruleForm.region = this.categoryId;
      },
      immediate: true,
    },
  },
  data() {
    return {
      webImgUrl: '',
      uploadModal: false,
      fileUrl: Setting.apiBaseURL + '/file/upload',
      header: {
        'Authori-zation': 'Bearer ' + getCookies('token'),
      },
      uploadData: {},
      props: { checkStrictly: true, emitPath: false, label: 'title', value: 'id' },
      disabled: false,
      ruleForm: {
        type: 0,
        region: '',
        imgList: [],
      },
      rules: { type: [{ required: true, message: 'Vui lòng chọn tài nguyên hoạt động', trigger: 'change' }] },
      qrcode: '',
      scanToken: '',
      limit: 20,
      loading: false,
      time: undefined,
    };
  },
  created() {},
  mounted() {},
  beforeDestroy() {
    clearInterval(this.time);
    this.time = undefined;
  },
  methods: {
    radioChange(type) {
      this.ruleForm.type = type;
      this.ruleForm.imgList = [];
      clearInterval(this.time);
      this.time = undefined;
      if (type == 2) {
        this.scanUploadQrcode();
        this.time = setInterval((e) => {
          this.scanUploadGet();
        }, 2000);
      }
    },
    scanUploadQrcode() {
      scanUploadQrcode(this.ruleForm.region).then((res) => {
        this.creatQrCode(res.data.url);
        this.scanToken = res.data.url;
      });
    },
    scanUploadGet() {
      let token = this.scanToken.split('token=')[1];
      scanUploadGet(token).then((res) => {
        this.ruleForm.imgList = res.data;
      });
    },

    getImg() {
      if (!this.webImgUrl) {
        this.$message.error('Vui lòng nhập địa chỉ ảnh trước');
        return;
      }
      if (this.webImgUrl.indexOf('.php') != -1) {
        this.$message.error('Vui lòng nhập địa chỉ ảnh khác trước');
        return;
      }
      this.ruleForm.imgList.push({
        url: this.webImgUrl,
      });
    },
    async submitUpload() {
      if (!this.ruleForm.imgList.length) return this.$message.warning('Vui lòng chọn ảnh trước');
      if (this.ruleForm.type == 0) {
        this.uploadData = {
          pid: this.ruleForm.region,
        };
        if (this.ruleForm.imgList.length) {
          if (this.loading) return;
          this.loading = true;
          for (let i = 0; i < this.ruleForm.imgList.length; i++) {
            const file = this.ruleForm.imgList[i].raw;
            await this.uploadItem(file);
            if (i == this.ruleForm.imgList.length - 1) {
              this.$message.success('Tải lên thành công');
              this.$emit('uploadSuccess');
              this.uploadModal = false;
              this.loading = false;
              this.initData();
            }
          }
        }
      } else if (this.ruleForm.type == 1) {
        let urls = this.ruleForm.imgList.map((e) => {
          return e.url;
        });
        if (urls.length) {
          if (this.loading) return;
          this.loading = true;
          onlineUpload({ pid: this.ruleForm.region, images: urls })
            .then((res) => {
              this.$message.success('Tải lên thành công');
              this.$emit('uploadSuccess');
              this.uploadModal = false;
              this.loading = false;
              this.initData();
            })
            .catch((err) => {
              this.loading = false;
              this.$message.error(err.msg);
            });
        }
      } else if (this.ruleForm.type == 2) {
        let attId = this.ruleForm.imgList.map((e) => {
          return e.att_id;
        });
        moveApi({ pid: this.ruleForm.region, images: attId }).then((res) => {
          this.$message.success('Tải lên thành công');
          this.$emit('uploadSuccess');
          this.uploadModal = false;
          this.initData();
        });
      }
    },
    uploadItem(file) {
      return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('pid', this.ruleForm.region);
        fileUpload(formData)
          .then((res) => {
            if (res.status == 200) {
              resolve();
              // this.$emit('uploadImgSuccess', res.data);
            } else {
              this.loading = false;
              this.$message({
                message: 'Tải lên thất bại',
                type: 'error',
                duration: 1000,
              });
            }
          })
          .catch((err) => {
            this.loading = false;
            this.$message.error(err.msg);
          });
      });
    },
    beforeUpload(file) {
      console.log(file);
    },
    creatQrCode(url) {
      this.$refs.qrCodeUrl.innerHTML = '';
      var qrcode = new QRCode(this.$refs.qrCodeUrl, {
        text: url, // Nội dung cần chuyển đổi thành mã QR
        width: 160,
        height: 160,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H,
      });
    },
    handleWebRemove(file) {
      let index = this.ruleForm.imgList.findIndex((e) => {
        return e.url == file.url;
      });
      this.ruleForm.imgList.splice(index, 1);
    },
    handleRemove(index) {
      this.ruleForm.imgList.splice(index, 1);
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
    handleDownload(file) {
      console.log(file);
    },
    async fileChange(file, fileList) {
      if (isPicUpload(file)) {
        if (file.size >= 2097152) {
          await this.comImg(file.raw).then((res) => {
            fileList.map((e) => {
              if (e.uid === file.uid) {
                e.raw = res;
              }
            });
            this.ruleForm.imgList = fileList;
          });
        } else {
          this.ruleForm.imgList = fileList;
        }
      } else {
        // Xóa phần tử cuối cùng trong mảng imgList của đối tượng ruleForm
        this.ruleForm.imgList.splice(this.ruleForm.imgList.length, 1);
      }
    },
    comImg(file) {
      return new Promise((resolve, reject) => {
        compressImg(file).then((res) => {
          resolve(res);
        });
      });
    },
    loadData(item, callback) {
      getCategoryListApi({
        pid: item.value,
      })
        .then(async (res) => {
          const data = res.data.list;
          callback(data);
        })
        .catch((res) => {});
    },
    handleChange(e) {
      if (this.ruleForm.type == 2) this.scanUploadQrcode();
    },
    // Di chuyển
    handleDragStart(e, item) {
      this.dragging = item;
    },
    handleDragEnd(e, item) {
      this.dragging = null;
    },
    handleDragOver(e) {
      e.dataTransfer.dropEffect = 'move';
    },
    handleDragEnter(e, item) {
      e.dataTransfer.effectAllowed = 'move';
      if (item === this.dragging) {
        return;
      }
      const newItems = [...this.ruleForm.imgList];
      const src = newItems.indexOf(this.dragging);
      const dst = newItems.indexOf(item);
      newItems.splice(dst, 0, ...newItems.splice(src, 1));
      this.ruleForm.imgList = newItems;
    },
    closed() {
      this.initData();
      scanUploadCode().then((res) => {});
    },
    clear() {
      this.uploadModal = false;
      this.initData();
    },
    initData() {
      this.ruleForm.type = 0;
      this.ruleForm.region = 0;
      this.scanToken = '';
      this.webImgUrl = '';
      this.ruleForm.imgList = [];
      clearInterval(this.time);
      this.time = undefined;
    },
  },
};
</script>
<style lang="scss" scoped>
::v-deep .el-dialog__title {
  font-size: 16px;
}
.main {
  min-height: 410px;
}
.pictrue {
  width: 60px !important;
  height: 60px !important;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 10px;
  position: relative;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
.btndel {
  position: absolute;
  z-index: 1;
  font-size: 18px;
  right: -5px;
  top: -5px;
  color: #999;
}
.form-width {
  width: 280px;
}
.tq-text {
  margin-left: 14px;
  font-size: 12px;
  font-weight: 400;
  color: var(--prev-color-primary);
  cursor: pointer;
}
.uploadCont ::v-deep .el-upload--picture-card,
::v-deep .el-upload-list--picture-card .el-upload-list__item {
  width: 64px;
  height: 64px;
  line-height: 72px;
  overflow: inherit;
}
.uploadCont ::v-deep .el-upload--picture-card,
::v-deep .el-upload-list--picture-card .el-upload-list__item img {
  width: 64px !important;
  height: 64px !important;
  border-radius: 6px;
  object-fit: cover;
}
.pl100 {
  padding-left: 100px;
}
.img-box {
  display: flex;
  flex-wrap: wrap;
}
.tips {
  font-size: 12px;
  color: #bbbbbb;
}
.code-image {
  display: flex;
  margin-top: 12px;
  .left {
    display: flex;
    flex-direction: column;
    margin-right: 20px;
    align-items: center;
    .code {
      border: 1px solid #dddddd;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 200px;
      height: 200px;
      border-radius: 4px;
      .code-img {
        width: 160px;
        height: 160px;
      }
    }
    .form-width {
      width: 200px;
    }
    .code {
      margin-bottom: 14px;
    }
    .trip {
      color: #333333;
      text-align: center;
      line-height: 18px;
    }
    .trip-small {
      font-size: 12px;
      font-weight: 400;
      color: #bbbbbb;
      text-align: center;
      line-height: 16px;
    }
  }
  .right {
    margin-top: 62px;
    .tip {
      font-size: 12px;
      font-weight: 400;
      color: #bbbbbb;
      margin: 10px 0;
    }
  }
}
</style>
