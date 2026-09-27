#!/usr/bin/env node

const chalk = require('chalk');

// In thông tin chào mừng
console.log(chalk.hex('#DEADED').underline('😄 Hello ~ Chào mừng bạn đến với CRMEB bản tiêu chuẩn, chúng tôi sẽ tận tâm phục vụ bạn!'));
console.log(chalk.yellow('info - [Gợi ý] Nhấn vào đây để xem thêm sản phẩm~ ') + chalk.blue.underline('https://doc.crmeb.com'));
console.log(chalk.yellow('info - [Gợi ý] Nhấn vào đây để xem tài liệu phát triển nhé~ ') + chalk.blue.underline('https://www.crmeb.com'));
console.log(
  chalk.yellow('info - [Gợi ý] Nhấn vào đây để xem diễn đàn cộng đồng của chúng tôi~ ') + chalk.blue.underline('https://www.crmeb.com/ask'),
);
console.log(chalk.blue('info - [Bạn có biết?] Nhấn Ctrl+C để dừng dịch vụ nhé~'));
