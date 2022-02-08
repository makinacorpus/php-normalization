#!/bin/bash
APP_DIR="`dirname $PWD`" docker-compose -p mnormalization up -d --build --remove-orphans --force-recreate
