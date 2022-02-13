FROM nginx:1.15-alpine

ADD ./provisioning/vhost.conf /etc/nginx/conf.d/default.conf
