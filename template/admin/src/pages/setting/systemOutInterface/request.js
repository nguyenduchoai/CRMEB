import axios from 'axios';
import Setting from '@/setting';
import { getCookies, removeCookies } from '@/libs/util';

const service = axios.create({
  baseURL: Setting.apiBaseURL,
  timeout: 10000, // Thời gian timeout của request
});
axios.defaults.withCredentials = true; // Kèm cookie

// Interceptor cho request
service.interceptors.request.use(
  (config) => {
    if (config.kefu) {
      let baseUrl = Setting.apiBaseURL.replace(/adminapi/, 'kefuapi');
      config.baseURL = baseUrl;
    } else {
      config.baseURL = Setting.apiBaseURL;
    }
    if (config.file) {
      config.headers['Content-Type'] = 'multipart/form-data';
    }
    try {
      if (config.headerItem) {
        for (let i in config.headerItem) {
          config.headers[i] = config.headerItem[i];
        }
      }
    } catch (error) {
      console.log(error);
    }

    const token = getCookies('token');
    const kefuToken = getCookies('kefu_token');
    if (token || kefuToken) {
      config.headers['Authori-zation'] = config.kefu ? 'Bearer ' + kefuToken : 'Bearer ' + token;
    }
    return config;
  },
  (error) => {
    // do something with request error
    return Promise.reject(error);
  },
);
service.interceptors.response.use(
  (response) => {
    let obj = {};
    if (!!response.data) {
      if (typeof response.data == 'string') {
        obj = JSON.parse(response.data);
      } else {
        obj = response.data;
      }
    }
    let status = response.data ? obj.status : 0;
    // let status = response.data ? response.data.status : 0;
    const code = status;
    switch (code) {
      case 200:
        return obj;
      default:
        return Promise.reject(obj || { msg: 'Lỗi không xác định' });
    }
  },
  (error) => {
    return Promise.reject(error);
  },
);
export default service;

// function sendRequest(url, method, params, header) {
//   const instance = axios.create({
//     baseURL: Setting.apiBaseURL, // Tên miền gốc của request
//     timeout: 1000, // Thời gian timeout của request
//     headers: {
//       'X-Custom-Header': header, // Thông tin header tùy chỉnh
//     },
//   });

//   if (method === 'GET') {
//     instance
//       .get(url, { params: params })
//       .then((response) => {
//         // Xử lý dữ liệu phản hồi
//       })
//       .catch((error) => {
//         // Xử lý lỗi
//       });
//   } else if (method === 'POST') {
//     instance
//       .post(url, params, { headers: header })
//       .then((response) => {
//         // Xử lý dữ liệu phản hồi
//       })
//       .catch((error) => {
//         // Xử lý lỗi
//       });
//   } else if (method === 'PUT') {
//     instance
//       .put(url, params, { headers: header })
//       .then((response) => {
//         // Xử lý dữ liệu phản hồi
//       })
//       .catch((error) => {
//         // Xử lý lỗi
//       });
//   } else if (method === 'DELETE') {
//     instance
//       .delete(url, { headers: header })
//       .then((response) => {
//         // Xử lý dữ liệu phản hồi
//       })
//       .catch((error) => {
//         // Xử lý lỗi
//       });
//   }

//   return instance;
// }
