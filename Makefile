COMPOSER = php composer.phar
PHP = php

build-phar:
	$(PHP) bin/build-console.php
	$(PHP) -d phar.readonly=0 bin/build-phar.php
	$(PHP) build/xspf.phar version -v


release:
	$(COMPOSER) install --no-dev -o --ignore-platform-reqs --no-progress --prefer-dist --no-scripts
	$(MAKE) build-phar


lint:
	./bin/phplint.sh


dev:
	$(COMPOSER) self-update
	$(COMPOSER) install --ignore-platform-reqs


phpcs:
	$(COMPOSER) install --ignore-platform-reqs -q
	./bin/php-cs-fixer.sh
	$(PHP) php-cs-fixer.phar fix --config=php_cs.php
