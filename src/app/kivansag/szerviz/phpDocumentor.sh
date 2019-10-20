#!/bin/sh
php app/kivansag/szerviz/phpDocumentor.phar -d . -t app/kivansag/www/phpdoc
find output -delete
