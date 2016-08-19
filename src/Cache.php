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
     * @var array
     */
    private $cache;

    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * Cache constructor.
     * @param FileSystem $fs
     * @param array $cache
     */
    public function __construct(FileSystem $fs, $cache = [])
    {
        $this->cache = $cache;
        $this->fs = $fs;
    }

    /**
     * @param string $key
     * @param string $val
     */
    public function add($key, $val)
    {
        $key = $this->fs->relativePath($key);
        $val = $this->fs->relativePath($val);
        if (array_key_exists($key, $this->cache)) {
            if (!in_array($val, $this->cache[$key])) {
                $this->cache[$key][] = $val;
            }
        } else {
            $this->cache[$key] = [$val];
        }
    }

    /**
     * @param string $key
     * @return array
     */
    public function get($key)
    {
        $key = $this->fs->relativePath($key);
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return [];
    }

    /**
     * @param string $key
     * @param string $val
     */
    public function remove($key, $val)
    {
        $key = $this->fs->relativePath($key);
        $val = $this->fs->relativePath($val);
        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key][$val]);
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->cache;
    }
}
