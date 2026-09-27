/**
 * Tập hợp phương thức tích hợp sẵn của hệ thống, bình thường bạn không nên sửa hoặc xóa file này
 * */

import { cloneDeep } from 'lodash';

/**
 * @description Dựa theo route hiện tại, tìm tên menu trên cùng
 * @param {String} currentPath Đường dẫn hiện tại
 * @param {Array} menuList Tất cả đường dẫn
 * */
function getHeaderName(to, menuList) {
  const allMenus = [];
  menuList.forEach((menu) => {
    const headerName = menu.path || '';
    const menus = transferMenu(menu, headerName);
    allMenus.push({
      path: menu.path,
      header: headerName,
    });
    menus.forEach((item) => allMenus.push(item));
  });
  const currentMenu = allMenus.find((item) => {
    let path = to.meta && to.meta.activeMenu ? to.meta.activeMenu : to.path;
    if (item.path === path) {
      return true;
    } else {
      return path === getPath(to, item.path);
    }
  });
  return currentMenu ? currentMenu.header : null;
}

function getPath(to, path) {
  let params = [];
  let query = [];
  Object.keys(to.params).forEach((item) => {
    params.push(to.params[item]);
  });
  Object.keys(to.query).forEach((item) => {
    query.push(item + '=' + to.query[item]);
  });
  return path + (params.length ? '/' + params.join('/') : '') + (query.length ? '?' + query.join('&') : '');
}

function transferMenu(menu, headerName) {
  if (menu.children && menu.children.length) {
    return menu.children.reduce((all, item) => {
      all.push({
        path: item.path,
        header: headerName,
      });
      const foundChildren = transferMenu(item, headerName);
      return all.concat(foundChildren);
    }, []);
  } else {
    return [menu];
  }
}

export { getHeaderName };

/**
 * @description Dựa theo route hiện tại, tìm tên menu trên cùng
 * @param {String} currentPath Đường dẫn hiện tại
 * @param {Array} menuList Tất cả đường dẫn
 * */
function getHeaderSider(menuList) {
  return menuList.filter((item) => item.pid === 0);
}

export { getHeaderSider };
/**
 * @description Dựa theo route hiện tại, tìm tên menu
 * @param {String} currentPath Đường dẫn hiện tại
 * @param {Array} menuList Tất cả đường dẫn
 * */
function getOneHeaderName(menuList, path) {
  return menuList.filter((item) => item.path === path);
}

export { getOneHeaderName };

/**
 * @description Dựa theo name của menu thanh trên cùng hiện tại, tìm menu cấp 2 tương ứng
 * @param {Array} menuList Tất cả menu cấp 2
 * @param {String} headerName Name của menu thanh trên cùng hiện tại
 * */
function getMenuSider(menuList, headerName = '') {
  if (headerName) {
    return menuList.filter((item) => item.path === headerName);
  } else {
    return menuList;
  }
}

export { getMenuSider };

/**
 * @description Dựa theo route hiện tại, tìm tất cả path của menu cha, làm căn cứ cho open-names khi mở sidebar
 * @param {String} currentPath Đường dẫn hiện tại
 * @param {Array} menuList Tất cả đường dẫn
 * */
// function getSiderSubmenu (currentPath, menuList) {
//     const allMenus = [];
//     menuList.forEach(menu => {
//         const menus = transferSubMenu(menu, []);
//         allMenus.push({
//             path: menu.path,
//             openNames: []
//         });
//         menus.forEach(item => allMenus.push(item));
//     });
//     const currentMenu = allMenus.find(item => item.path === currentPath);
//     return currentMenu ? currentMenu.openNames : [];
// }

function getSiderSubmenu(to, menuList) {
  const allMenus = [];
  menuList.forEach((menu) => {
    const menus = transferSubMenu(menu, []);
    allMenus.push({
      path: menu.path,
      openNames: [],
    });
    menus.forEach((item) => allMenus.push(item));
  });
  const currentMenu = allMenus.find((item) => {
    if (item.openNames.length) {
      return item.path === to.path || to.path === getPath(to, item.path);
    }
  });
  return currentMenu ? currentMenu.openNames : [];
}

function transferSubMenu(menu, openNames) {
  if (menu.children && menu.children.length) {
    const itemOpenNames = openNames.concat([menu.path]);
    return menu.children.reduce((all, item) => {
      all.push({
        path: item.path,
        openNames: itemOpenNames,
      });
      const foundChildren = transferSubMenu(item, itemOpenNames);
      return all.concat(foundChildren);
    }, []);
  } else {
    return [menu].map((item) => {
      return {
        path: item.path,
        openNames: openNames,
      };
    });
  }
}

export { getSiderSubmenu };

/**
 * @description Đệ quy lấy tất cả menu con
 * */
function getAllSiderMenu(menuList) {
  let allMenus = [];

  menuList.forEach((menu) => {
    if (menu.children && menu.children.length) {
      const menus = getMenuChildren(menu);
      menus.forEach((item) => allMenus.push(item));
    } else {
      allMenus.push(menu);
    }
  });

  return allMenus;
}

function getMenuChildren(menu) {
  if (menu.children && menu.children.length) {
    return menu.children.reduce((all, item) => {
      const foundChildren = getMenuChildren(item);
      return all.concat(foundChildren);
    }, []);
  } else {
    return [menu];
  }
}

export { getAllSiderMenu };

/**
 * @description Chuyển menu thành dạng ngang hàng (flat)
 * */
function flattenSiderMenu(menuList, newList) {
  menuList.forEach((menu) => {
    let newMenu = {};
    for (let i in menu) {
      if (i !== 'children') newMenu[i] = cloneDeep(menu[i]);
    }
    newList.push(newMenu);
    menu.children && flattenSiderMenu(menu.children, newList);
  });
  return newList;
}

export { flattenSiderMenu };

export const findFirstNonNullChildren = (arr) => {
  // Nếu mảng trống, trả về null
  if (!arr || arr.length === 0) {
    return null;
  }
  // Tìm đối tượng đầu tiên
  const firstObj = arr[0];
  // Nếu đối tượng đầu tiên không có thuộc tính children, trả về đối tượng đó
  if (!firstObj.children) {
    return firstObj;
  }

  // Nếu thuộc tính children của đối tượng đầu tiên là một mảng,
  // Đệ quy tìm thuộc tính children đầu tiên khác null trong thuộc tính children
  if (Array.isArray(firstObj.children)) {
    return findFirstNonNullChildren(firstObj.children);
  }
  // Nếu trong mảng không có thuộc tính children khác null, trả về null
  return null;
};

export const findFirstNonNullChildrenKeys = (obj, lastArr) => {
  let ids = lastArr;
  // Nếu đối tượng đầu tiên không có thuộc tính children, trả về đối tượng đó
  if (!obj.children) {
    ids.push(obj.id);
    return ids;
  }
  // Nếu thuộc tính children của đối tượng đầu tiên là một mảng,
  // Đệ quy tìm thuộc tính children đầu tiên khác null trong thuộc tính children
  if (Array.isArray(obj.children)) {
    ids.push(obj.id);
    return findFirstNonNullChildrenKeys(obj.children[0], ids);
  }
  return ids;
};

// Xử lý mảng lồng nhiều cấp thành mảng một chiều
export const formatFlatteningRoutes = (arr) => {
  if (arr.length <= 0) return false;
  for (let i = 0; i < arr.length; i++) {
    if (arr[i].children) {
      arr = arr.slice(0, i + 1).concat(arr[i].children, arr.slice(i + 1));
    }
  }
  return arr;
};

/**
 * @description Kiểm tra danh sách 1 có chứa một mục nào trong danh sách 2 không
 * Vì quyền người dùng access là một mảng, phương thức includes không thể trực tiếp cho ra kết luận
 * */
function includeArray(list1, list2) {
  let status = false;
  if (list1 === true) {
    return true;
  } else {
    if (typeof list2 !== 'object') {
      return false;
    }
    list2.forEach((item) => {
      if (list1.includes(item)) status = true;
    });
    return status;
  }
}
export { includeArray };
