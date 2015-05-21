This folder structure is required for unit tests.

Tests that require the database and/or the browser should go in src/Tests/

Tests herein should extend UnitTestCase

They can be run with, for example:

./core/vendor/phpunit/phpunit/phpunit --bootstrap ./core/tests/bootstrap.php modules/realistic_dummy_content/api/tests/src/Unit/UnitTestCase.php
