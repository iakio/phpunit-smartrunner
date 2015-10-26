<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\initcommand\tests;

use iakio\phpunit\smartrunner\commands\initcommand\ConfigGenerator;
use iakio\phpunit\smartrunner\FileSystem;
use Prophecy\Argument;

class ConfigGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fs = $this->prophesize('iakio\phpunit\smartrunner\FileSystem');
        $this->generator = new ConfigGenerator($this->fs->reveal());
    }

    public function test_generate_default_config_file()
    {
        $this->fs->fileExists(Argument::any())->willReturn(false);
        $expected = [
            'phpunit' => 'phpunit',
            'cacheignores' => [
                'vendor/**/*',
            ]
        ];
        $this->assertEquals($expected, $this->generator->generate());
    }

    public function test_set_phpunit_path_if_exists()
    {
        $this->fs->fileExists('vendor/bin/phpunit')->willReturn(true);
        $actual = $this->generator->generate();
        $this->assertThat(
            $actual['phpunit'],
            $this->logicalOr(
                'vendor/bin/phpunit',
                'vendor\bin\phpunit'
            )
        );

        $this->fs->fileExists('vendor/bin/phpunit')->willReturn(false);
        $this->fs->fileExists('phpunit.phar')->willReturn(true);
        $actual = $this->generator->generate();
        $this->assertEquals('php phpunit.phar', $actual['phpunit']);
    }
}
