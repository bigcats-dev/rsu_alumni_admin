#!/usr/bin/env sh
set -eu

envsubst '${NGINX_UPSTREAM}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"