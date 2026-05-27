composer install --no-dev --no-scripts --optimize-autoloader
sudo php bin/console asset-map:compile
sudo php bin/console cache:clear