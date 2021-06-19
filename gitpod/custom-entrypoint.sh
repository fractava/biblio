#!/bin/sh
chmod -R 777 /var/www/html/custom_apps/
bash /entrypoint.sh $1
#chown www-data:root /var/www/html/custom_apps/