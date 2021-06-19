#!/bin/sh
echo $@
bash /entrypoint.sh $@
chown www-data:root /var/www/html/custom_apps/