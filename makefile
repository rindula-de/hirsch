SHELL = /bin/bash
COMPOSER = composer
NPM = npm
GIT = git
EXEC_PHP = php
ENV = prod
ifdef WT_PROFILE_ID
  # If we are in a ddev project, we need to use the ddev-composer
  # command to install dependencies.
  COMPOSER = ddev composer
  NPM = ddev exec npm
  EXEC_PHP = ddev exec php
  ifeq (, $(shell which ddev))
    COMPOSER = composer
    NPM = npm
    EXEC_PHP = php
  endif
  ENV = dev
endif
SYMFONY = $(EXEC_PHP) bin/console
ARTIFACT_NAME = artifact.tar
MAKEFLAGS := --jobs=$(shell nproc)

help: ## Outputs this help screen
        @grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

msg:
	$(SYMFONY) messenger:consume async -vv --time-limit=3600

install: vendor .env.local.php public/build/manifest.json ## Install all neccecary dependencies

vendor vendor/autoload.php: | composer.json composer.lock
	$(COMPOSER) validate
	$(COMPOSER) install --prefer-dist --no-interaction

.env.local:
	@if [ -n "$(DBPASS)" ]; then echo 'DATABASE_URL="mysql://hirsch:$(DBPASS)@localhost:3306/hirsch?serverVersion=mariadb-10.6.4"' | tee .env.local; fi;
	@echo 'APP_ENV=prod' | tee -a .env.local
	@if [ -n "$(SALT)" ]; then echo 'APP_SECRET="$(SALT)"' | tee -a .env.local; fi;
	@echo 'MailAccess_host="{sslin.df.eu/imap/ssl}INBOX"' | tee -a .env.local
	@echo 'MailAccess_username="essen@hochwarth-e.com"' | tee -a .env.local
	@if [ -n "$(EMAILPASS)" ]; then echo 'MailAccess_password="$(EMAILPASS)"' | tee -a .env.local; echo 'EMAILPASS="$(EMAILPASS)"' | tee -a .env.local; fi;
	@echo 'EMAILUSER="essen@hochwarth-e.com"' | tee -a .env.local
	@echo 'MAILER_DSN=smtp://sslout.df.eu:465' | tee -a .env.local
	@if [ -n "$(VERSION)" ]; then echo 'APP_VERSION="$(VERSION)"' | tee -a .env.local; fi;
	@if [ -n "$(HT_USER)" ]; then echo 'HT_USERNAME="$(HT_USER)"' | tee -a .env.local; fi;
	@if [ -n "$(HT_PASS)" ]; then echo 'HT_PASSWORD="$(HT_PASS)"' | tee -a .env.local; fi;
	@if [ -n "$(MS_GRAPH_TENANT)" ]; then echo 'MS_GRAPH_TENANT="$(MS_GRAPH_TENANT)"' | tee -a .env.local; fi;
	@if [ -n "$(MS_GRAPH_CLIENT_SECRET)" ]; then echo 'MS_GRAPH_CLIENT_SECRET="$(MS_GRAPH_CLIENT_SECRET)"' | tee -a .env.local; fi;
	@if [ -n "$(MS_GRAPH_CLIENT_ID)" ]; then echo 'MS_GRAPH_CLIENT_ID="$(MS_GRAPH_CLIENT_ID)"' | tee -a .env.local; fi;
	@if [ -z "$(WT_PROFILE_ID)" ]; then grep -qxF 'FcgidWrapper "/home/httpd/cgi-bin/php80-fcgi-starter.fcgi" .php' public/.htaccess || echo 'FcgidWrapper "/home/httpd/cgi-bin/php80-fcgi-starter.fcgi" .php' | tee -a public/.htaccess; fi;


.env.local.php: .env.local
	$(COMPOSER) dump-env $(ENV) --no-interaction

node_modules node_modules/.bin node_modules/.bin/encore:
	$(NPM) ci

public public/build public/build/manifest.json: node_modules/.bin/encore vendor/autoload.php | assets/app.js assets/styles/app.scss assets/js/menu.js assets/js/orders.js assets/js/scripts.js
	$(NPM) run build

$(ARTIFACT_NAME):
	tar -cf "$(ARTIFACT_NAME)" .
