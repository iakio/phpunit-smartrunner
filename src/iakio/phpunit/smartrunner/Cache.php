<?php

namespace iakio\phpunit\smartrunner;

class Cache
{

    /**
     * @var string[]
     */
    private $cache;

    /**
     * @var string
     */
    private $cache_file;

    public function __construct($cache_file)
    {
        $this->cache = array();
        $this->cache_file = $cache_file;
    }

    public function loadCache()
    {
        if (file_exists($this->cache_file)) {
            $this->cache = json_decode(file_get_contents($this->cache_file), true);
        }
    }

    public function saveCache()
    {
        file_put_contents($this->cache_file, json_encode($this->cache, JSON_PRETTY_PRINT));
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
