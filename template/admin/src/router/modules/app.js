// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import LayoutMain from '@/layout';
import setting from '@/setting';
import { active } from 'sortablejs';
let routePre = setting.routePre;

const pre = 'app_';

export default {
  path: routePre + '/app',
  name: 'app',
  header: 'app',
  redirect: {
    name: `${pre}wechatMenus`,
  },
  meta: {
    auth: ['admin-app'],
  },
  component: LayoutMain,
  children: [
    {
      path: 'wechat/setting/menus/index',
      name: `${pre}wechatMenus`,
      meta: {
        auth: ['application-wechat-menus'],
        title: 'Menu WeChat',
      },
      component: () => import('@/pages/app/wechat/menus/index'),
    },
    {
      path: 'wechat/wechat_user/user/tag',
      name: `${pre}tag`,
      meta: {
        auth: ['wechat-wechat-user-tag'],
        title: 'Nhãn người dùng',
      },
      component: () => import('@/pages/app/wechat/user/tag'),
    },
    {
      path: 'wechat/wechat_user/user/group',
      name: `${pre}group`,
      meta: {
        auth: ['wechat-wechat-user-group'],
        title: 'Nhóm người dùng',
      },
      component: () => import('@/pages/app/wechat/user/tag'),
    },
    {
      path: 'wechat/wechat_user/user/message',
      name: `${pre}message`,
      meta: {
        auth: ['wechat-wechat-user-message'],
        title: 'Nhật ký hành vi người dùng',
      },
      component: () => import('@/pages/app/wechat/user/message'),
    },
    {
      path: 'wechat/news_category/index',
      name: `${pre}newsCategoryIndex`,
      meta: {
        auth: ['wechat-wechat-news-category-index'],
        title: 'Quản lý tin bài',
      },
      component: () => import('@/pages/app/wechat/newsCategory/index'),
    },
    {
      path: 'wechat/news_category/save/:id?',
      name: `${pre}newsCategorySave`,
      meta: {
        auth: ['wechat-wechat-news-category-save'],
        title: 'Thêm tin bài',
        activeMenu: routePre + '/app/wechat/news_category/index',
      },
      component: () => import('@/pages/app/wechat/newsCategory/save'),
    },
    {
      path: 'wechat/reply/follow/:key',
      name: `${pre}fllow`,
      meta: {
        auth: ['wechat-wechat-reply-subscribe'],
        title: 'Trả lời khi theo dõi WeChat',
      },
      component: () => import('@/pages/app/wechat/reply/follow'),
    },
    {
      path: 'wechat/reply/keyword',
      name: `${pre}keyword`,
      meta: {
        auth: ['wechat-wechat-reply-keyword'],
        title: 'Trả lời theo từ khóa',
      },
      component: () => import('@/pages/app/wechat/reply/keyword'),
    },
    {
      path: 'wechat/reply/keyword/save/:id?',
      name: `${pre}keywordAdd`,
      meta: {
        auth: ['wechat-wechat-reply-save'],
        title: 'Thêm từ khóa',
        activeMenu: routePre + '/app/wechat/reply/keyword',
      },
      component: () => import('@/pages/app/wechat/reply/follow'),
    },
    {
      path: 'wechat/reply/index/:key',
      name: `${pre}replyIndex`,
      meta: {
        auth: ['wechat-wechat-reply-default'],
        title: 'Trả lời từ khóa không khớp',
      },
      component: () => import('@/pages/app/wechat/reply/follow'),
    },
    {
      path: 'routine/download',
      name: `${pre}routineTemplate`,
      meta: {
        auth: ['routine-download'],
        title: 'Tải xuống Mini Program',
      },
      component: () => import('@/pages/app/routine/download/index'),
    },
    {
      path: 'routine/ci_upload',
      name: `${pre}routineCIUpload`,
      meta: {
        auth: ['routine-ci-upload'],
        title: 'Tải lên Mini Program',
      },
      component: () => import('@/pages/app/routine/ciUpload/index'),
    },
    {
      path: 'routine/link',
      name: `${pre}routineLink`,
      meta: {
        auth: ['routine-link'],
        title: 'Liên kết Mini Program',
      },
      component: () => import('@/pages/app/routine/link/index'),
    },
    {
      path: 'app/version',
      name: `${pre}version`,
      meta: {
        auth: ['admin-app-version'],
        title: 'Quản lý phiên bản APP',
      },
      component: () => import('@/pages/app/version/index'),
    },
    {
      path: 'app/agreement',
      name: `${pre}agreement `,
      meta: {
        auth: ['admin-app-agreement'],
        title: 'Chính sách bảo mật',
      },
      component: () => import('@/pages/app/app/index'),
    },
  ],
};
