<?php

namespace iakio\phpunit\smartrunner;

use iakio\phpunit\smartrunner\commands\InitCommand;
use iakio\phpunit\smartrunner\commands\RunCommand;

class SmartRunner
{
    public static function runCommand($argv)
    {
        if (count($argv) === 0) {
            self::usage();

            return;
        }
        $file_name = array_shift($argv);
        $fs = new FileSystem(getcwd());
        $config = $fs->loadConfig();
        $cache = new Cache($fs);
        $cache->loadCache();
        $arg_file = $fs->relativePath(realpath($file_name));
        $phpunit = new Phpunit($config['phpunit']);
        $command = new RunCommand($phpunit, $cache, $fs);
        $command->run($arg_file);
    }

    public static function usage()
    {
        echo "usage: smartrunner init\n";
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
            self::runCommand($argv);
        } elseif ($subcommand === 'init') {
            $fs = new FileSystem(getcwd());
            $command = new InitCommand($fs);
            $command->run();
        } else {
            self::usage();
        }
    }
}
