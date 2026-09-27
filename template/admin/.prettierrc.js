module.exports = {
  // Mỗi dòng tối đa 120 ký tự
  printWidth: 120,
  // Dùng 2 dấu cách để thụt lề
  tabWidth: 2,
  // Không dùng tab để thụt lề, mà dùng dấu cách
  useTabs: false,
  // Cuối dòng cần có dấu chấm phẩy
  semi: true,
  // Dùng dấu nháy đơn thay cho nháy đôi
  singleQuote: true,
  // Key của object chỉ dùng dấu nháy khi cần thiết
  quoteProps: 'as-needed',
  // jsx không dùng nháy đơn, mà dùng nháy đôi
  jsxSingleQuote: false,
  // Dùng dấu phẩy ở cuối
  trailingComma: 'all',
  // Đầu và cuối trong dấu ngoặc nhọn cần có dấu cách { foo: bar }
  bracketSpacing: true,
  // Hàm mũi tên (arrow function), khi chỉ có một tham số vẫn cần dấu ngoặc đơn
  arrowParens: 'always',
  // Phạm vi format của mỗi file là toàn bộ nội dung file
  rangeStart: 0,
  rangeEnd: Infinity,
  // Không cần viết @prettier ở đầu file
  requirePragma: false,
  // Không cần tự động chèn @prettier vào đầu file
  insertPragma: false,
  // Dùng chuẩn ngắt dòng mặc định
  proseWrap: 'preserve',
  // Dựa vào kiểu hiển thị để quyết định html có ngắt dòng hay không
  htmlWhitespaceSensitivity: 'css',
  // Ký tự xuống dòng dùng lf
  endOfLine: 'lf',
};
