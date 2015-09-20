<?php

require_once __DIR__.'/../src/Calc.php';
class CalcTest extends PHPUnit_Framework_TestCase
{
    public function test_add()
    {
        $calc = new Calc();
        $this->assertEquals(3, $calc->add(1, 2));
    }
}
