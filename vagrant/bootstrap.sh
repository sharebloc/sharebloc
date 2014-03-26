#!/usr/bin/env bash

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

mv /etc/nginx/nginx.conf /etc/nginx/nginx.conf.bak
ln -s /vagrant/vagrant/nginx.conf /etc/nginx/nginx.conf
nginx -s reload

mv /etc/php5/fpm/php-fpm.conf /etc/php5/fpm/php-fpm.conf.bak
ln -s /vagrant/vagrant/php-fpm.conf /etc/php5/fpm/php-fpm.conf
/etc/init.d/php5-fpm restart

rm -f /vagrant/includes/class.Settings.php
ln -s /vagrant/includes/class.Settings.php.vagrant /vagrant/includes/class.Settings.php

rm -f /vagrant/includes/log4php.xml
ln -s /vagrant/includes/log4php.xml.vagrant /vagrant/includes/log4php.xml

echo "create database sharebloc;" | mysql -u root --password=sharebloc
mysql -u root --password=sharebloc sharebloc < /vagrant/sharebloc.sql
