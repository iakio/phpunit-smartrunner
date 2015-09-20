<?php

require_once __DIR__.'/../src/BankAccount.php';
class BankAccountTest extends PHPUnit_Framework_TestCase
{
    public function test_deposit()
    {
        $ba = new BankAccount();
        $ba->deposit(100);
        $this->assertEquals(100, $ba->getBalance());
    }
}
