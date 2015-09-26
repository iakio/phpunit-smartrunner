<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\tests;

use iakio\phpunit\smartrunner\commands\RunCommand;
use iakio\phpunit\smartrunner\Cache;
use Prophecy\Argument;

class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    private $cache;
    private $fs;
    private $phpunit;

    public function setUp()
    {
        $this->cache = $this->prophesize('iakio\phpunit\smartrunner\Cache');
        $this->fs = $this->prophesize('iakio\phpunit\smartrunner\FileSystem');
        $this->phpunit = $this->prophesize('iakio\phpunit\smartrunner\Phpunit');
        $this->command = new RunCommand($this->phpunit->reveal(), $this->cache->reveal(), $this->fs->reveal());
        $this->fs->cacheDirExists()->willReturn(true);
    }

    public function test_do_nothing_if_file_is_unknown()
    {
        $arg = 'unknownfile';
        $this->cache->get($arg)
            ->shouldBeCalled()
            ->willReturn([]);
        $this->fs->saveSuiteFile()->shouldNotBeCalled();
        $this->phpunit->exec()->shouldNotBeCalled();
        $this->command->run([$arg]);
    }

    public function test_run_itself_if_file_is_testable()
    {
        $arg = 'tests/CalcTest.php';
        $this->cache->get($arg)
            ->shouldBeCalled()
            ->willReturn([]);
        $this->fs->saveSuiteFile([$arg])->shouldBeCalled();

        $this->phpunit->exec(Argument::any())->shouldBeCalled();
        $this->command->run([$arg]);
    }

    public function test_run_related_tests_if_file_is_cached()
    {
        $arg = 'src/Calc.php';
        $related_tests = [
            'tests/BankAccountTest.php',
            'tests/CalcTest.php',
        ];
        $this->cache->get($arg)
            ->shouldBeCalled()
            ->willReturn($related_tests);
        $this->fs->saveSuiteFile($related_tests)->shouldBeCalled();
        $this->phpunit->exec(Argument::any())->shouldBeCalled();
        $this->command->run([$arg]);
    }

    public function test_show_messages_if_cache_directory_does_not_exist()
    {
        $arg = 'tests/CalcTest.php';
        $this->fs->cacheDir()->willReturn('.smartrunner');
        $this->fs->cacheDirExists()->willReturn(false);
        $this->fs->saveSuiteFile(Argument::any())->shouldNotBeCalled();
        $this->command->run([$arg]);
        $this->expectOutputString(
            ".smartrunner directory does not exist.\n"
        );
    }

    public function test_show_usage_if_file_does_not_given()
    {
        $this->command->run([]);
        $this->expectOutputRegex('/Usage/');
    }
}
