#!/usr/bin/env bash

set -e

# Determine and validate action
ACTION=$1
if [[ "${ACTION}" == "" ]]; then
    echo "No action given!"
    exit 1
elif [[ "${ACTION}" != "before" ]] && [[ "${ACTION}" != "run" ]] && [[ "${ACTION}" != "after" ]]; then
    echo "Action '${ACTION}' is not supported. Use before, run or after"
    exit 1
fi
echo "Action: ${ACTION}"

# Determine PHP version
PHP_VERSION=$(php bin/phpversion.php)
TARGET_VERSION="7.4"
echo "PHP Version: ${PHP_VERSION}"


# Lint PHP on all php versions (ignore the version)
if [[ "${ACTION}" == "run" ]]; then
    ./bin/phplint.sh
fi


# Install composer and run phpunit on the target version
if [[ "${PHP_VERSION}" == "${TARGET_VERSION}" ]]; then
    if [[ "${ACTION}" == "before" ]]; then
        composer check-platform-reqs
        composer install
    elif [[ "${ACTION}" == "run" ]]; then
        # Run PHPUnit
        ./vendor/bin/phpunit --disallow-test-output -v

    elif [[ "${ACTION}" == "after" ]]; then
        # Build a phar
        make build-phar

        # Validate the phar
        php build/xspf.phar version -v
        php build/xspf.phar self-update
        php build/xspf.phar self-update -f

        # Build bundles
        make build-bundles
    fi

# Download and run phar on all non-target versions
else
    composer install
    make build-phar
    php build/xspf.phar version -v
    make build-bundles
fi
