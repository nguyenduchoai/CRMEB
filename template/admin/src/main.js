// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

// Vue core
import Vue from 'vue';
import App from './App';
import router from './router';
import store from './store';
import { i18n } from '@/i18n/index.js';

// Cấu hình và công cụ
import config from '@/config';
import settings from '@/setting';
import * as tools from '@/libs/tools';
import Auth from '@/libs/wechat';
import dialog from '@/libs/dialog';
import timeOptions from '@/libs/timeOptions';
import scroll from '@/libs/loading';

// Framework UI
import Element from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

// Thành phần và directive tùy chỉnh
import importDirective from '@/directive';
import { directive as clickOutside } from 'v-click-outside-x';
import installPlugin from '@/plugin';
import Pagination from '@/components/Pagination';
import pagesHeader from '@/components/pagesHeader';
import common_wrapper from '@/components/mobilePage/common_wrapper.vue';
import imgModal from './components/uploadPictures/model';
import videoModal from './components/uploadVideo2/model';

// Thư viện bên thứ ba
import moment from 'moment';
import TreeTable from 'tree-table-vue';
import VOrgTree from 'v-org-tree';
import 'xe-utils';
import VxeTable from 'vxe-table';
import VxeUIAll, { setI18n as setVxeI18n, setLanguage as setVxeLanguage } from 'vxe-pc-ui';
import vxeViVN from 'vxe-pc-ui/lib/language/vi-VN';
import VueAwesomeSwiper from 'vue-awesome-swiper';
import VueLazyload from 'vue-lazyload';
import Viewer from 'v-viewer';
import VueDND from 'awe-dnd';
import formCreate from '@form-create/element-ui';
import VueCodeMirror from 'vue-codemirror';
import schema from 'async-validator';
import VueTreeList from 'vue-tree-list';
import vuescroll from 'vuescroll';
import VueClipboard from 'vue-clipboard2';

// Hàm tiện ích (utility)
import modalForm from '@/utils/modalForm';
import exportExcel from '@/utils/newToExcel.js';
import videoCloud from '@/utils/videoCloud';
import { modalSure, HandlePrice } from '@/utils/public';
import { authLapse } from '@/utils/authLapse';

// File style
import './assets/fonts/font.css';
import '@/theme/index.scss';
import './assets/iconfontYI/iconfontYI.css';
import './plugin/emoji-awesome/css/google.min.css';
import 'v-org-tree/dist/v-org-tree.css';
import './styles/index.scss';
import './styles/font/iconfont.js';
import 'swiper/css/swiper.css';
import 'viewerjs/dist/viewer.css';
import 'codemirror/lib/codemirror.css';
import 'vxe-table/lib/style.css';
import 'vxe-table/lib/index.css';
import 'vxe-pc-ui/es/style.css';
import 'vue-happy-scroll/docs/happy-scroll.css';

// Filter toàn cục
import * as filters from './filters';

// Event bus toàn cục
Vue.prototype.bus = new Vue();

// Đăng ký thành phần toàn cục
Vue.component('Pagination', Pagination);
Vue.component('pagesHeader', pagesHeader);
Vue.component('common_wrapper', common_wrapper);

// Cấu hình thư viện bên thứ ba
moment.locale('zh-cn');
Vue.prototype.$moment = moment;

VueClipboard.config.copyText = true;

// Đăng ký plugin
Vue.use(Element, { i18n: (key, value) => i18n.t(key, value), size: 'small' });
Vue.use(formCreate);
Vue.use(VueCodeMirror);
Vue.use(VueDND);
Vue.use(TreeTable);
Vue.use(VOrgTree);
Vue.use(VueAwesomeSwiper);
// vxe-table/vxe-pc-ui mặc định dùng tiếng Trung
setVxeI18n('vi-VN', vxeViVN);
setVxeLanguage('vi-VN');
Vue.use(VxeUIAll);
Vue.use(VxeTable);
Vue.use(vuescroll);
Vue.use(imgModal);
Vue.use(videoModal);
Vue.use(VueClipboard);
Vue.use(VueTreeList);

// Cấu hình lazy load
Vue.use(VueLazyload, {
  preLoad: 1.3,
  error: require('./assets/images/no.png'),
  loading: require('./assets/images/moren.jpg'),
  attempt: 1,
  listenEvents: ['scroll', 'wheel', 'mousewheel', 'resize', 'animationend', 'transitionend', 'touchmove'],
});

// Cấu hình trình xem ảnh
Vue.use(Viewer, {
  defaultOptions: {
    zIndex: 9999,
  },
});

// Tùy chỉnh Element Message
// const messages = ['success', 'warning', 'info', 'error'];
// messages.forEach((type) => {
//   Element.Message[type] = (options) => {
//     if (typeof options === 'string') {
//       options = {
//         message: options,
//       };
//       // Cấu hình mặc định
//       options.duration = 2000;
//       options.showClose = false;
//     }
//     console.log(options);
//     // options.type = type || 'info';
//     return Element.Message(options);
//   };
// });

/**
 * @description Đăng ký plugin tích hợp sẵn của admin
 */
installPlugin(Vue);

/**
 * @description Tắt thông báo ở môi trường production
 */
Vue.config.productionTip = false;

/**
 * @description Đăng ký toàn cục cấu hình ứng dụng
 */
window.Promise = Promise;
Vue.prototype.$config = config;
Vue.prototype.$routeProStr = settings.routePre;
Vue.prototype.$modalForm = modalForm;
Vue.prototype.$modalSure = modalSure;
Vue.prototype.$HandlePrice = HandlePrice;
Vue.prototype.$exportExcel = exportExcel;
Vue.prototype.$videoCloud = videoCloud;
Vue.prototype.$authLapse = authLapse;
Vue.prototype.$wechat = Auth;
Vue.prototype.$dialog = dialog;
Vue.prototype.$timeOptions = timeOptions;
Vue.prototype.$scroll = scroll;
Vue.prototype.$tools = tools;
Vue.prototype.$validator = function (rule) {
  return new schema(rule);
};

/**
 * Đăng ký directive
 */
importDirective(Vue);
Vue.directive('clickOutside', clickOutside);

// Đăng ký filter toàn cục
Object.keys(filters).forEach((key) => {
  Vue.filter(key, filters[key]);
});

// Đã gỡ script thống kê của bên thứ ba (cdn.oss.9gt.net) vốn được nhúng vào mọi trang.
// Hệ thống không cần script này để chạy; gỡ để không gửi dữ liệu người dùng ra ngoài (Luật BVDLCN 2025).

// Thêm thống kê crmeb chat
fetch(`${settings.apiBaseURL}/custom_admin_js`)
  .then((response) => response.text())
  .then((content) => {
    // Thử phân tích xem có phải là HTML không (có thẻ <script>)
    const isHTML = content.trim().startsWith('<script');

    let externalScripts = [];
    let inlineScripts = [];

    if (isHTML) {
      // Trường hợp 1: có thẻ <script>, dùng DOMParser để phân tích
      const parser = new DOMParser();
      const doc = parser.parseFromString(content, 'text/html');
      const scripts = doc.querySelectorAll('script');

      externalScripts = Array.from(scripts).filter((script) => script.src);
      inlineScripts = Array.from(scripts).filter((script) => !script.src);
    } else {
      // Trường hợp 2: không có thẻ <script>, xử lý trực tiếp như script nội tuyến (inline)
      inlineScripts = [
        {
          textContent: content,
        },
      ];
    }

    // 1. Tải tất cả script bên ngoài trước (nếu có)
    const loadExternalScripts = externalScripts.map((script) => {
      return new Promise((resolve, reject) => {
        const newScript = document.createElement('script');
        newScript.src = script.src;
        newScript.onload = resolve;
        newScript.onerror = reject;
        document.body.appendChild(newScript);
      });
    });

    // 2. Sau khi script bên ngoài tải xong, mới thực hiện script nội tuyến (inline)
    Promise.all(loadExternalScripts)
      .then(() => {
        inlineScripts.forEach((script) => {
          const newScript = document.createElement('script');
          newScript.textContent = script.textContent;
          document.body.appendChild(newScript);
        });
      })
      .catch((error) => console.error('Failed to load external scripts:', error));
  })
  .catch((error) => console.error('Error fetching script:', error));

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  i18n,
  store,
  render: (h) => h(App),
});
