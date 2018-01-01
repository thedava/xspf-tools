#!/usr/bin/env bash

find ./bin -name "*.php" -type f -print0 | xargs -n 1 -0 php -l
find ./src -name "*.php" -type f -print0 | xargs -n 1 -0 php -l
find ./test -name "*.php" -type f -print0 | xargs -n 1 -0 php -l
