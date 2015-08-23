<?php

namespace iakio\phpunit\smartrunner;

class SmartRunner
{

    public function initCommand()
    {
        $fs = new FileSystem(getcwd());
        $fs->savePhpUnitConfig();
    }

    public function runCommand($file_name)
    {
        $fs = new FileSystem(getcwd());
        $cache = new Cache($fs);
        $cache->loadCache();
        $arg_file = $fs->normalizePath($file_name);
        $hits = $cache->get($arg_file);
        if (count($hits) === 0 && self::isTestable($arg_file)) {
            $hits = [$arg_file];
        }
        if (count($hits) > 0) {
            $fs->saveSuiteFile($hits);

            $command = implode(DIRECTORY_SEPARATOR, ["vendor", "bin", "phpunit"]);
            system("$command -c .smartrunner/phpunit.xml.dist  SmartrunnerSuite .smartrunner/suite.php");
        }
    }


    public static function run(array $argv)
    {
        $runner = new self;
        if (count($argv) <= 1) {
            return;
        }
        $command = $argv[1];
        if ($command === "run") {
            $runner->runCommand($argv[2]);
        } else if ($command === "init") {
            $runner->initCommand();
        }
    }

    public static function isTestable($arg_file)
    {
        return preg_match('/.*Test\.php$/', $arg_file);
    }
}
