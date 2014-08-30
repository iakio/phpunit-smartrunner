<?php
namespace iakio\phpunit\smartrunner;

use PHPUnit_TextUI_Command;

class Command extends PHPUnit_TextUI_Command
{

    /**
     * @var string
     */
    private $root;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct()
    {
        $this->root = getcwd();
        $this->cache = new Cache($this->root . DIRECTORY_SEPARATOR . ".smartrunner.cache");
    }

    protected function handleArguments(array $argv)
    {
        parent::handleArguments($argv);

        // add TestListener
        $this->arguments['listeners'] = array(
            new DependencyListener($this->cache)
        );

        $original_test_file = Utils::normalizePath($this->root, $this->arguments['testFile']);
        $suite_file = $this->root . DIRECTORY_SEPARATOR . ".smartrunner.suite.php";
        if (count($this->cache->get($original_test_file)) === 0) {
            return;
        }
        // replace test target
        $this->arguments['test'] = $suite_file;
        $this->arguments['testFile'] = $suite_file;

        // build test suite
        $add_suite_array = array_map(
            function($file) {
            return '$suite->addTestFile(' . var_export($file, true) . ');';
            }, $this->cache->get($original_test_file)
        );
        var_dump($add_suite_array);

        $add_suite = implode("\n        ", $add_suite_array);
        file_put_contents(
            $suite_file, <<<EOD
<?php
class SmartRunnerSuite {
    public static function suite()
    {
        \$suite = new \PHPUnit_Framework_TestSuite;
        $add_suite
        return \$suite;
    }
}
EOD
        );
    }
}
