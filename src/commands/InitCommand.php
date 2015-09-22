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
        if (file_exists($this->fs->config_file)) {
            echo $this->fs->relativePath($this->fs->config_file), " already exists.\n";
        } else {
            $this->fs->saveConfigFile($this->fs->loadConfig());
            echo $this->fs->relativePath($this->fs->config_file), " created.\n";
        }

        if (file_exists($this->fs->phpunit_config_file)) {
            echo $this->fs->relativePath($this->fs->phpunit_config_file), " already exists.\n";
        } else {
            $this->fs->savePhpUnitConfig();
            echo $this->fs->relativePath($this->fs->phpunit_config_file), " created.\n";
        }
    }
}
