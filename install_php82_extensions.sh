#!/bin/bash
# install_php82_extensions.sh
# Script otomatis install & enable PHP 8.2 extensions umum

# Update repo
sudo apt-get update

# Install PHP 8.2 core + extensions populer
sudo apt-get install -y php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-common \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-gd \
    php8.2-intl \
    php8.2-zip \
    php8.2-mysql \
    php8.2-bcmath

# Enable extensions
sudo phpenmod curl mbstring xml gd intl zip mysql bcmath

# Restart service PHP-FPM & Apache jika ada
if systemctl list-unit-files | grep -q "php8.2-fpm.service"; then
    sudo systemctl restart php8.2-fpm
fi
if systemctl list-unit-files | grep -q "apache2.service"; then
    sudo systemctl restart apache2
fi

# Reload shell
exec bash

# Tampilkan modul yg aktif
php -m | grep -E "curl|mbstring|xml|dom|gd|intl|zip|mysql|bcmath"
