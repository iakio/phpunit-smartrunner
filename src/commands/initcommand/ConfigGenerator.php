<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\initcommand;

use iakio\phpunit\smartrunner\SmartRunner;
use iakio\phpunit\smartrunner\FileSystem;

class ConfigGenerator
{
    public function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    private function findPhpunit()
    {
        if ($this->fs->fileExists('vendor/bin/phpunit')) {
            return implode(DIRECTORY_SEPARATOR, ['vendor', 'bin', 'phpunit']);
        }
        if ($this->fs->fileExists('phpunit.phar')) {
            return 'php phpunit.phar';
        }

        return '';
    }

    public function generate()
    {
        $default = SmartRunner::defaultConfig();
        $phpunit_path = $this->findPhpunit();
        if ($phpunit_path) {
            $default['phpunit'] = $phpunit_path;
        }

        return $default;
    }
}
