FROM php:cli

RUN apt-get update && apt-get -y install libpq-dev
RUN docker-php-ext-install  pdo_pgsql pcntl

WORKDIR /usr/src
ENTRYPOINT ["php", "app/kivansag/szerviz/hatterfolyamat.php"]

