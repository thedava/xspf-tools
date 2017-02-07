#!/usr/bin/env bash

find ./bin -name "*.php" -type f | xargs -n 1 php -l
find ./src -name "*.php" -type f | xargs -n 1 php -l
find ./test -name "*.php" -type f | xargs -n 1 php -l
