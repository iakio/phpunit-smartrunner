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
        $this->config_generator = $this->prophesize(
            'iakio\phpunit\smartrunner\commands\initcommand\ConfigGenerator'
        );
        $this->config_generator->generate()->willReturn([]);
        $this->phpunit_config_generator = $this->prophesize(
            'iakio\phpunit\smartrunner\commands\initcommand\PhpunitConfigGenerator'
        );
        $this->command = new InitCommand($this->fs, $this->config_generator->reveal(), $this->phpunit_config_generator->reveal());
    }

    public function test_create_configuration_files()
    {
        $this->expectOutputString(
            "Creating .smartrunner/config.json.\n".
            "Creating .smartrunner/phpunit.xml.dist.\n"
        );
        $this->command->run();
        $this->assertTrue(is_dir($this->cache_dir));
        $this->assertTrue(file_exists($this->cache_dir.'/config.json'));
        $this->assertTrue(file_exists($this->cache_dir.'/phpunit.xml.dist'));
    }

    public function test_do_not_overwrite_if_config_file_exists()
    {
        $this->expectOutputString(
            ".smartrunner/config.json already exists.\n".
            "Creating .smartrunner/phpunit.xml.dist.\n"
        );
        mkdir($this->cache_dir);
        touch($this->cache_dir.'/config.json');
        $this->command->run();
    }

    public function test_do_not_overwrite_if_phpunit_config_file_exists()
    {
        $this->expectOutputString(
            "Creating .smartrunner/config.json.\n".
            ".smartrunner/phpunit.xml.dist already exists.\n"
        );
        mkdir($this->cache_dir);
        touch($this->cache_dir.'/phpunit.xml.dist');
        $this->command->run();
    }

    public function test_generate_default_phpunit_config()
    {
        $this->phpunit_config_generator->generate('<phpunit />', null)->shouldBeCalled();

        $this->expectOutputString(
            "Creating .smartrunner/config.json.\n".
            "Creating .smartrunner/phpunit.xml.dist.\n"
        );
        $this->command->run();
    }

    public function test_invoke_generator_with_original_content_if_argument_is_passed()
    {
        $original_file = $this->root->url().'/phpunit.xml.dist';
        $original_content = '<phpunit colors="true"><listeners><listener class="MyListener"></listener></listeners></phpunit>';
        file_put_contents($original_file, $original_content);
        $this->expectOutputString(
            "Creating .smartrunner/config.json.\n".
            "Creating .smartrunner/phpunit.xml.dist.\n"
        );

        $this->phpunit_config_generator->generate($original_content, '..')->shouldBeCalled();
        $this->command->run([$original_file]);
    }
}
