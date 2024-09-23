#!/usr/bin/env bash

. .env

TIMESTAMP="$(date +%Y-%m-%d-%H-%m-%s)";
BACKUP_FOLDER="./backups/backup-${TIMESTAMP}";
FILENAME="dump_${TIMESTAMP}.sql";
FILEPATH="${BACKUP_FOLDER}/${FILENAME}";

mkdir ${BACKUP_FOLDER};

docker-compose exec db pg_dump -U ${DB_USERNAME} --data-only --column-inserts ${DB_DATABASE} > ${FILEPATH};
