import { Message } from 'element-ui';

/**
 * Hàm chuyển đổi màu
 * @method hexToRgb hex Chuyển màu sang màu rgb
 * @method rgbToHex rgb Chuyển màu sang màu Hex
 * @method getDarkColor Làm đậm giá trị màu
 * @method getLightColor Làm nhạt giá trị màu
 */
export function useChangeColor() {
  // str chuỗi giá trị màu
  const hexToRgb = (str) => {
    let hexs = '';
    let reg = /^#?[0-9A-Fa-f]{6}$/;
    if (!reg.test(str)) {
      Message.warning('Mã hex không hợp lệ');
      return '';
    }
    str = str.replace('#', '');
    hexs = str.match(/../g);
    for (let i = 0; i < 3; i++) hexs[i] = parseInt(hexs[i], 16);
    return hexs;
  };
  // r đại diện màu đỏ | g đại diện màu xanh lá | b đại diện màu xanh dương
  const rgbToHex = (r, g, b) => {
    let reg = /^\d{1,3}$/;
    if (!reg.test(r) || !reg.test(g) || !reg.test(b)) {
      Message.warning('Giá trị màu rgb không hợp lệ');
      return '';
    }
    let hexs = [r.toString(16), g.toString(16), b.toString(16)];
    for (let i = 0; i < 3; i++) if (hexs[i].length == 1) hexs[i] = `0${hexs[i]}`;
    return `#${hexs.join('')}`;
  };
  // color chuỗi giá trị màu | level mức độ làm nhạt, giới hạn từ 0-1
  const getDarkColor = (color, level) => {
    let reg = /^#?[0-9A-Fa-f]{6}$/;
    if (!reg.test(color)) {
      Message.warning('Giá trị màu hex không hợp lệ');
      return '';
    }
    let rgb = useChangeColor().hexToRgb(color);
    for (let i = 0; i < 3; i++) rgb[i] = Math.floor(rgb[i] * (1 - level));
    return useChangeColor().rgbToHex(rgb[0], rgb[1], rgb[2]);
  };
  // color chuỗi giá trị màu | level mức độ làm đậm, giới hạn từ 0-1
  const getLightColor = (color, level) => {
    let reg = /^#?[0-9A-Fa-f]{6}$/;
    if (!reg.test(color)) {
      Message.warning('Giá trị màu hex không hợp lệ');
      return '';
    }
    let rgb = useChangeColor().hexToRgb(color);
    for (let i = 0; i < 3; i++) rgb[i] = Math.floor((255 - rgb[i]) * level + rgb[i]);
    return useChangeColor().rgbToHex(rgb[0], rgb[1], rgb[2]);
  };
  return {
    hexToRgb,
    rgbToHex,
    getDarkColor,
    getLightColor,
  };
}
