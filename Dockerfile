FROM ubuntu:20.04
LABEL Maintainer="gonzalo <gonzalosantos89@gmail.com>" \
      Description="Lightweight container with Nginx 1.18 & PHP-FPM 8.0 based on Ubuntu."

ARG USER=nginx
ARG USER_GID=101
ARG USER_ID=101

RUN set -x \
# create nginx user/group first, to be consistent throughout docker variants
    && addgroup --system --gid ${USER_GID} ${USER} \
    && adduser --system --disabled-login --ingroup  ${USER} --no-create-home --home /nonexistent --gecos "${USER} user" --shell /bin/false --uid ${USER_ID} ${USER}

# Install basic system packages
RUN	export DEBIAN_FRONTEND=noninteractive \
	&& apt-get update && apt-get install -y --no-install-recommends \
	    build-essential \
        apt-transport-https \
        ca-certificates \
        curl \
        gnupg-agent \
        software-properties-common \
        bash \
        vim \
        sudo \
        nginx supervisor curl openssl

# php and extensions
RUN export DEBIAN_FRONTEND=noninteractive \
    && add-apt-repository ppa:ondrej/php \
	&& apt-get update && apt-get install -y --no-install-recommends \
        php8.0 \
        php8.0-fpm \
        php8.0-common \
        php8.0-mbstring \
        php8.0-intl\
        php8.0-zip \
        php8.0-xml \
        php8.0-curl \
        php8.0-sqlite \
        php8.0-xdebug \
    && apt-get autoremove -y && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# remove default server definition
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get autoremove -y && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Configure nginx
RUN rm -rf /etc/nginx/sites-available/*
RUN rm -rf /etc/nginx/sites-enabled/*
ADD ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
ADD ./docker/nginx/default.conf /etc/nginx/sites-available/default.conf
RUN ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/

# Configure PHP-FPM
ADD ./docker/php/fpm-pool.conf /etc/php/8.0/fpm/pool.d/www.conf
ADD ./docker/php/php.ini /etc/php/8.0/fpm/conf.d/custom.ini
ADD ./docker/php/xdebug.ini /etc/php/8.0/mods-available/xdebug.ini

# Configure supervisord
ADD ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log \
	&& ln -sf /dev/stderr /var/log/nginx/php8.0-fpm.log

# Make sure files/folders needed by the processes are accessable when they run under the nginx user
RUN chown -R nginx:nginx /run && \
  chown -R nginx:nginx /var/lib/nginx && \
  chown -R nginx:nginx /var/log/nginx

# remove passworrd for all sudoers
RUN echo "ALL ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Setup document root
RUN mkdir -p /var/www

# Add application
WORKDIR /var/www

# Copy proyect files
COPY / ./

# Install dependencies
RUN composer update --prefer-dist --optimize-autoloader --classmap-authoritative

# Set owner and permission for cache and logs folders
RUN chown -R nginx:nginx /var/www

# Expose the port nginx is reachable on
EXPOSE 9091

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
