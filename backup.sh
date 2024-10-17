#!/usr/bin/env bash

. .env

TIMESTAMP="$(date +%Y-%m-%d-%H-%m-%s)";

BACKUP_FOLDER="${BACKUP_ROOT_FOLDER}/backup-${TIMESTAMP}";

FILENAME="dump_${TIMESTAMP}.sql";
FILEPATH="${BACKUP_FOLDER}/${FILENAME}";

mkdir ${BACKUP_ROOT_FOLDER}
mkdir ${BACKUP_FOLDER};

${DOCKER_COMPOSE_COMMAND} exec db pg_dump -U ${DB_USERNAME} --data-only --column-inserts ${DB_DATABASE} > ${FILEPATH};
