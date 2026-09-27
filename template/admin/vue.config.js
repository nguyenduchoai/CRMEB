// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

const path = require('path');
// Import công cụ đóng gói (build) js
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');

const resolve = (dir) => {
  return path.join(__dirname, dir);
};
// Cơ sở deploy project
module.exports = {
  // Đường dẫn đóng gói (build)
  outputDir: 'dist',
  // Đường dẫn đóng gói (build) -- địa chỉ file deploy trên môi trường thực tế
  // outputDir: '../../crmeb/public/admin',
  runtimeCompiler: true,
  productionSourceMap: false, //Tắt file mapping SourceMap ở môi trường production
  // Nếu bạn không cần dùng eslint, đặt lintOnSave thành false là được
  lintOnSave: false,
  // Tối ưu đóng gói (build)
  configureWebpack: (config) => {
    const pluginsPro = [];
    pluginsPro.push(
      // Nén file js
      new UglifyJsPlugin({
        uglifyOptions: {
          compress: {
            drop_debugger: true,
            drop_console: true, //Tự động xóa console ở môi trường production
            pure_funcs: ['console.log'], //Xóa console
          },
        },
        sourceMap: false,
        parallel: true, //Dùng nhiều process chạy song song để tăng tốc độ build. Số lượng chạy đồng thời mặc định: os.cpus().length - 1.
      }),
    );
    if (process.env.NODE_ENV === 'production') {
      config.plugins = [...config.plugins, ...pluginsPro];
    }
  },
  css: {
    loaderOptions: {
      scss: {
        sassOptions: {
          silenceDeprecations: ['legacy-js-api'],
        },
      },
    },
  },
  chainWebpack: (config) => {
    config.plugins.delete('prefetch');
    config.resolve.alias
      .set('@', resolve('src')) // key, value tự định nghĩa, ví dụ .set('@@', resolve('src/components'))
      .set('_c', resolve('src/components'));
    config.module
      .rule('vue')
      .test(/\.vue$/)
      .end();
    // Đặt lại alias
    config.resolve.alias.set('@api', resolve('src/api'));
    // node
    config.node.set('__dirname', true).set('__filename', true);
    config.plugin('monaco').use(new MonacoWebpackPlugin());
  },

  // Đặt thành false thì khi build sẽ không tạo file .map
  productionSourceMap: false,
  // Ở đây ghi đường dẫn cơ sở gọi API của bạn, để xử lý cross-domain; nếu đã cấu hình proxy thì baseUrl của axios trong môi trường phát triển local phải ghi là '', tức chuỗi rỗng
  devServer: {
    port: 1617, // Cổng (port)
  },
  publicPath: '/admin',
  assetsDir: 'system_static',
  indexPath: 'index.html',
};
