#!/bin/bash

echo "Installing Composer dependencies..."
composer install

echo "Installing Node dependencies..."
npm install

echo "Application is now up and running."

php-fpm -D
tail -f /dev/null
