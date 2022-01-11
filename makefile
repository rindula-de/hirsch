COMPOSER = composer
YARN = yarn
GIT = git
EXEC_PHP = php
ifdef WT_PROFILE_ID
  # If we are in a ddev project, we need to use the ddev-composer
  # command to install dependencies.
  COMPOSER = ddev composer
  YARN = ddev exec yarn
  EXEC_PHP = ddev exec php
endif
SYMFONY = $(EXEC_PHP) bin/console
ARTIFACT_NAME = artifact.tar

help: ## Outputs this help screen
        @grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

install: .env.local.php public/build/manifest.json

vendor/autoload.php:
	$(COMPOSER) validate
	$(COMPOSER) install --prefer-dist --no-interaction

env.local: vendor/autoload.php
	ifdef DBPASS
		@echo 'DATABASE_URL="mysql://hirsch:${ DBPASS }@localhost:3306/hirsch?serverVersion=mariadb-10.6.4"' > .env.local
	endif
	@echo 'APP_ENV=prod' >> .env.local
	ifdef SALT
		@echo 'APP_SECRET="${ SALT }"' >> .env.local
	endif
	@echo 'MailAccess_host="{sslin.df.eu/imap/ssl}INBOX"' >> .env.local
	@echo 'MailAccess_username="essen@hochwarth-e.com"' >> .env.local
	ifdef EMAILPASS
		@echo 'MailAccess_password="${ EMAILPASS }"' >> .env.local
		@echo 'EMAILPASS="${ EMAILPASS }"' >> .env.local
	endif
	@echo 'EMAILUSER="essen@hochwarth-e.com"' >> .env.local
	@echo 'POBOX="{sslin.df.eu/imap/ssl}INBOX/"' >> .env.local
	ifdef VERSION
		@echo 'APP_VERSION="${ VERSION }"' >> .env.local
	endif
	ifdef HT_USER
		@echo 'HT_USERNAME="${ HT_USER }"' >> .env.local
	endif
	ifdef HT_PASS
		@echo 'HT_PASSWORD="${ HT_PASS }"' >> .env.local
	endif
	@echo 'FcgidWrapper "/home/httpd/cgi-bin/php80-fcgi-starter.fcgi" .php' >> public/.htaccess

.env.local.php: env.local
	composer dump-env prod

node_modules/.bin/encore:
	npm ci

public/build/manifest.json: node_modules/.bin/encore
	npm run build

$(ARTIFACT_NAME):
	tar -cf "$(ARTIFACT_NAME)" .
