#!/bin/bash
set -e

# Install dependencies
composer install


# Wait for MySQL
echo "⏳ Waiting for MySQL to be ready..."
until nc -z "$DB_HOST" 3306; do
    echo "Waiting for MySQL..."
    sleep 2
done
echo "✅ MySQL is ready!"

# Run migrations
php /var/www/html/artisan migrate --seed

# Start Passed CMD
exec "$@"