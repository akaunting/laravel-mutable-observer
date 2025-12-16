.PHONY: help test coverage analyse phpstan psalm format format-check infection quality install

# Default target
.DEFAULT_GOAL := help

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Install dependencies
	composer install

update: ## Update dependencies
	composer update

test: ## Run tests
	vendor/bin/phpunit

test-coverage: ## Run tests with coverage
	vendor/bin/phpunit --coverage-html coverage

phpstan: ## Run PHPStan static analysis
	vendor/bin/phpstan analyse --ansi

psalm: ## Run Psalm static analysis
	vendor/bin/psalm --show-info=true

analyse: phpstan psalm ## Run all static analysis tools

format: ## Fix code style issues
	vendor/bin/php-cs-fixer fix --ansi

format-check: ## Check code style without fixing
	vendor/bin/php-cs-fixer fix --dry-run --diff --ansi

infection: ## Run mutation testing
	vendor/bin/infection --ansi --min-msi=90 --min-covered-msi=95

quality: format-check analyse test ## Run all quality checks

ci: ## Run CI checks (no formatting)
	composer validate --strict
	vendor/bin/php-cs-fixer fix --dry-run --diff --ansi
	vendor/bin/phpstan analyse --ansi
	vendor/bin/psalm --show-info=false
	vendor/bin/phpunit

clean: ## Clean generated files
	rm -rf coverage
	rm -rf .phpunit.cache
	rm -rf .php-cs-fixer.cache
	rm -f infection.log
	rm -f infection-summary.log
	rm -f infection.json
