<?php

namespace iakio\phpunit\smartrunner\commands\tests;

use iakio\phpunit\smartrunner\commands\InitCommand;
use iakio\phpunit\smartrunner\FileSystem;
use org\bovigo\vfs\vfsStream;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_configuration_files()
    {
        $root = vfsStream::setup();
        $cache_dir = $root->url().'/.smartrunner';

        $fs = new FileSystem($root->url());

        $command = new InitCommand($fs);
        $command->run();
        $this->assertTrue(is_dir($cache_dir));
        $this->assertTrue(file_exists($cache_dir.'/config.json'));
        $this->assertTrue(file_exists($cache_dir.'/phpunit.xml.dist'));
    }
}
