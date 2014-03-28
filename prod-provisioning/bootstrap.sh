#!/usr/bin/env bash

# this script expects code to be at /var/www/prod
# you'll need to manually install a settings file to /var/www/prod/includes/class.Settings.php

echo "deb http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
echo "deb-src http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
echo "deb http://packages.dotdeb.org wheezy-php55 all" >> /etc/apt/sources.list
echo "deb-src http://packages.dotdeb.org wheezy-php55 all" >> /etc/apt/sources.list
wget http://www.dotdeb.org/dotdeb.gpg
apt-key add dotdeb.gpg

apt-get update

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password sharebloc'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password sharebloc'
apt-get install -y nginx memcached mysql-server php5 php5-fpm php5-cli php5-curl php5-gd php5-mysql php5-memcache

rm /etc/nginx/sites-enabled/default

mkdir /var/www/prod/log
mkdir /var/www/prod/includes/libs/templates_c
mkdir /var/www/prod/html/logos
mkdir /var/www/prod/html/screenshots

chown -r www-data:www-data /var/www/prod

mv /etc/nginx/nginx.conf /etc/nginx/nginx.conf.bak
ln -s /var/www/prod/prod-provisioning/nginx.conf /etc/nginx/nginx.conf
nginx -s reload

mv /etc/php5/fpm/php-fpm.conf /etc/php5/fpm/php-fpm.conf.bak
ln -s /var/www/prod/prod-provisioning/php-fpm.conf /etc/php5/fpm/php-fpm.conf
/etc/init.d/php5-fpm restart
