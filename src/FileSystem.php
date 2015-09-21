<?php

namespace iakio\phpunit\smartrunner;

use Webmozart\PathUtil\Path;

class FileSystem
{
    /**
     * @var string
     */
    private $root;

    const CACHE_DIR = '.smartrunner';

    const CACHE_FILE = 'cache.json';

    const SUITE_FILE = 'suite.php';

    const CONFIG_FILE = 'config.json';

    const PHPUNIT_CONFIG_FILE = 'phpunit.xml.dist';

    public function __construct($root)
    {
        $this->root = $root;
        $this->cache_dir = $this->root.DIRECTORY_SEPARATOR.self::CACHE_DIR;
        $this->cache_file = $this->cache_dir.DIRECTORY_SEPARATOR.self::CACHE_FILE;
        $this->config_file = $this->cache_dir.DIRECTORY_SEPARATOR.self::CONFIG_FILE;
        $this->phpunit_config_file = $this->cache_dir.DIRECTORY_SEPARATOR.self::PHPUNIT_CONFIG_FILE;
    }

    public function relativePath($path)
    {
        return Path::makeRelative($path, $this->root);
    }

    private function ensureDirectory()
    {
        if (!$this->cacheDirExists()) {
            mkdir($this->cache_dir);
        }
    }

    public function cacheDirExists()
    {
        return is_dir($this->cache_dir);
    }

    public function saveCache($cache_data)
    {
        $this->ensureDirectory();
        file_put_contents($this->cache_file, json_encode($cache_data, JSON_PRETTY_PRINT));
    }

    public function loadCache()
    {
        if (file_exists($this->cache_file)) {
            return json_decode(file_get_contents($this->cache_file), true);
        }

        return [];
    }

    public function saveSuiteFile($files)
    {
        $this->ensureDirectory();
        $addtest = implode("\n        ", array_map(function ($file) {
            return '$suite->addTestFile('.var_export($file, true).');';
        }, $files));
        $suite = <<<EOD
<?php class SmartrunnerSuite {
    public static function suite() {
        \$suite = new PHPUnit_Framework_TestSuite('Smartrunner');
        $addtest
        return \$suite;
    }
}
EOD;
        file_put_contents(self::CACHE_DIR.DIRECTORY_SEPARATOR.self::SUITE_FILE, $suite);
    }

    public function savePhpUnitConfig()
    {
        $config = <<<EOD
<?xml version="1.0"?>
<phpunit>
  <listeners>
    <listener class="iakio\phpunit\smartrunner\DependencyListener"></listener>
  </listeners>
</phpunit>
EOD;
        $this->ensureDirectory();
        file_put_contents($this->phpunit_config_file, $config);
    }

    public function saveConfigFile(array $config)
    {
        $this->ensureDirectory();
        file_put_contents($this->config_file, json_encode($config, JSON_PRETTY_PRINT));
    }

    public function loadConfig()
    {
        if (file_exists($this->config_file)) {
            return json_decode(file_get_contents($this->config_file), true);
        }

        return [];
    }
}
