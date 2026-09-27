/*
 * @Author: From-wh from-wh@hotmail.com
 * @Date: 2023-03-09 18:02:23
 * @FilePath: /admin/src/i18n/index.js
 * @Description:
 */
import Vue from 'vue';
import VueI18n from 'vue-i18n';
import viLocale from 'element-ui/lib/locale/lang/vi';
import zhcnLocale from 'element-ui/lib/locale/lang/zh-CN';
import enLocale from 'element-ui/lib/locale/lang/en';
import zhtwLocale from 'element-ui/lib/locale/lang/zh-TW';
import store from '@/store/index.js';

import nextVi from '@/i18n/lang/vi.js';
import nextZhcn from '@/i18n/lang/zh-cn.js';
import nextEn from '@/i18n/lang/en.js';
import nextZhtw from '@/i18n/lang/zh-tw.js';

import pagesHomeVi from '@/i18n/pages/home/vi.js';
import pagesHomeZhcn from '@/i18n/pages/home/zh-cn.js';
import pagesHomeEn from '@/i18n/pages/home/en.js';
import pagesHomeZhtw from '@/i18n/pages/home/zh-tw.js';
import pagesLoginVi from '@/i18n/pages/login/vi.js';
import pagesLoginZhcn from '@/i18n/pages/login/zh-cn.js';
import pagesLoginEn from '@/i18n/pages/login/en.js';
import pagesLoginZhtw from '@/i18n/pages/login/zh-tw.js';
// Sử dụng plugin
Vue.use(VueI18n);

// Định nghĩa nội dung đa ngôn ngữ (i18n)
/**
 * Giải thích:
 * Các file js trong /src/i18n/lang là nội dung đa ngôn ngữ của framework
 * Các file js trong /src/i18n/pages là nội dung đa ngôn ngữ của từng màn hình
 */
const messages = {
  vi: {
    ...viLocale,
    message: {
      ...nextVi,
      ...pagesHomeVi,
      ...pagesLoginVi,
    },
  },
  'zh-cn': {
    ...zhcnLocale,
    message: {
      ...nextZhcn,
      ...pagesHomeZhcn,
      ...pagesLoginZhcn,
    },
  },
  en: {
    ...enLocale,
    message: {
      ...nextEn,
      ...pagesHomeEn,
      ...pagesLoginEn,
    },
  },
  'zh-tw': {
    ...zhtwLocale,
    message: {
      ...nextZhtw,
      ...pagesHomeZhtw,
      ...pagesLoginZhtw,
    },
  },
};

// Export nội dung đa ngôn ngữ
export const i18n = new VueI18n({
  locale: store.state.themeConfig.themeConfig.globalI18n,
  fallbackLocale: 'vi',
  messages,
  silentTranslationWarn: true, // Bỏ cảnh báo đa ngôn ngữ (i18n)
});
