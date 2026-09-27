// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import { tableDelApi } from '@/api/common';
export function modalSure(delfromData) {
  return new Promise((resolve, reject) => {
    let content = `<p>Bạn có chắc chắn muốn ${delfromData.title} không?</p>`;
    if (!delfromData.info) {
      delfromData.info = '';
    }
    const h = this.$createElement;
    this.$msgbox({
      title: 'Thông báo',
      message: h('p', null, [h('div', null, `Bạn có chắc chắn muốn ${delfromData.title} không?`), h('div', null, `${delfromData.info}`)]),
      showCancelButton: true,
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Xác nhận',
      iconClass: 'el-icon-warning',
      confirmButtonClass: 'btn-custom-cancel',
    })
      .then(() => {
        if (delfromData.success) {
          delfromData.success
            .then(async (res) => {
              resolve(res);
            })
            .catch((res) => {
              reject(res);
            });
        } else {
          tableDelApi(delfromData)
            .then(async (res) => {
              resolve(res);
            })
            .catch((res) => {
              reject(res);
            });
        }
      })
      .catch(() => {});
  });
}

export function HandlePrice(num, type) {
  let obj = [];
  if (typeof num == 'number') {
    obj = num.toString().split('.');
  } else {
    obj = num.split('.');
  }
  if (type) {
    if (obj.length && obj[1]) {
      return '.' + obj[1];
    } else {
      return '';
    }
  } else {
    return obj[0];
  }
}
