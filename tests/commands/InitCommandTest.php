<?php
namespace iakio\phpunit\smartrunner\commands\tests;

use iakio\phpunit\smartrunner\commands\InitCommand;
use iakio\phpunit\smartrunner\FileSystem;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    function test_create_configuration_files()
    {
        $root = __DIR__ . '/../tmp';
        $cache_dir = realpath($root . '/.smartrunner');

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            exec("rd /S /Q $cache_dir");
        } else {
            exec("rm -rf $cache_dir");
        }
        @mkdir($root);
        $fs = new FileSystem($root);

        $command = new InitCommand($fs);
        $command->run();
        $this->assertTrue(is_dir($cache_dir));
        $this->assertTrue(file_exists($cache_dir . '/config.json'));
        $this->assertTrue(file_exists($cache_dir . '/phpunit.xml.dist'));
    }
}
