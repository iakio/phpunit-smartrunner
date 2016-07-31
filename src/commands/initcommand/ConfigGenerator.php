<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\initcommand;

use iakio\phpunit\smartrunner\Config;
use iakio\phpunit\smartrunner\SmartRunner;
use iakio\phpunit\smartrunner\FileSystem;

class ConfigGenerator
{
    /**
     * @var FileSystem
     */
    private $fs;

    public function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    private function findPhpunit()
    {
        $has_phpdbg = $this->fs->phpdbgExists();
        if ($this->fs->fileExists('vendor/bin/phpunit')) {
            if ($has_phpdbg) {
                return "phpdbg -qrr ".implode(DIRECTORY_SEPARATOR, ['vendor', 'phpunit', 'phpunit', 'phpunit']);
            } else {
                return implode(DIRECTORY_SEPARATOR, ['vendor', 'bin', 'phpunit']);
            }
        }
        if ($this->fs->fileExists('phpunit.phar')) {
            if ($has_phpdbg) {
                return 'phpdbg -qrr phpunit.phar';
            } else {
                return 'php phpunit.phar';
            }
        }

        return '';
    }

    public function generate()
    {
        $default = Config::defaultConfig();
        $phpunit_path = $this->findPhpunit();
        if ($phpunit_path) {
            $default['phpunit'] = $phpunit_path;
        }

        return $default;
    }
}
