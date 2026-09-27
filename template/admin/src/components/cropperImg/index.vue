<template>
  <div class="cropper-content">
    <div class="cropper-box">
      <div class="cropper">
        <vue-cropper
          ref="cropper"
          :img="option.img"
          :outputSize="option.outputSize"
          :outputType="option.outputType"
          :info="option.info"
          :canScale="option.canScale"
          :autoCrop="option.autoCrop"
          :autoCropWidth="option.autoCropWidth"
          :autoCropHeight="option.autoCropHeight"
          :fixed="option.fixed"
          :fixedNumber="option.fixedNumber"
          :full="option.full"
          :fixedBox="option.fixedBox"
          :canMove="option.canMove"
          :canMoveBox="option.canMoveBox"
          :original="option.original"
          :centerBox="option.centerBox"
          :height="option.height"
          :infoTrue="option.infoTrue"
          :maxImgSize="option.maxImgSize"
          :enlarge="option.enlarge"
          :mode="option.mode"
          @realTime="realTime"
          @imgLoad="imgLoad"
        >
        </vue-cropper>
      </div>
      <!--Nút công cụ thao tác ở dưới-->
      <div class="footer-btn">
        <div class="scope-btn">
          <input
            type="file"
            id="uploads"
            style="position: absolute; clip: rect(0 0 0 0)"
            accept="image/png, image/jpeg, image/gif, image/jpg"
            @change="selectImg($event)"
          />
          <el-button size="mini" type="danger" plain icon="el-icon-zoom-in" v-db-click @click="changeScale(1)"
            >Phóng to</el-button
          >
          <el-button size="mini" type="danger" plain icon="el-icon-zoom-out" v-db-click @click="changeScale(-1)"
            >Thu nhỏ</el-button
          >
          <el-button size="mini" type="danger" plain v-db-click @click="rotateLeft">↺ Xoay trái</el-button>
          <el-button size="mini" type="danger" plain v-db-click @click="rotateRight">↻ Xoay phải</el-button>
        </div>
      </div>
    </div>
    <!--Ảnh xem trước hiệu ứng-->
    <div class="show-preview">
      <div class="preview">
        <img :src="previews.url" :style="previews.img" />
      </div>
      <div class="upload-btn">
        <label class="btn" for="uploads">Chọn ảnh</label>
        <el-button size="mini" type="success" v-db-click @click="uploadImg()">Xác nhận tải lên</el-button>
      </div>
    </div>
  </div>
</template>

<script>
import { VueCropper } from 'vue-cropper';
// import { updateAvatar } from ''; // Ở đây là API tải file lên, đổi thành file của bạn
import { fileUpload } from '@/api/setting';
export default {
  name: 'CropperImage',
  components: {
    VueCropper,
  },
  data() {
    return {
      name: '',
      resImg: '',
      previews: {},
      option: {
        img: '', //Địa chỉ ảnh cần cắt (crop)
        outputSize: 1, //Chất lượng ảnh sau khi cắt (tùy chọn 0.1 - 1)
        outputType: 'png', //Định dạng ảnh sau khi cắt (jpeg || png || webp)
        info: true, //Thông tin kích thước ảnh
        canScale: true, //Ảnh có cho phép zoom bằng lăn chuột không
        autoCrop: true, //Có mặc định tạo khung crop không
        autoCropWidth: 200, //Chiều rộng khung crop mặc định
        autoCropHeight: 200, //Chiều cao khung crop mặc định
        fixed: true, //Có bật tỉ lệ cố định chiều rộng/cao khung crop không
        fixedNumber: [1, 1], //Tỉ lệ chiều rộng/cao khung crop
        full: false, //false: cắt ảnh theo tỉ lệ gốc, không bị biến dạng
        fixedBox: false, //Cố định kích thước khung crop, không cho thay đổi
        canMove: true, //Ảnh tải lên có thể di chuyển không
        canMoveBox: true, //Khung crop có kéo được không
        original: false, //Ảnh tải lên render theo tỉ lệ gốc
        centerBox: true, //Khung crop có bị giới hạn trong ảnh không
        height: false, //Có xuất ảnh theo tỉ lệ dpr của thiết bị không
        infoTrue: false, //true: hiển thị chiều rộng/cao thực của ảnh xuất ra, false: hiển thị chiều rộng/cao khung crop nhìn thấy
        maxImgSize: 3000, //Giới hạn chiều rộng và chiều cao tối đa của ảnh
        enlarge: 1, //Ảnh xuất theo hệ số tỉ lệ của khung crop
        mode: '300px 300px', //Cách render ảnh mặc định
      },
    };
  },
  methods: {
    //Hàm khởi tạo
    imgLoad(msg) {
      console.log('Hàm khởi tạo công cụ=====' + msg);
    },
    //Zoom ảnh
    changeScale(num) {
      num = num || 1;
      this.$refs.cropper.changeScale(num);
    },
    //Xoay trái
    rotateLeft() {
      this.$refs.cropper.rotateLeft();
    },
    //Xoay phải
    rotateRight() {
      this.$refs.cropper.rotateRight();
    },
    // // Hàm xem trước theo thời gian thực
    realTime(data) {
      let that = this;
      that.previews = data;
      this.$refs.cropper.getCropBlob((data) => {
        this.blobToDataURI(data, function (res) {
          that.previewImg = res;
        });
      });
    },
    blobToDataURI(blob, callback) {
      var reader = new FileReader();
      reader.readAsDataURL(blob);
      reader.onload = function (e) {
        callback(e.target.result);
      };
    },
    //Chọn ảnh
    selectImg(e) {
      let file = e.target.files[0];
      if (!/\.(jpg|jpeg|png|JPG|PNG)$/.test(e.target.value)) {
        this.$message({
          message: 'Định dạng ảnh yêu cầu: jpeg, jpg, png',
          type: 'error',
        });
        return false;
      }
      //Chuyển thành blob
      let reader = new FileReader();
      reader.onload = (e) => {
        let data;
        if (typeof e.target.result === 'object') {
          data = window.URL.createObjectURL(new Blob([e.target.result]));
        } else {
          data = e.target.result;
        }
        this.option.img = data;
      };
      //Chuyển thành base64
      reader.readAsDataURL(file);
    },

    base64ImgtoFile(dataurl, filename = 'file') {
      //Tách chuỗi base64: ['data:image/png;base64','XXXX']
      const arr = dataurl.split(',');
      // .*? nghĩa là khớp với ký tự bất kỳ cho tới ký tự tiếp theo thỏa điều kiện, khớp đúng đến:
      // image/png
      const mime = arr[0].match(/:(.*?);/)[1]; //image/png
      //[image,png] lấy đuôi loại ảnh
      const suffix = mime.split('/')[1]; //png
      const bstr = atob(arr[1]); //Phương thức atob() dùng để decode chuỗi được encode bằng base-64
      let n = bstr.length;
      const u8arr = new Uint8Array(n);
      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }
      return new File([u8arr], `${filename}.${suffix}`, {
        type: mime,
      });
    },

    uploadFile(file) {
      const formData = new FormData();
      formData.append('file', file);
      fileUpload(formData).then((res) => {
        if (res.status == 200) {
          this.$emit('uploadImgSuccess', res.data);
        } else {
          this.$message({
            message: 'Tải lên thất bại',
            type: 'error',
            duration: 1000,
          });
        }
      });
    },
    //Tải lên ảnh
    uploadImg() {
      this.$refs.cropper.getCropData((data) => {
        this.resImg = this.base64ImgtoFile(data);
        this.uploadFile(this.resImg);
      });
    },
  },
};
</script>

<style scoped lang="scss">
.btn {
  outline: none;
  display: inline-block;
  line-height: 1;
  white-space: nowrap;
  cursor: pointer;
  -webkit-appearance: none;
  text-align: center;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  outline: 0;
  -webkit-transition: 0.1s;
  transition: 0.1s;
  font-weight: 500;
  padding: 8px 15px;
  font-size: 12px;
  border-radius: 3px;
  color: #fff;
  background-color: #409eff;
  border-color: #409eff;
  margin-right: 10px;
}
.cropper-content {
  display: flex;
  display: -webkit-flex;
  justify-content: flex-end;
  .cropper-box {
    flex: 1;
    width: 100%;
    .cropper {
      width: auto;
      height: 300px;
    }
  }

  .show-preview {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    .preview {
      overflow: hidden;
      height: 200px;
      width: 200px;
      background: #cccccc;
      transform: scale(0.8);
      border-radius: 50%;
    }
  }
}
.footer-btn {
  margin-top: 30px;
  display: flex;
  display: -webkit-flex;
  justify-content: space-around;
  .scope-btn {
    display: flex;
    display: -webkit-flex;
    justify-content: space-between;
    padding-right: 10px;
  }
  .upload-btn {
    flex: 1;
    -webkit-flex: 1;
    display: flex;
    display: -webkit-flex;
    justify-content: center;
  }
}
</style>
