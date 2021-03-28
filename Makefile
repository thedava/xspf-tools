COMPOSER = php composer.phar
PHP = php

build-console:
	$(PHP) bin/build-console.php

build-phar:
	$(MAKE) build-console
	$(PHP) -d phar.readonly=0 bin/build-phar.php
	$(PHP) build/xspf.phar version -v

build-bundles:
	$(PHP) bin/build-bundles.php

release:
	$(COMPOSER) install --no-dev -o --ignore-platform-reqs --no-progress --prefer-dist --no-scripts
	$(MAKE) build-phar build-bundles
	cp -f LICENSE build/

lint:
	./bin/phplint.sh

dev:
	$(COMPOSER) self-update
	$(COMPOSER) install --ignore-platform-reqs

phpcs:
	$(COMPOSER) install --ignore-platform-reqs -q
	./bin/php-cs-fixer.sh
	$(PHP) php-cs-fixer.phar fix --config=php_cs.php
