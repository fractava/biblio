FROM nextcloud
#RUN cat /entrypoint.sh
#RUN echo "chown www-data:root /var/www/html/custom_apps/" >> /entrypoint.sh
#RUN sed -i '/^exec "$@".*/i chown -R www-data:root /var/www/html/custom_apps/' /entrypoint.sh
#RUN sed -i '/^exec "$@".*/i chmod -R 777 /var/www/html/custom_apps/' /entrypoint.sh
#RUN cat /entrypoint.sh
#COPY custom-entrypoint.sh /custom-entrypoint.sh
#RUN chmod +x /custom-entrypoint.sh
#ENTRYPOINT ["/custom-entrypoint.sh"]
#RUN ls -la  /var/www/html/
#RUN chown www-data:root /var/www/html/custom_apps/
RUN mkdir /var/www/html/custom_apps/
RUN chmod -R 777 /var/www/html/custom_apps/
