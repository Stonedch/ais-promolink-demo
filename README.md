# АИС

Репозиторий "АИС" от Promolink.

## Страницы

- [Инструкция по созданию бота Telegram](/readmy/HOW_TO_CREATE_TG_BOT.md)

## Содержания:

- [Основная информация](#основная-информация)
- [Требования](#требования)
- [Шаги установки](#шаги-установки)
- [Установка](#установка)
- [Настройка cron-задач](#настройка-cron-задач)
- [Импорт данных в базу](#импорт-данных-в-базу)
- [Экспорт данных из базы](#экспорт-данных-из-базы)
- [Перезагрузка контейнеров](#перезагрузка-контейнеров)
- [Контакты](#контакты)

## Основная информация

Репозиторий "АИС" от Promolink.

## Требования

- docker (>=24.0.5)
- docker-compose (>=1.29.2)
- zip (>=3.0)
- cron
- apache2-utils
- curl

## Шаги установки

- [Установить требуемый софт](#требования)
- [Настроить и поднять контейнеры](#установка)
- [Настроить cron-задачи](#Настройка-cron-задач)

## Установка

```console
$ cp .env.example .env (and configurate)
USER=user ($ whoami) - required
UID=1000 ($ echo $UID) - required
GID=1000 ($ echo $UID) - required
$ cp ./app/.env.example. env (and configurate)
$ cp ./docker/nginx/nginx.without.sll.conf.example ./docker/nginx.conf (and configurate)
$ htpasswd -c ./docker/datalens/nginx/htpasswd promolink
$ docker-compose up -d --build
$ docker-compose exec laravel composer install
$ docker-compose exec laravel php artisan key:generate --ansi
$ docker-compose exec laravel php artisan storage:link
$ docker-compose exec laravel php artisan migrate
$ docker-compose exec laravel php artisan orchid:admin
```

## Настройка cron-задач

```console
$ crontab -e (and configurate)
* * * * * cd /path/to/project && docker-compose exec -T laravel php artisan schedule:run >> /dev/null 2>&1
0 6 * * * cd /path/to/project && ./backup.sh
0 2 1 * * cd /path/to/project && ./backup-archive.sh
```

## Импорт данных в базу

```console
$ docker-compose exec -T db psql -U formfiller formfiller < dump.sql (dumping)
```

## Экспорт данных из базы

```console
$ docker-compose exec db pg_dump -U formfiller --data-only --column-inserts formfiller > dump_`date +%Y-%m-%d"_"%H_%M_%S`.sql
```

## Перезагрузка контейнеров

```console
$ docker-compose down && docker-compose up -d --build
```

## Контакты

Created by [@stonedch](https://github.com/stonedch) and [@promolinkru](https://github.com/promolinkru)
