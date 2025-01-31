# АИС

Репозиторий "АИС" от Promolink.

## Страницы

- [Инструкция по созданию бота Telegram](/readmy/HOW_TO_CREATE_TG_BOT.md)
- [Как обновиться до последней стабильной версии](/readmy/HOW_UPDATE_TO_LATEST_STABILITY_VERSION.md)
- [Как обновиться до последней нестабильной версии](/readmy//HOW_UPDATE_TO_LATEST_UNSTABILITY_VERSION.md)

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

### Совмещенный с datalens

```console
$ cp .env.datalens.example .env (and configurate)
USER=user ($ whoami) - required
UID=1000 ($ echo $UID) - required
GID=1000 ($ echo $UID) - required
$ cp ./app/.env.example. env (and configurate)
$ cp ./docker/nginx/nginx.without.sll.conf.example ./docker/nginx/nginx.conf (and configurate)
$ htpasswd -c ./docker/datalens/nginx/htpasswd promolink
$ docker-compose -f docker-compose.datalens.yml up -d --build
$ docker-compose -f docker-compose.datalens.yml exec laravel composer install
$ docker-compose -f docker-compose.datalens.yml exec laravel php artisan key:generate --ansi
$ docker-compose -f docker-compose.datalens.yml exec laravel php artisan storage:link
$ docker-compose -f docker-compose.datalens.yml exec laravel php artisan migrate
$ docker-compose -f docker-compose.datalens.yml exec laravel php artisan orchid:admin
```

### Раздельный с datalens (+zitadel)

#### Настройка и запуск АИС

```console
$ cp .env.example .env (and configurate)
USER=user ($ whoami) - required
UID=1000 ($ echo $UID) - required
GID=1000 ($ echo $UID) - required
$ cp ./app/.env.example. env (and configurate)
$ cp ./docker/nginx/nginx.without.sll.conf.example ./docker/nginx/nginx.conf (and configurate)
$ docker-compose up -d --build
$ docker-compose exec laravel composer install
$ docker-compose exec laravel php artisan key:generate --ansi
$ docker-compose exec laravel php artisan storage:link
$ docker-compose exec laravel php artisan migrate
$ docker-compose exec laravel php artisan orchid:admin
$ docker-compose exec laravel php artisan seeding:roles
```

#### Настройка и запуск Datalens/Zitadel

```console
$ cd ./datalens
$ ./init.fixed.sh (или оригинал ./init.sh)
$ docker-compose -f docker-compose.zitadel.yml down
$ sudo rm -rf metadata
```

* Если в процессе ./init.sh получаешь ошибку docker-compose, попробуй в алиас прописать "альтернативный" docker compose (alias docker-compose='docker compose')

прописать в .env ZITADEL_EXTERNALDOMAIN=form-filler.com

```console
$ HC=1 docker-compose -f docker-compose.network.zitadel.yml up -d
```

Настраиваем ssh-туннель на 8085 (локально)

```console
$ ssh -L 8085:localhost:8085 user@form-filler.com
```

1. Переходим в панель zitadel (http://hostname:8085)
1. Вводим дефолтные логин/пароль (zitadel-admin@zitadel.localhost/Password1!)
1. Меняем пароль
1. Переходим в "Projects"
1. В блоке "Applications" находим "Charts" и переходим
1. Переходим в "Redirect Settings"
1. Добавляем новые URIs (http://80.87.199.97:8080/api/auth/callback, )

```
http://80.87.199.97:8080/api/auth/callback (Redirect URIs)
http://80.87.199.97:8080/auth (Post Logout URIs)
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

            POSTGRES_DB: ${DB_DATABASE}
            POSTGRES_USER: ${DB_USERNAME}
```console
$ docker-compose exec db pg_dump -U <username> --data-only --column-inserts --exclude-table-data='public.migrations' <database> > dump_`date +%Y-%m-%d"_"%H_%M_%S`.sql
```

## Перезагрузка контейнеров

```console
$ docker-compose down && docker-compose up -d --build
```

## Контакты

Created by [@stonedch](https://github.com/stonedch) and [@promolinkru](https://github.com/promolinkru)
