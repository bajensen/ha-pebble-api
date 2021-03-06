FROM bjensena/pag2471sc

ENV ENVIRONMENT docker
ENV HOME_ASSISTANT_URL http://localhost:8123
ENV HOME_ASSISTANT_PASSWORD hackme
ENV API_KEY hackmemore
ENV ENTITIES_FILE_PATH entities.default.php

COPY logs /opt/ha-pebble-api/logs
COPY public /opt/ha-pebble-api/public
COPY src /opt/ha-pebble-api/src
COPY composer.json /opt/ha-pebble-api/
COPY composer.lock /opt/ha-pebble-api/
COPY docker-entrypoint.sh /opt/ha-pebble-api/

WORKDIR /opt/ha-pebble-api/

RUN composer install

COPY docker-lookup.conf /etc/httpd/conf.d/

RUN rm /etc/httpd/conf.d/welcome.conf && \
    chmod +x /opt/ha-pebble-api/docker-entrypoint.sh

WORKDIR /opt/ha-pebble-api/public

COPY config /opt/ha-pebble-api/config

EXPOSE 80

ENTRYPOINT ["/opt/ha-pebble-api/docker-entrypoint.sh"]

CMD ["httpd-foreground"]