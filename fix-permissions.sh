#!/bin/bash

# Fix Laravel permissions
cd /www/wwwroot/stg-dcpower.dharmap.com/project

# Set correct ownership
chown -R www-data:www-data storage bootstrap/cache

# Set correct permissions
chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan optimize:clear

echo "✓ Permissions fixed successfully!"
