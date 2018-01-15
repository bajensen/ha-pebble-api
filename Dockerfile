FROM bjensena/pag2471sc

COPY logs /opt/ha-pebble-api/logs
COPY public /opt/ha-pebble-api/public
COPY src /opt/ha-pebble-api/src
COPY view /opt/ha-pebble-api/view
COPY composer.json /opt/ha-pebble-api/
COPY composer.lock /opt/ha-pebble-api/
COPY docker-entrypoint.sh /opt/ha-pebble-api/

WORKDIR /opt/ha-pebble-api/

RUN composer install

COPY config /etc/httpd/conf.d/

RUN rm /etc/httpd/conf.d/welcome.conf && \
    chmod +x /opt/ha-pebble-api/docker-entrypoint.sh

WORKDIR /opt/ha-pebble-api/public

EXPOSE 80

ENTRYPOINT ["/opt/ha-pebble-api/docker-entrypoint.sh"]

CMD ["httpd-foreground"]