<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner;

use iakio\phpunit\smartrunner\Smartrunner;
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

    public function fileExists($path)
    {
        return file_exists($path);
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

    public function cacheDir()
    {
        return $this->relativePath($this->cache_dir);
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

    public function savePhpUnitConfig($config)
    {
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
        $config = Smartrunner::defaultConfig();
        if (file_exists($this->config_file)) {
            $config = array_merge($config, json_decode(file_get_contents($this->config_file), true));
        }

        return $config;
    }
}
