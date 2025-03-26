#!/usr/bin/env bash

. .env

TIMESTAMP="$(date +%Y-%m-%d-%H-%m-%s)";

BACKUP_FOLDER="${BACKUP_ROOT_FOLDER}/backup-${TIMESTAMP}";

INSERTS_FILENAME="inserts-${TIMESTAMP}.sql";
INSERTS_FILEPATH="${BACKUP_FOLDER}/${INSERTS_FILENAME}";

SCHEMA_FILENAME="schema-${TIMESTAMP}.sql";
SCHEMA_FILEPATH="${BACKUP_FOLDER}/${SCHEMA_FILENAME}";

mkdir -p ${BACKUP_ROOT_FOLDER}
mkdir -p ${BACKUP_FOLDER};

${DOCKER_COMPOSE_COMMAND:-docker-compose} exec -T db pg_dump -a --inserts --attribute-inserts -U ${DB_USERNAME} ${DB_DATABASE} > ${INSERTS_FILEPATH};
${DOCKER_COMPOSE_COMMAND:-docker-compose} exec -T db pg_dump --schema-only -U ${DB_USERNAME} ${DB_DATABASE} > ${SCHEMA_FILEPATH};

if ${FTP_BACKUP_AVAILABLE}; then
    curl -T ${INSERTS_FILEPATH} --ftp-create-dirs -u ${FTP_BACKUP_USERNAME}:${FTP_BACKUP_PASSWORD} ${FTP_BACKUP_HOST};
    curl -T ${SCHEMA_FILEPATH} --ftp-create-dirs -u ${FTP_BACKUP_USERNAME}:${FTP_BACKUP_PASSWORD} ${FTP_BACKUP_HOST};
fi
