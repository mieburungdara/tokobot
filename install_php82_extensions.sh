#!/bin/bash
# install_php82_extensions.sh
# Script otomatis install & enable PHP 8.2 extensions umum

# Update repo

sudo apt update
sudo apt-get update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-fpm php8.2-curl php8.2-mbstring php8.2-xml php8.2-gd php8.2-intl php8.2-zip php8.2-mysql php8.2-bcmath

# atur default
sudo update-alternatives --install /usr/bin/php php /usr/bin/php8.2 82
sudo update-alternatives --set php /usr/bin/php8.2
php -v

# Reload shell
exec bash

# Tampilkan modul yg aktif
php -m | grep -E "curl|mbstring|xml|dom|gd|intl|zip|mysql|bcmath"
apt list --upgradable
