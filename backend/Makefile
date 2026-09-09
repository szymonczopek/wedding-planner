ake pint.PHONY: pint pint-fix phpstan stan

pint:
	./vendor/bin/pint --test

pint-fix:
	./vendor/bin/pint

phpstan:
	./vendor/bin/phpstan analyse --memory-limit=512M app

stan: phpstan
