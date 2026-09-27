import E from 'wangeditor'; // Cài đặt npm
// const E = window.wangEditor // Cách import qua CDN
import util from '../../utils/bus';
// Lấy các biến cần thiết, các biến này sẽ được dùng ở phần sau
const { $, BtnMenu, DropListMenu, PanelMenu, DropList, Panel, Tooltip } = E;
var _this = null;
export default class HtmlMenu extends BtnMenu {
  constructor(editor) {
    _this = editor;
    // Thuộc tính data-title thể hiện chú thích ngắn về chức năng của nút khi di chuột vào (hover)
    const $elem = E.$(
      `<div class="w-e-menu" data-title="html">
                <div>HTML</div>
            </div>`,
    );
    super($elem, editor);
  }
  // Sự kiện bấm menu
  clickHandler() {
    // getHtmlint()
    util.$emit('Html');
  }
  tryChangeActive() {
    this.active();
  }
}
