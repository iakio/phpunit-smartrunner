<?php

namespace iakio\phpunit\smartrunner;

use iakio\phpunit\smartrunner\commands\InitCommand;
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
            $command = self::createRunCommand();
            $command->run($argv);
        } elseif ($subcommand === 'init') {
            $fs = new FileSystem(getcwd());
            $command = new InitCommand($fs);
            $command->run();
        } else {
            self::usage();
        }
    }
}
