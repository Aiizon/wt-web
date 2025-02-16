# Build JS
FROM node:22.11.0-alpine3.20 AS build-stage

WORKDIR /app
COPY package*.json ./

RUN yarn --silent

COPY . .

RUN yarn build

FROM webdevops/php-nginx:8.3 AS final-stage

# Dépendances système
RUN apt update && apt upgrade
RUN apt install curl vim bash

# Copie des fichiers du projet
WORKDIR /app
COPY . .

RUN mkdir -p /var/log/php /var/cache/php
RUN chmod -R 777 /var/log/php /var/cache/php

COPY ./.env.dist .env

# Installation des dépendances PHP
ENV COMPOSER_Avhost.confLLOW_SUPERUSER=1
RUN composer install --no-interaction --no-progress --optimize-autoloader --ignore-platform-reqs --quiet
COPY --from=build-stage /app/public/build public/build

RUN echo "error_log = /var/log/php/error.log" >> /opt/docker/etc/php/php.ini

RUN mkdir -p /var/cache/nginx/client_temp
RUN chmod -R 777 /var/cache/nginx/client_temp

RUN mkdir -p /var/cache/php
RUN chmod -R 777 /var/cache/php

RUN mkdir -p /run/nginx
COPY ./vhost.conf /opt/docker/etc/nginx/main.conf

RUN chown -R nobody /app

EXPOSE 80