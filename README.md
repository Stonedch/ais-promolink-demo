# АИС Промолинк - Автоматизированная информационная система

> [!WARNING]
> **Демонстрационная версия**  
> Данная сборка является демоверсией программного обеспечения.  
> Функционал может быть ограничен, а стабильность работы не гарантируется.  

Система для централизованного сбора отчетов, их проверки руководящими лицами и визуализации данных через дашборды с горячим обновлением, включая конструктор DataLens.

## Основное содержание

- [Основная информация](#основная-информация)
- [Требования к системе](#требования)
- [Установка и настройка](#установка)
  - [Установка демонстрационной версии](#установка-демонстрационной-версии)
  - [Совмещенная установка с DataLens](#совмещенный-с-datalens)
  - [Раздельная установка с DataLens и Zitadel](#раздельный-с-datalens-и-zitadel)
- [Настройка cron-задач](#настройка-cron-задач)
- [Работа с базой данных](#работа-с-базой-данных)
  - [Импорт данных](#импорт-данных)
  - [Экспорт данных](#экспорт-данных)
- [Управление сервисом](#управление-сервисом)
- [Дополнительные материалы](#дополнительные-материалы)
- [Контакты](#контакты)

---

## Основная информация

АИС Промолинк - это система для:
- Централизованного сбора отчетов от министерств Нижегородской области
- Проверки отчетов руководящими лицами
- Визуализации данных через интерактивные дашборды
- Интеграции с конструктором дашбордов DataLens

## Требования

Для работы системы необходимы:
- Docker (≥24.0.5)
- Docker Compose (≥1.29.2)
- Дополнительные утилиты:
  - zip (≥3.0)
  - cron
  - apache2-utils
  - curl

## Установка

### Установка демонстрационной версии

1. Настройка окружения:
```bash
cp .env.datalens.example .env  # (обязательно ввести USER, UID, GUID)
cp ./app/.env.example ./app/.env
cp ./docker/nginx/nginx.without.ssl.conf.example ./docker/nginx/nginx.conf
```
2. Запуск и инициализация:
```bash
docker-compose -f docker-compose.datalens.yml up -d --build
docker-compose -f docker-compose.datalens.yml exec laravel composer install
docker-compose -f docker-compose.datalens.yml exec laravel php artisan key:generate --ansi
docker-compose -f docker-compose.datalens.yml exec laravel php artisan storage:link
docker-compose -f docker-compose.datalens.yml exec laravel php artisan migrate
docker-compose exec -T db psql -U username database < demo-dump.sql
```
3. Логин и пароль: +7 (999) 999-99-99 / password

### Совмещенный с DataLens

1. Настройка окружения:
```bash
cp .env.datalens.example .env  # и настройте файл
cp ./app/.env.example ./app/.env  # и настройте файл
cp ./docker/nginx/nginx.without.ssl.conf.example ./docker/nginx/nginx.conf
htpasswd -c ./docker/datalens/nginx/htpasswd promolink
```
2. Запуск и инициализация:
```bash
docker-compose -f docker-compose.datalens.yml up -d --build
docker-compose -f docker-compose.datalens.yml exec laravel composer install
docker-compose -f docker-compose.datalens.yml exec laravel php artisan key:generate --ansi
docker-compose -f docker-compose.datalens.yml exec laravel php artisan storage:link
docker-compose -f docker-compose.datalens.yml exec laravel php artisan migrate
docker-compose -f docker-compose.datalens.yml exec laravel php artisan orchid:admin
docker-compose exec laravel php artisan seeding:roles
```

### Раздельный с DataLens и Zitadel

1. Настройка АИС
1.1. Настройка окружения:
```bash
cp .env.example .env  # и настройте файл
cp ./app/.env.example ./app/.env  # и настройте файл
cp ./docker/nginx/nginx.without.ssl.conf.example ./docker/nginx/nginx.conf
```
1.2. Запуск и инициализация:
```bash
docker-compose up -d --build
docker-compose exec laravel composer install
docker-compose exec laravel php artisan key:generate --ansi
docker-compose exec laravel php artisan storage:link
docker-compose exec laravel php artisan migrate
docker-compose exec laravel php artisan orchid:admin
docker-compose exec laravel php artisan seeding:roles
```
2. Настройка DataLens/Zitadel
2.1. Запуск и инициализация
```bash
cd ./datalens
./init.fixed.sh  # или оригинальный ./init.sh
docker-compose -f docker-compose.zitadel.yml down
sudo rm -rf metadata
```
2.2. Настройте SSH-туннель:
```bash
ssh -L 8085:localhost:8085 user@form-filler.com
```
2.3. После запуска Zitadel необходимо:
  - Войти в панель (http://hostname:8085)
  - Использовать учетные данные по умолчанию (zitadel-admin@zitadel.localhost/Password1!)
  - Использовать учетные данные по умолчанию (zitadel-admin@zitadel.localhost/Password1!)
  - Сменить пароль
  - Добавить redirect URI в настройках проекта:
```text
http://80.87.199.97:8080/api/auth/callback
http://80.87.199.97:8080/auth
```

## Настройка cron-задач

Добавьте в crontab:

```bash
* * * * * cd /path/to/project && docker-compose exec -T laravel php artisan schedule:run >> /dev/null 2>&1
0 6 * * * cd /path/to/project && ./backup.sh
0 2 1 * * cd /path/to/project && ./backup-archive.sh
```

## Работа с базой данных

### Импорт данных

```bash
docker-compose exec -T db psql -U formfiller formfiller < dump.sql
```

### Экспорт данных

```bash
docker-compose exec db pg_dump -U <username> --data-only --column-inserts --exclude-table-data='public.migrations' <database> > dump_`date +%Y-%m-%d"_"%H_%M_%S`.sql
# или полный дамп:
docker-compose exec db pg_dump -U <username> <database> > dump_`date +%Y-%m-%d"_"%H_%M_%S`.sql
```

---

## Управление сервисом

Перезагрузка контейнеров:


```bash
docker-compose down && docker-compose up -d --build
```

---

## Дополнительные материалы

- [Инструкция по созданию бота Telegram](/readmy/HOW_TO_CREATE_TG_BOT.md)
- [Как обновиться до последней стабильной версии](/readmy/HOW_UPDATE_TO_LATEST_STABILITY_VERSION.md)
- [Как обновиться до последней нестабильной версии](/readmy//HOW_UPDATE_TO_LATEST_UNSTABILITY_VERSION.md)

## Контакты

Разработчики:

- [@stonedch](https://github.com/stonedch)
- [@promolinkru](https://github.com/promolinkru)

