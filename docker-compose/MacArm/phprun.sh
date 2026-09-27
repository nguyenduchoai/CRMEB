#!/bin/bash

# Vào container dự án để chạy file script PHP

echo "Khởi động tác vụ định kỳ: php think timer start --d"
echo "Khởi động kết nối liên tục: php think workerman start --d"
echo "Khởi động hàng đợi: php think queue:listen --queue"

# Vào container
docker exec -it crmeb_php /bin/bash
# Vào dự án
cd /var/www
# Khởi động tác vụ định kỳ
php think timer start --d
# Khởi động kết nối lâu dài
php think workerman start --d
# Khởi động hàng đợi
php think queue:listen --queue


