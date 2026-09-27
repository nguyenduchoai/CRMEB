// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import axios from 'axios';
import { Message } from 'element-ui';
import { getCookies, removeCookies } from '@/libs/util';
import Setting from '@/setting';
import router from '@/router';
const service = axios.create({
  baseURL: Setting.apiBaseURL,
  timeout: 100000, // Thời gian timeout của request
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

// response interceptor
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
      case 401:
        localStorage.clear();
        removeCookies('token');
        removeCookies('expires_time');
        removeCookies('uuid');
        router.replace({ name: 'login' }).catch(() => {});
        return Promise.reject({ msg: 'Chưa đăng nhập' });
      case 402:
        removeCookies('kefuInfo');
        removeCookies('kefu_token');
        removeCookies('kefu_expires_time');
        removeCookies('kefu_uuid');
        router.replace({ path: '/kefu' }).catch(() => {});
        return Promise.reject({ msg: 'Chưa đăng nhập' });
      case 403:
        router.replace({ name: 'system_opendir_login' }).catch(() => {});
        return Promise.reject({ msg: 'Không có quyền' });
      default:
        return Promise.reject(obj || { msg: 'Lỗi không xác định' });
    }
  },
  (error) => {
    Message.error(error.msg);
    return Promise.reject(error);
  },
);

export default service;
