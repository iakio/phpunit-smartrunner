<?php

/*
 * This file is part of phpunit-smartrunner.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iakio\phpunit\smartrunner\commands\initcommand;

use DOMDocument;
use DOMAttr;
use DOMElement;
use DOMXPath;
use DOMText;
use Webmozart\PathUtil\Path;

class PhpunitConfigGenerator
{
    private $doc;

    public function __construct()
    {
        $this->doc = new DOMDocument();
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = true;
    }

    private function createListenerNode()
    {
        $node = $this->doc->createElement('listener');
        $node->setAttributeNode(new DomAttr('class', 'iakio\phpunit\smartrunner\DependencyListener'));

        return $node;
    }

    private function fixSuitePath($fix_path)
    {
        $xpath = new DOMXPath($this->doc);
        $node_list = $xpath->query('//testsuite/directory | //testsuite/file | //testsuite/exclude');
        foreach ($node_list as $node) {
            if (Path::isRelative($path = $node->textContent)) {
                $new_text = new DOMText(Path::canonicalize($fix_path.'/'.$path));
                if ($node->firstChild) {
                    $node->replaceChild($new_text, $node->firstChild);
                } else {
                    $node->appendChild($new_text);
                }
            }
        }
    }

    private function fixBootstrapPaht($fix_path, DOMElement $node)
    {
        if ($node->hasAttribute('bootstrap')) {
            $node->setAttribute('bootstrap', Path::canonicalize($fix_path.'/'.$node->getAttribute('bootstrap')));
        }
    }

    public function generate($original, $fix_path = null)
    {
        $this->doc->loadXML($original);

        $phpunit_nodes = $this->doc->getElementsByTagName('phpunit');
        $listeners_nodes = $this->doc->getElementsByTagName('listeners');
        if ($listeners_nodes->length === 0) {
            $listeners = $phpunit_nodes->item(0)->appendChild($this->doc->createElement('listeners'));
        } else {
            $listeners = $listeners_nodes->item(0);
        }
        $listeners->appendChild($this->createListenerNode());

        if ($fix_path) {
            $this->fixSuitePath($fix_path);
            $this->fixBootstrapPaht($fix_path, $phpunit_nodes->item(0));
        }

        return $this->doc->saveXML();
    }
}
