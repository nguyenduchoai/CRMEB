// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import { TOKENNAME, HTTP_REQUEST_URL } from "../config/app.js";
import store from "../store";
import i18n from "./lang.js";
import { pathToBase64 } from "@/plugin/image-tools/index.js";
// #ifdef APP-PLUS
import permision from "./permission.js";
// #endif
export default {
  /**
   * opt  object | string
   * to_url object | string
   * Ví dụ:
   * this.Tips('/pages/test/test'); chuyển trang không hiển thị thông báo
   * this.Tips({title:'Thông báo'},'/pages/test/test'); hiển thị thông báo rồi chuyển trang
   * this.Tips({title:'Thông báo'},{tab:1,url:'/pages/index/index'}); hiển thị thông báo rồi chuyển đến tab
   * tab=1 sau một khoảng thời gian sẽ chuyển đến tab
   * tab=2 sau một khoảng thời gian sẽ chuyển đến trang không phải tab
   * tab=3 sau một khoảng thời gian sẽ quay lại trang trước
   * tab=4 đóng tất cả trang, mở đến một trang nào đó trong ứng dụng
   * tab=5 đóng trang hiện tại, chuyển đến một trang nào đó trong ứng dụng
   */
  Tips: function (opt, to_url) {
    if (typeof opt == "string") {
      to_url = opt;
      opt = {};
    }
    let title = opt.title || "",
      icon = opt.icon || "none",
      endtime = opt.endtime || 2000,
      success = opt.success;
    if (title)
      uni.showToast({
        title: title,
        icon: icon,
        duration: endtime,
        success,
      });
    if (to_url != undefined) {
      if (typeof to_url == "object") {
        let tab = to_url.tab || 1,
          url = to_url.url || "";
        switch (tab) {
          case 1:
            //Sau một khoảng thời gian sẽ chuyển đến tab
            setTimeout(function () {
              uni.switchTab({
                url: url,
              });
            }, endtime);
            break;
          case 2:
            //Chuyển đến trang không phải tab
            setTimeout(function () {
              uni.navigateTo({
                url: url,
              });
            }, endtime);
            break;
          case 3:
            //Quay lại trang trước
            setTimeout(function () {
              // #ifndef H5
              uni.navigateBack({
                delta: parseInt(url),
              });
              // #endif
              // #ifdef H5
              history.back();
              // #endif
            }, endtime);
            break;
          case 4:
            //Đóng tất cả trang, mở đến một trang nào đó trong ứng dụng
            setTimeout(function () {
              uni.reLaunch({
                url: url,
              });
            }, endtime);
            break;
          case 5:
            //Đóng trang hiện tại, chuyển đến một trang nào đó trong ứng dụng
            setTimeout(function () {
              uni.redirectTo({
                url: url,
              });
            }, endtime);
            break;
        }
      } else if (typeof to_url == "function") {
        setTimeout(function () {
          to_url && to_url();
        }, endtime);
      } else {
        //Khi không có thông báo thì chuyển trang không delay
        setTimeout(
          function () {
            uni.navigateTo({
              url: to_url,
            });
          },
          title ? endtime : 0
        );
      }
    }
  },
  /**
   * Loại bỏ một mảng con nào đó trong mảng và tạo thành mảng mới để trả về
   * @param array array Mảng cần loại bỏ
   * @param int index Giá trị khóa (key) của mảng cần loại bỏ
   * @param string | int Giá trị
   * @return array
   *
   */
  ArrayRemove: function (array, index, value) {
    const valueArray = [];
    if (array instanceof Array) {
      for (let i = 0; i < array.length; i++) {
        if (typeof index == "number" && array[index] != i) {
          valueArray.push(array[i]);
        } else if (typeof index == "string" && array[i][index] != value) {
          valueArray.push(array[i]);
        }
      }
    }
    return valueArray;
  },
  /**
   * Tạo poster, lấy văn bản
   * @param string text Là văn bản được truyền vào
   * @param int num Là độ dài byte hiển thị trên một dòng
   * @return array
   */
  textByteLength: function (text, num) {
    let strLength = 0;
    let rows = 1;
    let str = 0;
    let arr = [];
    for (let j = 0; j < text.length; j++) {
      if (text.charCodeAt(j) > 255) {
        strLength += 2;
        if (strLength > rows * num) {
          strLength++;
          arr.push(text.slice(str, j));
          str = j;
          rows++;
        }
      } else {
        strLength++;
        if (strLength > rows * num) {
          arr.push(text.slice(str, j));
          str = j;
          rows++;
        }
      }
    }
    arr.push(text.slice(str, text.length));
    return [strLength, arr, rows]; //  [Tổng độ dài byte của văn bản xử lý, mảng nội dung hiển thị mỗi dòng, số dòng]
  },

  /**
   * Lấy poster chia sẻ sản phẩm
   * @param array arr2 Tư liệu poster
   * @param string store_name Văn bản tư liệu
   * @param string price Giá
   * @param string ot_price Giá gốc
   * @param function successFn Hàm callback
   *
   *
   */
  PosterCanvas: function (arr2, store_name, price, ot_price, successFn) {
    let that = this;
    uni.showLoading({
      title: i18n.t(`Đang tạo poster`),
      mask: true,
    });
    const ctx = uni.createCanvasContext("myCanvas");
    ctx.clearRect(0, 0, 0, 0);

    /**
     * Chỉ có thể lấy thông tin ảnh thuộc domain hợp lệ, không thể lấy khi debug local
     *
     */
    ctx.fillStyle = "#fff";
    ctx.fillRect(0, 0, 750, 1250);
    uni.getImageInfo({
      src: arr2[0],
      success: function (res) {
        const WIDTH = res.width;
        const HEIGHT = res.height;
        // ctx.drawImage(arr2[0], 0, 0, WIDTH, 1050);
        ctx.drawImage(arr2[1], 0, 0, WIDTH, WIDTH);
        ctx.save();
        let r = 110;
        let d = r * 2;
        let cx = 480;
        let cy = 790;
        ctx.arc(cx + r, cy + r, r, 0, 2 * Math.PI);
        // ctx.clip();
        ctx.drawImage(arr2[2], cx, cy, d, d);
        ctx.restore();
        const CONTENT_ROW_LENGTH = 20;
        let [contentLeng, contentArray, contentRows] = that.textByteLength(
          store_name,
          CONTENT_ROW_LENGTH
        );
        if (contentRows > 2) {
          contentRows = 2;
          let textArray = contentArray.slice(0, 2);
          textArray[textArray.length - 1] += "…";
          contentArray = textArray;
        }
        ctx.setTextAlign("left");
        ctx.setFontSize(36);
        ctx.setFillStyle("#000");
        // let contentHh = 36 * 1.5;
        let contentHh = 36;
        for (let m = 0; m < contentArray.length; m++) {
          if (m) {
            ctx.fillText(contentArray[m], 50, 1000 + contentHh * m + 18, 1100);
          } else {
            ctx.fillText(contentArray[m], 50, 1000 + contentHh * m, 1100);
          }
        }
        ctx.setTextAlign("left");
        ctx.setFontSize(72);
        ctx.setFillStyle("#DA4F2A");
        ctx.fillText(i18n.t(`￥`) + price, 40, 820 + contentHh);

        ctx.setTextAlign("left");
        ctx.setFontSize(36);
        ctx.setFillStyle("#999");
        // Giá gốc trên poster sản phẩm
        if (ot_price) {
          ctx.fillText(i18n.t(`￥`) + ot_price, 50, 876 + contentHh);
          var underline = function (
            ctx,
            text,
            x,
            y,
            size,
            color,
            thickness,
            offset
          ) {
            var width = ctx.measureText(text).width;
            switch (ctx.textAlign) {
              case "center":
                x -= width / 2;
                break;
              case "right":
                x -= width;
                break;
            }

            y += size + offset;

            ctx.beginPath();
            ctx.strokeStyle = color;
            ctx.lineWidth = thickness;
            ctx.moveTo(x, y);
            ctx.lineTo(x + width, y);
            ctx.stroke();
          };
          underline(ctx, i18n.t(`￥`) + ot_price, 55, 865, 36, "#999", 2, 0);
        }
        ctx.setTextAlign("left");
        ctx.setFontSize(28);
        ctx.setFillStyle("#999");
        ctx.fillText(i18n.t(`Nhấn giữ hoặc quét mã để xem`), 490, 1030 + contentHh);
        ctx.draw(true, function () {
          uni.canvasToTempFilePath({
            canvasId: "myCanvas",
            fileType: "png",
            destWidth: WIDTH,
            destHeight: HEIGHT,
            success: function (res) {
              uni.hideLoading();
              successFn && successFn(res.tempFilePath);
            },
          });
        });
      },
      fail: function (err) {
        uni.hideLoading();
        that.Tips({
          title: i18n.t(`Không thể lấy thông tin hình ảnh`),
        });
      },
    });
  },
  /**
   * Lấy poster săn giảm giá/mua chung
   * @param array arr2 Tư liệu poster: ảnh nền
   * @param string store_name Văn bản tư liệu
   * @param string price Giá
   * @param string ot_price Giá gốc
   * @param function successFn Hàm callback
   *
   *
   */
  bargainPosterCanvas: function (
    arr2,
    title,
    label,
    msg,
    price,
    wd,
    hg,
    successFn
  ) {
    let that = this;
    const ctx = uni.createCanvasContext("myCanvas");
    ctx.clearRect(0, 0, 0, 0);
    /**
     * Chỉ có thể lấy thông tin ảnh thuộc domain hợp lệ, không thể lấy khi debug local
     *
     */
    ctx.fillStyle = "#fff";
    ctx.fillRect(0, 0, wd * 2, hg * 2);
    uni.getImageInfo({
      src: arr2[0],
      success: function (res) {
        const WIDTH = res.width;
        const HEIGHT = res.height;
        ctx.drawImage(arr2[0], 0, 0, wd, hg);

        // Đảm bảo tọa độ chính xác trên các dòng máy khác nhau
        let labelx = 0.65; //Nhãn x
        let labely = 0.166; //Nhãn y
        let pricex = 0.1857; //Giá x
        let pricey = 0.18; //Giá x
        let codex = 0.385; //Mã QR
        let codey = 0.77;
        let picturex = 0.1571; //Điểm trên trái của ảnh sản phẩm
        let picturey = 0.2916;
        let picturebx = 0.6857; //Điểm dưới phải của ảnh sản phẩm
        let pictureby = 0.4316;
        let msgx = 0.1036; //msg
        let msgy = 0.2306;
        let codew = 0.25;
        ctx.drawImage(
          arr2[1],
          wd * picturex,
          hg * picturey,
          wd * picturebx,
          hg * pictureby
        );
        ctx.drawImage(arr2[2], wd * codex, hg * codey, wd * codew, wd * codew);
        ctx.save();
        //Tiêu đề
        const CONTENT_ROW_LENGTH = 32;
        let [contentLeng, contentArray, contentRows] = that.textByteLength(
          title,
          CONTENT_ROW_LENGTH
        );
        if (contentRows > 2) {
          contentRows = 2;
          let textArray = contentArray.slice(0, 2);
          textArray[textArray.length - 1] += "…";
          contentArray = textArray;
        }
        ctx.setTextAlign("left");
        ctx.setFillStyle("#000");
        if (contentArray.length < 2) {
          ctx.setFontSize(22);
        } else {
          ctx.setFontSize(20);
        }
        let contentHh = 8;
        for (let m = 0; m < contentArray.length; m++) {
          if (m) {
            ctx.fillText(contentArray[m], 20, 35 + contentHh * m + 18, 1100);
          } else {
            ctx.fillText(contentArray[m], 20, 35, 1100);
          }
        }
        // Nội dung nhãn
        ctx.setTextAlign("left");
        ctx.setFontSize(16);
        ctx.setFillStyle("#FFF");
        ctx.fillText(label, wd * labelx, hg * labely);
        ctx.save();
        // Giá
        ctx.setFillStyle("red");
        ctx.setFontSize(26);
        ctx.fillText(price, wd * pricex, hg * pricey);
        ctx.save();
        // msg
        ctx.setFillStyle("#333");
        ctx.setFontSize(16);
        ctx.fillText(msg, wd * msgx, hg * msgy);
        ctx.save();
        ctx.draw(true, () => {
          uni.canvasToTempFilePath({
            canvasId: "myCanvas",
            fileType: "png",
            quality: 1,
            success: (res) => {
              successFn && successFn(res.tempFilePath);
              uni.hideLoading();
            },
          });
        });
      },
      fail: function (err) {
        uni.hideLoading();
        that.Tips({
          title: i18n.t(`Không thể lấy thông tin hình ảnh`),
        });
      },
    });
  },
  /**
   * Poster chia sẻ thông tin người dùng
   * @param array arr2 Tư liệu poster: 1 nền, 0 mã QR
   * @param string nickname Biệt danh
   * @param string sitename Giá
   * @param function successFn Hàm callback
   *
   *
   */
  userPosterCanvas: function (
    arr2,
    nickname,
    sitename,
    index,
    w,
    h,
    successFn
  ) {
    let that = this;
    const ctx = uni.createCanvasContext("myCanvas" + index);
    ctx.clearRect(0, 0, 0, 0);
    /**
     * Chỉ có thể lấy thông tin ảnh thuộc domain hợp lệ, không thể lấy khi debug local
     *
     */
    uni.getImageInfo({
      src: arr2[1],
      success: function (res) {
        const WIDTH = res.width;
        const HEIGHT = res.height;
        ctx.fillStyle = "#fff";
        ctx.fillRect(0, 0, w, h);
        ctx.drawImage(arr2[1], 0, 0, w, h);
        ctx.setTextAlign("left");
        ctx.setFontSize(12);
        ctx.setFillStyle("#333");

        // x:240 y:426
        let codex = 0.1906;
        let codey = 0.7746;
        let codeSize = 0.21666;
        let namex = 0.4283;
        let namey = 0.8215;
        let markx = 0.4283;
        let marky = 0.8685;
        ctx.drawImage(
          arr2[0],
          w * codex,
          h * codey,
          w * codeSize,
          w * codeSize
        );
        if (w < 270) {
          ctx.setFontSize(8);
        } else {
          ctx.setFontSize(10);
        }
        ctx.fillText(nickname, w * namex, h * namey);
        if (w < 270) {
          ctx.setFontSize(8);
        } else {
          ctx.setFontSize(10);
        }
        ctx.fillText(i18n.t(`Mời bạn tham gia`) + sitename, w * markx, h * marky);
        ctx.save();
        ctx.draw(true, function () {
          uni.canvasToTempFilePath({
            canvasId: "myCanvas" + index,
            fileType: "png",
            quality: 1,
            success: function (res) {
              successFn && successFn(res.tempFilePath);
            },
          });
        });
      },
      fail: function (err) {
        uni.hideLoading();
        that.Tips({
          title: i18n.t(`Không thể lấy thông tin hình ảnh`),
        });
      },
    });
  },
  /*
   * Tải lên một ảnh
   * @param object opt
   * @param callable successCallback Phương thức thực hiện khi thành công, data
   * @param callable errorCallback Phương thức thực hiện khi thất bại
   */
  uploadImageOne: function (opt, successCallback, errorCallback) {
    let that = this;
    if (typeof opt === "string") {
      let url = opt;
      opt = {};
      opt.url = url;
    }
    let count = opt.count || 1,
      sizeType = opt.sizeType || ["compressed"],
      sourceType = opt.sourceType || ["album", "camera"],
      is_load = opt.is_load || true,
      uploadUrl = opt.url || "",
      inputName = opt.name || "pics",
      fileType = opt.fileType || "image";
    uni.chooseImage({
      count: count, //Tổng số ảnh tối đa có thể chọn
      sizeType: sizeType, // Có thể chỉ định là ảnh gốc hay ảnh đã nén, mặc định có cả hai
      sourceType: sourceType, // Có thể chỉ định nguồn là album hay camera, mặc định có cả hai
      success: function (res) {
        //Đang bắt đầu tải lên...
        uni.showLoading({
          title: i18n.t(`Đang tải ảnh lên`),
        });
        uni.uploadFile({
          url: HTTP_REQUEST_URL + "/api/" + uploadUrl,
          filePath: res.tempFilePaths[0],
          fileType: fileType,
          name: inputName,
          formData: {
            filename: inputName,
          },
          header: {
            // #ifdef MP
            "Content-Type": "multipart/form-data",
            // #endif
            [TOKENNAME]: "Bearer " + store.state.app.token,
          },
          success: function (res) {
            uni.hideLoading();
            if (res.statusCode == 403) {
              that.Tips({
                title: res.data,
              });
            } else {
              let data = res.data ? JSON.parse(res.data) : {};
              if (data.status == 200) {
                successCallback && successCallback(data);
              } else {
                errorCallback && errorCallback(data);
                that.Tips({
                  title: data.msg,
                });
              }
            }
          },
          fail: function (res) {
            uni.hideLoading();
            that.Tips({
              title: i18n.t(`Tải ảnh lên thất bại`),
            });
          },
        });
      },
    });
  },
  /*
   * Tải lên một ảnh, bản nén
   * @param object opt
   * @param callable successCallback Phương thức thực hiện khi thành công, data
   * @param callable errorCallback Phương thức thực hiện khi thất bại
   */
  uploadImageChange: function (
    opt,
    successCallback,
    errorCallback,
    sizeCallback
  ) {
    let that = this;
    if (typeof opt === "string") {
      let url = opt;
      opt = {};
      opt.url = url;
    }
    let count = opt.count || 1,
      sizeType = opt.sizeType || ["compressed"],
      sourceType = opt.sourceType || ["album", "camera"],
      is_load = opt.is_load || true,
      uploadUrl = opt.url || "",
      inputName = opt.name || "pics",
      fileType = opt.fileType || "image";
    uni.chooseImage({
      count: count, //Tổng số ảnh tối đa có thể chọn
      sizeType: sizeType, // Có thể chỉ định là ảnh gốc hay ảnh đã nén, mặc định có cả hai
      sourceType: sourceType, // Có thể chỉ định nguồn là album hay camera, mặc định có cả hai
      success: function (res) {
        //Đang bắt đầu tải lên...
        let imgSrc;
        uni.getImageInfo({
          src: res.tempFilePaths[0],
          success(ress) {
            uni.showLoading({
              title: i18n.t(`Đang tải ảnh lên`),
            });
            if (res.tempFiles[0].size <= 2097152) {
              uploadImg(ress.path);
              return;
            }
            // uploadImg(canvasPath.tempFilePath)
            let canvasWidth,
              canvasHeight,
              xs,
              maxWidth = 750;
            xs = ress.width / ress.height; // Tỷ lệ chiều rộng/cao
            if (ress.width > maxWidth) {
              canvasWidth = maxWidth; // Đây là chiều rộng giới hạn tối đa
              canvasHeight = maxWidth / xs;
            } else {
              canvasWidth = ress.width;
              canvasHeight = ress.height;
            }
            sizeCallback &&
              sizeCallback({
                w: canvasWidth,
                h: canvasHeight,
              });
            let canvas = uni.createCanvasContext("canvas");
            canvas.width = canvasWidth;
            canvas.height = canvasHeight;
            canvas.clearRect(0, 0, canvasWidth, canvasHeight);
            canvas.drawImage(ress.path, 0, 0, canvasWidth, canvasHeight);
            canvas.save();
            // drawImage của canvas ở đây là thuộc tính bất đồng bộ (async), có thể xảy ra trường hợp chưa vẽ xong đã thực thi draw, so thêm delay
            setTimeout((e) => {
              canvas.draw(true, () => {
                uni.canvasToTempFilePath({
                  canvasId: "canvas",
                  fileType: "JPEG",
                  destWidth: canvasWidth,
                  destHeight: canvasHeight,
                  quality: 0.7,
                  success: function (canvasPath) {
                    uploadImg(canvasPath.tempFilePath);
                  },
                });
              });
            }, 200);
          },
        });
      },
    });

    function uploadImg(filePath) {
      uni.uploadFile({
        url: HTTP_REQUEST_URL + "/api/" + uploadUrl,
        filePath,
        fileType: fileType,
        name: inputName,
        formData: {
          filename: inputName,
        },
        header: {
          // #ifdef MP
          "Content-Type": "multipart/form-data",
          // #endif
          [TOKENNAME]: "Bearer " + store.state.app.token,
        },
        success: function (res) {
          uni.hideLoading();
          if (res.statusCode == 403) {
            that.Tips({
              title: res.data,
            });
          } else {
            let data = res.data ? JSON.parse(res.data) : {};
            if (data.status == 200) {
              successCallback && successCallback(data);
            } else {
              errorCallback && errorCallback(data);
              that.Tips({
                title: data.msg,
              });
            }
          }
        },
        fail: function (res) {
          uni.hideLoading();
          that.Tips({
            title: i18n.t(`Tải ảnh lên thất bại`),
          });
        },
      });
    }
  },
  /**
   * Mini Program lấy và tải lên ảnh đại diện
   * @param uploadUrl Địa chỉ API tải lên
   * @param filePath Đường dẫn file tải lên
   * @param successCallback Callback success
   * @param errorCallback Callback err
   */
  uploadImgs(uploadUrl, filePath, successCallback, errorCallback) {
    let that = this;
    uni.uploadFile({
      url: HTTP_REQUEST_URL + "/api/" + uploadUrl,
      filePath: filePath,
      fileType: "image",
      name: "pics",
      formData: {
        filename: "pics",
      },
      header: {
        // #ifdef MP
        "Content-Type": "multipart/form-data",
        // #endif
        [TOKENNAME]: "Bearer " + store.state.app.token,
      },
      success: (res) => {
        uni.hideLoading();
        if (res.statusCode == 403) {
          that.Tips({
            title: res.data,
          });
        } else {
          let data = res.data ? JSON.parse(res.data) : {};
          if (data.status == 200) {
            successCallback && successCallback(data);
          } else {
            errorCallback && errorCallback(data);
            that.Tips({
              title: data.msg,
            });
          }
        }
      },
      fail: (err) => {
        uni.hideLoading();
        that.Tips({
          title: i18n.t(`Tải ảnh lên thất bại`),
        });
      },
    });
  },
  /**
   * So sánh thông tin phiên bản Mini Program
   * @param v1 Phiên bản hiện tại
   * @param v2 Phiên bản đem so sánh
   * @return boolen
   *
   */
  compareVersion(v1, v2) {
    v1 = v1.split(".");
    v2 = v2.split(".");
    const len = Math.max(v1.length, v2.length);

    while (v1.length < len) {
      v1.push("0");
    }
    while (v2.length < len) {
      v2.push("0");
    }

    for (let i = 0; i < len; i++) {
      const num1 = parseInt(v1[i]);
      const num2 = parseInt(v2[i]);

      if (num1 > num2) {
        return 1;
      } else if (num1 < num2) {
        return -1;
      }
    }

    return 0;
  },
  /*
   * Lấy thời gian hiện tại
   */
  getNowTime() {
    let today = new Date();
    let year = today.getFullYear(); // Lấy năm hiện tại
    let month = today.getMonth() + 1; // Lấy tháng hiện tại (chú ý: tháng tính từ 0, nên cần +1)
    let day = today.getDate(); // Lấy ngày hiện tại (ngày mấy)
    let hour = today.getHours(); // Lấy giờ hiện tại
    let minute = today.getMinutes(); // Lấy phút hiện tại
    let second = today.getSeconds(); // Lấy giây hiện tại

    // Định dạng và xuất thời gian hiện tại
    let nowTime =
      year + "/" + month + "/" + day + " " + hour + ":" + minute + ":" + second;
    return nowTime;
  },
  /**
   * Xử lý tham số được mang vào khi quét mã QR từ server
   * @param string param Tham số mang theo khi quét mã
   * @param string k Ký tự phân tách tổng thể, mặc định là: &
   * @param string p Ký tự phân tách từng cặp, mặc định là: =
   * @return object
   *
   */
  // #ifdef MP
  getUrlParams: function (param, k, p) {
    if (typeof param != "string") return {};
    k = k ? k : "&"; //Ký tự phân tách tham số tổng thể
    p = p ? p : "="; //Ký tự phân tách từng tham số
    var value = {};
    if (param.indexOf(k) !== -1) {
      param = param.split(k);
      for (var val in param) {
        if (param[val].indexOf(p) !== -1) {
          var item = param[val].split(p);
          value[item[0]] = item[1];
        }
      }
    } else if (param.indexOf(p) !== -1) {
      var item = param.split(p);
      value[item[0]] = item[1];
    } else {
      return param;
    }
    return value;
  },
  // #endif
  /*
   * Gộp mảng
   */
  SplitArray(list, sp) {
    if (typeof list != "object") return [];
    if (sp === undefined) sp = [];
    for (var i = 0; i < list.length; i++) {
      sp.push(list[i]);
    }
    return sp;
  },
  trim(backUrlCRshlcICwGdGY) {
    return String.prototype.trim.call(backUrlCRshlcICwGdGY);
  },
  $h: {
    //Hàm chia, dùng để lấy kết quả chia chính xác
    //Giải thích: kết quả phép chia của javascript có sai số, thể hiện rõ khi chia hai số thực (float). Hàm này trả về kết quả chia chính xác hơn.
    //Gọi: $h.Div(arg1,arg2)
    //Giá trị trả về: kết quả chính xác của arg1 chia cho arg2
    Div: function (arg1, arg2) {
      arg1 = parseFloat(arg1);
      arg2 = parseFloat(arg2);
      var t1 = 0,
        t2 = 0,
        r1,
        r2;
      try {
        t1 = arg1.toString().split(".")[1].length;
      } catch (e) {}
      try {
        t2 = arg2.toString().split(".")[1].length;
      } catch (e) {}
      r1 = Number(arg1.toString().replace(".", ""));
      r2 = Number(arg2.toString().replace(".", ""));
      return this.Mul(r1 / r2, Math.pow(10, t2 - t1));
    },
    //Hàm cộng, dùng để lấy kết quả cộng chính xác
    //Giải thích: kết quả phép cộng của javascript có sai số, thể hiện rõ khi cộng hai số thực (float). Hàm này trả về kết quả cộng chính xác hơn.
    //Gọi: $h.Add(arg1,arg2)
    //Giá trị trả về: kết quả chính xác của arg1 cộng arg2
    Add: function (arg1, arg2) {
      arg2 = parseFloat(arg2);
      var r1, r2, m;
      try {
        r1 = arg1.toString().split(".")[1].length;
      } catch (e) {
        r1 = 0;
      }
      try {
        r2 = arg2.toString().split(".")[1].length;
      } catch (e) {
        r2 = 0;
      }
      m = Math.pow(100, Math.max(r1, r2));
      return (this.Mul(arg1, m) + this.Mul(arg2, m)) / m;
    },
    //Hàm trừ, dùng để lấy kết quả trừ chính xác
    //Giải thích: kết quả phép cộng của javascript có sai số, thể hiện rõ khi cộng hai số thực (float). Hàm này trả về kết quả trừ chính xác hơn.
    //Gọi: $h.Sub(arg1,arg2)
    //Giá trị trả về: kết quả chính xác của arg1 trừ arg2
    Sub: function (arg1, arg2) {
      arg1 = parseFloat(arg1);
      arg2 = parseFloat(arg2);
      var r1, r2, m, n;
      try {
        r1 = arg1.toString().split(".")[1].length;
      } catch (e) {
        r1 = 0;
      }
      try {
        r2 = arg2.toString().split(".")[1].length;
      } catch (e) {
        r2 = 0;
      }
      m = Math.pow(10, Math.max(r1, r2));
      //Điều khiển động độ dài phần chính xác
      n = r1 >= r2 ? r1 : r2;
      return ((this.Mul(arg1, m) - this.Mul(arg2, m)) / m).toFixed(n);
    },
    //Hàm nhân, dùng để lấy kết quả nhân chính xác
    //Giải thích: kết quả phép nhân của javascript có sai số, thể hiện rõ khi nhân hai số thực (float). Hàm này trả về kết quả nhân chính xác hơn.
    //Gọi: $h.Mul(arg1,arg2)
    //Giá trị trả về: kết quả chính xác của arg1 nhân arg2
    Mul: function (arg1, arg2) {
      arg1 = parseFloat(arg1);
      arg2 = parseFloat(arg2);
      var m = 0,
        s1 = arg1.toString(),
        s2 = arg2.toString();
      try {
        m += s1.split(".")[1].length;
      } catch (e) {}
      try {
        m += s2.split(".")[1].length;
      } catch (e) {}
      return (
        (Number(s1.replace(".", "")) * Number(s2.replace(".", ""))) /
        Math.pow(10, m)
      );
    },
  },
  // Lấy vị trí địa lý;
  $L: {
    async getLocation() {
      // #ifdef APP-PLUS
      let status = await this.checkPermission();
      if (status !== 1) {
        return;
      }
      // #endif
      // #ifdef MP-WEIXIN || MP-TOUTIAO || MP-QQ
      let status = await this.getSetting();
      if (status === 2) {
        this.openSetting();
        return;
      }
      // #endif

      this.doGetLocation();
    },
    doGetLocation() {
      uni.getLocation({
        success: (res) => {
          uni.removeStorageSync("CACHE_LONGITUDE");
          uni.removeStorageSync("CACHE_LATITUDE");
          uni.setStorageSync("CACHE_LONGITUDE", res.longitude);
          uni.setStorageSync("CACHE_LATITUDE", res.latitude);
        },
        fail: (err) => {
          // #ifdef MP-BAIDU
          if (err.errCode === 202 || err.errCode === 10003) {
            // 202 máy giả lập, 10003 máy thật, user deny
            this.openSetting();
          }
          // #endif
          // #ifndef MP-BAIDU
          if (err.errMsg.indexOf("auth deny") >= 0) {
            uni.showToast({
              title: i18n.t(`Quyền truy cập vị trí bị từ chối`),
            });
          } else {
            uni.showToast({
              title: err.errMsg,
            });
          }
          // #endif
        },
      });
    },
    getSetting: function () {
      return new Promise((resolve, reject) => {
        uni.getSetting({
          success: (res) => {
            if (res.authSetting["scope.userLocation"] === undefined) {
              resolve(0);
              return;
            }
            if (res.authSetting["scope.userLocation"]) {
              resolve(1);
            } else {
              resolve(2);
            }
          },
        });
      });
    },
    openSetting: function () {
      uni.openSetting({
        success: (res) => {
          if (res.authSetting && res.authSetting["scope.userLocation"]) {
            this.doGetLocation();
          }
        },
        fail: (err) => {},
      });
    },
    async checkPermission() {
      let status = permision.isIOS
        ? await permision.requestIOS("location")
        : await permision.requestAndroid(
            "android.permission.ACCESS_FINE_LOCATION"
          );

      if (status === null || status === 1) {
        status = 1;
      } else if (status === 2) {
        uni.showModal({
          content: i18n.t(`Định vị hệ thống đã tắt`),
          confirmText: i18n.t(`Xác nhận`),
          showCancel: false,
          success: function (res) {},
        });
      } else if (status.code) {
        uni.showModal({
          content: status.message,
        });
      } else {
        uni.showModal({
          content: i18n.t(`Cần quyền truy cập vị trí`),
          confirmText: i18n.t(`Xác nhận`),
          success: function (res) {
            if (res.confirm) {
              permision.gotoAppSetting();
            }
          },
        });
      }
      return status;
    },
  },
  /**
   * Hàm đóng gói đường dẫn chuyển trang
   * @param url Đường dẫn chuyển hướng
   */
  JumpPath: function (url) {
    let arr = url.split("@APPID=");
    if (arr.length > 1) {
      //#ifdef MP
      uni.navigateToMiniProgram({
        appId: arr[arr.length - 1], // Đây là appid dịch vụ thanh toán hóa đơn sinh hoạt
        path: arr[0], // Đây là đường dẫn trang chủ thanh toán hóa đơn sinh hoạt
        envVersion: "release",
        success: (res) => {
          console.log("Mở thành công", res);
        },
        fail: (err) => {},
      });
      //#endif
      //#ifndef MP
      this.Tips({
        title: "Bản h5 và app không hỗ trợ chuyển đến Mini Program bên ngoài",
      });
      //#endif
    } else {
      if (url.indexOf("http") != -1) {
        uni.navigateTo({
          url: `/pages/annex/web_view/index?url=${url}`,
        });
      } else {
        if (
          [
            "/pages/goods_cate/goods_cate",
            "/pages/order_addcart/order_addcart",
            "/pages/user/index",
            "/pages/index/index",
          ].indexOf(url) == -1
        ) {
          uni.navigateTo({
            url,
          });
        } else {
          uni.switchTab({
            url,
          });
        }
      }
    }
  },
  // Tính chiều cao thanh điều hướng tùy chỉnh ở đầu trang;
  getWXStatusHeight() {
    // Lấy khoảng cách phía trên
    const barTop = uni.getWindowInfo().statusBarHeight;
    // #ifdef MP
    // Lấy thông tin vị trí nút capsule (capsule button)
    const menuButtonInfo = wx.getMenuButtonBoundingClientRect() || 0;
    // Lấy chiều cao thanh điều hướng
    const barHeight = menuButtonInfo.height + (menuButtonInfo.top - barTop) * 2;
    let barWidth = menuButtonInfo.width;
    // #endif
    // #ifndef MP
    // Lấy chiều cao thanh điều hướng
    const barHeight = parseInt(barTop) + 10;
    let barWidth = "100%";
    // #endif
    return {
      // #ifdef MP
      menuButtonInfo,
      // #endif
      barHeight,
      barTop,
      barWidth,
    };
  },
};
