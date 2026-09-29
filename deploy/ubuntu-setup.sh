#!/usr/bin/env bash
#
# Dựng ứng dụng Dinh Dưỡng trên một VPS Ubuntu 24.04 LTS còn trắng.
#
# Cách dùng — chạy bằng root trên VPS:
#
#   export DOMAIN=dinhduong.example.vn
#   export REPO=https://github.com/phalconsupply/dinhduong.git
#   bash ubuntu-setup.sh
#
# Biến tuỳ chọn:
#   APP_DIR     thư mục cài đặt          (mặc định /var/www/dinhduong)
#   DB_NAME     tên CSDL                  (mặc định dinhduong)
#   DB_USER     tài khoản CSDL            (mặc định dinhduong)
#   DB_PASS     mật khẩu CSDL             (mặc định: tự sinh ngẫu nhiên)
#   PHP_VER     phiên bản PHP             (mặc định 8.3)
#   BRANCH      nhánh git                 (mặc định main)
#
# Script CỐ Ý KHÔNG tự nhập dữ liệu và không tự xin chứng chỉ HTTPS —
# hai việc đó cần file dữ liệu và tên miền đã trỏ DNS, nên để làm tay
# sau khi script chạy xong. Cuối script có in hướng dẫn.

set -euo pipefail

DOMAIN="${DOMAIN:?Thiếu biến DOMAIN, ví dụ: export DOMAIN=dinhduong.example.vn}"
REPO="${REPO:-https://github.com/phalconsupply/dinhduong.git}"
APP_DIR="${APP_DIR:-/var/www/dinhduong}"
DB_NAME="${DB_NAME:-dinhduong}"
DB_USER="${DB_USER:-dinhduong}"
DB_PASS="${DB_PASS:-$(head -c 32 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 24)}"
PHP_VER="${PHP_VER:-8.3}"
BRANCH="${BRANCH:-main}"

xanh() { printf '\033[0;32m%s\033[0m\n' "$*"; }
vang() { printf '\033[1;33m%s\033[0m\n' "$*"; }
buoc() { printf '\n\033[1;36m══ %s ══\033[0m\n' "$*"; }

[ "$(id -u)" -eq 0 ] || { echo "Phải chạy bằng root."; exit 1; }

buoc "1/9  Cài gói hệ thống"
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y -qq \
    nginx mysql-server git unzip curl ca-certificates \
    php${PHP_VER}-fpm php${PHP_VER}-cli \
    php${PHP_VER}-mysql php${PHP_VER}-mbstring php${PHP_VER}-xml \
    php${PHP_VER}-curl php${PHP_VER}-gd php${PHP_VER}-zip \
    php${PHP_VER}-intl php${PHP_VER}-bcmath
# php-zip là bắt buộc: maatwebsite/excel cần nó, thiếu thì composer install dừng.

buoc "2/9  Cài Composer"
if ! command -v composer >/dev/null 2>&1; then
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet
    rm -f /tmp/composer-setup.php
fi
xanh "  $(composer --version)"

buoc "3/9  Tạo CSDL"
# utf8mb4_unicode_ci cho khớp config/database.php
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"
xanh "  CSDL ${DB_NAME}, tài khoản ${DB_USER}"

buoc "4/9  Lấy mã nguồn"
if [ -d "${APP_DIR}/.git" ]; then
    git -C "${APP_DIR}" fetch --quiet origin
    git -C "${APP_DIR}" checkout --quiet "${BRANCH}"
    git -C "${APP_DIR}" reset --hard --quiet "origin/${BRANCH}"
else
    mkdir -p "$(dirname "${APP_DIR}")"
    git clone --quiet --branch "${BRANCH}" "${REPO}" "${APP_DIR}"
fi
cd "${APP_DIR}"
xanh "  $(git log --oneline -1)"

buoc "5/9  Cài thư viện PHP"
composer install --no-dev --optimize-autoloader --no-interaction --quiet

buoc "6/9  Tạo file .env"
if [ ! -f .env ]; then
    cp .env.example .env
fi
set_env() {
    local k="$1" v="$2"
    if grep -qE "^${k}=" .env; then
        # dùng | làm phân tách để không vướng dấu / trong URL
        sed -i "s|^${k}=.*|${k}=${v}|" .env
    else
        printf '%s=%s\n' "$k" "$v" >> .env
    fi
}
set_env APP_NAME '"Dinh Dưỡng"'
set_env APP_ENV production
set_env APP_DEBUG false
set_env APP_URL "https://${DOMAIN}"
set_env LOG_LEVEL warning
set_env DB_CONNECTION mysql
set_env DB_HOST 127.0.0.1
set_env DB_PORT 3306
set_env DB_DATABASE "${DB_NAME}"
set_env DB_USERNAME "${DB_USER}"
set_env DB_PASSWORD "${DB_PASS}"
php artisan key:generate --force --quiet
chmod 640 .env

buoc "7/9  Phân quyền thư mục"
chown -R www-data:www-data "${APP_DIR}"
find "${APP_DIR}" -type d -exec chmod 755 {} \;
find "${APP_DIR}" -type f -exec chmod 644 {} \;
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chown www-data:www-data "${APP_DIR}/.env"

buoc "8/9  Cấu hình nginx"
PHP_SOCK="/run/php/php${PHP_VER}-fpm.sock"
sed -e "s|__DOMAIN__|${DOMAIN}|g" \
    -e "s|__ROOT__|${APP_DIR}|g" \
    -e "s|__PHP_SOCK__|${PHP_SOCK}|g" \
    "${APP_DIR}/deploy/nginx-dinhduong.conf" > /etc/nginx/sites-available/dinhduong
ln -sf /etc/nginx/sites-available/dinhduong /etc/nginx/sites-enabled/dinhduong
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
systemctl enable --now "php${PHP_VER}-fpm" nginx mysql >/dev/null 2>&1 || true

buoc "9/9  Dựng cấu trúc CSDL"
sudo -u www-data php artisan migrate --force

xanh "
════════════════════════════════════════════════
  Đã dựng xong phần hệ thống
════════════════════════════════════════════════"
cat <<THONGTIN

  Thư mục   : ${APP_DIR}
  Tên miền  : http://${DOMAIN}
  CSDL      : ${DB_NAME}
  Tài khoản : ${DB_USER}
  Mật khẩu  : ${DB_PASS}
              ^^^ GHI LẠI NGAY, script không lưu ở đâu khác ngoài .env

CÒN 3 VIỆC PHẢI LÀM TAY:

1) Nạp dữ liệu từ máy cũ
   Trên máy cũ:   php artisan data:export
   Chuyển sang:   scp <file>.json root@<ip>:${APP_DIR}/storage/app/data-export/
   Trên VPS:      cd ${APP_DIR} && sudo -u www-data php artisan data:import <file>.json --fresh

   PHẢI có --fresh. Migration add_zscore_method_setting đã chèn sẵn 1 dòng vào
   bảng settings, nên không có --fresh thì lệnh nhập coi bảng đó "đã có dữ liệu"
   và bỏ qua, làm mất toàn bộ cấu hình lẫn nội dung lời khuyên.

2) Sinh dữ liệu tham chiếu WHO
   cd ${APP_DIR}
   sudo -u www-data php artisan who:import-2006
   sudo -u www-data php artisan who:import-2007

3) Chép ảnh người dùng đã tải lên (không nằm trong git)
   scp -r public/uploads root@<ip>:${APP_DIR}/public/
   cd ${APP_DIR} && sudo -u www-data php artisan storage:link
   chown -R www-data:www-data ${APP_DIR}/public/uploads

SAU KHI XONG 3 VIỆC TRÊN:

   cd ${APP_DIR}
   sudo -u www-data php artisan config:cache
   sudo -u www-data php artisan route:cache
   sudo -u www-data php artisan view:cache

BẬT HTTPS (sau khi DNS đã trỏ về VPS):

   apt-get install -y certbot python3-certbot-nginx
   certbot --nginx -d ${DOMAIN} --agree-tos -m admin@${DOMAIN} --redirect

KIỂM TRA:

   curl -I http://${DOMAIN}/
   sudo -u www-data php artisan tinker --execute="echo App\\Models\\History::count();"

THONGTIN
