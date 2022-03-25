# Setup ————————————————————————————————————————————————————————————————————————
SHELL         = bash
EXEC_PHP      = symfony php
COMPOSER      = symfony composer

## —— 🐝 The Makefile 🐝 ———————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Composer 🧙‍️ ————————————————————————————————————————————————————————————
install: composer.lock ## Install vendors according to the current composer.lock file
	$(COMPOSER) install --no-progress --no-suggest --prefer-dist --optimize-autoloader

update: composer.json ## Update vendors according to the composer.json file
	$(COMPOSER) update

## —— Tests ✅ —————————————————————————————————————————————————————————————————
test: test-phpunit test-phpmd test-phpcs ## Launch all tests

test-phpunit: ## Run phpunit tests
	${EXEC_PHP} ./vendor/bin/phpunit --stop-on-failure --testsuite unit-tests

test-phpmd:
	 ${EXEC_PHP} ./vendor/bin/phpmd src/ ansi phpmd.xml

test-phpcs:
	 ${EXEC_PHP} ./vendor/bin/phpcs src/ tests/ --colors -p
