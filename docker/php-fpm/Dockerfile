FROM php:fpm-alpine

RUN set -ex && apk --no-cache add postgresql-dev openssl

RUN docker-php-ext-install  pdo_pgsql  

CMD ["php-fpm"]

EXPOSE 9000
