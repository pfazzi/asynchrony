#!/usr/bin/env bash

CURDIR=`/bin/pwd`
BASEDIR=$(dirname $0)
ABSPATH=$(readlink -f $0)
ABSDIR=$(dirname $ABSPATH)

SCRIPT_NAME=${ABSDIR}/../demo/demo_app.php

ps -ef | grep ${SCRIPT_NAME} | grep -v grep | awk '{print $2}' | xargs -r kill -9
