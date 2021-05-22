FROM php:8.0-fpm-alpine

#add required packages
RUN apk --update add --no-cache \
    bash \
    libjpeg libjpeg-turbo-utils libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev libpng-dev freetype-dev \
    libxslt-dev \
    gettext-dev \
    icu-dev \
    php8-bcmath \
    php8-xml \
    php8-iconv \
    php8-redis \
    php8-xsl \
    php8-zip \
    php8-pdo_mysql \
    php8-soap \
    php8-pecl-redis \
    php8-opcache \
    php8-intl \
    php8-sockets \
    php8-imap \
    libzip-dev \
    zip \
    unzip \
    nano \
    curl \
    git \
    wget \
    openssh \
    composer \
    rsync \
    patch \
    nodejs \
    npm \
    libsodium-dev \
    imap-dev \
    sudo \
    openssl-dev \
    file

RUN apk upgrade \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure imap --with-imap --with-imap-ssl \
    && docker-php-ext-install imap

RUN docker-php-ext-install -j$(nproc) \
	gd \
	soap \
	bcmath \
	xsl \
	zip \
	intl \
	pdo_mysql \
	mysqli \
	opcache \
	sockets \
	> /dev/null
#
# PHP Configuration
#

ENV PHP_INI=/usr/local/etc/php/php.ini
RUN cp /etc/php8/php.ini $PHP_INI
RUN \
    sed 's,;always_populate_raw_post_data,always_populate_raw_post_data,g' -i $PHP_INI && \
    sed 's,memory_limit = 128M,memory_limit = 3G,g' -i $PHP_INI && \
    sed 's,;sendmail_path =,sendmail_path ="/usr/sbin/sendmail -S vendor-mailhog:1025",g' -i $PHP_INI && \
    sed 's,;max_input_vars = 1000,max_input_vars = 10000,g' -i $PHP_INI && \
    sed 's,;session.save_path = "/tmp",session.save_path = "/tmp",g' -i $PHP_INI && \
    sed 's,;zlib.output_compression = Off, zlib.output_compression = On,g' -i $PHP_INI

RUN \
    echo "alias ll='ls -alh'" >> ~/.bashrc && \
    echo "alias dir='ls -alh'" >> ~/.bashrc && \
    echo "alias pico='nano -c'" >> ~/.bashrc;

#get laravel to be ready
RUN composer global require "laravel/installer"
RUN echo "export PATH=~/.composer/vendor/bin:\$PATH" >> ~/.bashrc
RUN source ~/.bashrc

##setup binance
RUN mkdir /var/www/binance

CMD ["php-fpm"]
