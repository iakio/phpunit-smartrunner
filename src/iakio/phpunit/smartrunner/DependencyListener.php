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

    public function __construct(Cache $cache)
    {
        $this->root = getcwd();
        $this->cache = $cache;
        $this->cache->loadCache();
    }


    public function startTest(PHPUnit_Framework_Test $test)
    {
        xdebug_start_code_coverage();
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $class  = new ReflectionClass($test);
        $testFile = $class->getFileName();
        $files = array_keys(xdebug_get_code_coverage());
        foreach ($files as $sut) {
            $this->cache->add(
                Utils::normalizePath($this->root, $sut),
                Utils::normalizePath($this->root, $testFile)
            );
        }
        xdebug_stop_code_coverage();
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->cache->saveCache();
    }
}
