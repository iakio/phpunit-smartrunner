<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands;

use iakio\phpunit\smartrunner\FileSystem;
use iakio\phpunit\smartrunner\commands\initcommand\ConfigGenerator;
use iakio\phpunit\smartrunner\commands\initcommand\PhpunitConfigGenerator;
use Webmozart\PathUtil\Path;

class InitCommand
{
    /** @var FileSystem */
    private $fs;

    /** @var PhpunitConfigGenerator */
    private $phpunit_config_generator;

    public function __construct(FileSystem $fs, ConfigGenerator $config_generator, PhpunitConfigGenerator $phpunit_config_generator)
    {
        $this->fs = $fs;
        $this->config_generator = $config_generator;
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
        $config_file = $this->fs->getConfigFile();
        if (file_exists($config_file)) {
            echo $this->fs->relativePath($config_file), " already exists.\n";
        } else {
            echo 'Creating '.$this->fs->relativePath($config_file), ".\n";
            $this->fs->saveConfigFile($this->config_generator->generate());
        }

        $phpunit_config_file = $this->fs->getPhpunitConfigFile();
        if (file_exists($phpunit_config_file)) {
            echo $this->fs->relativePath($phpunit_config_file), " already exists.\n";
        } else {
            echo 'Creating '.$this->fs->relativePath($phpunit_config_file), ".\n";
            $this->fs->savePhpUnitConfig($this->phpunitConfig($argv));
        }
    }
}
