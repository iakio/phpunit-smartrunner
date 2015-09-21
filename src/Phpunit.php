<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

class Phpunit
{
    public function __construct($phpunit_bin)
    {
        $this->phpunit_bin = $phpunit_bin;
    }

    public function exec($arg)
    {
        system($this->phpunit_bin.' '.$arg);
    }
}
