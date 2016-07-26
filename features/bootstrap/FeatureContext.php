<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->smartrunner = 'php bin/smartrunner';
    }

    /**
     * @BeforeScenario
     */
    public function removeCacheDir()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            exec('rd /S /Q .smartrunner');
        } else {
            exec('rm -rf .smartrunner');
        }
    }

    /**
     * @When I initialize smartrunner
     */
    public function iInitializeSmartrunner()
    {
        exec($this->smartrunner." init");
    }

    /**
     * @When I run smartrunner with argument :arg
     */
    public function iRunSmartrunnerWithArgument($arg)
    {
        $file = str_replace("/", DIRECTORY_SEPARATOR, $arg);
        exec($this->smartrunner." run $file", $output);
        $this->output = trim(implode("\n", $output));
    }

    /**
     * @Then :dir directory should be created
     */
    public function directoryShouldBeCreated($dir)
    {
        PHPUnit_Framework_Assert::assertTrue(is_dir('.smartrunner'));
    }

    /**
     * @Then /^(\d+) tests? and (\d+) assertions? should be executed$/
     */
    public function testShouldBeExecuted($tests, $assertions)
    {
        PHPUnit_Framework_Assert::assertRegExp("/OK \\($tests tests?, $assertions assertions?\\)/", $this->output);
    }

    /**
     * @When I have phpunit.phar file
     */
    public function iHavePhpunitPharFile()
    {
        if (!file_exists('phpunit.phar')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://phar.phpunit.de/phpunit.phar');
            curl_setopt($ch, CURLOPT_FILE, fopen('phpunit.phar', 'w'));
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_exec($ch);
        }
    }

    /**
     * @When I set up my configuration file as
     */
    public function iSetUpMyConfigurationFileAs(PyStringNode $string)
    {
        file_put_contents('.smartrunner/config.json', (string) $string);
    }

    /**
     * @Then Cache should contain :num entries
     */
    public function cacheShouldContainEntries($num)
    {
        $cache = json_decode(file_get_contents('.smartrunner/cache.json'), true);
        // ignore `src\\DependencyListener.php`
        $without_listeners = array_filter(array_keys($cache), function ($entry) {
            return preg_match('/^features/', $entry);
        });
        PHPUnit_Framework_Assert::assertEquals($num, count($without_listeners));
    }
}
