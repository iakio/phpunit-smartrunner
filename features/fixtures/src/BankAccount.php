<?php

require_once __DIR__.'/Calc.php';

class BankAccount
{
    private $balance = 0;
    private $calc;

    public function __construct()
    {
        $this->calc = new Calc;
    }

    public function deposit($n)
    {
        $this->balance = $this->calc->add($this->balance, $n);
    }

    public function getBalance()
    {
        return $this->balance;
    }
}
