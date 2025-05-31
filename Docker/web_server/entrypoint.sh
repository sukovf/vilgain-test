#!/bin/bash

set -e

# create working directories
mkdir -p ./var/log ./var/cache
chown www-data:www-data ./var/log ./var/cache

# install PHP dependencies
composer install

# generate the JWT keys
mkdir -p ./config/jwt
chown www-data:www-data ./config/jwt
php bin/console lexik:jwt:generate-keypair --skip-if-exists

# if the Doctrine migrations aren't up to date...
# wait for the DB to warmup
/usr/local/bin/wait_for_it.sh db_server:3306 -t 30

# run Doctrine migrations if the dev DB isn't up to date
if [[ $(php ./bin/console doctrine:migrations:up-to-date) != *"[OK]"* ]]; then
  php ./bin/console doctrine:migrations:migrate --no-interaction
fi

# run Doctrine migrations if the test DB isn't up to date
if [[ $(php ./bin/console doctrine:migrations:up-to-date --env=test) != *"[OK]"* ]]; then
  php ./bin/console doctrine:migrations:migrate --no-interaction --env=test
fi

# run Apache server
apachectl -D FOREGROUND