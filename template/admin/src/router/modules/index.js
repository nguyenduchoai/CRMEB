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

const meta = {
  auth: true,
};

const pre = 'home_';

export default {
  path: routePre + '/home',
  name: 'home',
  header: 'home',
  redirect: {
    name: `${pre}index`,
  },
  meta,
  component: LayoutMain,
  children: [
    {
      path: routePre + '/index',
      name: `${pre}index`,
      header: 'home',
      meta: {
        auth: ['admin-index-index'],
        title: 'Trang chủ',
        isAffix: false,
      },
      component: () => import('@/pages/index/index'),
    },
  ],
};
