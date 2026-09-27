// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import * as qiniu from 'qiniu-js';
import Cos from 'cos-js-sdk-v5';
import axios from 'axios';
import { upload, ossUpload } from '@/api/upload';

const sign = (method, publicKey, privateKey, md5, contentType, date, bucketName, fileName) => {
  const CryptoJS = require('crypto-js'); // Ở đây dùng thư viện thuật toán mã hóa crypto-js, cách cài đặt sẽ nói ở phần sau
  const CanonicalizedResource = `/${bucketName}/${fileName}`;
  const StringToSign = method + '\n' + md5 + '\n' + contentType + '\n' + date + '\n' + CanonicalizedResource; // Ở đây md5 và date là tùy chọn, contentType đối với request PUT là tùy chọn, còn đối với request POST là bắt buộc
  let Signature = CryptoJS.HmacSHA1(StringToSign, privateKey);
  Signature = CryptoJS.enc.Base64.stringify(Signature);
  return 'UCloud' + ' ' + publicKey + ':' + Signature;
};
export default {
  videoUpload(config) {
    let result;
    switch (config.type) {
      case 'COS':
        result = this.cosUpload(config.evfile, config.res.data, config.uploading);
        break;
      case 'OSS':
        result = this.ossHttp(config.evfile, config.res, config.uploading);
        break;
      case 'OBS':
        result = this.obsHttp(config.evfile, config.res, config.uploading);
        break;
      case 'US3':
        result = this.us3Http(config.evfile, config.res, config.uploading);
        break;
      case 'JDOSS':
        result = this.jdHttp(config.evfile, config.res, config.uploading);
        break;
      case 'CTOSS':
        result = this.obsHttp(config.evfile, config.res, config.uploading);
        break;
      case 'QINIU':
        result = this.qiniuHttp(config.evfile, config.res, config.uploading);
        break;
      case 'local':
        result = this.uploadMp4ToLocal(config.evfile, config.res, config.uploading);
        break;
    }
    return result;
  },
  cosUpload(file, config, uploading) {
    let cos = new Cos({
      getAuthorization(options, callback) {
        callback({
          TmpSecretId: config.credentials.tmpSecretId, // tmpSecretId của khóa tạm thời
          TmpSecretKey: config.credentials.tmpSecretKey, // tmpSecretKey của khóa tạm thời
          XCosSecurityToken: config.credentials.sessionToken, // sessionToken của khóa tạm thời
          ExpiredTime: config.expiredTime, // Timestamp hết hạn của khóa tạm thời, là timestamp lúc xin khóa tạm thời cộng thêm durationSeconds
        });
      },
    });
    let fileObject = file.target.files[0];
    let Key = fileObject.name;
    let pos = Key.lastIndexOf('.');
    let suffix = '';
    if (pos !== -1) {
      suffix = Key.substring(pos);
    }
    let filename = this.getVideoName(suffix);
    return new Promise((resolve, reject) => {
      cos.sliceUploadFile(
        {
          Bucket: config.bucket /* Bắt buộc */,
          Region: config.region /* Bắt buộc */,
          Key: filename /* Bắt buộc */,
          Body: fileObject, // Đối tượng file upload
          onProgress: function (progressData) {
            uploading(progressData);
          },
        },
        function (err, data) {
          if (err) {
            reject({ msg: err });
          } else {
            resolve({ url: 'http://' + data.Location, ETag: data.ETag });
          }
        },
      );
    });
  },
  cosHttp(evfile, res, videoIng) {
    // Tencent Cloud
    // Định dạng url encode cho nhiều ký tự hơn
    let camSafeUrlEncode = function (str) {
      return encodeURIComponent(str)
        .replace(/!/g, '%21')
        .replace(/'/g, '%27')
        .replace(/\(/g, '%28')
        .replace(/\)/g, '%29')
        .replace(/\*/g, '%2A');
    };
    let fileObject = evfile.target.files[0];
    let Key = fileObject.name;
    let pos = Key.lastIndexOf('.');
    let suffix = '';
    if (pos !== -1) {
      suffix = Key.substring(pos);
    }
    let filename = this.getVideoName(suffix);
    let data = res.data;
    let XCosSecurityToken = data.credentials.sessionToken;
    let url = data.url + camSafeUrlEncode(filename).replace(/%2F/g, '/');
    let xhr = new XMLHttpRequest();
    xhr.open('PUT', url, true);
    XCosSecurityToken && xhr.setRequestHeader('x-cos-security-token', XCosSecurityToken);
    xhr.upload.onprogress = function (e) {
      let progress = Math.round((e.loaded / e.total) * 10000) / 100;
      videoIng(true, progress);
    };
    return new Promise((resolve, reject) => {
      xhr.onload = function () {
        if (/^2\d\d$/.test('' + xhr.status)) {
          var ETag = xhr.getResponseHeader('etag');
          videoIng(false, 0);
          resolve({ url: url, ETag: ETag });
        } else {
          reject({ msg: 'Tệp ' + filename + ' tải lên thất bại, mã trạng thái:' + xhr.statu });
        }
      };
      xhr.onerror = function () {
        reject({ msg: 'Tệp ' + filename + 'tải lên thất bại, vui lòng kiểm tra xem đã cấu hình quy tắc CORS (truy cập chéo nguồn) chưa' });
      };
      xhr.send(fileObject);
      xhr.onreadystatechange = function () {};
    });
  },
  ossHttp(evfile, res, videoIng) {
    let that = this;
    let fileObject = evfile.target.files[0];
    let file = fileObject.name;
    let pos = file.lastIndexOf('.');
    let suffix = '';
    if (pos !== -1) {
      suffix = file.substring(pos);
    }
    let filename = this.getVideoName(suffix);
    let formData = new FormData();
    let data = res.data;
    // Chú ý chữ hoa/thường của key khi append vào formData
    formData.append('key', filename); // Đường dẫn file lưu trên oss
    formData.append('OSSAccessKeyId', data.accessid); // accessKeyId
    formData.append('policy', data.policy); // policy
    formData.append('Signature', data.signature); // Chữ ký
    // Nếu là file base64 thì chỉ cần chuyển chuỗi base64 thành đối tượng blob rồi upload
    formData.append('file', fileObject);
    formData.append('success_action_status', 200); // Mã thao tác trả về sau khi thành công
    let url = data.host;
    let fileUrl = url + '/' + filename;
    videoIng(true, 100);
    return new Promise((resolve, reject) => {
      axios.defaults.withCredentials = false;
      axios
        .post(url, formData)
        .then(() => {
          // that.progress = 0;
          videoIng(false, 0);
          resolve({ url: fileUrl });
        })
        .catch((res) => {
          reject({ msg: res });
        });
    });
  },
  obsHttp(file, res, videoIng) {
    const fileObject = file.target.files[0];
    const Key = fileObject.name;
    const pos = Key.lastIndexOf('.');
    let suffix = '';
    if (pos !== -1) {
      suffix = Key.substring(pos);
    }
    const filename = this.getVideoName(suffix);
    const formData = new FormData();
    const data = res.data;
    // Chú ý chữ hoa/thường của key khi append vào formData
    formData.append('key', filename);
    formData.append('AccessKeyId', data.accessid);
    formData.append('policy', data.policy);
    formData.append('signature', data.signature);
    formData.append('file', fileObject);
    formData.append('success_action_status', 200);
    const url = data.host;
    const fileUrl = url + '/' + filename;
    videoIng(true, 100);
    return new Promise((resolve, reject) => {
      axios.defaults.withCredentials = false;
      axios
        .post(url, formData)
        .then(() => {
          videoIng(false, 0);
          resolve({ url: data.cdn ? data.cdn + '/' + filename : fileUrl });
        })
        .catch((res) => {
          reject({ msg: res });
        });
    });
  },
  us3Http(file, res, videoIng) {
    const fileObject = file.target.files[0];
    const Key = fileObject.name;
    const pos = Key.lastIndexOf('.');
    let suffix = '';
    if (pos !== -1) {
      suffix = Key.substring(pos);
    }
    const filename = this.getVideoName(suffix);
    const data = res.data;

    const auth = sign('PUT', data.accessid, data.secretKey, '', fileObject.type, '', data.storageName, filename);
    return new Promise((resolve, reject) => {
      axios.defaults.withCredentials = false;
      const url = `https://${data.storageName}.cn-bj.ufileos.com/${filename}`;
      axios
        .put(url, fileObject, {
          headers: {
            Authorization: auth,
            'content-type': fileObject.type,
          },
        })
        .then((res) => {
          videoIng(false, 0);
          resolve({ url: data.cdn ? data.cdn + '/' + filename : url });
        })
        .catch((res) => {
          reject({ msg: res });
        });
    });
  },
  qiniuHttp(evfile, res, videoIng) {
    const uptoken = res.data.token;
    const file = evfile.target.files[0]; // Đối tượng Blob, file được upload
    const Key = file.name; // Sau khi upload, tên resource của file sẽ theo key đã đặt, nếu key là null hoặc undefined thì tên resource sẽ dùng giá trị hash làm tên.
    const pos = Key.lastIndexOf('.');
    let suffix = '';
    if (pos !== -1) {
      suffix = Key.substring(pos);
    }
    const filename = this.getVideoName(suffix);
    const fileUrl = res.data.domain + '/' + filename;
    const config = {
      useCdnDomain: true,
    };
    const putExtra = {
      fname: '', // Tên file gốc
      params: {}, // Dùng để đặt biến tùy chỉnh
      mimeType: null, // Dùng để giới hạn loại file upload, khi là null nghĩa là không giới hạn loại file; các loại giới hạn đặt trong mảng: ["image/png", "image/jpeg", "image/gif"]
    };
    const observable = qiniu.upload(file, filename, uptoken, putExtra, config);

    return new Promise((resolve, reject) => {
      observable.subscribe({
        next: (result) => {
          const progress = Math.round(result.total.loaded / result.total.size);
          videoIng(true, progress);
          // Chủ yếu dùng để hiển thị tiến trình
        },
        error: (errResult) => {
          // Thông tin lỗi khi thất bại
          reject({ msg: errResult });
        },
        complete: (result) => {
          // Thông tin trả về sau khi nhận thành công
          videoIng(false, 0);
          resolve({ url: res.data.cdn ? res.data.cdn + '/' + filename : fileUrl });
        },
      });
    });
  },
  // Tải lên JD Cloud
  jdHttp(evfile, r, videoIng) {
    const fileObject = evfile.target.files[0]; // Đối tượng file lấy được
    const formData = new FormData();
    formData.append('file', fileObject);
    return new Promise((resolve, reject) => {
      ossUpload(r.data.upload_url, formData)
        .then((res) => {
          console.log(res);
        })
        .catch((err) => {
          videoIng(true, 100);
          resolve(r.data);
        });
    });
  },
  // Tải lên từ máy
  uploadMp4ToLocal(evfile, res, videoIng) {
    const fileObject = evfile.target.files[0]; // Đối tượng file lấy được
    const formData = new FormData();
    formData.append('file', fileObject);
    videoIng(true, 100);
    return upload(formData);
  },
  // Lấy tên video đã upload lên lưu trữ cloud
  getVideoName(suffix) {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const name = new Date().getTime();
    return `attach/${year}/${month}/${name}` + suffix;
  },
};
