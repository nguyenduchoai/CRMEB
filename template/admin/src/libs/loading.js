// +---------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +---------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +---------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +---------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +---------------------------------------------------------------------

const events = [];

const $scroll = function (dom, fn) {
  events.push({ dom, fn });
  fn._index = events.length - 1;
};

$scroll.remove = function (fn) {
  fn._index && events.splice(fn._index, 1);
};

//Tải thêm khi kéo lên;
const Scroll = {
  addHandler: function (element, type, handler) {
    if (element.addEventListener) element.addEventListener(type, handler, false);
    else if (element.attachEvent) element.attachEvent('on' + type, handler);
    else element['on' + type] = handler;
  },
  listenTouchDirection: function () {
    this.addHandler(window, 'scroll', function () {
      const wh = window.innerHeight,
        st = window.scrollY;
      events
        .filter((e) => e.dom.scrollHeight && e.dom.scrollHeight > 0)
        .forEach((e) => {
          var dh = e.dom.scrollHeight;
          var s = Math.ceil((st / (dh - wh)) * 100);
          if (s > 85) e.fn();
        });
    });
  },
};

Scroll.listenTouchDirection();

export default $scroll;
export { Scroll };
