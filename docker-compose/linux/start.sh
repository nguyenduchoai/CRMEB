#!/bin/bash

echo ">> run"
docker-compose up -d
echo "[Địa chỉ trang quản trị]\n http://localhost:8011/"
echo "[Thông tin MySql]\n host:192.168.10.11:3306  user:root password:123456 database:crmeb"
echo "[Thông tin Redis]\n host:192.168.10.10:6379 password:123456"
