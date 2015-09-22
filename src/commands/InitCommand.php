<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands;

use iakio\phpunit\smartrunner\FileSystem;

class InitCommand
{
    public function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    public function run()
    {
        if ($this->fs->cacheDirExists()) {
            echo $this->fs->cacheDir()." directory already exists.\n";

            return;
        }
        $this->fs->savePhpUnitConfig();
        $this->fs->saveConfigFile($this->fs->loadConfig());
        echo $this->fs->relativePath($this->fs->config_file), " created.\n";
        echo $this->fs->relativePath($this->fs->phpunit_config_file), " created.\n";
    }
}
