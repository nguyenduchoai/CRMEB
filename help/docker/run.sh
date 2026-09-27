#!/bin/bash

# Script quản lý môi trường phát triển CRMEB Docker

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR"

# Tệp cấu hình (có thể tùy chỉnh, mặc định là docker-compose.yml)
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"

# Định nghĩa màu
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Thông tin trợ giúp
show_help() {
    echo -e "${GREEN}CRMEB Docker - script quản lý${NC}"
    echo ""
    echo "Cách dùng: $0 [tùy chọn]"
    echo ""
    echo "Tùy chọn:"
    echo "  install   Cài đặt và khởi động (xóa dữ liệu, triển khai lần đầu)"
    echo "  start     Khởi động container"
    echo "  restart   Khởi động lại container"
    echo "  stop      Dừng container"
    echo "  delete    Xóa container và dữ liệu"
    echo "  logs      Xem log"
    echo "  -h, --help Hiển thị trợ giúp"
    echo ""
    echo "Biến môi trường:"
    echo "  COMPOSE_FILE  Chỉ định tệp compose (mặc định: docker-compose.yml)"
    echo ""
    echo "Ví dụ:"
    echo "  $0 install                    # Dùng cấu hình mặc định"
    echo "  COMPOSE_FILE=docker-compose.build.yml $0 install   # Dùng cấu hình khác"
    echo "  $0 start                      # Khởi động dịch vụ"
    echo "  $0 logs                       # Xem log"
}

# Kiểm tra docker-compose có khả dụng không
check_docker() {
    if ! command -v docker-compose &> /dev/null; then
        echo -e "${RED}Lỗi: docker-compose chưa được cài đặt${NC}"
        exit 1
    fi
    if [ ! -f "$COMPOSE_FILE" ]; then
        echo -e "${RED}Lỗi: tệp cấu hình $COMPOSE_FILE không tồn tại${NC}"
        exit 1
    fi
}

# Dọn dẹp dữ liệu
cleanup() {
    echo -e "${YELLOW}=== Dọn dẹp dữ liệu cũ ===${NC}"

    # Xóa tệp install.lock
    if [ -f "../../crmeb/public/install.lock" ]; then
        echo "Đang xóa install.lock..."
        rm -f ../../crmeb/public/install.lock
    fi

    # Xóa nội dung thư mục dữ liệu MySQL
    if [ -d "mysql/data" ] && [ -n "$(ls -A mysql/data 2>/dev/null)" ]; then
        echo "Đang xóa dữ liệu MySQL..."
        rm -rf mysql/data/*
    fi

    # Xóa nội dung thư mục runtime
    if [ -d "../../crmeb/runtime" ] && [ -n "$(ls -A ../../crmeb/runtime 2>/dev/null)" ]; then
        echo "Đang xóa cache runtime..."
        rm -rf ../../crmeb/runtime/*
    fi

    # Đặt quyền thư mục thành 777
    echo "Đang thiết lập quyền thư mục..."
    chmod -R 777 ../../crmeb/runtime
    chmod -R 777 ../../crmeb/public
    chmod 777 ../../crmeb/.env 2>/dev/null
    chmod 777 ../../crmeb/.version 2>/dev/null
    chmod 777 ../../crmeb/.constant 2>/dev/null
}

# Dọn dẹp network
cleanup_network() {
    echo "Đang dọn dẹp các mạng có thể gây xung đột..."
    
    # Xóa các network có thể bị xung đột (docker-compose mặc định dùng <tên thư mục>_app_net)
    for net in $(docker network ls --format "{{.Name}}" | grep -E "(app_net|docker_app_net|crmeb_app_net)"); do
        echo "Đang xóa mạng: $net"
        docker network rm "$net" 2>/dev/null
    done
    
    # Dọn dẹp các network không dùng đến
    docker network prune -f 2>/dev/null
}

# Cài đặt (dọn dữ liệu và khởi động)
do_install() {
    check_docker
    
    echo -e "${YELLOW}=== Dừng container cũ ===${NC}"
    docker-compose -f "$COMPOSE_FILE" down 2>/dev/null
    
    cleanup
    
    # Dọn dẹp network
    cleanup_network
    
    echo -e "${YELLOW}=== Cài đặt và khởi động môi trường Docker ===${NC}"
    docker-compose -f "$COMPOSE_FILE" up -d
    
    echo ""
    echo -e "${GREEN}=== Cài đặt hoàn tất ===${NC}"
    echo "Tệp cấu hình: $COMPOSE_FILE"
    echo "Địa chỉ truy cập: http://localhost:8011"
    echo "Xem log: $0 logs"
}

# Khởi động
do_start() {
    check_docker
    cleanup_network
    echo -e "${YELLOW}=== Khởi động container ===${NC}"
    docker-compose -f "$COMPOSE_FILE" up -d
    echo ""
    echo -e "${GREEN}=== Khởi động hoàn tất ===${NC}"
    echo "Tệp cấu hình: $COMPOSE_FILE"
    echo "Địa chỉ truy cập: http://localhost:8011"
}

# Khởi động lại
do_restart() {
    check_docker
    echo -e "${YELLOW}=== Khởi động lại container ===${NC}"
    docker-compose -f "$COMPOSE_FILE" restart
    echo ""
    echo -e "${GREEN}=== Khởi động lại hoàn tất ===${NC}"
}

# Dừng
do_stop() {
    check_docker
    echo -e "${YELLOW}=== Dừng container ===${NC}"
    docker-compose -f "$COMPOSE_FILE" down
    echo -e "${GREEN}=== Đã dừng ===${NC}"
}

# Xóa
do_delete() {
    check_docker
    echo -e "${YELLOW}=== Xóa container và dữ liệu ===${NC}"
    docker-compose -f "$COMPOSE_FILE" down -v
    rm -rf mysql/data/* 2>/dev/null
    rm -rf ../../crmeb/runtime/* 2>/dev/null
    rm -f ../../crmeb/public/install.lock 2>/dev/null
    echo -e "${GREEN}=== Đã xóa ===${NC}"
}

# Xem log
do_logs() {
    check_docker
    docker-compose -f "$COMPOSE_FILE" logs -f
}

# Menu tương tác
show_menu() {
    echo ""
    echo "  1. Cài đặt và khởi động"
    echo "  2. Khởi động container"
    echo "  3. Khởi động lại container"
    echo "  4. Dừng container"
    echo "  5. Xóa container và dữ liệu"
    echo "  6. Xem log"
    echo "  7. Xem trợ giúp"
    echo "  8. Thoát"
    echo ""
    read -p "Vui lòng chọn thao tác (1-8): " choice
    echo ""
    
    case $choice in
        1) do_install ;;
        2) do_start ;;
        3) do_restart ;;
        4) do_stop ;;
        5) do_delete ;;
        6) do_logs ;;
        7) show_help; show_menu ;;
        8) echo "Đã thoát"; exit 0 ;;
        *) echo "Lựa chọn không hợp lệ, vui lòng thử lại"; show_menu ;;
    esac
}

# Logic chính
if [ $# -eq 0 ]; then
    # Hiển thị menu tương tác khi không có tham số
    show_menu
else
    case "$1" in
        install)
            do_install
            ;;
        start)
            do_start
            ;;
        restart)
            do_restart
            ;;
        stop)
            do_stop
            ;;
        delete)
            do_delete
            ;;
        logs)
            do_logs
            ;;
        -h|--help)
            show_help
            ;;
        *)
            show_help
            exit 1
            ;;
    esac
fi
