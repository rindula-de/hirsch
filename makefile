SHELL = /bin/bash
COMPOSER = composer
YARN = yarn
NPX = npx
GIT = git
EXEC_PHP = php
ENV = dev
ifneq (, $(shell which ddev))
  # If we are in a ddev project, we need to use the ddev-composer
  # command to install dependencies.
  COMPOSER = ddev composer
  YARN = ddev yarn
  EXEC_PHP = ddev php
  NPX = ddev exec npx
endif
ifdef CI
    ENV = prod
endif
ifdef APP_ENV
	ENV = $(APP_ENV)
endif
SYMFONY = $(EXEC_PHP) bin/console
ARTIFACT_NAME = artifact.tar

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

msg: ## Run symfony message consumer
	$(SYMFONY) messenger:consume async -vv --time-limit=3600

install: all install_db  ## Install the project

all: vendor .env.local public/build .git/hooks/post-merge ## Install and build all dependencies

.git/lfs:
	git lfs install
	@touch .git/lfs

.git/hooks/post-merge: githooks/post-merge .git/lfs
	cp githooks/post-merge .git/hooks/post-merge

install_db: vendor .env.local migrations ## Install the database
	$(SYMFONY) doctrine:migrations:migrate --no-interaction

vendor: composer.json composer.lock
	$(COMPOSER) validate
	$(COMPOSER) install --prefer-dist --no-interaction
	@touch vendor

.env.local:
	@echo 'APP_ENV=$(ENV)' | tee .env.local
	@if [ -n "$(DBPASS)" ]; then echo 'DATABASE_URL="mysql://hirsch:$(DBPASS)@localhost:3306/hirsch?serverVersion=mariadb-10.6.4"' | tee -a .env.local; fi;
	@if [ -n "$(VERSION)" ]; then echo 'APP_VERSION="$(VERSION)"' | tee -a .env.local; fi;
	@if [ -n "$(CI)" ]; then grep -qxF 'FcgidWrapper "/home/httpd/cgi-bin/php82-fcgi-starter.fcgi" .php' public/.htaccess || echo 'FcgidWrapper "/home/httpd/cgi-bin/php82-fcgi-starter.fcgi" .php' | tee -a public/.htaccess; fi;

.env.test.local:
    @echo 'MAILER_DSN="null://null"' | tee -a .env.test.local;

node_modules node_modules/.bin/encore &: vendor
	$(YARN) install --force
	@touch node_modules

public/build: assets $(shell find assets -name '*') webpack.config.js node_modules
ifeq ($(CI), true)
	$(NPX) -y browserslist@latest --update-db
endif
	$(YARN) build
	@touch public/build

$(ARTIFACT_NAME):
	tar -cf "$(ARTIFACT_NAME)" .

tests_db: .env.test.local vendor
	$(SYMFONY) doctrine:database:drop --env=test --force || true
	$(SYMFONY) doctrine:database:create --env=test
	$(SYMFONY) doctrine:migrations:migrate --env=test --no-interaction

tests: export APP_ENV=test
tests: .git/lfs tests_db vendor
	$(EXEC_PHP) vendor/bin/phpstan
	$(EXEC_PHP) bin/phpunit

coverage.xml coverage-xml coverage-html &: tests_db
ifneq (, $(shell which ddev))
	ddev xdebug on
endif
	$(EXEC_PHP) -d xdebug.mode=coverage ./bin/phpunit --coverage-html coverage-html --coverage-xml coverage-xml --coverage-clover coverage.xml || true
ifneq (, $(shell which ddev))
	ddev xdebug off
endif

coverage_check: tests_db coverage.xml
	$(EXEC_PHP) ./bin/coverage-checker coverage.xml 50

infection_test: export APP_ENV=test
infection_test: tests_db coverage.xml
ifneq (, $(shell which ddev))
	$(error "Infection test is not supported on ddev")
endif
	$(EXEC_PHP) -d xdebug.mode=coverage ./vendor/bin/infection --only-covered --min-msi=98

clean: ## Clean up the project
	git clean -fdx

.PHONY: tests install msg help clean all infection_test tests_db coverage_check
