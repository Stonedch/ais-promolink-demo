# Как обновиться до последней стабильной версии

## Страницы

- [Главная](/README.md)

## Содержание

- [Переключение локальной репозитория на стабильную ветку](#переключение-локальной-репозитория-на-стабильную-ветку)
- [Выгрузка обновлений из репозитория](#выгрузка-обновлений-из-репозитория)
- [Выполнение миграция](#выполнение-миграций)
- [Выполнение скриптов конвертации](#выполнение-скриптов-конвертации)
- [Обновление кэша](#обновление-кэша)

## Переключение локальной репозитория на стабильную ветку

```console
$ git checkout main
```

## Выгрузка обновлений из репозитория

```console
$ git pull
```

## Выполнение миграций

```console
$ docker-compose exec laravel php artisan migrate
```

## Выполнение скриптов конвертации

```console
$ docker-compose exec laravel php artisan find-event-authors:run
$ docker-compose exec laravel php artisan saved-structure:convert
$ docker-compose exec laravel php artisan event-prepare:run
```

## Обновление кэша

```console
$ docker-compose exec laravel php artisan cache:clear
$ docker-compose exec laravel php artisan config:clear
$ docker-compose exec laravel php artisan view:cache
$ docker-compose exec laravel php artisan route:cache
```
