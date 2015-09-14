@pharbuild
Feature:
    I want to use smartrunner without composer installation
    Background:
        Given I run smartrunner.phar with argument "init"

    Scenario: Run smartrunner.phar
        Given I have phpunit.phar file
        And I have ".smartrunner/bootstrap_smartrunner.php" file as
          """
          <?php
          require "phar://smartrunner.phar/vendor/autoload.php";
          """
        And I set up my configuration file as
          """
          {
              "phpunit": "php phpunit.phar --bootstrap=.smartrunner/bootstrap_smartrunner.php",
              "cacheignores": []
          }
          """
        When I run smartrunner.phar with argument "run features/fixtures/tests/CalcTest.php"
        And I run smartrunner.phar with argument "run features/fixtures/tests/OtherTest.php"
        And I run smartrunner.phar with argument "run features/fixtures/src/Calc.php"
        Then 2 tests and 2 assertions should be executed
