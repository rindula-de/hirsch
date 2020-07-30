#!/bin/bash
if [ "$HOSTNAME" = "rindula.de" ]; then
    cd /var/www/vhosts/rindula.de/hirsch.rindula.de/ || exit

    # Composer Update
    /opt/plesk/php/7.2/bin/php /usr/lib/plesk-9.0/composer.phar install --no-ansi --optimize-autoloader --no-interaction --no-plugins --no-progress --no-suggest

    # Cake
    ## Datenbank Migration
    /opt/plesk/php/7.2/bin/php ./bin/cake.php migrations migrate

    ## Cache leeren
    /opt/plesk/php/7.2/bin/php ./bin/cake.php cache clear_all
else
    git reset --hard
    git pull
    composer install --no-ansi --optimize-autoloader --no-interaction --no-plugins --no-progress --no-suggest
    chmod +x bin/cake
    chmod +x gogogo.sh
    bin/cake migrations migrate
    bin/cake cache clear_all
fi
