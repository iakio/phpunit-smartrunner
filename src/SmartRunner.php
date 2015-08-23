<?php

namespace iakio\phpunit\smartrunner;

class SmartRunner
{

    private function defaultConfig()
    {
        return [
            'phpunit' => implode(DIRECTORY_SEPARATOR, ['vendor', 'bin' , 'phpunit'])
        ];
    }

    public function initCommand()
    {
        $fs = new FileSystem(getcwd());
        $fs->savePhpUnitConfig();
        $fs->saveConfigFile($this->defaultConfig());
    }

    public function runCommand($file_name)
    {
        $fs = new FileSystem(getcwd());
        $config = $fs->loadConfig();
        $cache = new Cache($fs);
        $cache->loadCache();
        $arg_file = $fs->normalizePath($file_name);
        $hits = $cache->get($arg_file);
        if (count($hits) === 0 && self::isTestable($arg_file)) {
            $hits = [$arg_file];
        }
        if (count($hits) > 0) {
            $fs->saveSuiteFile($hits);

            $command = $config['phpunit'];
            system("$command -c .smartrunner/phpunit.xml.dist SmartrunnerSuite .smartrunner/suite.php");
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
