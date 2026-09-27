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
let routePre = setting.routePre;

const pre = 'cms_';

export default {
  path: routePre + '/cms',
  name: 'cms',
  header: 'cms',
  redirect: {
    name: `${pre}article`,
  },
  component: LayoutMain,
  children: [
    {
      path: 'article/index/:id?',
      name: `${pre}article`,
      meta: {
        auth: ['cms-article-index'],
        title: 'Quản lý bài viết',
        keepAlive: true,
      },
      component: () => import('@/pages/cms/article/index'),
    },
    {
      path: 'article_category/index',
      name: `${pre}articleCategory`,
      meta: {
        auth: ['cms-article-category'],
        title: 'Danh mục bài viết',
      },
      component: () => import('@/pages/cms/articleCategory/index'),
    },
    {
      path: 'article/add_article/:id?',
      name: `${pre}addArticle`,
      meta: {
        auth: ['cms-article-creat'],
        title: 'Thêm bài viết',
        activeMenu: routePre + '/cms/article/index',
      },
      component: () => import('@/pages/cms/addArticle/index'),
    },
  ],
};
