FROM nginx:1.15.12-alpine

ADD ./provisioning/vhost.prod.conf /etc/nginx/conf.d/default.conf

COPY public /var/www/rsu/public

WORKDIR  /var/www/rsu/public

RUN ln -s /var/www/rsu/storage/app/public storage