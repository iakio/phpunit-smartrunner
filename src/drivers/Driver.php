<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\drivers;

interface Driver
{
    public function startCodeCoverage();
    public function stopCodeCoverage();
}
