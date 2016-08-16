<?php
/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;


use iakio\phpunit\smartrunner\drivers\Driver;

class DependencyHandler
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $memo;
    /**
     * @var FileSystem
     */
    private $fs;

    /**
     * DependencyHandler constructor.
     * @param Driver $driver
     * @param Cache $cache
     * @param Config $config
     */
    public function __construct(Driver $driver, Cache $cache, Config $config, FileSystem $fs)
    {
        $this->driver = $driver;
        $this->cache = $cache;
        $this->config = $config;
        $this->fs = $fs;
        $this->memo = [];
    }

    public function startTest()
    {
        $this->driver->startCodeCoverage();
    }

    /**
     * @param string $file
     * @return bool
     */
    private function isIgnoredInternal($file)
    {
        if (strpos($file, 'phar://') === 0) {
            return true;
        }
        $relative_path = $this->fs->relativePath($file);

        return $this->config->isIgnored($relative_path);
    }

    /**
     * @param string $file
     * @return bool
     */
    private function isIgnored($file)
    {
        if (array_key_exists($file, $this->memo)) {
            return $this->memo[$file];
        }
        return $this->memo[$file] = $this->isIgnoredInternal($file);
    }

    /**
     * @param string $testFile
     */
    public function endTest($testFile)
    {
        $files = array_keys($this->driver->stopCodeCoverage());
        foreach ($files as $file) {
            if (!$this->isIgnored($file)) {
                $this->cache->add($file, $testFile);
            }
        }
    }

    public function endTestSuite()
    {
        $this->cache->saveCache();
    }
}
