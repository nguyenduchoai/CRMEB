import CryptoJS from 'crypto-js';
/**
 * @word Nội dung cần mã hóa
 * @keyWord String  Từ khóa server trả về ngẫu nhiên
 *  */
export function aesEncrypt(word, keyWord = 'XwKsGlMcdPMEhR1B') {
  var key = CryptoJS.enc.Utf8.parse(keyWord);
  var srcs = CryptoJS.enc.Utf8.parse(word);
  var encrypted = CryptoJS.AES.encrypt(srcs, key, { mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7 });
  return encrypted.toString();
}

/**
 * @word Nội dung cần mã hóa bằng hash256
 * @keyWord String  Từ khóa server trả về ngẫu nhiên
 *  */
export function aesEncryptHash(word, keyWord = 'XwKsGlMcdPMEhR1B') {
  var key = CryptoJS.enc.Utf8.parse(keyWord);
  var srcs = CryptoJS.enc.Utf8.parse(word);
  var encrypted = CryptoJS.HmacSHA256(srcs, key);
  return CryptoJS.enc.Hex.stringify(encrypted);
}
