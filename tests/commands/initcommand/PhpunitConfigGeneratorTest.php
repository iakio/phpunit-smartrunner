<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\initcommand\tests;

use iakio\phpunit\smartrunner\commands\initcommand\PhpunitConfigGenerator;

class PhpunitConfigGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_reflect_original_phpunit_config_file()
    {
        $config = new PhpunitConfigGenerator();
        $original = '<!--commment--><phpunit colors="true"></phpunit>';
        $expected = <<<EOD
            <phpunit colors="true">
              <listeners>
                <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
              </listeners>
            </phpunit>
EOD;

        $this->assertXmlStringEqualsXmlString(
            $expected,
            $config->generate($original)
        );
    }

    public function test_append_listener_to_original_phpunit_config_file()
    {
        $config = new PhpunitConfigGenerator();
        $original = <<<EOD
            <phpunit colors="true">
              <listeners>
                <listener class="MyListener"></listener>
              </listeners>
            </phpunit>
EOD;

        $expected = <<<EOD
            <phpunit colors="true">
              <listeners>
                <listener class="MyListener"></listener>
                <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
              </listeners>
            </phpunit>
EOD;
        $this->assertXmlStringEqualsXmlString(
            $expected,
            $config->generate($original)
        );
    }

    public function test_fix_path()
    {
        $config = new PhpunitConfigGenerator();
        $original = <<<EOD
            <phpunit bootstrap="vendor/autoload.php">
              <testsuites>
                <testsuite name="My Test Suite">
                  <directory>tests/</directory>
                  <directory>/path/to/tests/*Test.php</directory>
                  <file>path/to/MyTest.php</file>
                  <exclude>path/to/exclude</exclude>
                </testsuite>
              </testsuites>
            </phpunit>
EOD;

        $expected = <<<EOD
            <phpunit bootstrap="../vendor/autoload.php">
              <testsuites>
                <testsuite name="My Test Suite">
                  <directory>../tests</directory>
                  <directory>/path/to/tests/*Test.php</directory>
                  <file>../path/to/MyTest.php</file>
                  <exclude>../path/to/exclude</exclude>
                </testsuite>
              </testsuites>
              <listeners>
                <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
              </listeners>
            </phpunit>
EOD;
        $this->assertXmlStringEqualsXmlString(
            $expected,
            $config->generate($original, '..')
        );
    }
}
