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

const pre = 'agent_';
const meta = {
  auth: true,
};
export default {
  path: `${routePre}/agent`,
  name: 'agent',
  header: 'agent',
  redirect: {
    name: `${pre}agentManage`,
  },
  meta,
  component: LayoutMain,
  children: [
    {
      path: 'agent_manage/index',
      name: `${pre}agentManage`,
      meta: {
        auth: ['agent-agent-manage'],
        title: 'Quản lý cộng tác viên',
      },
      component: () => import('@/pages/agent/agentManage'),
    },
    {
      path: 'spread/apply',
      name: `${pre}agentManage`,
      meta: {
        auth: ['admin-agent-spread-apply'],
        title: 'Đơn đăng ký cộng tác viên',
      },
      component: () => import('@/pages/agent/spread/apply'),
    },
  ],
};
