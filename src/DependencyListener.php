<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

use iakio\phpunit\smartrunner\drivers\Driver;
use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;
use ReflectionClass;
use iakio\phpunit\smartrunner\drivers\XdebugDriver;
use iakio\phpunit\smartrunner\drivers\PhpdbgDriver;

class DependencyListener extends PHPUnit_Framework_BaseTestListener
{
    /** @var DependencyHandler */
    private $handler;

    public function __construct()
    {
        $root = getcwd();
        $fs = new FileSystem($root);
        $cache = new Cache($fs);
        $cache->loadCache();
        $config = $fs->loadConfig();
        if (function_exists('phpdbg_start_oplog')) {
            $driver = new PhpdbgDriver();
        } else {
            $driver = new XdebugDriver();
        }
        $this->handler = new DependencyHandler(
            $driver,
            $cache,
            $config,
            $fs
        );
    }

    /**
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->handler->startTest();
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param float $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $this->handler->endTest($test);
    }

    /**
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->handler->endTestSuite();
    }
}
