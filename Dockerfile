FROM php:8-fpm
WORKDIR /app
COPY . .
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 
RUN composer install
EXPOSE 9000
CMD ["php-fpm"]
