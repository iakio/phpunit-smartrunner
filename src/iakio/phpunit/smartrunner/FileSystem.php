<?php
namespace iakio\phpunit\smartrunner;

class FileSystem
{
    /**
     * @var string
     */
    private $root;

    const CACHE_FILE = 'cache.json';

    const CACHE_DIR = '.smartrunner';

    public function __construct($root)
    {
        $this->root = $root;
        $this->cache_dir = $this->root . DIRECTORY_SEPARATOR . self::CACHE_DIR;
        $this->cache_file = $this->cache_dir . DIRECTORY_SEPARATOR . self::CACHE_FILE;
    }

    public function normalizePath($path)
    {
        return str_replace(realpath($this->root) . DIRECTORY_SEPARATOR, "", realpath($path));
    }

    public function saveCache($cache_data)
    {
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir);
        }
        file_put_contents($this->cache_file, json_encode($cache_data, JSON_PRETTY_PRINT));
    }

    public function loadCache()
    {
        if (file_exists($this->cache_file)) {
            return json_decode(file_get_contents($this->cache_file), true);
        }
        return [];
    }
}
