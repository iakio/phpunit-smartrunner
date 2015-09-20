<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

class Cache
{
    /**
     * @var string[]
     */
    private $cache;

    /**
     * @var FileSystem
     */
    private $fs;

    public function __construct(FileSystem $fs)
    {
        $this->cache = [];
        $this->fs = $fs;
    }

    public function loadCache()
    {
        $this->cache = $this->fs->loadCache();
    }

    public function saveCache()
    {
        $this->fs->saveCache($this->cache);
    }

    public function add($key, $val)
    {
        if (array_key_exists($key, $this->cache)) {
            if (!in_array($val, $this->cache[$key])) {
                $this->cache[$key][] = $val;
            }
        } else {
            $this->cache[$key] = array($val);
        }
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return array();
    }
}
