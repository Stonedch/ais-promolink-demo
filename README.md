# Form Filler

Repository of the "Form Filler" application from Promolink.

## Table of content:

- [General info](#general-info)
- [Setup](#setup)
- [Data base dump loading](#db-dump-loading)
- [Make data base dump](#make-db-dump)
- [Reload containers](#reload-containers)
- [Contacts](#contacts)

## General info

Repository of the "Form Filler" application from Promolink.

## Setup

```console
$ cp .env.example .env (and configurate)
$ cp ./app/.env.example. env (and configurate)
$ cp ./docker/nginx/nginx.conf.example ./docker/nginx.conf (and configurate)
$ htpasswd -c ./docker/datalens/nginx/htpasswd promolink
$ docker-compose up -d --build
$ docker-compose exec laravel composer install
$ docker-compose exec laravel php artisan key:generate --ansi
$ docker-compose exec laravel php artisan migrate
$ docker-compose exec laravel php artisan orchid:admin
```

## Cron settings

```console
$ crontab -e (and configurate)
* * * * * cd /path/to/project && docker-compose exec -T laravel php artisan schedule:run >> /dev/null 2>&1
0 6 * * * cd /path/to/project && ./backup.sh
```

## Data base dump loading

```console
$ docker-compose exec -T db psql -U formfiller formfiller < dump.sql (dumping)
```

## Make data base dump

```console
$ docker-compose exec db pg_dump -U formfiller --data-only --column-inserts formfiller > dump_`date +%Y-%m-%d"_"%H_%M_%S`.sql
```

## Reload containers

```console
$ docker-compose down && docker-compose up -d --build
```

## Contacts

Created by [@stonedch](https://github.com/stonedch) and [@promolinkru](https://github.com/promolinkru)
