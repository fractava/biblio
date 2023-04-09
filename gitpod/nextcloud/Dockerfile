FROM nextcloud

RUN apt-get update && apt-get install -y --no-install-recommends jq

COPY custom-entrypoint.sh /custom-entrypoint.sh
COPY after-install.sh /after-install.sh

RUN chmod +x /custom-entrypoint.sh
RUN chmod +x /after-install.sh

ENTRYPOINT ["/custom-entrypoint.sh"]

CMD ["/after-install.sh"]