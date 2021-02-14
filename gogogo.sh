#!/bin/bash
/usr/bin/git reset --hard
/usr/bin/git pull
/usr/home/hochwa/.linuxbrew/bin/composer install --no-ansi --optimize-autoloader --no-interaction --no-plugins --no-progress --no-suggest

echo '' >> webroot/.htaccess
echo 'AuthType Basic' >> webroot/.htaccess
echo 'AuthName "Bestellungen"' >> webroot/.htaccess
echo 'AuthUserFile /usr/www/users/hochwa/hirsch/.htpasswd' >> webroot/.htaccess
echo 'Require valid-user' >> webroot/.htaccess

bin/cake migrations migrate
bin/cake cache clear_all

WD=$(pwd)

cd webroot/vue-apps/bezahlen
/usr/home/hochwa/.linuxbrew/bin/npm install
/usr/home/hochwa/.linuxbrew/bin/npm run build --modern
cd $WD
