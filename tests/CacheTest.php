<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\tests;

use iakio\phpunit\smartrunner\Cache;
use iakio\phpunit\smartrunner\FileSystem;
use org\bovigo\vfs\vfsStream;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = vfsStream::setup();
        $this->fs = new FileSystem($this->root->url());
    }

    public function test_cache_does_not_accept_duplicated_entry()
    {
        $cache = new Cache($this->fs);
        $cache->add('foo', 'bar');
        $cache->add('foo', 'bar');
        $this->assertEquals(['bar'], $cache->get('foo'));
    }

    public function test_paths_are_converted_to_relative_path()
    {
        $cache = new Cache($this->fs);
        $cache->add($this->root->url().'/foo', $this->root->url().'/bar');
        $this->assertEquals(['bar'], $cache->get('foo'));
    }
}
