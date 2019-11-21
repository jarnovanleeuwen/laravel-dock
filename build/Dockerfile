# Directory containing the source files
ARG SRC="./src"

#
# Apache + PHP
#
FROM php:7.2-apache as app

# Create non-root user
RUN useradd --create-home webdev

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y mariadb-client nano supervisor zip

# Install extensions
RUN docker-php-ext-install opcache pcntl pdo_mysql
# RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
# RUN docker-php-ext-install gd

# Use default PHP production configuration and enable Apache's mod_rewrite
RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini \
	&& a2enmod rewrite

# Override with custom PHP settings
COPY ./config/php/local.ini $PHP_INI_DIR/conf.d/local.ini

# Overwrite the default Apache vhost with the custom vhost
COPY ./config/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Add Supervisor config for Laravel Horizon
RUN rm -rf /var/log/supervisor ; mkdir -m 777 /var/log/supervisor
COPY ./config/supervisor/ /etc/supervisor/

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

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
RUN chmod -R 777 bootstrap/cache storage \
	&& composer install --no-interaction --no-plugins --prefer-dist --ansi

# Add test script for automated testing
COPY ./build/run_tests.sh /usr/local/bin/run_tests

# Switch default user back to root
USER root