#!/bin/bash

./generate --instance=default > Kinan/accounts.csv
echo "Generation complete, copied accounts to Kinan/accounts.csv"
echo

cd Kinan
echo "Running Kinan City..."
echo
java -jar KinanCity-core-1.3.2-SNAPSHOT.jar -a accounts.csv
echo
echo
echo "Kinan complete!"
