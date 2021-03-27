#!/usr/bin/env bash

php xspf.phar batch "./Batch.yml"

echo
echo
echo "Begin interactive conflict solving"

php xspf.phar duplicates:show -i --no-progress -- duplicates.txt
