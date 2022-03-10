FROM php:7.3.5-fpm

RUN apt-get update && apt-get install  -y \
    cron \
    supervisor \
    libmcrypt-dev \
    libzip-dev \ 
    vim \
    unzip \
    libfreetype6-dev \
    mysql-client \
    libjpeg62-turbo-dev \
    libpng-dev \
    libjpeg62 \
    libaio1 \
    libmagickwand-dev --no-install-recommends \
    curl

COPY imagick-3.7.0.tgz .

COPY mcrypt-1.0.4.tgz .

RUN pecl install imagick-3.7.0.tgz mcrypt-1.0.4.tgz

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-enable imagick mcrypt

RUN docker-php-ext-install pdo_mysql zip

RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/ \
        --with-png-dir=/usr/lib/ \
        --with-jpeg-dir=/usr/lib/ \
        --with-gd

RUN NUMPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NUMPROC} gd
    
# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
# RUN curl -sS https://getcomposer.org/installer | php -- \
#     --install-dir=/usr/local/bin \
#     --filename=composer

# ORACLE oci
RUN mkdir /opt/oracle \
    && cd /opt/oracle

# Install Oracle Instantclient
ADD instantclient-basic-linux.x64-19.8.0.0.0dbru.zip /opt/oracle/instantclient-basic-linux.x64-19.8.0.0.0dbru.zip
ADD instantclient-sdk-linux.x64-19.8.0.0.0dbru.zip /opt/oracle/instantclient-sdk-linux.x64-19.8.0.0.0dbru.zip
RUN unzip /opt/oracle/instantclient-basic-linux.x64-19.8.0.0.0dbru.zip -d /opt/oracle \
    && unzip /opt/oracle/instantclient-sdk-linux.x64-19.8.0.0.0dbru.zip -d /opt/oracle \
    && ln -sf /opt/oracle/instantclient_19_8/libclntsh.so.19.1 /opt/oracle/instantclient_19_8/libclntsh.so \
    && ln -sf /opt/oracle/instantclient_19_8/libclntshcore.so.19.1 /opt/oracle/instantclient_19_8/libclntshcore.so \
    && ln -sf /opt/oracle/instantclient_19_8/libocci.so.19.1 /opt/oracle/instantclient_19_8/libocci.so \
    && rm -rf /opt/oracle/*.zip

ENV LD_LIBRARY_PATH  /opt/oracle/instantclient_19_8:${LD_LIBRARY_PATH}

COPY oci8-2.2.0.tgz .

# Install Oracle extensions
RUN echo 'instantclient,/opt/oracle/instantclient_19_8/' | pecl install oci8-2.2.0.tgz \
    && docker-php-ext-enable oci8
    
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_19_8,19.1 \
    && docker-php-ext-install pdo_oci \
    # add error Unable to load dynamic library 'oci8.so'
    && echo /opt/oracle/instantclient_19_8/ > /etc/ld.so.conf.d/oracle-insantclient.conf \
    && ldconfig
    # end

COPY  . /var/www/rsu/
WORKDIR /var/www/rsu/

# install composer
RUN composer install

RUN chown -R www-data:www-data \
        /var/www/rsu/storage \
        /var/www/rsu/bootstrap/cache

RUN mv .env.prod .env

# RUN php artisan optimize

# RUN php artisan storage:link

EXPOSE 9000

CMD ["php-fpm"]