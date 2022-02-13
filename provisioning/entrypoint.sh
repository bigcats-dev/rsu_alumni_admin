#!/usr/bin/env sh

cron -f & docker-php-entrypoint php-fpm & supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf