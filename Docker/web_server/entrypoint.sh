#!/bin/bash

set -e

# create working directories
mkdir -p ./var/log ./var/cache
chown www-data:www-data ./var/log ./var/cache

# install PHP dependencies
composer install

# run Apache server
apachectl -D FOREGROUND