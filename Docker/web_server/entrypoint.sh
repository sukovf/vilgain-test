#!/bin/bash

set -e

# install PHP dependencies
composer install

# run Apache server
apachectl -D FOREGROUND