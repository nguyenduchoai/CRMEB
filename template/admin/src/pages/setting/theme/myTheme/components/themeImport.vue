<template>
  <div class="theme-import">
    <div class="goods-upload">
      <el-upload
        v-show="!fileUrl && !importStatus"
        ref="upload"
        class="upload-demo"
        :drag="!fileUrl"
        :show-file-list="false"
        :action="uploadUrl"
        :headers="header"
        :before-upload="fileChange"
        :on-success="handleSuccess"
        :on-error="handleError"
        accept=".zip"
      >
        <div v-if="uploadLoading" class="flex-column">
          <i class="el-icon-loading" style="font-size: 40px; color: #ccc"></i>
          <div class="el-upload__text">Đang tải lên...</div>
        </div>
        <template v-else>
          <img class="el-upload-dragger__icon mb20" src="@/assets/images/upload-theme-icon.png" alt="" />
          <div class="el-upload__text">Kéo tệp vào đây, hoặc<em>nhấn để thêm</em></div>
          <div class="el-upload__trip">Hỗ trợ .zip, tối đa 50M</div>
        </template>
      </el-upload>
      <div v-show="fileUrl && !importStatus" class="file-info">
        <img class="el-upload-dragger__icon mb20" src="@/assets/images/upload-theme-icon.png" alt="" />
        <div class="el-upload__text">{{ fileName }}</div>
        <div class="flex mt12" v-if="fileUrl && !importLoading">
          <div class="active-btn" @click="selectFile">Tải lên lại</div>
          <div class="active-btn" @click="fileUrl = ''">Xóa</div>
        </div>
        <div class="el-upload__trip" v-if="importLoading">
          Đang nhập, bạn có thể đóng cửa sổ này, lát nữa có thể xem kết quả nhập trong danh sách
          <i class="el-icon-loading"></i>
        </div>
        <el-button v-else class="btn-import" type="primary" size="small" @click="importThemePkg">Nhập ngay</el-button>
      </div>
      <div v-show="fileUrl && importStatus" class="file-info">
        <img class="el-upload-dragger__icon mb20" :src="statusImage" alt="" />
        <div class="el-upload__text">Nhập thành công</div>
        <div>
          <el-button class="btn-import" size="small" @click="selectFile">Nhập lại</el-button>
          <el-button type="primary" class="btn-import" @click="close">Hoàn thành</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { importTheme } from '@/api/diy';
import Setting from '@/setting';
import { getCookies } from '@/libs/util';

export default {
  name: 'themeImport',
  data() {
    return {
      uploadUrl: Setting.apiBaseURL + '/file/upload/1?type=1',
      header: {
        'Authori-zation': 'Bearer ' + getCookies('token'),
      },
      fileName: '',
      fileUrl: '',
      importStatus: false,
      importLoading: false,
      uploadLoading: false,
      statusImage: require('@/assets/images/file-success.png'),
    };
  },
  methods: {
    fileChange(file, fileList) {
      const isZip = file.name.endsWith('.zip');
      if (!isZip) {
        this.$message.error('Vui lòng tải lên tệp định dạng .zip');
        return false;
      }
      // Giới hạn 50M
      if (file.size >= 52428800) {
        this.$message.error('Kích thước tệp không được vượt quá 50MB');
        return false;
      } else {
        this.uploadLoading = true;
        this.fileName = file.name;
        return true;
      }
    },
    selectFile() {
      this.importStatus = false;
      this.importLoading = false;
      this.uploadLoading = false;
      // Gọi mở chọn file
      this.$refs['upload'].$refs['upload-inner'].handleClick();
    },
    handleSuccess(res, file, fileList) {
      this.uploadLoading = false;
      if (res.status === 200) {
        this.fileUrl = res.data.src;
      } else {
        this.$message.error(res.msg);
      }
    },
    handleError(err, file, fileList) {
      this.uploadLoading = false;
      this.$message.error('Tải lên thất bại');
    },
    importThemePkg() {
      this.importLoading = true;
      this.importStatus = false;
      importTheme({
        url: this.fileUrl,
      })
        .then((res) => {
          // Trả về kết quả nhập
          this.importStatus = true;
          this.importLoading = false;
          this.statusImage = require('@/assets/images/file-success.png');
          this.$message.success('Nhập thành công');
          this.$emit('success');
        })
        .catch((err) => {
          this.importLoading = false;
          this.importStatus = false;
          this.$message.error(err.msg);
        });
    },
    close() {
      this.fileUrl = '';
      this.fileName = '';
      this.importStatus = false;
      this.$emit('close');
    },
  },
};
</script>

<style lang="scss" scoped>
.goods-upload {
  width: 100%;
  height: 680px;
  .upload-demo {
    height: 100%;
  }
  ::v-deep .el-upload {
    width: 100%;
    height: 100%;
    .el-upload-dragger {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 100px 0; // Adjusted padding since there is no top part
      .el-upload-dragger__icon {
        width: 54px;
        // height: 57px;
      }
      .el-upload__trip {
        font-weight: 400;
        font-size: 12px;
        color: #999999;
        margin-top: 6px;
      }
    }
  }
  .file-info {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    border: 1px dashed #d9d9d9;
    border-radius: 4px;
    .active-btn {
      color: var(--prev-color-primary);
      font-size: 12px;
      font-weight: 400;
      margin: 0 6px;
      cursor: pointer;
    }
    .btn-import {
      margin-top: 26px;
    }
    .el-upload-dragger__icon {
      height: 57px;
    }
    .el-upload__trip {
      display: flex;
      align-items: center;
      font-weight: 400;
      font-size: 12px;
      color: #999;
      margin-top: 6px;
    }
  }
}
</style>
