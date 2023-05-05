.PHONY: tests
tests:
	php bin/console doctrine:database:drop --force --if-exists --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update --force --complete --env=test
	php bin/console doctrine:fixtures:load --append --env=test

	php bin/phpunit --testdox tests/Unit/
	php bin/phpunit --testdox tests/Functional/