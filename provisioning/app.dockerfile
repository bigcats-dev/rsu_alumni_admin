FROM php:7.3.5-fpm

RUN apt-get update && apt-get install  -y cron supervisor libpng-dev libmcrypt-dev libzip-dev -qqy git vim unzip libfreetype6-dev \
    mysql-client libmagickwand-dev --no-install-recommends \
    && pecl install imagick mcrypt-1.0.2 \
    libjpeg62-turbo-dev \
    libpng-dev \
    libjpeg62 \
    libaio1 wget && apt-get clean autoclean && apt-get autoremove --yes &&  rm -rf /var/lib/{apt,dpkg,cache,log}/ \
    && docker-php-ext-enable imagick mcrypt \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip

RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/ \
        --with-png-dir=/usr/lib/ \
        --with-jpeg-dir=/usr/lib/ \
        --with-gd

RUN NUMPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NUMPROC} gd
    
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

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

# Install Oracle extensions
RUN echo 'instantclient,/opt/oracle/instantclient_19_8/' | pecl install oci8-2.2.0 \
    && docker-php-ext-enable oci8
    
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_19_8,19.1 \
    && docker-php-ext-install pdo_oci \
    # add error Unable to load dynamic library 'oci8.so'
    && echo /opt/oracle/instantclient_19_8/ > /etc/ld.so.conf.d/oracle-insantclient.conf \
    && ldconfig
    # end

# add schedule sh
# COPY ./provisioning/schedule.sh /

# RUN ["chmod", "+x", "/schedule.sh"]

# RUN crontab -l | { cat; echo '* * * * * /schedule.sh > /var/www/rsu/storage/logs/schedule.log 2>&1'; } | crontab -

# COPY ./provisioning/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# COPY ./provisioning/laravel-schedule.conf /etc/supervisor/conf.d/laravel-schedule.conf

# COPY ./provisioning/entrypoint.sh /

# RUN ["chmod", "+x", "/entrypoint.sh"]

# ENTRYPOINT ["/entrypoint.sh"]