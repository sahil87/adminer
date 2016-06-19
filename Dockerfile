FROM php:5-apache

#Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '070854512ef404f16bac87071a6db9fd9721da1684cd4589b1196c3faf71b9a2682e2311b36a5079825e155ac7ce150d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;";\
    php composer-setup.php; \
    php -r "unlink('composer-setup.php');"

#Install postgresql php extension
RUN apt-get update \
    && apt-get install -y php5-pgsql libpq-dev \
    && docker-php-ext-install -j$(nproc) pgsql

COPY . /var/www/html

RUN cd /var/www/html \
    && php composer.phar install \
    && php compile.php \
    && mv adminer*.php index.php \
    && cp designs/hever/adminer.css .
CMD ["apachectl", "-f", "/etc/apache2/apache2.conf", "-e", "info", "-DFOREGROUND"]
