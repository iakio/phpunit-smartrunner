<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Behat context class.
 */
class FeatureContext implements SnippetAcceptingContext
{
    private $output;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @BeforeScenario
     */
    public function before($event)
    {
        exec("rm -rf behat_tests"); 
        exec("mkdir behat_tests"); 
    }

    /**
     * @AfterScenario
     */
    public function after($event)
    {
    }

    /**
     * @Given I have a file named :filename with:
     */
    public function iHaveAFileNamedWith($filename, PyStringNode $string)
    {
        file_put_contents($filename, $string->getRaw());
    }

    /**
     * @When I run a smartrunner with no argument
     */
    public function iRunASmartrunnerWithNoArgument()
    {
        exec("bin/smartrunner test", $output);
        $this->output = trim(implode("\n", $output));
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $string)
    {
        if (strpos($this->output, ((string) $string)) === false) {
            throw new Exception(
                "Actual output is:\n" . $this->output
            );
        }
    }

    /**
     * @When I run a smartrunner with argument :arg1
     */
    public function iRunASmartrunnerWithArgument($arg1)
    {
        exec("bin/smartrunner test $arg1", $output);
        $this->output = trim(implode("\n", $output));
    }
}
