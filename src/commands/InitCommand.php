<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands;

use DOMDocument;
use DOMAttr;
use iakio\phpunit\smartrunner\FileSystem;

class InitCommand
{
    public function __construct(FileSystem $fs)
    {
        $this->fs = $fs;
    }

    private function defaultPhpUnitConfig()
    {
        $config = <<<EOD
<?xml version="1.0"?>
<phpunit>
  <listeners>
    <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
  </listeners>
</phpunit>
EOD;

        return $config;
    }

    private function phpunitConfig(array $argv)
    {
        if (count($argv) > 0) {
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace  = false;
            $doc->formatOutput = true;
            $doc->loadXML(
                file_get_contents($argv[0])
            );
            $listeners_tags = $doc->getElementsByTagName('listeners');
            if ($listeners_tags->length === 0) {
                $listeners = $doc->firstChild->appendChild($doc->createElement('listeners'));
            } else {
                $listeners = $listeners_tags->item(0);
            }
            $listener = $doc->createElement('listener');
            $listener->setAttributeNode(
                new DomAttr('class', 'iakio\phpunit\smartrunner\DependencyListener'));
            $listeners->appendChild($listener);
            $phpunit_config = $doc->saveXML();
        } else {
            $phpunit_config = $this->defaultPhpUnitConfig();
        }

        return $phpunit_config;
    }

    public function run(array $argv = [])
    {
        if (file_exists($this->fs->config_file)) {
            echo $this->fs->relativePath($this->fs->config_file), " already exists.\n";
        } else {
            echo 'Creating '.$this->fs->relativePath($this->fs->config_file), ".\n";
            $this->fs->saveConfigFile($this->fs->loadConfig());
        }

        if (file_exists($this->fs->phpunit_config_file)) {
            echo $this->fs->relativePath($this->fs->phpunit_config_file), " already exists.\n";
        } else {
            echo 'Creating '.$this->fs->relativePath($this->fs->phpunit_config_file), ".\n";
            $this->fs->savePhpUnitConfig($this->phpunitConfig($argv));
        }
    }
}
