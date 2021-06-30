# Directory containing the source files
ARG SRC="./src"

#
# Apache + PHP
#
FROM php:8.0-apache as app

# Install dependencies
RUN apt-get update && apt-get install -y git mariadb-client nano supervisor libpng-dev libjpeg-dev libicu-dev libxml2-dev libzip-dev zip gnupg2 ssl-cert redis-tools

# Install extensions
RUN pecl install redis xdebug

RUN docker-php-ext-configure gd --with-jpeg &&\
	docker-php-ext-configure intl && \
    docker-php-ext-configure zip && \
	docker-php-ext-install bcmath gd intl opcache pcntl pdo_mysql soap zip && \
	docker-php-ext-enable redis xdebug

# Create non-root user
RUN useradd --create-home webdev

# Use default PHP production configuration and enable Apache's mod_rewrite and mod_ssl.
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini && \
	chown -R webdev $PHP_INI_DIR/conf.d && \
	a2enmod ssl && \
	a2enmod rewrite

# Override with custom PHP settings
COPY ./config/php/php.ini $PHP_INI_DIR/conf.d/php.ini

# Overwrite the default Apache vhost with the custom vhost
COPY ./config/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Add Supervisor config for Laravel Horizon
COPY ./config/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy useful scripts (e.g. scripts that help waiting for services to boot)
COPY ./build/bin/ /usr/local/bin/

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Custom entrypoint to run the app as scheduler or queue worker
COPY ./build/docker-entrypoint /usr/local/bin/
CMD ["/usr/local/bin/docker-entrypoint"]


#
# Production build
#
FROM app as production
ARG SRC

# Switch user
USER webdev

# Copy existing application directory contents and change
# ownership of the application files so that they are not
# writable by Apache's www-user
COPY --chown=webdev:webdev $SRC ./

# Set proper permissions + install dependencies using Composer
RUN touch storage/logs/laravel.log \
    && chmod -R 777 bootstrap/cache storage \
	&& composer install --no-interaction --no-plugins --prefer-dist --ansi

# Switch default user back to root
USER root
