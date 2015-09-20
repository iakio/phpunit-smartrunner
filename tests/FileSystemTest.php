<?php

namespace iakio\phpunit\smartrunner\tests;

use iakio\phpunit\smartrunner\FileSystem;
use org\bovigo\vfs\vfsStream;

class FileSystemTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = vfsStream::setup();
        $this->fs = new FileSystem($this->root->url());
    }

    public function test_cache()
    {
        $cache = [
            'tests\CalcTest.php' => [
                'src\BankAccount.php',
                'src\Calc.php',
            ],
        ];
        $this->fs->saveCache($cache);
        $this->assertEquals($cache, $this->fs->loadCache());
    }

    public function test_phpunit_config()
    {
        $this->fs->savePhpUnitConfig();
        $this->assertTrue(file_exists($this->root->url().'/.smartrunner/phpunit.xml.dist'));
    }

    public function test_config()
    {
        $config = [
            'phpunit' => 'phpunit',
        ];
        $this->fs->saveConfigFile($config);
        $this->assertEquals($config, $this->fs->loadConfig());
    }
}