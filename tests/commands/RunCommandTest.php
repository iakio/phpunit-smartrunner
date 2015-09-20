<?php
namespace iakio\phpunit\smartrunner\commands\tests;

use iakio\phpunit\smartrunner\commands\RunCommand;
use iakio\phpunit\smartrunner\Cache;
use Prophecy\Argument;

class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    private $cache;
    private $fs;
    private $phpunit;

    function setUp()
    {
        $this->cache = $this->prophesize('iakio\phpunit\smartrunner\Cache');
        $this->fs = $this->prophesize('iakio\phpunit\smartrunner\FileSystem');
        $this->phpunit = $this->prophesize('iakio\phpunit\smartrunner\Phpunit');
        $this->command = new RunCommand($this->phpunit->reveal(), $this->cache->reveal(), $this->fs->reveal());
    }

    function test_do_nothing_if_file_is_unknown()
    {
        $arg = 'unknownfile';
        $this->cache->get($arg)
            ->shouldBeCalled()
            ->willReturn([]);
        $this->fs->saveSuiteFile()->shouldNotBeCalled();
        $this->phpunit->exec()->shouldNotBeCalled();
        $this->command->run($arg);
    }

    function test_run_itself_if_file_is_testable()
    {
        $arg = 'tests/CalcTest.php';
        $this->cache->get($arg)
            ->shouldBeCalled()
            ->willReturn([]);
        $this->fs->saveSuiteFile([$arg])->shouldBeCalled();

        $this->phpunit->exec(Argument::any())->shouldBeCalled();
        $this->command->run($arg);
    }

    function test_run_related_tests_if_file_is_cached()
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
        $this->command->run($arg);
    }
}
