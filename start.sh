#!/bin/bash

checkIfFirstStart()
{
    if [ ! -f /.notfirststart ]; then
        echo "first Start"
        firstStart
        echo "first start init done"
    fi
}
firstStart()
{
    if [ ! -f /config/www/config/ ]; then
        backupConfig
    fi

    clearConfigFolder

    mv /config-buildtime/* /config/
    mv /config-buildtime/.* /config/

    cp /config/nginx/custom-config-include.conf /etc/nginx/nginx.conf
    cp /config/nginx/fastcgi_params /etc/nginx/

    if [ ! -f /config-backup/ ]; then
        copyPersistentFilesBack
    fi

    touch /.notfirststart
    
    echo "first start init done"
}

clearConfigFolder()
{
    rm -f -R /config/*
}

backupConfig()
{
    echo "config backup"
    mkdir /config-backup/
    cp -r /config/ /config-backup/
}

copyPersistentFilesBack()
{
    echo "copy persistent files back"
    if [ -e /config-backup/config/www/config/config.inc.php ]; then
        mkdir /config/www/config/
        cp /config-backup/config/www/config/config.inc.php /config/www/config/config.inc.php
    fi
}

startPhp()
{
    echo "starting php ..."
    /usr/sbin/php-fpm7 &
    echo "php start done"
}

startNginx()
{
    echo "starting nginx ..."
    nginx &
    echo "nginx start done"
}


checkIfFirstStart
startPhp
startNginx

while true
do
    sleep 1
done
