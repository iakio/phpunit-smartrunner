Feature: smartrunner
    Background:
        Given I have a file named "behat_tests/BankAccount.php" with:
          """
          <?php
          class BankAccount {
              protected $balance = 0;
              public function getBalance() {
                  return $this->balance;
              }
          }
          """
        And I have a file named "behat_tests/BankAccountTest.php" with:
          """
          <?php
          require_once "BankAccount.php";
          class BankAccountTest extends PHPUnit_Framework_TestCase {
              function setUp() {
                  $this->ba = new BankAccount();
              }
              function testBalanceIsInitiallyZero() {
                  $this->assertEquals(0, $this->ba->getBalance());
              }
          }
          """
        And I have a file named "behat_tests/AnotherTest.php" with:
          """
          <?php
          class AnotherTest extends PHPUnit_Framework_TestCase {
              function testAnother() {
                  $this->assertTrue(true);
              }
          }
          """
    Scenario: Run with no argument
        When I run a smartrunner with no argument
        Then I should see:
          """
          OK (2 tests, 2 assertions)
          """

    Scenario: Run with TestCase
        When I run a smartrunner with argument "behat_tests/BankAccountTest.php"
        Then I should see:
          """
          OK (1 test, 1 assertion)
          """
