<?php
namespace iakio\phpunit\smartrunner;

use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use ReflectionClass;
use Webmozart\PathUtil\Path;


class DependencyListener extends \PHPUnit_Framework_BaseTestListener
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct()
    {
        $this->fs = new FileSystem(getcwd());
        $this->cache = new Cache($this->fs);
        $this->cache->loadCache();
    }


    public function startTest(PHPUnit_Framework_Test $test)
    {
        xdebug_start_code_coverage();
    }


    private function isIgnored($file) {
        if (strpos($file, 'phar://') === 0) {
            return true;
        }
        return Path::isBasePath("vendor", $this->fs->normalizePath($file));
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $class  = new ReflectionClass($test);
        $testFile = $class->getFileName();
        $executedFiles = array_keys(xdebug_get_code_coverage());
        foreach ($executedFiles as $executedFile) {
            if (!$this->isIgnored($executedFile)) {
                $this->cache->add(
                    $this->fs->normalizePath($executedFile),
                    $this->fs->normalizePath($testFile)
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
