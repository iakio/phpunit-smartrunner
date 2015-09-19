<?php

namespace iakio\phpunit\smartrunner;

class SmartRunner
{

    private function defaultConfig()
    {
        return [
            'phpunit' => implode(DIRECTORY_SEPARATOR, ['vendor', 'bin' , 'phpunit']),
            'cacheignores' => [
                'vendor/**/*'
            ]
        ];
    }

    public function initCommand()
    {
        $fs = new FileSystem(getcwd());
        $fs->savePhpUnitConfig();
        $fs->saveConfigFile($this->defaultConfig());
    }

    public function runCommand($argv)
    {
        if (count($argv) < 3) {
            $this->usage();
            return;
        }
        $file_name = $argv[2];
        $fs = new FileSystem(getcwd());
        $config = $fs->loadConfig();
        $cache = new Cache($fs);
        $cache->loadCache();
        $arg_file = $fs->relativePath($file_name);
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

    public static function usage()
    {
        echo "usage: smartrunner init\n";
        echo "    or smartrunner run <filename>\n";
    }

    public static function run(array $argv)
    {
        $runner = new self;
        if (count($argv) <= 1) {
            self::usage();
            return;
        }
        $command = $argv[1];
        if ($command === "run") {
            $runner->runCommand($argv);
        } else if ($command === "init") {
            $runner->initCommand();
        } else {
            self::usage();
        }
    }

    public static function isTestable($arg_file)
    {
        return preg_match('/.*Test\.php$/', $arg_file);
    }
}
