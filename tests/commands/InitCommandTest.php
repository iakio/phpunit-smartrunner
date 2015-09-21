<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\tests;

use iakio\phpunit\smartrunner\commands\InitCommand;
use iakio\phpunit\smartrunner\FileSystem;
use org\bovigo\vfs\vfsStream;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = vfsStream::setup();
        $this->cache_dir = $this->root->url().DIRECTORY_SEPARATOR.'.smartrunner';

        $this->fs = new FileSystem($this->root->url());
        $this->command = new InitCommand($this->fs);
    }

    public function test_create_configuration_files()
    {
        $this->expectOutputString(
            ".smartrunner/config.json created.\n".
            ".smartrunner/phpunit.xml.dist created.\n"
        );
        $this->command->run();
        $this->assertTrue(is_dir($this->cache_dir));
        $this->assertTrue(file_exists($this->cache_dir.'/config.json'));
        $this->assertTrue(file_exists($this->cache_dir.'/phpunit.xml.dist'));
    }

    public function test_do_nothing_if_cache_directory_exists()
    {
        mkdir($this->root->url().'/.smartrunner');
        $this->expectOutputString(
            ".smartrunner directory already exists.\n"
        );
        $this->command->run();
    }
}
