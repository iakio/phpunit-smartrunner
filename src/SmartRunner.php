<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

use iakio\phpunit\smartrunner\commands\InitCommand;
use iakio\phpunit\smartrunner\commands\initcommand\PhpunitConfigGenerator;
use iakio\phpunit\smartrunner\commands\initcommand\ConfigGenerator;
use iakio\phpunit\smartrunner\commands\RunCommand;

class SmartRunner
{

    public static function createRunCommand()
    {
        $fs = new FileSystem(getcwd());
        $config = $fs->loadConfig();
        $cache = new Cache($fs);
        $cache->loadCache();
        $phpunit = new Phpunit($config['phpunit']);

        return new RunCommand($phpunit, $cache, $fs);
    }

    public static function createInitCommand()
    {
        $fs = new FileSystem(getcwd());

        return new InitCommand($fs, new ConfigGenerator($fs), new PhpunitConfigGenerator());
    }

    public static function usage()
    {
        echo "Usage: smartrunner init [phpunit.xml]\n";
        echo "    or smartrunner run <filename>\n";
    }

    public static function run(array $argv)
    {
        array_shift($argv);
        if (count($argv) === 0) {
            self::usage();

            return;
        }
        $subcommand = array_shift($argv);
        if ($subcommand === 'run') {
            $command = self::createRunCommand();
            $command->run($argv);
        } elseif ($subcommand === 'init') {
            $command = self::createInitCommand();
            $command->run($argv);
        } else {
            self::usage();
        }
    }
}
