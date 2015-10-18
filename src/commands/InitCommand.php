<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands;

use iakio\phpunit\smartrunner\FileSystem;
use iakio\phpunit\smartrunner\commands\initcommand\PhpunitConfigGenerator;
use Webmozart\PathUtil\Path;

class InitCommand
{
    /** @var FileSystem */
    private $fs;

    /** @var PhpunitConfigGenerator */
    private $phpunit_config_generator;

    public function __construct(FileSystem $fs, PhpunitConfigGenerator $phpunit_config_generator)
    {
        $this->fs = $fs;
        $this->phpunit_config_generator = $phpunit_config_generator;
    }

    private function phpunitConfig(array $argv)
    {
        if (count($argv) > 0) {
            $original = file_get_contents($argv[0]);
            $fix = Path::canonicalize('../'.$this->fs->relativePath(dirname($argv[0])));
        } else {
            $original = '<phpunit />';
            $fix = null;
        }

        return $this->phpunit_config_generator->generate($original, $fix);
    }

    public function run(array $argv = [])
    {
        if (file_exists($this->fs->config_file)) {
            echo $this->fs->relativePath($this->fs->config_file), " already exists.\n";
        } else {
            echo 'Creating '.$this->fs->relativePath($this->fs->config_file), ".\n";
            $this->fs->saveConfigFile($this->fs->loadConfig());
        }

        if (file_exists($this->fs->phpunit_config_file)) {
            echo $this->fs->relativePath($this->fs->phpunit_config_file), " already exists.\n";
        } else {
            echo 'Creating '.$this->fs->relativePath($this->fs->phpunit_config_file), ".\n";
            $this->fs->savePhpUnitConfig($this->phpunitConfig($argv));
        }
    }
}
