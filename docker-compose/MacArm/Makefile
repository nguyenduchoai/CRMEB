
# Khởi động docker-compose
run:
	@echo ">> run"
	@docker-compose -f ./docker-compose.yml up -d
	@echo "[Địa chỉ trang quản trị]\n http://localhost:8011/"
	@echo "[Thông tin MySql]\n host:192.168.10.11:3306  user:root password:123456 database:crmeb"
	@echo "[Thông tin Redis]\n host:192.168.10.10:6379 password:123456"

# Khởi động container php, vào chạy lệnh khởi động PHP
phprun:
	@echo ">> Sau khi vào container dự án, vui lòng chạy ba lệnh sau"
	@echo "Khởi động tác vụ định kỳ  php think timer start --d"
	@echo "Khởi động kết nối liên tục  php think workerman start --d"
	@echo "Khởi động hàng đợi  php think queue:listen --queue"
	@echo ">> Vào container dự án"
	@docker exec -it crmeb_php /bin/bash && cd /var/www

# Build
build:
	@echo ">> php build"
	@docker-compose -f ./docker-compose.yml build

# Dừng
down:
	@echo ">> php stop"
	@docker-compose -f ./docker-compose.yml down

# Xem log
logs:
	@echo ">> cat logs"
	@docker-compose -f ./docker-compose.yml logs -f

# Xem trạng thái
status:
	@echo ">> cat logs"
	@docker-compose -f ./docker-compose.yml logs ps
