#!/bin/sh
echo "$1"
echo "before chmod"
chmod -R 777 /var/www/html/custom_apps/
echo "between"
/entrypoint.sh "$@"
echo "after"
#chown www-data:root /var/www/html/custom_apps/