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

const pre = 'crud_';

export default {
  path: routePre + '/crud',
  name: 'crud',
  header: 'crud',
  redirect: {
    name: `${pre}crud`,
  },
  meta: {
    auth: true,
  },
  component: LayoutMain,
  children: [
    {
      path: ':table_name',
      name: `${pre}crud`,
      meta: {
        auth: true,
        title: 'Thêm/xóa/sửa/tra cứu',
      },
      component: () => import('@/pages/crud/index'),
    },
  ],
};
