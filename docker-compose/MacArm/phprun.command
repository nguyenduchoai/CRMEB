#!/bin/bash

# Vào container dự án để chạy file script PHP

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

