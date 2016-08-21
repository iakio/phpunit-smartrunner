<?php
/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\tests;


use iakio\phpunit\smartrunner\DependencyHandler;
use Prophecy\Argument;

class DependencyHandlerTest extends \PHPUnit_Framework_TestCase
{

    /** @var  DependencyHandler */
    private $handler;

    public function setUp()
    {
        $this->config = $this->prophesize('iakio\phpunit\smartrunner\Config');
        $this->cache = $this->prophesize('iakio\phpunit\smartrunner\Cache');
        $this->driver = $this->prophesize('iakio\phpunit\smartrunner\drivers\Driver');
        $this->fs = $this->prophesize('iakio\phpunit\smartrunner\FileSystem');
        $this->handler = new DependencyHandler(
            $this->driver->reveal(),
            $this->cache->reveal(),
            $this->config->reveal(),
            $this->fs->reveal()
        );
    }

    public function test_start_coverage()
    {
        $this->driver->startCodeCoverage()->shouldBeCalled();
        $this->handler->startTest();
    }

    public function test_update_cache()
    {
        $this->config->isIgnored(Argument::any())->willReturn(false);
        $this->cache->add('path\to\Calc.php', 'CalcTest.php')->shouldBeCalled();
        $this->driver->stopCodeCoverage()->willReturn([
            'path\to\Calc.php' => ''
        ]);
        $this->fs->relativePath('path\to\Calc.php')->shouldBeCalled()->willReturn('Calc.php');
        $this->handler->endTest('CalcTest.php');
    }

    public function test_do_not_update_cache_if_its_ignored()
    {
        $this->config->isIgnored(Argument::any())->willReturn(true);
        $this->cache->add(Argument::any())->shouldNotBeCalled();
        $this->driver->stopCodeCoverage()->willReturn([
            'path\to\Calc.php' => ''
        ]);
        $this->handler->endTest('CalcTest.php');
    }

    public function test_stop_coverage()
    {
        $this->fs->saveCache($this->cache)->shouldBeCalled();
        $this->handler->endTestSuite();
    }
}
