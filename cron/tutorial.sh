#!/bin/bash

ROCKET_PATH=/rmap

echo "Searching for tutorial process..."

function findTutorialPid()
{
    tutPid=$(ps axf | grep runserver.py | grep -v grep | grep tutorial | awk '{ print $1 }')
}

findTutorialPid

if [ -z "$tutPid" ]; then
    echo "Tutorial not running."
else
    echo "Tutorial runner found! Terminating process..."
    kill -15 $tutPid

    sleep 5s
    findTutorialPid

    if [ -z "$tutPid" ]; then
        echo "Successfully killed tutorial process."
    else
        echo "Tutorial process still running! Sending SIGKILL..."
        kill -9 $tutPid
    fi
fi

echo
echo "Checking tutorial status of previous run..."
grep 'successfully spun' $ROCKET_PATH/tutorial.log | sed -e "s/\(.\)\+Account \([a-zA-Z0-9]\+\) \(.\)\+/\2/g" > /tmp/complete.csv
php checktut.php

echo
echo "Grabbing new tutorial accounts..."
php tutorial.php

if [ $? -ne 0 ]; then
    echo "All accounts have completed tutorial!"
else
    echo "Starting RocketMap to complete tutorials..."
    echo
    echo
    cd "$ROCKET_PATH"

    tmux new-session -s RMAPTUTORIAL -d ./runserver.py -cf config/tutorial.ini
fi
