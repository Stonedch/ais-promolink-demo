#!/usr/bin/env bash

. .env

TIMESTAMP="$(date +%Y-%m-%d-%H-%m-%s)";

FILENAME="dump-archive-${TIMESTAMP}.zip";
FILEPATH="${BACKUP_ARCHIVE_ROOT_FOLDER}/${FILENAME}";

mkdir ${BACKUP_ROOT_FOLDER}
mkdir ${BACKUP_ARCHIVE_ROOT_FOLDER}

rm -rf ${BACKUP_ARCHIVE_ROOT_FOLDER}/*
zip -r ${FILEPATH} ${BACKUP_ROOT_FOLDER}/*
rm -rf ${BACKUP_ROOT_FOLDER}/*