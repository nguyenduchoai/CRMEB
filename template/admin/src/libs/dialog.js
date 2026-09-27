// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

import {
  Confirm as confirm,
  Alert as alert,
  Toast as toast,
  Notify as notify,
  Loading as loading,
} from 'vue-ydui/dist/lib.rem/dialog';

const dialog = {
  confirm,
  alert,
  toast,
  notify,
  loading,
};

const icons = { error: 'Thao tác thất bại', success: 'Thao tác thành công' };
Object.keys(icons).reduce((dialog, key) => {
  dialog[key] = (mes, obj = {}) => {
    return new Promise(function (resolve) {
      toast({
        mes: mes || icons[key],
        timeout: 1000,
        icon: key,
        callback: () => {
          resolve();
        },
        ...obj,
      });
    });
  };
  return dialog;
}, dialog);

dialog.message = (mes = 'Thao tác thất bại', obj = {}) => {
  return new Promise(function (resolve) {
    toast({
      mes,
      timeout: 1000,
      callback: () => {
        resolve();
      },
      ...obj,
    });
  });
};

dialog.validateError = (...args) => {
  validatorDefaultCatch(...args);
};

export function validatorDefaultCatch(err, type = 'message') {
  return dialog[type](err.errors[0].message);
}

export default dialog;
