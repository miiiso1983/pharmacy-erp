web: vendor/bin/heroku-php-apache2 public/
release: php artisan config:cache && php artisan migrate --force && php artisan db:seed --force
