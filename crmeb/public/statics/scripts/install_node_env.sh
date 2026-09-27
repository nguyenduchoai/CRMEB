#!/bin/bash
#
# Script cài đặt môi trường Node.js một cú nhấp cho CRMEB
# Hỗ trợ: CentOS, Ubuntu, Debian, Alpine, macOS
#

set -e

# Định nghĩa màu
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Lệnh sudo (để trống nếu là root)
SUDO_CMD=""

# Hàm ghi log
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Kiểm tra quyền root
check_root() {
    if [ "$EUID" -eq 0 ]; then
        log_info "Đang chạy với người dùng root"
        SUDO_CMD=""
    else
        log_warn "Người dùng hiện tại không phải root, sẽ dùng sudo để thực thi lệnh cài đặt"
        # Kiểm tra sudo có dùng được không
        if command -v sudo &> /dev/null; then
            SUDO_CMD="sudo"
            # Thử quyền sudo
            if ! sudo -n true 2>/dev/null; then
                log_info "Vui lòng nhập mật khẩu của bạn để tiếp tục cài đặt..."
                sudo true
            fi
        else
            log_error "sudo chưa được cài đặt, vui lòng chạy script này với người dùng root"
            exit 1
        fi
    fi
}

# Phát hiện hệ điều hành
detect_os() {
    if [[ "$OSTYPE" == "darwin"* ]]; then
        OS_TYPE="macos"
        OS_VERSION=$(sw_vers -productVersion)
    elif [ -f /etc/os-release ]; then
        . /etc/os-release
        OS_TYPE=$(echo "$ID" | tr '[:upper:]' '[:lower:]')
        OS_VERSION="$VERSION_ID"
    elif [ -f /etc/redhat-release ]; then
        OS_TYPE="centos"
        OS_VERSION=$(cat /etc/redhat-release | grep -oE '[0-9]+\.[0-9]+' | head -1)
    else
        OS_TYPE="unknown"
        OS_VERSION="unknown"
    fi
    
    log_info "Phát hiện hệ điều hành: $OS_TYPE $OS_VERSION"
}

# Kiểm tra đã cài đặt Node.js chưa
check_node() {
    if command -v node &> /dev/null; then
        NODE_VERSION=$(node -v 2>/dev/null | sed 's/v//')
        log_info "Node.js đã được cài đặt, phiên bản: $NODE_VERSION"
        return 0
    fi
    return 1
}

# Kiểm tra đã cài đặt npm chưa
check_npm() {
    if command -v npm &> /dev/null; then
        NPM_VERSION=$(npm -v 2>/dev/null)
        log_info "npm đã được cài đặt, phiên bản: $NPM_VERSION"
        return 0
    fi
    return 1
}

# Kiểm tra đã cài đặt miniprogram-ci chưa
check_miniprogram_ci() {
    if command -v miniprogram-ci &> /dev/null; then
        CI_VERSION=$(miniprogram-ci --version 2>/dev/null | head -1)
        log_info "miniprogram-ci đã được cài đặt, phiên bản: $CI_VERSION"
        return 0
    fi
    return 1
}

# Cài đặt Node.js trên CentOS
install_node_centos() {
    # Phát hiện phiên bản CentOS
    local centos_version="7"
    if [ -f /etc/redhat-release ]; then
        centos_version=$(cat /etc/redhat-release | grep -oE '[0-9]+' | head -1)
    fi
    
    # CentOS 7 chỉ dùng được Node.js 16.x (giới hạn của glibc 2.17)
    if [ "$centos_version" = "7" ]; then
        log_warn "Phát hiện CentOS 7, do giới hạn phiên bản thư viện hệ thống, sẽ cài đặt Node.js 16.x"
        log_warn "Khuyến nghị nâng cấp lên CentOS 8/9 để dùng phiên bản Node.js mới hơn"
        
        if command -v curl &> /dev/null; then
            curl -fsSL https://rpm.nodesource.com/setup_16.x | $SUDO_CMD bash -
            $SUDO_CMD yum install -y nodejs
        else
            $SUDO_CMD yum install -y curl
            curl -fsSL https://rpm.nodesource.com/setup_16.x | $SUDO_CMD bash -
            $SUDO_CMD yum install -y nodejs
        fi
    else
        log_info "Đang cài đặt Node.js 20.x (LTS) trên CentOS $centos_version..."
        
        if command -v curl &> /dev/null; then
            curl -fsSL https://rpm.nodesource.com/setup_20.x | $SUDO_CMD bash -
            $SUDO_CMD yum install -y nodejs
        else
            $SUDO_CMD yum install -y curl
            curl -fsSL https://rpm.nodesource.com/setup_20.x | $SUDO_CMD bash -
            $SUDO_CMD yum install -y nodejs
        fi
    fi
}

# Cài đặt Node.js trên Ubuntu/Debian
install_node_debian() {
    log_info "Đang cài đặt Node.js 20.x (LTS) trên $OS_TYPE..."
    
    $SUDO_CMD apt-get update
    
    # Dùng kho NodeSource để cài đặt Node.js 20.x
    if command -v curl &> /dev/null; then
        curl -fsSL https://deb.nodesource.com/setup_20.x | $SUDO_CMD bash -
        $SUDO_CMD apt-get install -y nodejs
    else
        $SUDO_CMD apt-get install -y curl
        curl -fsSL https://deb.nodesource.com/setup_20.x | $SUDO_CMD bash -
        $SUDO_CMD apt-get install -y nodejs
    fi
}

# Cài đặt Node.js trên Alpine
install_node_alpine() {
    log_info "Đang cài đặt Node.js trên Alpine..."
    $SUDO_CMD apk update
    $SUDO_CMD apk add nodejs npm
}

# Cài đặt Node.js trên macOS
install_node_macos() {
    log_info "Đang cài đặt Node.js trên macOS..."
    
    # Kiểm tra có Homebrew không
    if command -v brew &> /dev/null; then
        brew install node
    else
        log_error "Vui lòng cài đặt Homebrew trước, sau đó thử lại"
        log_info "Lệnh cài đặt: /bin/bash -c \"\$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)\""
        exit 1
    fi
}

# Cài đặt Node.js
install_node() {
    if check_node; then
        log_info "Node.js đã được cài đặt, bỏ qua bước cài đặt"
        return 0
    fi
    
    case "$OS_TYPE" in
        centos|rhel|fedora|rocky|almalinux)
            install_node_centos
            ;;
        ubuntu|debian)
            install_node_debian
            ;;
        alpine)
            install_node_alpine
            ;;
        macos)
            install_node_macos
            ;;
        *)
            log_error "Hệ điều hành không được hỗ trợ: $OS_TYPE"
            log_info "Vui lòng cài đặt Node.js thủ công: https://nodejs.org/"
            exit 1
            ;;
    esac
    
    # Xác minh cài đặt
    if check_node && check_npm; then
        log_info "Node.js cài đặt thành công!"
    else
        log_error "Node.js cài đặt thất bại"
        exit 1
    fi
}

# Cài đặt miniprogram-ci
install_miniprogram_ci() {
    if check_miniprogram_ci; then
        log_info "miniprogram-ci đã được cài đặt, bỏ qua bước cài đặt"
        return 0
    fi
    
    log_info "Đang cài đặt miniprogram-ci ở phạm vi toàn cục (global)..."
    
    # Đặt mirror npm (tùy chọn, tăng tốc tải xuống tại Trung Quốc)
    npm config set registry https://registry.npmmirror.com
    
    # Cài đặt toàn cục miniprogram-ci
    $SUDO_CMD npm install -g miniprogram-ci
    
    # Xác minh cài đặt
    if check_miniprogram_ci; then
        log_info "miniprogram-ci cài đặt thành công!"
    else
        log_error "miniprogram-ci cài đặt thất bại"
        exit 1
    fi
}

# Hiển thị kết quả cài đặt
show_result() {
    echo ""
    echo "=============================================="
    echo -e "${GREEN}Cài đặt hoàn tất!${NC}"
    echo "=============================================="
    echo ""
    
    if check_node; then
        echo -e "Node.js:        ${GREEN}✓${NC} $(node -v)"
    else
        echo -e "Node.js:        ${RED}✗${NC} chưa cài đặt"
    fi
    
    if check_npm; then
        echo -e "npm:            ${GREEN}✓${NC} v$(npm -v)"
    else
        echo -e "npm:            ${RED}✗${NC} chưa cài đặt"
    fi
    
    if check_miniprogram_ci; then
        echo -e "miniprogram-ci: ${GREEN}✓${NC} $(miniprogram-ci --version 2>/dev/null | head -1)"
    else
        echo -e "miniprogram-ci: ${RED}✗${NC} chưa cài đặt"
    fi
    
    echo ""
}

# Hàm chính
main() {
    echo ""
    echo "=============================================="
    echo "  CRMEB Node.js - script cài đặt môi trường một cú nhấp"
    echo "=============================================="
    echo ""
    
    # Kiểm tra quyền root
    check_root
    
    # Phát hiện hệ điều hành
    detect_os
    
    # Cài đặt Node.js
    install_node
    
    # Cài đặt miniprogram-ci
    install_miniprogram_ci
    
    # Hiển thị kết quả
    show_result
    
    exit 0
}

# Thực thi hàm chính
main "$@"
