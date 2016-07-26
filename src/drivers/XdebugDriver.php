<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\drivers;

class XdebugDriver implements Driver
{
    public function startCodeCoverage()
    {
        xdebug_start_code_coverage();
    }

    public function stopCodeCoverage()
    {
        $data = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        return $data;
    }
}
