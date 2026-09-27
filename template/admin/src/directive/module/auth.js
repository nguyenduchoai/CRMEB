// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
/**
 * @description Directive kiểm tra quyền
 * Khi quyền được truyền vào mà người dùng hiện tại không có, thành phần đó sẽ bị gỡ bỏ
 * Ví dụ: <Tag v-auth="['admin']">text</Tag>
 * */
import store from '@/store';
import { includeArray } from '@/libs/auth';

export default {
  inserted(el, binding, vnode) {
    const { value } = binding;
    const access = store.state.userInfo.access;
    if (value && value instanceof Array && value.length && access && access.length) {
      const isPermission = includeArray(value, access);
      if (!isPermission) {
        // el.parentNode && el.parentNode.removeChild(el);
      }
    }
  },
};
