<?php
require_once __DIR__ . '/../src/Calc.php';
class OtherTest extends PHPUnit_Framework_TestCase
{
    function test_other() {
        $calc = new Calc;
        $this->assertEquals(30, $calc->add(10, 20));
    }
}
