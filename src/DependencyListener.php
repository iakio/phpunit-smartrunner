<?php

namespace iakio\phpunit\smartrunner;

use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use ReflectionClass;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class DependencyListener extends \PHPUnit_Framework_BaseTestListener
{
    /** @var FileSystem */
    private $fs;

    /** @var Cache */
    private $cache;

    /** @var array */
    private $config;

    public function __construct()
    {
        $this->fs = new FileSystem(getcwd());
        $this->cache = new Cache($this->fs);
        $this->cache->loadCache();
        $this->config = $this->fs->loadConfig();
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        xdebug_start_code_coverage();
    }

    private function isIgnored($file)
    {
        if (strpos($file, 'phar://') === 0) {
            return true;
        }
        $canonical_path = Path::canonicalize($file);
        foreach ($this->config['cacheignores'] as $pattern) {
            if (Glob::match($canonical_path, Path::makeAbsolute($pattern, getcwd()))) {
                return true;
            }
        }

        return false;
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $class = new ReflectionClass($test);
        $testFile = $class->getFileName();
        $executedFiles = array_keys(xdebug_get_code_coverage());
        foreach ($executedFiles as $executedFile) {
            if (!$this->isIgnored($executedFile)) {
                $this->cache->add($executedFile, $testFile);
            }
        }
        xdebug_stop_code_coverage();
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->cache->saveCache();
    }
}
