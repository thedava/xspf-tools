#!/usr/bin/env bash

if [[ -f 'php-cs-fixer.phar' ]]; then
    php php-cs-fixer.phar self-update
else
    php -r 'file_put_contents("php-cs-fixer.phar", file_get_contents("http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar"));';
fi
