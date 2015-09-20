<?php
namespace iakio\phpunit\smartrunner;

class Phpunit
{
    public function __construct($phpunit_bin)
    {
        $this->phpunit_bin = $phpunit_bin;
    }

    public function exec($arg)
    {
        system($this->phpunit_bin . ' ' . $arg);
    }
}
