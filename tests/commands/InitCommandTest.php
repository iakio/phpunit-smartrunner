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

    public function test_reflect_original_phpunit_config_file()
    {
        $original = $this->root->url().'/phpunit.xml.dist';
        $generated = $this->cache_dir.'/phpunit.xml.dist';
        file_put_contents($original, '<phpunit colors="true"></phpunit>');
        $expected = <<<EOD
            <phpunit colors="true">
              <listeners>
                <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
              </listeners>
            </phpunit>
EOD;

        $this->expectOutputString(
            "Creating .smartrunner/config.json.\n".
            "Creating .smartrunner/phpunit.xml.dist.\n"
        );
        $this->command->run([$original]);
        $this->assertXmlStringEqualsXmlString(
            $expected,
            file_get_contents($generated)
        );
    }

    public function test_append_listener_to_original_phpunit_config_file()
    {
        $original = $this->root->url().'/phpunit.xml.dist';
        $generated = $this->cache_dir.'/phpunit.xml.dist';
        file_put_contents($original,
            '<phpunit colors="true"><listeners><listener class="MyListener"></listener></listeners></phpunit>');
        $expected = <<<EOD
            <phpunit colors="true">
              <listeners>
                <listener class="MyListener"></listener>
                <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
              </listeners>
            </phpunit>
EOD;
        $this->expectOutputString(
            "Creating .smartrunner/config.json.\n".
            "Creating .smartrunner/phpunit.xml.dist.\n"
        );
        $this->command->run([$original]);
        $this->assertXmlStringEqualsXmlString(
            $expected,
            file_get_contents($generated)
        );
    }
}
