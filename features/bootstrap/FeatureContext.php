<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

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
        $this->smartrunner = "php bin/smartrunner";
    }

    /**
     * @BeforeScenario
     */
    public function removeCacheDir()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            exec("rd /S /Q .smartrunner");
        } else {
            exec("rm -rf .smartrunner");
        }
    }

    /**
     * @When I run smartrunner with argument :arg
     */
    public function iRunSmartrunnerWithArgument($arg)
    {
        exec($this->smartrunner . " $arg", $output);
        $this->output = trim(implode("\n", $output));
    }

    /**
     * @Then :dir directory should be created
     */
    public function directoryShouldBeCreated($dir)
    {
        PHPUnit_Framework_Assert::assertTrue(is_dir(".smartrunner"));
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
        if (!file_exists("phpunit.phar")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://phar.phpunit.de/phpunit.phar');
            curl_setopt($ch, CURLOPT_FILE, fopen('phpunit.phar', 'w'));
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_exec($ch);
        }
    }

    /**
     * @When I set up my configuration file as
     */
    public function iSetUpMyConfigurationFileAs(PyStringNode $string)
    {
        file_put_contents(".smartrunner/config.json", (string) $string);
    }
}
