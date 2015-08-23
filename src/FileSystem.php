<?php
namespace iakio\phpunit\smartrunner;

class FileSystem
{
    /**
     * @var string
     */
    private $root;

    const CACHE_DIR = '.smartrunner';

    const CACHE_FILE = 'cache.json';

    const SUITE_FILE = 'suite.php';

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

    private function ensureDirectory()
    {
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir);
        }
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
            return "\$suite->addTestFile(" . var_export($file, true) . ");";
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
        file_put_contents(self::CACHE_DIR . DIRECTORY_SEPARATOR . self::SUITE_FILE, $suite);
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
        file_put_contents(self::CACHE_DIR . DIRECTORY_SEPARATOR . 'phpunit.xml.dist', $config);
    }
}
