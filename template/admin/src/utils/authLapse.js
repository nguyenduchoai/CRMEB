// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

export function authLapse(data) {
  return new Promise((resolve, reject) => {
    const h = this.$createElement;
    this.$notify.warning({
      title: data.title,
      duration: 3000,
      message: h('div', [
        h(
          'a',
          {
            attrs: {
              href: 'http://www.crmeb.com',
              target: '_blank',
            },
          },
          data.info,
        ),
      ]),
    });
  });
}
