#!/bin/bash

if [ $1 == 'test' ];then
   touch /sp_edaijia/www/v2/protected/config/test.lock
fi

mkdir -p  /sp_edaijia/www/v2/cache/assets/
chown -R www.www /sp_edaijia/www/v2/cache/assets/
/usr/local/php/sbin/php-fpm
/usr/local/nginx/sbin/nginx
