#!/usr/bin/env bash

. .env

TIMESTAMP="$(date +%Y-%m-%d-%H-%m-%s)";

BACKUP_FOLDER="${BACKUP_ROOT_FOLDER}/backup-${TIMESTAMP}";

FILENAME="dump_${TIMESTAMP}.sql";
FILEPATH="${BACKUP_FOLDER}/${FILENAME}";

mkdir ${BACKUP_ROOT_FOLDER}
mkdir ${BACKUP_FOLDER};

${DOCKER_COMPOSE_COMMAND} exec db pg_dump -U ${DB_USERNAME} ${DB_DATABASE} > ${FILEPATH};

if ${FTP_BACKUP_AVAILABLE}
    then curl -T ${FILEPATH} --ftp-create-dirs -u ${FTP_BACKUP_USERNAME}:${FTP_BACKUP_PASSWORD} ${FTP_BACKUP_HOST}
fi
