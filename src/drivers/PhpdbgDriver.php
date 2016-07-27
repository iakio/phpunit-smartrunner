<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\drivers;

class PhpdbgDriver implements Driver
{
    public function startCodeCoverage()
    {
        phpdbg_start_oplog();
    }

    public function stopCodeCoverage()
    {
        return phpdbg_end_oplog();
    }
}
