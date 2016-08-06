Feature:
    Background:
        When I initialize smartrunner
        Then ".smartrunner" directory should be created

    Scenario: Run with TestCase
        When I run smartrunner with argument "features/fixtures/tests/CalcTest.php"
        Then 1 test and 1 assertion should be executed

    Scenario: Run with SUT
        When I run smartrunner with argument "features/fixtures/tests/CalcTest.php"
        And I run smartrunner with argument "features/fixtures/tests/BankAccountTest.php"
        And I run smartrunner with argument "features/fixtures/src/Calc.php"
        Then 2 tests and 2 assertions should be executed
        And Cache should contain 4 entries

    @phpunit.phar
    Scenario: Using phpunit.phar
        Given I have phpunit.phar file
        And I set up my configuration file as
            """
            <?php
            return function ($config) {
                $config["phpunit"] = "php phpunit.phar --bootstrap=vendor/autoload.php";
                $config["ignore"] = [
                    "^vendor"
                ];
            };
            """
        When I run smartrunner with argument "features/fixtures/tests/CalcTest.php"
        And I run smartrunner with argument "features/fixtures/tests/BankAccountTest.php"
        And I run smartrunner with argument "features/fixtures/src/Calc.php"
        Then 2 tests and 2 assertions should be executed
        And Cache should contain 4 entries

    @phpdbg
    Scenario: Using phpdbg
        Given I set up my configuration file as
            """
            <?php
            return function ($config) {
                $config["phpunit"] = "phpdbg -qrr vendor/phpunit/phpunit/phpunit";
                $config["ignore"] = [
                    "^vendor"
                ];
            };
            """
        When I run smartrunner with argument "features/fixtures/tests/CalcTest.php"
        And I run smartrunner with argument "features/fixtures/tests/BankAccountTest.php"
        And I run smartrunner with argument "features/fixtures/src/Calc.php"
        Then 2 tests and 2 assertions should be executed
        And Cache should contain 4 entries
