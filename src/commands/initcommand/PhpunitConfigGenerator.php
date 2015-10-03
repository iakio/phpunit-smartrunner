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


    public function generate($original)
    {
        $this->doc->loadXML($original);

        $listeners_nodes = $this->doc->getElementsByTagName('listeners');
        if ($listeners_nodes->length === 0) {
            $listeners = $this->doc->firstChild->appendChild($this->doc->createElement('listeners'));
        } else {
            $listeners = $listeners_nodes->item(0);
        }
        $listeners->appendChild($this->createListenerNode());

        return $this->doc->saveXML();
    }
}
