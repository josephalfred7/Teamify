#!/bin/bash

cd /var/www/html/Teamify/teamify
composer install --no-plugins --no-scripts --ignore-platform-reqs
php artisan migrate:fresh --seed --force
