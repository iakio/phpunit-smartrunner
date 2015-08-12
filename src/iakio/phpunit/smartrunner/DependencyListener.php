<?php
namespace iakio\phpunit\smartrunner;

use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use ReflectionClass;


class DependencyListener extends \PHPUnit_Framework_BaseTestListener
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct()
    {
        $this->root = getcwd();
        $this->cache = new Cache(".smartrunner.cache.json");
        $this->cache->loadCache();
    }


    public function startTest(PHPUnit_Framework_Test $test)
    {
        xdebug_start_code_coverage();
    }


    private function isVendorFile($file) {
        return (strpos(Utils::normalizePath($this->root, $file), "vendor" . DIRECTORY_SEPARATOR) === 0);
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $class  = new ReflectionClass($test);
        $testFile = $class->getFileName();
        $executedFiles = array_keys(xdebug_get_code_coverage());
        foreach ($executedFiles as $executedFile) {
            if (!$this->isVendorFile($executedFile)) {
                $this->cache->add(
                    Utils::normalizePath($this->root, $executedFile),
                    Utils::normalizePath($this->root, $testFile)
                );
            }
        }
        xdebug_stop_code_coverage();
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->cache->saveCache();
    }
}
