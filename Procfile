web: vendor/bin/heroku-php-apache2 public/
release: mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache && chmod -R 775 storage bootstrap/cache && touch database/database.sqlite && php artisan migrate --force && php artisan db:seed --force
