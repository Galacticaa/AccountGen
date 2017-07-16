#!/bin/bash

mailPid=$(ps axf | grep "KinanCity-mail" | grep -v grep | awk '{ print $1 }')

if [ -z "$mailPid" ]; then
    echo "KinanMail isn't running! Time to fix that..."

    cd Kinan
    tmux new-session -s KinanMail -d sudo java -jar KinanCity-mail-1.3.2-SNAPSHOT.jar

    # Rest a bit to make sure it kicks in
    cd .. && sleep 3s
fi


corePid=$(ps axf | grep "KinanCity-core" | grep -v grep | awk '{ print $1 }')

if [ -z "$corePid" ]; then
    accounts=$(php get-incomplete.php)

    if [ $? -ne 0 ]; then
        echo "All accounts have been created!"
        exit
    fi

    echo "Saving accounts to Kinan/accounts.csv..."

    cd Kinan
    echo "$accounts" > accounts.csv
    tmux new-session -s KinanCore-d java -jar KinanCity-core-1.3.2-SNAPSHOT.jar -a accounts.csv
else
    echo "Kinan is busy!"
fi
