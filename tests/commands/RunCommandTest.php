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
use iakio\phpunit\smartrunner\FileSystem;
use iakio\phpunit\smartrunner\Phpunit;
use Prophecy\Argument;

class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Cache */
    private $cache;

    /** @var  FileSystem */
    private $fs;

    /** @var  Phpunit */
    private $phpunit;

    /** @var  RunCommand */
    private $command;

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
        $this->fs->fileExists(Argument::any())->willReturn(true);

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
        $this->fs->fileExists(Argument::any())->willReturn(true);
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
            ".smartrunner directory does not exist.\n".
            "Run `smartrunner init [phpunit.xml]` first.\n"
        );
    }

    public function test_show_usage_if_file_does_not_given()
    {
        $this->command->run([]);
        $this->expectOutputRegex('/Usage/');
    }

    public function test_remove_cache_if_files_do_not_exist()
    {
        $arg = 'src/Calc.php';
        $related_tests = [
            'tests/BankAccountTest.php',
            'tests/CalcTest.php',
        ];
        $this->cache->get($arg)
            ->shouldBeCalled()
            ->willReturn($related_tests);
        $this->cache->remove('src/Calc.php', 'tests/BankAccountTest.php')->shouldBeCalled();
        $this->fs->saveCache($this->cache)->shouldBeCalled();

        $this->fs->cacheDir()->willReturn('.smartrunner');
        $this->fs->fileExists('tests/BankAccountTest.php')->willReturn(false);
        $this->fs->fileExists('tests/CalcTest.php')->willReturn(true);
        $this->fs->saveSuiteFile(['tests/CalcTest.php'])->shouldBeCalled();
        $this->command->run([$arg]);
    }
}
