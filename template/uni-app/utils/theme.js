import { getThemeInfo } from "@/api/api.js";

/**
 * Xử lý màu sắc
 */
export function hexToRgba(hex, alpha) {
  let sColor = hex.toLowerCase();
  const reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/;
  if (sColor && reg.test(sColor)) {
    if (sColor.length === 4) {
      let sColorNew = "#";
      for (let i = 1; i < 4; i += 1) {
        sColorNew += sColor.slice(i, i + 1).concat(sColor.slice(i, i + 1));
      }
      sColor = sColorNew;
    }
    //Xử lý giá trị màu 6 chữ số
    let sColorChange = [];
    for (let i = 1; i < 7; i += 2) {
      sColorChange.push(parseInt("0x" + sColor.slice(i, i + 2)));
    }
    return "rgba(" + sColorChange.join(",") + "," + alpha + ")";
  }
  return sColor;
}

/**
 * Đặt màu chủ đề
 * @param {Object} data Dữ liệu chủ đề
 */
export function setThemeColor(data) {
  let selectedTheme;
  // Xử lý dữ liệu màu chủ đề tùy chỉnh
  if (data.theme_color) {
    let themeColor = data.theme_color;
    let gradientColor = data.gradient_color;
    let subColor = data.sub_color;
    let lightColor = data.light_color;
    selectedTheme = `
      --view-theme: ${hexToRgba(themeColor, 1)};
      --view-theme-16: ${themeColor};
      --view-priceColor: ${themeColor};
      --view-minorColor: ${subColor};
      --view-minorColorT: ${lightColor};
      --view-bntColor: ${subColor};
      --view-op-ten: ${hexToRgba(themeColor, 0.1)};
      --view-main-start: ${gradientColor};
      --view-main-over: ${themeColor};
      --view-op-point-four: ${hexToRgba(themeColor, 0.04)};
      --view-op-point-eight: ${hexToRgba(themeColor, 0.8)};
      --view-linear: linear-gradient(180deg, ${hexToRgba(
        themeColor,
        0.2,
      )} 0%, rgba(255,255,255,0) 100%);
      --view-gradient: ${gradientColor};
    `;
    uni.setStorageSync("viewColor", selectedTheme);
    uni.$emit("ok", selectedTheme);
  }
}

/**
 * Lấy và áp dụng chủ đề
 * @param {Number|String} themeId ID chủ đề
 */
export function applyTheme(themeId) {
  let data = {};
  if (themeId) data.theme_id = themeId;
  return getThemeInfo("theme", data).then((res) => {
    uni.setStorageSync("is_diy", 1);
    uni.$emit("is_diy", 1);
    setThemeColor(res.data);
    return res.data;
  });
}
