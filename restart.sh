#!/bin/bash

function findInstancePid()
{
    instancePid=$(ps axf | grep runserver.py | grep -v grep | grep "$2" | awk '{ print $1 }')
}

findInstancePid

if [ -z "$instancePid" ]; then
    echo "Instance $2 is not running."
else
    echo -n "Stopping instance $2..."
    tmux kill-session -t "scan_$2"

    sleep 2s
    findInstancePid

    if [ -z "$instancePid" ]; then
        echo "Done!"
    else
        echo "Failed... Oh well."
    fi
fi

echo "Restarting $2 in new tmux session..."
cd "$1"
tmux new-session -s "scan_$2" -d ./runserver.py -cf "config/$2.ini"
