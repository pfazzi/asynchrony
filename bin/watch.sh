#!/usr/bin/env bash

CURDIR=`/bin/pwd`
BASEDIR=$(dirname $0)
ABSPATH=$(readlink -f $0)
ABSDIR=$(dirname $ABSPATH)

SCRIPT_NAME=${ABSDIR}/../demo/demo_app.php

exit_script() {
    trap - SIGINT SIGTERM
    kill_script

}

kill_script() {
	 ps -ef | grep ${SCRIPT_NAME} | grep -v grep | awk '{print $2}' | xargs -r kill -9
}

start_script() {
	nohup php ${SCRIPT_NAME} &
#	tail -n 1 -f nohup.out
}

trap exit_script SIGINT SIGTERM

kill_script
start_script

while inotifywait -e modify ${ABSDIR}/../src/ -r; do
	kill_script
	start_script
done