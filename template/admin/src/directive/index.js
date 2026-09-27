import directive from './directives';

const importDirective = (Vue) => {
  /**
   * Directive kéo thả v-draggable="options"
   * options = {
   *  trigger: /Ở đây truyền vào CSS selector dùng làm trigger kéo thả/,
   *  body:    /Ở đây truyền vào CSS selector của container cần di chuyển/,
   *  recover: /Sau khi kéo xong có khôi phục về vị trí ban đầu không/
   * }
   */
  Vue.directive('draggable', directive.draggable);
  /**
   * Directive clipboard v-draggable="options"
   * options = {
   *  value:    /Giá trị được gắn bằng v-model trong ô nhập/,
   *  success:  /Callback sau khi copy thành công/,
   *  error:    /Callback sau khi copy thất bại/
   * }
   */
  Vue.directive('clipboard', directive.clipboard);
  /**
   * v-auth="['string-string']"
   * */
  Vue.directive('auth', directive.auth);

  Vue.directive('permission', directive.permission);
  Vue.directive('dbClick', directive.dbClick);
};

export default importDirective;
